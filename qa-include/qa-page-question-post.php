<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-page-question-post.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: More control for question page if it's submitted by HTTP POST


	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	More about this license: http://www.question2answer.org/license.php
*/

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../');
		exit;
	}

	require_once QA_INCLUDE_DIR.'qa-app-cookies.php';
	require_once QA_INCLUDE_DIR.'qa-app-limits.php';
	require_once QA_INCLUDE_DIR.'qa-app-post-create.php';
	require_once QA_INCLUDE_DIR.'qa-app-post-update.php';


//	Process incoming answer (or button)

	if ($question['answerbutton']) {
		if (qa_clicked('doanswerq'))
			qa_redirect($qa_request, array('state' => 'answer'));
		
		if (qa_clicked('doansweradd') || ($qa_state=='answer'))
			switch (qa_user_permit_error('permit_post_a', 'A')) {
				case 'login':
					$pageerror=qa_insert_login_links(qa_lang_html('question/answer_must_login'), $qa_request);
					break;
					
				case 'confirm':
					$pageerror=qa_insert_login_links(qa_lang_html('question/answer_must_confirm'), $qa_request);
					break;
					
				case 'limit':
					$pageerror=qa_lang_html('question/answer_limit');
					break;
				
				default:
					$pageerror=qa_lang_html('users/no_permission');
					break;
					
				case false:
					if (qa_clicked('doansweradd')) {
						$innotify=qa_post_text('notify') ? true : false;
						$inemail=qa_post_text('email');
						
						qa_get_post_content('editor', 'content', $ineditor, $incontent, $informat, $intext);
						
						$errors=qa_answer_validate($incontent, $informat, $intext, $innotify, $inemail);
						
						if ($usecaptcha)
							qa_captcha_validate($_POST, $errors);
						
						if (empty($errors)) {
							$isduplicate=false;
							foreach ($answers as $answer)
								if (!$answer['hidden'])
									if (implode(' ', qa_string_to_words($answer['content'])) == implode(' ', qa_string_to_words($incontent)))
										$isduplicate=true;
							
							if (!$isduplicate) {
								if (!isset($qa_login_userid))
									$qa_cookieid=qa_cookie_get_create(); // create a new cookie if necessary
					
								$answerid=qa_answer_create($qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid,
									$incontent, $informat, $intext, $innotify, $inemail, $question);
								qa_report_write_action($qa_login_userid, $qa_cookieid, 'a_post', $questionid, $answerid, null);
								qa_redirect($qa_request, null, null, null, qa_anchor('A', $answerid));
								
							} else {
								$pageerror=qa_lang_html('question/duplicate_content');
							}
	
						} else {
							$formtype='a_add'; // show form again
						}

					} else {
						$formtype='a_add'; // show form as if first time
					}
					break;
			}
	}


//	Process incoming selection of the best answer
	
	if ($question['aselectable']) {
		if (qa_clicked('select_'))
			$inselect=''; // i.e. unselect current selection
		
		foreach ($answers as $answerid => $answer)
			if (qa_clicked('select_'.$answerid)) {
				$inselect=$answerid;
				break;
			}
	
		if (isset($inselect)) {
			qa_question_set_selchildid($qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, $question, strlen($inselect) ? $inselect : null, $answers);
			qa_report_write_action($qa_login_userid, $qa_cookieid, strlen($inselect) ? 'a_select' : 'a_unselect',
				$questionid, strlen($inselect) ? $answerid : $question['selchildid'], null);
			qa_redirect($qa_request, null, null, null, qa_anchor('A', $answerid));
		}
	}


//	Process hiding or showing or claiming or comment on a question
		
	if (qa_clicked('dohideq') && $question['hideable']) {
		qa_question_set_hidden($question, true, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, $answers, $commentsfollows);
		qa_report_write_action($qa_login_userid, $qa_cookieid, 'q_hide', $questionid, null, null);
		qa_redirect($qa_request);
	}
	
	if (qa_clicked('doshowq') && $question['reshowable']) {
		qa_question_set_hidden($question, false, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, $answers, $commentsfollows);
		qa_report_write_action($qa_login_userid, $qa_cookieid, 'q_reshow', $questionid, null, null);
		qa_redirect($qa_request);
	}
	
	if (qa_clicked('dodeleteq') && $question['deleteable']) {
		qa_question_delete($question, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid);
		qa_report_write_action($qa_login_userid, $qa_cookieid, 'q_delete', $questionid, null, null);
		qa_redirect(''); // redirect since question has gone
	}
	
	if (qa_clicked('doclaimq') && $question['claimable']) {
		if (qa_limits_remaining($qa_login_userid, 'Q')) { // already checked 'permit_post_q'
			qa_question_set_userid($question, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid);
			qa_report_write_action($qa_login_userid, $qa_cookieid, 'q_claim', $questionid, null, null);
			qa_redirect($qa_request);

		} else
			$pageerror=qa_lang_html('question/ask_limit');
	}
	

//	Process flag or unflag button for question

	if (qa_clicked('doflagq') && $question['flagbutton']) {
		require_once QA_INCLUDE_DIR.'qa-app-votes.php';
		
		$pageerror=qa_flag_error_html($question, $qa_login_userid, $qa_request);
		if (!$pageerror) {
			if (qa_flag_set_tohide($question, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, $question))
				qa_question_set_hidden($question, true, null, null, null, $answers, $commentsfollows); // hiding not really by this user so pass nulls

			qa_redirect($qa_request);
		}
	}
	
	if (qa_clicked('dounflagq') && $question['unflaggable']) {
		require_once QA_INCLUDE_DIR.'qa-app-votes.php';
		
		qa_flag_clear($question, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid);
		qa_redirect($qa_request);
	}
	
	if (qa_clicked('doclearflagsq') && $question['clearflaggable']) {
		require_once QA_INCLUDE_DIR.'qa-app-votes.php';
	
		qa_flags_clear_all($question, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid);
		qa_redirect($qa_request);
	}

	
//	Process edit or save button for question

	if ($question['editbutton']) {
		if (qa_clicked('docancel'))
			qa_redirect($qa_request);
		
		elseif (qa_clicked('doeditq'))
			qa_redirect($qa_request, array('state' => 'edit-'.$questionid));
			
		elseif (qa_clicked('dosaveq') && qa_page_q_permit_edit($question, 'permit_edit_q')) {
			$incategoryid=qa_get_category_field_value('category');
			$inqtitle=qa_post_text('qtitle');

			$inqtags=qa_get_tags_field_value('qtags');
			$tagstring=qa_using_tags() ? qa_tags_to_tagstring($inqtags) : $question['tags'];
	
			qa_get_post_content('editor', 'qcontent', $ineditor, $inqcontent, $inqformat, $inqtext);

			$innotify=qa_post_text('notify') ? true : false;
			$inemail=qa_post_text('email');
			
			$qerrors=qa_question_validate($inqtitle, $inqcontent, $inqformat, $inqtext, $tagstring, $innotify, $inemail);
			
			if (empty($qerrors)) {
				$setnotify=$question['isbyuser'] ? qa_combine_notify_email($question['userid'], $innotify, $inemail) : $question['notify'];
				
				if (qa_using_categories() && strcmp($incategoryid, $question['categoryid']))
					qa_question_set_category($question, $incategoryid,
						$qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, $answers, $commentsfollows);
				
				qa_question_set_content($question, $inqtitle, $inqcontent, $inqformat, $inqtext, $tagstring, $setnotify,
					$qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid);

				qa_report_write_action($qa_login_userid, $qa_cookieid, 'q_edit', $questionid, null, null);
				
				if (qa_q_request($questionid, $question['title']) != qa_q_request($questionid, $inqtitle))
					qa_redirect(qa_q_request($questionid, $inqtitle)); // redirect if URL changed
				else
					qa_redirect($qa_request);
			
			} else
				$formtype='q_edit'; // keep editing if an error

		} else if (($qa_state==('edit-'.$questionid)) && qa_page_q_permit_edit($question, 'permit_edit_q'))
			$formtype='q_edit';
		
		if ($formtype=='q_edit') { // get tags for auto-completion
			if (qa_opt('do_complete_tags'))
				$completetags=array_keys(qa_db_select_with_pending(qa_db_popular_tags_selectspec(0, QA_DB_RETRIEVE_COMPLETE_TAGS)));
			else
				$completetags=array();
		}
	}
	

//	Process adding a comment to question (shows form or processes it)
	
	if ($question['commentbutton']) {
		if (qa_clicked('docommentq'))
			qa_redirect($qa_request, array('state' => 'comment-'.$questionid));
			
		if (qa_clicked('docommentaddq') || ($qa_state==('comment-'.$questionid)))
			qa_page_q_do_comment(null);
	}


//	Process hide, show, delete, flag, unflag, edit, save, comment or follow-on button for answers

	foreach ($answers as $answerid => $answer) {
		if (qa_clicked('dohidea_'.$answerid) && $answer['hideable']) {
			qa_answer_set_hidden($answer, true, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, $question, $commentsfollows);
			qa_report_write_action($qa_login_userid, $qa_cookieid, 'a_hide', $questionid, $answerid, null);
			qa_redirect($qa_request, null, null, null, qa_anchor('A', $answerid));
		}
		
		if (qa_clicked('doshowa_'.$answerid) && $answer['reshowable']) {
			qa_answer_set_hidden($answer, false, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, $question, $commentsfollows);
			qa_report_write_action($qa_login_userid, $qa_cookieid, 'a_reshow', $questionid, $answerid, null);
			qa_redirect($qa_request, null, null, null, qa_anchor('A', $answerid));
		}
		
		if (qa_clicked('dodeletea_'.$answerid) && $answer['deleteable']) {
			qa_answer_delete($answer, $question, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid);
			qa_report_write_action($qa_login_userid, $qa_cookieid, 'a_delete', $questionid, $answerid, null);
			qa_redirect($qa_request);
		}
		
		if (qa_clicked('doclaima_'.$answerid) && $answer['claimable']) {
			if (qa_limits_remaining($qa_login_userid, 'A')) { // already checked 'permit_post_a'
				qa_answer_set_userid($answer, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid);
				qa_report_write_action($qa_login_userid, $qa_cookieid, 'a_claim', $questionid, $answerid, null);
				qa_redirect($qa_request, null, null, null, qa_anchor('A', $answerid));
			
			} else
				$pageerror=qa_lang_html('question/answer_limit');
		}
		
		if (qa_clicked('doflaga_'.$answerid) && $answer['flagbutton']) {
			require_once QA_INCLUDE_DIR.'qa-app-votes.php';
			
			$pageerror=qa_flag_error_html($answer, $qa_login_userid, $qa_request);
			if (!$pageerror) {
				if (qa_flag_set_tohide($answer, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, $question))
					qa_answer_set_hidden($answer, true, null, null, null, $question, $commentsfollows); // hiding not really by this user so pass nulls
					
				qa_redirect($qa_request, null, null, null, qa_anchor('A', $answerid));
			}
		}

		if (qa_clicked('dounflaga_'.$answerid) && $answer['unflaggable']) {
			require_once QA_INCLUDE_DIR.'qa-app-votes.php';
			
			qa_flag_clear($answer, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid);
			qa_redirect($qa_request, null, null, null, qa_anchor('A', $answerid));
		}
		
		if (qa_clicked('doclearflagsa_'.$answerid) && $answer['clearflaggable']) {
			require_once QA_INCLUDE_DIR.'qa-app-votes.php';
			
			qa_flags_clear_all($answer, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid);
			qa_redirect($qa_request, null, null, null, qa_anchor('A', $answerid));
		}

		if ($answer['editbutton']) {
			if (qa_clicked('docancel'))
				qa_redirect($qa_request);
			
			elseif (qa_clicked('doedita_'.$answerid))
				qa_redirect($qa_request, array('state' => 'edit-'.$answerid));
				
			elseif (qa_clicked('dosavea_'.$answerid) && qa_page_q_permit_edit($answer, 'permit_edit_a')) {
				$innotify=qa_post_text('notify') ? true : false;
				$inemail=qa_post_text('email');
				$intocomment=qa_post_text('tocomment');
				$incommenton=qa_post_text('commenton');
				
				qa_get_post_content('editor', 'acontent', $ineditor, $inacontent, $inaformat, $inatext);
				
				$aerrors=qa_answer_validate($inacontent, $inaformat, $inatext, $innotify, $inemail);
				
				if (empty($aerrors)) {
					$setnotify=$answer['isbyuser'] ? qa_combine_notify_email($answer['userid'], $innotify, $inemail) : $answer['notify'];
					
					if ($intocomment && (
						(($incommenton==$questionid) && $question['commentable']) ||
						(($incommenton!=$answerid) && @$answers[$incommenton]['commentable'])
					)) { // convert to a comment
						if (qa_limits_remaining($qa_login_userid, 'C')) { // already checked 'permit_post_c'
							qa_answer_to_comment($answer, $incommenton, $inacontent, $inaformat, $inatext, $setnotify,
								$qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, $question, $answers, $commentsfollows);
							qa_report_write_action($qa_login_userid, $qa_cookieid, 'a_to_c', $questionid, $answerid, null);
							qa_redirect($qa_request, null, null, null, qa_anchor('C', $answerid));

						} else {
							$pageerror=qa_lang_html('question/comment_limit');
						}
					
					} else {
						qa_answer_set_content($answer, $inacontent, $inaformat, $inatext, $setnotify,
							$qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, $question);
						qa_report_write_action($qa_login_userid, $qa_cookieid, 'a_edit', $questionid, $answerid, null);
						qa_redirect($qa_request, null, null, null, qa_anchor('A', $answerid));
					}

				} else {
					$formtype='a_edit';
					$formpostid=$answerid; // keep editing if an error
				}

			} elseif (($qa_state==('edit-'.$answerid)) && qa_page_q_permit_edit($answer, 'permit_edit_a')) {
				$formtype='a_edit';
				$formpostid=$answerid;
			}
		}
		
		if ($answer['commentbutton']) {
			if (qa_clicked('docommenta_'.$answerid))
				qa_redirect($qa_request, array('state' => 'comment-'.$answerid));
				
			if (qa_clicked('docommentadda_'.$answerid) || ($qa_state==('comment-'.$answerid)))
				qa_page_q_do_comment($answer);
		}

		if (qa_clicked('dofollowa_'.$answerid)) {
			$params=array('follow' => $answerid);
			if (isset($question['categoryid']))
				$params['cat']=$question['categoryid'];
			
			qa_redirect('ask', $params);
		}
	}


//	Process hide, show, delete, flag, unflag, edit or save button for comments

	foreach ($commentsfollows as $commentid => $comment)
		if ($comment['basetype']=='C') {
			$commentanswer=@$answers[$comment['parentid']];

			if (isset($commentanswer)) {
				$commentparenttype='A';
				$commentanswerid=$commentanswer['postid'];
			
			} else {
				$commentparenttype='Q';
				$commentanswerid=null;
			}

			$commentanswer=@$answers[$comment['parentid']];
			$commentanswerid=$commentanswer['postid'];
			
			if (qa_clicked('dohidec_'.$commentid) && $comment['hideable']) {
				qa_comment_set_hidden($comment, true, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, $question, $commentanswer);
				qa_report_write_action($qa_login_userid, $qa_cookieid, 'c_hide', $questionid, $commentanswerid, $commentid);
				qa_redirect($qa_request, null, null, null, qa_anchor($commentparenttype, $comment['parentid']));
			}
			
			if (qa_clicked('doshowc_'.$commentid) && $comment['reshowable']) {
				qa_comment_set_hidden($comment, false, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, $question, $commentanswer);
				qa_report_write_action($qa_login_userid, $qa_cookieid, 'c_reshow', $questionid, $commentanswerid, $commentid);
				qa_redirect($qa_request, null, null, null, qa_anchor($commentparenttype, $comment['parentid']));
			}
			
			if (qa_clicked('dodeletec_'.$commentid) && $comment['deleteable']) {
				qa_comment_delete($comment, $question, $commentanswer, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid);
				qa_report_write_action($qa_login_userid, $qa_cookieid, 'c_delete', $questionid, $commentanswerid, $commentid);
				qa_redirect($qa_request, null, null, null, qa_anchor($commentparenttype, $comment['parentid']));
			}
			
			if (qa_clicked('doclaimc_'.$commentid) && $comment['claimable']) {
				if (qa_limits_remaining($qa_login_userid, 'C')) {
					qa_comment_set_userid($comment, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid);
					qa_report_write_action($qa_login_userid, $qa_cookieid, 'c_claim', $questionid, $commentanswerid, $commentid);
					qa_redirect($qa_request, null, null, null, qa_anchor($commentparenttype, $comment['parentid']));
					
				} else
					$pageerror=qa_lang_html('question/comment_limit');
			}
			
			if (qa_clicked('doflagc_'.$commentid) && $comment['flagbutton']) {
				require_once QA_INCLUDE_DIR.'qa-app-votes.php';
				
				$pageerror=qa_flag_error_html($comment, $qa_login_userid, $qa_request);
				if (!$pageerror) {
					if (qa_flag_set_tohide($comment, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, $question))
						qa_comment_set_hidden($comment, true, null, null, null, $question, $commentanswer); // hiding not really by this user so pass nulls
					
					qa_redirect($qa_request, null, null, null, qa_anchor($commentparenttype, $comment['parentid']));
				}
			}

			if (qa_clicked('dounflagc_'.$commentid) && $comment['unflaggable']) {
				require_once QA_INCLUDE_DIR.'qa-app-votes.php';
				
				qa_flag_clear($comment, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid);
				qa_redirect($qa_request, null, null, null, qa_anchor($commentparenttype, $comment['parentid']));
			}
			
			if (qa_clicked('doclearflagsc_'.$commentid) && $comment['clearflaggable']) {
				require_once QA_INCLUDE_DIR.'qa-app-votes.php';
				
				qa_flags_clear_all($comment, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid);
				qa_redirect($qa_request, null, null, null, qa_anchor($commentparenttype, $comment['parentid']));
			}

			if ($comment['editbutton']) {
				if (qa_clicked('docancel'))
					qa_redirect($qa_request);
					
				elseif (qa_clicked('doeditc_'.$commentid))
					qa_redirect($qa_request, array('state' => 'edit-'.$commentid));
				
				elseif (qa_clicked('dosavec_'.$commentid) && qa_page_q_permit_edit($comment, 'permit_edit_c')) {
					$innotify=qa_post_text('notify') ? true : false;
					$inemail=qa_post_text('email');
					
					qa_get_post_content('editor', 'comment', $ineditor, $incomment, $informat, $intext);
					
					$errors=qa_comment_validate($incomment, $informat, $intext, $innotify, $inemail);
					
					if (empty($errors)) {
						$setnotify=$comment['isbyuser'] ? qa_combine_notify_email($comment['userid'], $innotify, $inemail) : $comment['notify'];
						
						qa_comment_set_content($comment, $incomment, $informat, $intext, $setnotify,
							$qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, $question, $commentanswer);
						qa_report_write_action($qa_login_userid, $qa_cookieid, 'c_edit', $questionid, $commentanswerid, $commentid);
						qa_redirect($qa_request, null, null, null, qa_anchor($commentparenttype, $comment['parentid']));
					
					} else {
						$formtype='c_edit';
						$formpostid=$commentid; // keep editing if an error
					}

				} elseif (($qa_state==('edit-'.$commentid)) && qa_page_q_permit_edit($comment, 'permit_edit_c')) {
					$formtype='c_edit';
					$formpostid=$commentid;
				}
			}
		}


	function qa_page_q_permit_edit($post, $permitoption)
/*
	Return whether the editing operation (as specified by $permitoption) on $post is permitted.
	If not, set the $pageerror variable appropriately
*/
	{
		global $pageerror, $qa_request;
		
		$permiterror=qa_user_permit_error($post['isbyuser'] ? null : $permitoption);
			// if it's by the user, this will only check whether they are blocked
		
		switch ($permiterror) {
			case 'login':
				$pageerror=qa_insert_login_links(qa_lang_html('question/edit_must_login'), $qa_request);
				break;
				
			case 'confirm':
				$pageerror=qa_insert_login_links(qa_lang_html('question/edit_must_confirm'), $qa_request);
				break;
				
			default:
				$pageerror=qa_lang_html('users/no_permission');
				break;
				
			case false:
				break;
		}
		
		return !$permiterror;
	}


//	Question and answer editing forms

	function qa_page_q_edit_q_form()
/*
	Return form for editing the question and set up $qa_content accordingly
*/
	{
		global $qa_content, $question, $inqtitle, $inqcontent, $inqformat, $inqeditor, $inqtags, $qerrors, $innotify, $inemail, $completetags, $categories;
		
		$content=isset($inqcontent) ? $inqcontent : $question['content'];
		$format=isset($inqformat) ? $inqformat : $question['format'];
		
		$editorname=isset($inqeditor) ? $inqeditor : qa_opt('editor_for_qs');
		$editor=qa_load_editor($content, $format, $editorname);

		$form=array(
			'style' => 'tall',
			
			'fields' => array(
				'title' => array(
					'label' => qa_lang_html('question/q_title_label'),
					'tags' => 'NAME="qtitle"',
					'value' => qa_html(isset($inqtitle) ? $inqtitle : $question['title']),
					'error' => qa_html(@$qerrors['title']),
				),
				
				'category' => array(
					'label' => qa_lang_html('question/q_category_label'),
				),
				
				'content' => array_merge(
					$editor->get_field($qa_content, $content, $format, 'qcontent', 12, true),
					array(
						'label' => qa_lang_html('question/q_content_label'),
						'error' => qa_html(@$qerrors['content']),
					)
				),
				
				'tags' => array(
					'error' => qa_html(@$qerrors['tags']),
				),

			),
			
			'buttons' => array(
				'save' => array(
					'label' => qa_lang_html('main/save_button'),
				),
				
				'cancel' => array(
					'tags' => 'NAME="docancel"',
					'label' => qa_lang_html('main/cancel_button'),
				),
			),
			
			'hidden' => array(
				'editor' => qa_html($editorname),
				'dosaveq' => '1',
			),
		);
		
		if (qa_using_categories() && count($categories))
			qa_set_up_category_field($qa_content, $form['fields']['category'], 'category', $categories,
				isset($incategoryid) ? $incategoryid : $question['categoryid'], 
				qa_opt('allow_no_category') || !isset($question['categoryid']), qa_opt('allow_no_sub_category'));
		else
			unset($form['fields']['category']);
		
		if (qa_using_tags())
			qa_set_up_tag_field($qa_content, $form['fields']['tags'], 'qtags', isset($inqtags) ? $inqtags : qa_tagstring_to_tags($question['tags']),
				array(), $completetags, qa_opt('page_size_ask_tags'));
		else
			unset($form['fields']['tags']);
				
		if ($question['isbyuser'])
			qa_set_up_notify_fields($qa_content, $form['fields'], 'Q', qa_get_logged_in_email(),
				isset($innotify) ? $innotify : !empty($question['notify']),
				isset($inemail) ? $inemail : @$question['notify'], @$qerrors['email']);
		
		return $form;
	}
	

	function qa_page_q_edit_a_form($answerid)
/*
	Return form for editing an answer and set up $qa_content accordingly
*/
	{
		require_once QA_INCLUDE_DIR.'qa-util-string.php';

		global $questionid, $question, $answers, $inacontent, $inaformat, $inaeditor, $aerrors, $qa_content, $innotify, $inemail, $jumptoanchor, $commentsfollows;
		
		$answer=$answers[$answerid];
		
		$content=isset($inacontent) ? $inacontent : $answer['content'];
		$format=isset($inaformat) ? $inaformat : $answer['format'];
		
		$editorname=isset($inaeditor) ? $inaeditor : qa_opt('editor_for_as');
		$editor=qa_load_editor($content, $format, $editorname);
		
		$hascomments=false;
		foreach ($commentsfollows as $commentfollow)
			if ($commentfollow['parentid']==$answerid)
				$hascomments=true;
		
		$form=array(
			'title' => '<A NAME="a_edit">'.qa_lang_html('question/edit_a_title').'</A>',
			
			'style' => 'tall',
			
			'fields' => array(
				'content' => array_merge(
					$editor->get_field($qa_content, $content, $format, 'acontent', 12, true),
					array(
						'error' => qa_html(@$aerrors['content']),
					)
				),
			),
			
			'buttons' => array(
				'save' => array(
					'label' => qa_lang_html('main/save_button'),
				),
				
				'cancel' => array(
					'tags' => 'NAME="docancel"',
					'label' => qa_lang_html('main/cancel_button'),
				),
			),
			
			'hidden' => array(
				'editor' => qa_html($editorname),
				'dosavea_'.qa_html($answerid) => '1',
			),
		);
		
	//	Show option to convert this answer to a comment, if appropriate
		
		$commentonoptions=array();

		$lastbeforeid=$questionid; // used to find last post created before this answer - this is default given
		$lastbeforetime=$question['created'];
		
		if ($question['commentable'])
			$commentonoptions[$questionid]=
				qa_lang_html('question/comment_on_q').qa_html(qa_shorten_string_line($question['title'], 80));
		
		foreach ($answers as $otheranswer)
			if (($otheranswer['postid']!=$answerid) && ($otheranswer['created']<$answer['created']) && $otheranswer['commentable'] && !$otheranswer['hidden']) {
				$commentonoptions[$otheranswer['postid']]=
					qa_lang_html('question/comment_on_a').qa_html(qa_shorten_string_line(qa_viewer_text($otheranswer['content'], $otheranswer['format']), 80));
				
				if ($otheranswer['created']>$lastbeforetime) {
					$lastbeforeid=$otheranswer['postid'];
					$lastebeforetime=$otheranswer['created'];
				}
			}
				
		if (count($commentonoptions)) {
			$form['fields']['tocomment']=array(
				'tags' => 'NAME="tocomment" ID="tocomment"',
				'label' => '<SPAN ID="tocomment_shown">'.qa_lang_html('question/a_convert_to_c_on').'</SPAN>'.
								'<SPAN ID="tocomment_hidden" STYLE="display:none;">'.qa_lang_html('question/a_convert_to_c').'</SPAN>',
				'type' => 'checkbox',
				'tight' => true,
			);
			
			$form['fields']['commenton']=array(
				'tags' => 'NAME="commenton"',
				'id' => 'commenton',
				'type' => 'select',
				'note' => qa_lang_html($hascomments ? 'question/a_convert_warn_cs' : 'question/a_convert_warn'),
				'options' => $commentonoptions,
				'value' => @$commentonoptions[$lastbeforeid],
			);
			
			qa_set_display_rules($qa_content, array(
				'commenton' => 'tocomment',
				'tocomment_shown' => 'tocomment',
				'tocomment_hidden' => '!tocomment',
			));
		}
		
	//	Show notification field if appropriate
		
		if ($answer['isbyuser'])
			qa_set_up_notify_fields($qa_content, $form['fields'], 'A', qa_get_logged_in_email(),
				isset($innotify) ? $innotify : !empty($answer['notify']),
				isset($inemail) ? $inemail : @$answer['notify'], @$aerrors['email']);
		
		$form['c_list']=qa_page_q_comment_follow_list($answer);
		
		$jumptoanchor='a_edit';
		
		return $form;
	}


//	Comment-related functions

	function qa_page_q_do_comment($answer)
/*
	Process an incoming new comment form for $answer, or question if it is null
*/
	{
		global $qa_login_userid, $qa_cookieid, $question, $questionid, $formtype, $formpostid,
			$errors, $reloadquestion, $pageerror, $qa_request, $ineditor, $incomment, $informat, $innotify, $inemail, $commentsfollows, $jumptoanchor, $usecaptcha;
		
		$parent=isset($answer) ? $answer : $question;
		
		switch (qa_user_permit_error('permit_post_c', 'C')) {
			case 'login':
				$pageerror=qa_insert_login_links(qa_lang_html('question/comment_must_login'), $qa_request);
				break;
				
			case 'confirm':
				$pageerror=qa_insert_login_links(qa_lang_html('question/comment_must_confirm'), $qa_request);
				break;
				
			case 'limit':
				$pageerror=qa_lang_html('question/comment_limit');
				break;
				
			default:
				$pageerror=qa_lang_html('users/no_permission');
				break;
				
			case false:
				$incomment=qa_post_text('comment');
	
				if (!isset($incomment)) {
					$formtype='c_add';
					$formpostid=$parent['postid']; // show form first time
				
				} else {
					$innotify=qa_post_text('notify') ? true : false;
					$inemail=qa_post_text('email');
	
					qa_get_post_content('editor', 'comment', $ineditor, $incomment, $informat, $intext);
	
					$errors=qa_comment_validate($incomment, $informat, $intext, $innotify, $inemail);
					
					if ($usecaptcha)
						qa_captcha_validate($_POST, $errors);
	
					if (empty($errors)) {
						$isduplicate=false;
						foreach ($commentsfollows as $comment)
							if (($comment['basetype']=='C') && ($comment['parentid']==$parent['postid']) && (!$comment['hidden']))
								if (implode(' ', qa_string_to_words($comment['content'])) == implode(' ', qa_string_to_words($incomment)))
									$isduplicate=true;
									
						if (!$isduplicate) {
							if (!isset($qa_login_userid))
								$qa_cookieid=qa_cookie_get_create(); // create a new cookie if necessary
							
							$commentid=qa_comment_create($qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, $incomment, $informat, $intext, $innotify, $inemail, $question, $answer, $commentsfollows);
							qa_report_write_action($qa_login_userid, $qa_cookieid, 'c_post', $questionid, @$answer['postid'], $commentid);
							qa_redirect($qa_request, null, null, null, qa_anchor(isset($answer) ? 'A' : 'Q', $parent['postid']));
						
						} else {
							$pageerror=qa_lang_html('question/duplicate_content');
						}
					
					} else {
						$formtype='c_add';
						$formpostid=$parent['postid']; // show form again
					}
				}
				break;
		}
	}

	
	function qa_page_q_add_c_form($answerid)
/*
	Return form for adding a comment on $answerid (or the question if $answerid is null), and set up $qa_content accordingly
*/
	{
		global $qa_content, $incomment, $informat, $errors, $questionid, $ineditor, $innotify, $inemail, $jumptoanchor, $focusonid, $usecaptcha, $qa_login_userid;
		
		$jumptoanchor=isset($answerid) ? qa_anchor('A', $answerid) : qa_anchor('Q', $questionid);
		$focusonid='comment';
		
		$editorname=isset($ineditor) ? $ineditor : qa_opt('editor_for_cs');
		$editor=qa_load_editor(@$incomment, @$informat, $editorname);

		$form=array(
			'title' => qa_lang_html(isset($answerid) ? 'question/your_comment_a' : 'question/your_comment_q'),

			'style' => 'tall',
			
			'fields' => array(
				'content' => array_merge(
					$editor->get_field($qa_content, @$incomment, @$informat, 'comment', 4, true),
					array(
						'error' => qa_html(@$errors['content']),
					)
				),
			),
			
			'buttons' => array(
				'comment' => array(
					'tags' => 'NAME="'.(isset($answerid) ? ('docommentadda_'.$answerid) : 'docommentaddq').'"',
					'label' => qa_lang_html('question/add_comment_button'),
				),
				
				'cancel' => array(
					'tags' => 'NAME="docancel"',
					'label' => qa_lang_html('main/cancel_button'),
				),
			),
			
			'hidden' => array(
				'editor' => qa_html($editorname),
			),
		);

		qa_set_up_notify_fields($qa_content, $form['fields'], 'C', qa_get_logged_in_email(),
			isset($innotify) ? $innotify : qa_opt('notify_users_default'), @$inemail, @$errors['email']);
		
		if ($usecaptcha)
			qa_set_up_captcha_field($qa_content, $form['fields'], @$errors,
				qa_insert_login_links(qa_lang_html(isset($qa_login_userid) ? 'misc/captcha_confirm_fix' : 'misc/captcha_login_fix')));
				
		return $form;
	}

	
	function qa_page_q_edit_c_form($commentid, $answerid)
/*
	Return form for editing $commentid on $answerid (or the question if $answerid is null), and set up $qa_content accordingly
*/
	{
		global $commentsfollows, $qa_content, $errors, $incomment, $informat, $ineditor, $questionid, $jumptoanchor, $focusonid, $innotify, $inemail;
		
		$comment=$commentsfollows[$commentid];
		
		$content=isset($incomment) ? $incomment : $comment['content'];
		$format=isset($informat) ? $informat : $comment['format'];
		
		$editorname=isset($ineditor) ? $ineditor : qa_opt('editor_for_cs');
		$editor=qa_load_editor($content, $format, $editorname);
		
		$jumptoanchor=isset($answerid) ? qa_anchor('A', $answerid) : qa_anchor('Q', $questionid);
		
		$form=array(
			'title' => '<A NAME="edit">'.qa_lang_html('question/edit_c_title').'</A>',
			
			'style' => 'tall',
			
			'fields' => array(
				'content' => array_merge(
					$editor->get_field($qa_content, $content, $format, 'comment', 4, true),
					array(
						'error' => qa_html($errors['content']),
					)
				),
			),
			
			'buttons' => array(
				'save' => array(
					'label' => qa_lang_html('main/save_button'),
				),
				
				'cancel' => array(
					'tags' => 'NAME="docancel"',
					'label' => qa_lang_html('main/cancel_button'),
				),
			),
			
			'hidden' => array(
				'editor' => qa_html($editorname),
				'dosavec_'.qa_html($commentid) => '1',
			),
		);
		
		if ($comment['isbyuser'])
			qa_set_up_notify_fields($qa_content, $form['fields'], 'C', qa_get_logged_in_email(),
				isset($innotify) ? $innotify : !empty($comment['notify']),
				isset($inemail) ? $inemail : @$comment['notify'], @$errors['email']);

		return $form;
	}
	

/*
	Omit PHP closing tag to help avoid accidental output
*/