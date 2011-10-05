<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-page-question.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Controller for question page (only viewing functionality here)


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

	require_once QA_INCLUDE_DIR.'qa-app-format.php';
	require_once QA_INCLUDE_DIR.'qa-db-selects.php';
	require_once QA_INCLUDE_DIR.'qa-util-sort.php';
	require_once QA_INCLUDE_DIR.'qa-util-string.php';
	require_once QA_INCLUDE_DIR.'qa-app-captcha.php';
	
	$questionid=$pass_questionid; // picked up from index.php


//	Get information about this question

	function qa_page_q_load_q()
/*
	Load all the necessary content relating to the question from the database into the appropriate global variables
*/
	{
		global $qa_login_userid, $questionid, $question, $parentquestion, $answers, $commentsfollows,
			$relatedcount, $relatedquestions, $question, $categories;

		list($question, $childposts, $achildposts, $parentquestion, $relatedquestions, $categories)=qa_db_select_with_pending(
			qa_db_full_post_selectspec($qa_login_userid, $questionid),
			qa_db_full_child_posts_selectspec($qa_login_userid, $questionid),
			qa_db_full_a_child_posts_selectspec($qa_login_userid, $questionid),
			qa_db_post_parent_q_selectspec($questionid),
			qa_db_related_qs_selectspec($qa_login_userid, $questionid),
			qa_db_category_nav_selectspec($questionid, true, true)
		);
		
		if ($question['basetype']!='Q') // don't allow direct viewing of other types of post
			$question=null;

		$answers=array();
		$commentsfollows=array();
		
		foreach ($childposts as $postid => $post)
			switch ($post['type']) {
				case 'Q': // never show follow-on Qs which have been hidden, even to admins
				case 'C':
				case 'C_HIDDEN':
					$commentsfollows[$postid]=$post;
					break;
					
				case 'A':
				case 'A_HIDDEN':
					$answers[$postid]=$post;
					break;
			}
		
		foreach ($achildposts as $postid => $post)
			switch ($post['type']) {
				case 'Q':
				case 'Q_HIDDEN':
				case 'C':
				case 'C_HIDDEN':
					$commentsfollows[$postid]=$post;
					break;
			}
		
		if (isset($question)) {
			$relatedcount=qa_opt('do_related_qs') ? (1+qa_opt('page_size_related_qs')) : 0;
			$relatedquestions=array_slice($relatedquestions, 0, $relatedcount); // includes question itself at this point

			qa_page_q_post_rules($question);
			
			if ($question['selchildid'] && (@$answers[$question['selchildid']]['type']!='A'))
				$question['selchildid']=null; // if selected answer is hidden or somehow not there, consider it not selected

			foreach ($answers as $key => $answer) {
				$question['deleteable']=false;
				
				qa_page_q_post_rules($answers[$key]);
				if ($answers[$key]['isbyuser'] && !qa_opt('allow_multi_answers'))
					$question['answerbutton']=false;
				
				$answers[$key]['isselected']=($answer['postid']==$question['selchildid']);
			}
	
			foreach ($commentsfollows as $key => $commentfollow) {
				if ($commentfollow['parentid']==$questionid)
					$question['deleteable']=false;
				
				if (isset($answers[$commentfollow['parentid']]))
					$answers[$commentfollow['parentid']]['deleteable']=false;
					
				qa_page_q_post_rules($commentsfollows[$key]);
			}
		}
	}

	
	function qa_page_q_post_rules(&$post)
/*
	Add elements to the array $post which describe which operations this user may perform on that post
*/
	{
		global $qa_login_userid, $qa_cookieid;
		
		$post['isbyuser']=qa_post_is_by_user($post, $qa_login_userid, $qa_cookieid);

	//	Cache some responses to the user permission checks
	
		$permiterror_post_q=qa_user_permit_error('permit_post_q');
		$permiterror_post_a=qa_user_permit_error('permit_post_a');
		$permiterror_post_c=qa_user_permit_error('permit_post_c');

		$permiterror_edit=qa_user_permit_error(($post['basetype']=='Q') ? 'permit_edit_q' :
			(($post['basetype']=='A') ? 'permit_edit_a' : 'permit_edit_c'));
		$permiterror_hide_show=qa_user_permit_error($post['isbyuser'] ? null : 'permit_hide_show');
	
	//	General permissions
	
		$post['authorlast']=(($post['lastuserid']===$post['userid']) || !isset($post['lastuserid']));
		$post['viewable']=(!$post['hidden']) || !$permiterror_hide_show;
		
	//	Answer, comment and edit might show the button even if the user still needs to do something (e.g. log in)
		
		$post['answerbutton']=($post['type']=='Q') && ($permiterror_post_a!='level');

		$post['commentbutton']=(($post['type']=='Q') || ($post['type']=='A')) &&
			($permiterror_post_c!='level') &&
			qa_opt(($post['type']=='Q') ? 'comment_on_qs' : 'comment_on_as');
		$post['commentable']=$post['commentbutton'] && !$permiterror_post_c;

		$post['editbutton']=(!$post['hidden']) && ($post['isbyuser'] || ($permiterror_edit!='level'));
		$post['aselectable']=($post['type']=='Q') && !qa_user_permit_error($post['isbyuser'] ? null : 'permit_select_a');

		$post['flagbutton']=qa_opt('flagging_of_posts') && (!$post['isbyuser']) && (!$post['hidden']) &&
			(!@$post['userflag']) && (qa_user_permit_error('permit_flag')!='level');
		$post['flagtohide']=$post['flagbutton'] && (!qa_user_permit_error('permit_flag')) && (($post['flagcount']+1)>=qa_opt('flagging_hide_after'));
		$post['unflaggable']=@$post['userflag'] && (!$post['hidden']);
		$post['clearflaggable']=($post['flagcount']>=(@$post['userflag'] ? 2 : 1)) && !qa_user_permit_error('permit_hide_show');
		
	//	Other actions only show the button if it's immediately possible
		
		$post['hideable']=(!$post['hidden']) && !$permiterror_hide_show;
		$post['reshowable']=$post['hidden'] && (!$permiterror_hide_show) &&
			(($post['authorlast'] && !$post['flagcount']) || (!$post['isbyuser']) || !qa_user_permit_error('permit_hide_show'));
			// can only reshow a question if you're the one who hid it and it hasn't been flagged, or of course if you have general showing permissions
		$post['deleteable']=$post['hidden'] && !qa_user_permit_error('permit_delete_hidden');
			// this does not check the post has no children - that check is performed in qa_page_q_load_q()
		$post['claimable']=(!isset($post['userid'])) && isset($qa_login_userid) && (strcmp(@$post['cookieid'], $qa_cookieid)==0) &&
			!(($post['basetype']=='Q') ? $permiterror_post_q : (($post['basetype']=='A') ? $permiterror_post_a : $permiterror_post_c));
		$post['followable']=($post['type']=='A') ? qa_opt('follow_on_as') : false;
	}

	
	function qa_page_q_comment_follow_list($parent)
/*
	Return a theme-ready structure with all the comments and follow-on questions to show for post $parent (question or answer)
*/
	{
		global $commentsfollows, $qa_login_userid, $qa_cookieid, $usershtml, $formtype, $formpostid, $formrequested;
		
		foreach ($commentsfollows as $commentfollowid => $commentfollow)
			if (($commentfollow['parentid']==$parent['postid']) && $commentfollow['viewable'] && ($commentfollowid!=$formpostid) ) {
				if ($commentfollow['basetype']=='C') {
					$htmloptions=qa_post_html_defaults('C', true);
					$htmloptions['avatarsize']=qa_opt('avatar_q_page_c_size');
					$c_view=qa_post_html_fields($commentfollow, $qa_login_userid, $qa_cookieid, $usershtml, null, $htmloptions);
						

				//	Buttons for operating on this comment
						
					if (!$formrequested) { // don't show if another form is currently being shown on page
						$c_view['form']=array(
							'style' => 'light',
							'buttons' => array(),
						);
					
						if ($commentfollow['editbutton'])
							$c_view['form']['buttons']['edit']=array(
								'tags' => 'NAME="doeditc_'.qa_html($commentfollowid).'"',
								'label' => qa_lang_html('question/edit_button'),
								'popup' => qa_lang_html('question/edit_c_popup'),
							);
							
						if ($commentfollow['flagbutton'])
							$c_view['form']['buttons']['flag']=array(
								'tags' => 'NAME="doflagc_'.qa_html($commentfollowid).'"',
								'label' => qa_lang_html($commentfollow['flagtohide'] ? 'question/flag_hide_button' : 'question/flag_button'),
								'popup' => qa_lang_html('question/flag_c_popup'),
							);
						
						if ($commentfollow['unflaggable'])
							$c_view['form']['buttons']['unflag']=array(
								'tags' => 'NAME="dounflagc_'.qa_html($commentfollowid).'"',
								'label' => qa_lang_html('question/unflag_button'),
								'popup' => qa_lang_html('question/unflag_popup'),
							);
							
						if ($commentfollow['clearflaggable'])
							$c_view['form']['buttons']['clearflags']=array(
								'tags' => 'NAME="doclearflagsc_'.qa_html($commentfollowid).'"',
								'label' => qa_lang_html('question/clear_flags_button'),
								'popup' => qa_lang_html('question/clear_flags_popup'),
							);
	
						if ($commentfollow['hideable'])
							$c_view['form']['buttons']['hide']=array(
								'tags' => 'NAME="dohidec_'.qa_html($commentfollowid).'"',
								'label' => qa_lang_html('question/hide_button'),
								'popup' => qa_lang_html('question/hide_c_popup'),
							);
							
						if ($commentfollow['reshowable'])
							$c_view['form']['buttons']['reshow']=array(
								'tags' => 'NAME="doshowc_'.qa_html($commentfollowid).'"',
								'label' => qa_lang_html('question/reshow_button'),
							);
							
						if ($commentfollow['deleteable'])
							$c_view['form']['buttons']['delete']=array(
								'tags' => 'NAME="dodeletec_'.qa_html($commentfollowid).'"',
								'label' => qa_lang_html('question/delete_button'),
								'popup' => qa_lang_html('question/delete_c_popup'),
							);
							
						if ($commentfollow['claimable'])
							$c_view['form']['buttons']['claim']=array(
								'tags' => 'NAME="doclaimc_'.qa_html($commentfollowid).'"',
								'label' => qa_lang_html('question/claim_button'),
							);
							
						if ($parent['commentbutton'] && qa_opt('show_c_reply_buttons') && !$commentfollow['hidden'])
							$c_view['form']['buttons']['comment']=array(
								'tags' => 'NAME="'.(($parent['basetype']=='Q') ? 'docommentq' : ('docommenta_'.qa_html($parent['postid']))).'"',
								'label' => qa_lang_html('question/reply_button'),
								'popup' => qa_lang_html('question/reply_c_popup'),
							);

					}

				} elseif ($commentfollow['basetype']=='Q') {
					$htmloptions=qa_post_html_defaults('Q');
					$htmloptions['avatarsize']=qa_opt('avatar_q_page_c_size');
					
					$c_view=qa_post_html_fields($commentfollow, $qa_login_userid, $qa_cookieid, $usershtml, null, $htmloptions);
				}

				$commentlist[]=$c_view;
			}
			
		return @$commentlist;
	}


//	Get information about this question

	qa_page_q_load_q();
	
	$usecaptcha=qa_user_use_captcha('captcha_on_anon_post');


//	Deal with question not found or not viewable, otherwise report the view event

	if (!isset($question))
		return include QA_INCLUDE_DIR.'qa-page-not-found.php';

	if (!$question['viewable']) {
		$qa_content=qa_content_prepare();
		
		if ($question['flagcount'] && !isset($question['lastuserid']))
			$qa_content['error']=qa_lang_html('question/q_hidden_flagged');
		elseif ($question['authorlast'])
			$qa_content['error']=qa_lang_html('question/q_hidden_author');
		else
			$qa_content['error']=qa_lang_html('question/q_hidden_other');

		$qa_content['suggest_next']=qa_html_suggest_qs_tags(qa_using_tags());

		return $qa_content;
	}
	
	$permiterror=qa_user_permit_error('permit_view_q_page');
	
	if ( $permiterror && (qa_is_human_probably() || !qa_opt('allow_view_q_bots')) ) {
		$qa_content=qa_content_prepare();
		$topage=qa_q_request($questionid, $question['title']);
		
		switch ($permiterror) {
			case 'login':
				$qa_content['error']=qa_insert_login_links(qa_lang_html('main/view_q_must_login'), $topage);
				break;
				
			case 'confirm':
				$qa_content['error']=qa_insert_login_links(qa_lang_html('main/view_q_must_confirm'), $topage);
				break;
				
			default:
				$qa_content['error']=qa_lang_html('users/no_permission');
				break;
		}
		
		return $qa_content;
	}
	

//	If we're responding to an HTTP POST, include file that handles all posting/editing/etc... logic
//	This is in a separate file because it's a *lot* of logic, and will slow down ordinary page views

	$pageerror=null;
	$formtype=null;
	$formpostid=null;
	$jumptoanchor=null;
	$focusonid=null;
	
	if (qa_is_http_post() || strlen($qa_state)) {
		require QA_INCLUDE_DIR.'qa-page-question-post.php';
		qa_page_q_load_q(); // reload since we may have changed something
	}
	
	$formrequested=isset($formtype);

	if ((!$formrequested) && $question['answerbutton']) {
		$immedoption=qa_opt('show_a_form_immediate');

		if ( ($immedoption=='always') || (($immedoption=='if_no_as') && (!$question['isbyuser']) && (!$question['acount'])) )
			$formtype='a_add'; // show answer form by default
	}
	
	
//	Get information on the users referenced

	$usershtml=qa_userids_handles_html(array_merge(array($question), $answers, $commentsfollows, $relatedquestions), true);
	
	
//	Prepare content for theme
	
	$qa_content=qa_content_prepare(true, array_keys(qa_category_path($categories, $question['categoryid'])));
	
	$qa_content['main_form_tags']='METHOD="POST" ACTION="'.qa_self_html().'"';
	
	if (isset($pageerror))
		$qa_content['error']=$pageerror; // might also show voting error set in qa-index.php
	
	if ($question['hidden'])
		$qa_content['hidden']=true;
	
	qa_sort_by($commentsfollows, 'created');


//	Prepare content for the question...
	
	if ($formtype=='q_edit') { // ...in edit mode
		$qa_content['title']=qa_lang_html('question/edit_q_title');
		$qa_content['form_q_edit']=qa_page_q_edit_q_form();
		$qa_content['q_view']['raw']=$question;

	} else { // ...in view mode
		$htmloptions=qa_post_html_defaults('Q', true);
		$htmloptions['answersview']=false; // answer count is displayed separately so don't show it here
		$htmloptions['avatarsize']=qa_opt('avatar_q_page_q_size');
		
		$qa_content['q_view']=qa_post_html_fields($question, $qa_login_userid, $qa_cookieid, $usershtml, null, $htmloptions);
		
		$qa_content['title']=$qa_content['q_view']['title'];
		
		$qa_content['description']=qa_html(qa_shorten_string_line(qa_viewer_text($question['content'], $question['format']), 150));
		
		$qa_content['canonical']=qa_path_html(qa_q_request($question['postid'], $question['title']), null, qa_opt('site_url'));
		
		$categorykeyword=@$categories[$question['categoryid']]['title'];
		
		$qa_content['keywords']=qa_html(implode(',', array_merge(
			(qa_using_categories() && strlen($categorykeyword)) ? array($categorykeyword) : array(),
			qa_tagstring_to_tags($question['tags'])
		))); // as far as I know, META keywords have zero effect on search rankings or listings, but many people have asked for this
		

	//	Buttons for operating on the question
		
		if (!$formrequested) { // don't show if another form is currently being shown on page
			$qa_content['q_view']['form']=array(
				'style' => 'light',
				'buttons' => array(),
			);
			
			if ($question['editbutton'])
				$qa_content['q_view']['form']['buttons']['edit']=array(
					'tags' => 'NAME="doeditq"',
					'label' => qa_lang_html('question/edit_button'),
					'popup' => qa_lang_html('question/edit_q_popup'),
				);
				
			if ($question['flagbutton'])
				$qa_content['q_view']['form']['buttons']['flag']=array(
					'tags' => 'NAME="doflagq"',
					'label' => qa_lang_html($question['flagtohide'] ? 'question/flag_hide_button' : 'question/flag_button'),
					'popup' => qa_lang_html('question/flag_q_popup'),
				);

			if ($question['unflaggable'])
				$qa_content['q_view']['form']['buttons']['unflag']=array(
					'tags' => 'NAME="dounflagq"',
					'label' => qa_lang_html('question/unflag_button'),
					'popup' => qa_lang_html('question/unflag_popup'),
				);
				
			if ($question['clearflaggable'])
				$qa_content['q_view']['form']['buttons']['clearflags']=array(
					'tags' => 'NAME="doclearflagsq"',
					'label' => qa_lang_html('question/clear_flags_button'),
					'popup' => qa_lang_html('question/clear_flags_popup'),
				);

			if ($question['hideable'])
				$qa_content['q_view']['form']['buttons']['hide']=array(
					'tags' => 'NAME="dohideq"',
					'label' => qa_lang_html('question/hide_button'),
					'popup' => qa_lang_html('question/hide_q_popup'),
				);
				
			if ($question['reshowable'])
				$qa_content['q_view']['form']['buttons']['reshow']=array(
					'tags' => 'NAME="doshowq"',
					'label' => qa_lang_html('question/reshow_button'),
				);
				
			if ($question['deleteable'])
				$qa_content['q_view']['form']['buttons']['delete']=array(
					'tags' => 'NAME="dodeleteq"',
					'label' => qa_lang_html('question/delete_button'),
					'popup' => qa_lang_html('question/delete_q_popup'),
				);
				
			if ($question['claimable'])
				$qa_content['q_view']['form']['buttons']['claim']=array(
					'tags' => 'NAME="doclaimq"',
					'label' => qa_lang_html('question/claim_button'),
				);
			
			if ($question['answerbutton'] && ($formtype!='a_add')) // don't show if shown by default
				$qa_content['q_view']['form']['buttons']['answer']=array(
					'tags' => 'NAME="doanswerq"',
					'label' => qa_lang_html('question/answer_button'),
					'popup' => qa_lang_html('question/answer_q_popup'),
				);
			
			if ($question['commentbutton'])
				$qa_content['q_view']['form']['buttons']['comment']=array(
					'tags' => 'NAME="docommentq"',
					'label' => qa_lang_html('question/comment_button'),
					'popup' => qa_lang_html('question/comment_q_popup'),
				);
		}
		

	//	Information about the question of the answer that this question follows on from (or a question directly)
			
		if (isset($parentquestion)) {
			$parentquestion['title']=qa_block_words_replace($parentquestion['title'], qa_get_block_words_preg());

			$qa_content['q_view']['follows']=array(
				'label' => qa_lang_html(($question['parentid']==$parentquestion['postid']) ? 'question/follows_q' : 'question/follows_a'),
				'title' => qa_html($parentquestion['title']),
				'url' => qa_path_html(qa_q_request($parentquestion['postid'], $parentquestion['title']),
					null, null, null, ($question['parentid']==$parentquestion['postid']) ? null : qa_anchor('A', $question['parentid'])),
			);
		}
			
	}
	

//	Prepare content for an answer being edited (if any)

	if ($formtype=='a_edit')
		$qa_content['q_view']['a_form']=qa_page_q_edit_a_form($formpostid);


//	Prepare content for comments on the question, plus add or edit comment forms

	$qa_content['q_view']['c_list']=qa_page_q_comment_follow_list($question); // ...for viewing
	
	if (($formtype=='c_add') && ($formpostid==$questionid)) // ...to be added
		$qa_content['q_view']['c_form']=qa_page_q_add_c_form(null);
	
	elseif (($formtype=='c_edit') && (@$commentsfollows[$formpostid]['parentid']==$questionid)) // ...being edited
		$qa_content['q_view']['c_form']=qa_page_q_edit_c_form($formpostid, null);
	

//	Prepare content for existing answers

	$qa_content['a_list']['as']=array();
	
	if (qa_opt('sort_answers_by')=='votes') {
		foreach ($answers as $answerid => $answer)
			$answers[$answerid]['sortvotes']=$answer['downvotes']-$answer['upvotes'];

		qa_sort_by($answers, 'sortvotes', 'created');

	} else
		qa_sort_by($answers, 'created');

	$priority=0;

	foreach ($answers as $answerid => $answer)
		if ($answer['viewable'] && !(($formtype=='a_edit') && ($formpostid==$answerid))) {
			$htmloptions=qa_post_html_defaults('A', true);
			$htmloptions['isselected']=$answer['isselected'];
			$htmloptions['avatarsize']=qa_opt('avatar_q_page_a_size');
			$a_view=qa_post_html_fields($answer, $qa_login_userid, $qa_cookieid, $usershtml, null, $htmloptions);
			

		//	Selection/unselect buttons and others for operating on the answer

			if (!$formrequested) { // don't show if another form is currently being shown on page
				if ($question['aselectable'] && !$answer['hidden']) {
					if ($answer['isselected'])
						$a_view['unselect_tags']='TITLE="'.qa_lang_html('question/unselect_popup').'" NAME="select_"';
					elseif (!isset($question['selchildid']))
						$a_view['select_tags']='TITLE="'.qa_lang_html('question/select_popup').'" NAME="select_'.qa_html($answerid).'"';
				}
				
				$a_view['form']=array(
					'style' => 'light',
					'buttons' => array(),
				);
				
				if ($answer['editbutton'])
					$a_view['form']['buttons']['edit']=array(
						'tags' => 'NAME="doedita_'.qa_html($answerid).'"',
						'label' => qa_lang_html('question/edit_button'),
						'popup' => qa_lang_html('question/edit_a_popup'),
					);
					
				if ($answer['flagbutton'])
					$a_view['form']['buttons']['flag']=array(
						'tags' => 'NAME="doflaga_'.qa_html($answerid).'"',
						'label' => qa_lang_html($answer['flagtohide'] ? 'question/flag_hide_button' : 'question/flag_button'),
						'popup' => qa_lang_html('question/flag_a_popup'),
					);

				if ($answer['unflaggable'])
					$a_view['form']['buttons']['unflag']=array(
						'tags' => 'NAME="dounflaga_'.qa_html($answerid).'"',
						'label' => qa_lang_html('question/unflag_button'),
						'popup' => qa_lang_html('question/unflag_popup'),
					);
					
				if ($answer['clearflaggable'])
					$a_view['form']['buttons']['clearflags']=array(
						'tags' => 'NAME="doclearflagsa_'.qa_html($answerid).'"',
						'label' => qa_lang_html('question/clear_flags_button'),
						'popup' => qa_lang_html('question/clear_flags_popup'),
					);
	
				if ($answer['hideable'])
					$a_view['form']['buttons']['hide']=array(
						'tags' => 'NAME="dohidea_'.qa_html($answerid).'"',
						'label' => qa_lang_html('question/hide_button'),
						'popup' => qa_lang_html('question/hide_a_popup'),
					);
					
				if ($answer['reshowable'])
					$a_view['form']['buttons']['reshow']=array(
						'tags' => 'NAME="doshowa_'.qa_html($answerid).'"',
						'label' => qa_lang_html('question/reshow_button'),
					);
					
				if ($answer['deleteable'])
					$a_view['form']['buttons']['delete']=array(
						'tags' => 'NAME="dodeletea_'.qa_html($answerid).'"',
						'label' => qa_lang_html('question/delete_button'),
						'popup' => qa_lang_html('question/delete_a_popup'),
					);
					
				if ($answer['claimable'])
					$a_view['form']['buttons']['claim']=array(
						'tags' => 'NAME="doclaima_'.qa_html($answerid).'"',
						'label' => qa_lang_html('question/claim_button'),
					);

				if ($answer['followable'])
					$a_view['form']['buttons']['follow']=array(
						'tags' => 'NAME="dofollowa_'.qa_html($answerid).'"',
						'label' => qa_lang_html('question/follow_button'),
						'popup' => qa_lang_html('question/follow_a_popup'),
					);

				if ($answer['commentbutton'])
					$a_view['form']['buttons']['comment']=array(
						'tags' => 'NAME="docommenta_'.qa_html($answerid).'"',
						'label' => qa_lang_html('question/comment_button'),
						'popup' => qa_lang_html('question/comment_a_popup'),
					);

			}
			

		//	Prepare content for comments on this answer, plus add or edit comment forms
			
			$a_view['c_list']=qa_page_q_comment_follow_list($answer); // ...for viewing

			if (($formtype=='c_add') && ($formpostid==$answerid)) // ...to be added
				$a_view['c_form']=qa_page_q_add_c_form($answerid);

			else if (($formtype=='c_edit') && (@$commentsfollows[$formpostid]['parentid']==$answerid)) // ...being edited
				$a_view['c_form']=qa_page_q_edit_c_form($formpostid, $answerid);


		//	Determine this answer's place in the order on the page

			if ($answer['hidden'])
				$a_view['priority']=10000+($priority++);
			elseif ($answer['isselected'] && qa_opt('show_selected_first'))
				$a_view['priority']=0;
			else
				$a_view['priority']=5000+($priority++);
				

		//	Add the answer to the list
				
			$qa_content['a_list']['as'][]=$a_view;
		}
		
	qa_sort_by($qa_content['a_list']['as'], 'priority');
	
	$countanswers=$question['acount'];
	
	if ($countanswers==1)
		$qa_content['a_list']['title']=qa_lang_html('question/1_answer_title');
	else
		$qa_content['a_list']['title']=qa_lang_html_sub('question/x_answers_title', $countanswers);


//	Prepare content for form to add an answer

	if ($formtype=='a_add') { // Form for adding answers
		$answerform=null;
		
		switch (qa_user_permit_error('permit_post_a')) {
			case 'login':
				$answerform=array(
					'style' => 'tall',
					'title' => qa_insert_login_links(qa_lang_html('question/answer_must_login'), $qa_request)
				);
				break;
				
			case 'confirm':
				$answerform=array(
					'style' => 'tall',
					'title' => qa_insert_login_links(qa_lang_html('question/answer_must_confirm'), $qa_request)
				);
				break;
			
			case false:
				$editorname=isset($ineditor) ? $ineditor : qa_opt('editor_for_as');
				$editor=qa_load_editor(@$incontent, @$informat, $editorname);

				$answerform=array(
					'title' => qa_lang_html('question/your_answer_title'),
					
					'style' => 'tall',
					
					'fields' => array(
						'content' => array_merge(
							$editor->get_field($qa_content, @$incontent, @$informat, 'content', 12, $formrequested),
							array(
								'error' => qa_html(@$errors['content']),
							)
						),
					),
					
					'buttons' => array(
						'answer' => array(
							'tags' => 'NAME="doansweradd"',
							'label' => qa_lang_html('question/add_answer_button'),
						),
					),
					
					'hidden' => array(
						'editor' => qa_html($editorname),
					),
				);
				
				if ($formrequested) { // only show cancel button if user explicitly requested the form
					$answerform['buttons']['cancel']=array(
						'tags' => 'NAME="docancel"',
						'label' => qa_lang_html('main/cancel_button'),
					);
				}
				
				qa_set_up_notify_fields($qa_content, $answerform['fields'], 'A', qa_get_logged_in_email(),
					isset($innotify) ? $innotify : qa_opt('notify_users_default'), @$inemail, @$errors['email']);
					
				if ($usecaptcha)
					qa_set_up_captcha_field($qa_content, $answerform['fields'], @$errors,
						qa_insert_login_links(qa_lang_html(isset($qa_login_userid) ? 'misc/captcha_confirm_fix' : 'misc/captcha_login_fix')));
				break;
		}
		
		if ($formrequested || empty($qa_content['a_list']['as']))
			$qa_content['q_view']['a_form']=$answerform; // show directly under question
		else {
			$answerkeys=array_keys($qa_content['a_list']['as']);
			$qa_content['a_list']['as'][$answerkeys[count($answerkeys)-1]]['c_form']=$answerform; // under last answer
		}
	}


//	List of related questions
	
	if (($relatedcount>1) && !$question['hidden']) {
		$minscore=qa_match_to_min_score(qa_opt('match_related_qs'));
		
		foreach ($relatedquestions as $key => $related)
			if ( ($related['postid']==$questionid) || ($related['score']<$minscore) ) // related questions will include itself so remove that
				unset($relatedquestions[$key]);
		
		if (count($relatedquestions))
			$qa_content['q_list']['title']=qa_lang('main/related_qs_title');
		else
			$qa_content['q_list']['title']=qa_lang('main/no_related_qs_title');
			
		$qa_content['q_list']['qs']=array();
		foreach ($relatedquestions as $related)
			$qa_content['q_list']['qs'][]=qa_post_html_fields($related, $qa_login_userid, $qa_cookieid, $usershtml, null, qa_post_html_defaults('Q'));
	}
	

//	Some generally useful stuff
	
	if (qa_using_categories() && count($categories))
		$qa_content['navigation']['cat']=qa_category_navigation($categories, $question['categoryid']);

	if (isset($jumptoanchor))
		$qa_content['script_onloads'][]=array(
			"window.location.hash=".qa_js($jumptoanchor).";",
		);
		
	if (isset($focusonid))
		$qa_content['script_onloads'][]=array(
			"document.getElementById(".qa_js($focusonid).").focus();"
		);
		
		
//	Determine whether the page view should be counted
	
	if (
		qa_opt('do_count_q_views') &&
		(!$formrequested) &&
		(!qa_is_http_post()) &&
		qa_is_human_probably() &&
		( (!$question['views']) || ( // if it has more than zero views
			( ($question['lastviewip']!=@$_SERVER['REMOTE_ADDR']) || (!isset($question['lastviewip'])) ) && // then it must be different IP from last view
			( ($question['createip']!=@$_SERVER['REMOTE_ADDR']) || (!isset($question['createip'])) ) && // and different IP from the creator
			( ($question['userid']!=$qa_login_userid) || (!isset($question['userid'])) ) && // and different user from the creator
			( ($question['cookieid']!=$qa_cookieid) || (!isset($question['cookieid'])) ) // and different cookieid from the creator
		) )
	)
		$qa_content['inc_views_postid']=$questionid;

		
	return $qa_content;


/*
	Omit PHP closing tag to help avoid accidental output
*/