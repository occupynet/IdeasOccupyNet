<?php
	
/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-app-post-create.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Creating questions, answers and comments (application level)


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

	require_once QA_INCLUDE_DIR.'qa-db-maxima.php';
	require_once QA_INCLUDE_DIR.'qa-db-post-create.php';
	require_once QA_INCLUDE_DIR.'qa-db-points.php';
	require_once QA_INCLUDE_DIR.'qa-db-hotness.php';
	require_once QA_INCLUDE_DIR.'qa-util-string.php';
	
	
	$qa_post_indexing_suspended=0;

	
	function qa_notify_validate(&$errors, $notify, $email)
/*
	Add textual element ['email'] to $errors if user-entered values for $notify checkbox and $email field invalid
*/
	{
		if ($notify && !empty($email)) {
			if (!qa_email_validate($email))
				$errors['email']=qa_lang('users/email_invalid');
			elseif (qa_strlen($email)>QA_DB_MAX_EMAIL_LENGTH)
				$errors['email']=qa_lang_sub('main/max_length_x', QA_DB_MAX_EMAIL_LENGTH);
		}
	}
	

	function qa_length_validate(&$errors, $field, $input, $minlength, $maxlength)
/*
	Add textual element $field to $errors if length of $input is not between $minlength and $maxlength
*/
	{
		if (isset($input)) {
			$length=qa_strlen($input);
			
			if ($length < $minlength)
				$errors[$field]=($minlength==1) ? qa_lang('main/field_required') : qa_lang_sub('main/min_length_x', $minlength);
			elseif (isset($maxlength) && ($length > $maxlength))
				$errors[$field]=qa_lang_sub('main/max_length_x', $maxlength);
		}
	}

	
	function qa_question_validate($title, $content, $format, $text, $tagstring, $notify, $email)
/*
	Return $errors fields for any invalid aspect of user-entered question
*/
	{
		require_once QA_INCLUDE_DIR.'qa-app-options.php';

		$options=qa_get_options(array('min_len_q_title', 'max_len_q_title', 'min_len_q_content', 'min_num_q_tags', 'max_num_q_tags'));
		
		$errors=array();
		
		$maxtitlelength=max($options['min_len_q_title'], min($options['max_len_q_title'], QA_DB_MAX_TITLE_LENGTH));
		
		qa_length_validate($errors, 'title', $title, $options['min_len_q_title'], $maxtitlelength);
		
		qa_length_validate($errors, 'content', $content, 0, QA_DB_MAX_CONTENT_LENGTH); // for storage
		qa_length_validate($errors, 'content', $text, $options['min_len_q_content'], null); // for display
		
		if (isset($tagstring)) {
			$counttags=count(qa_tagstring_to_tags($tagstring));
			
			$mintags=min($options['min_num_q_tags'], $options['max_num_q_tags']); // to deal with silly settings
			
			if ($counttags<$mintags)
				$errors['tags']=qa_lang_sub('question/min_tags_x', $mintags);
			elseif ($counttags>$options['max_num_q_tags'])
				$errors['tags']=qa_lang_sub('question/max_tags_x', $options['max_num_q_tags']);
			else
				qa_length_validate($errors, 'tags', $tagstring, 0, QA_DB_MAX_TAGS_LENGTH);
		}
		
		qa_notify_validate($errors, $notify, $email);
			
		return $errors;
	}

	
	function qa_combine_notify_email($userid, $notify, $email)
/*
	Return value to store in database combining $notify and $email values entered by user $userid (or null for anonymous)
*/
	{
		return $notify ? (empty($email) ? (isset($userid) ? '@' : null) : $email) : null;
	}
	
	
	function qa_question_create($followanswer, $userid, $handle, $cookieid, $title, $content, $format, $text, $tagstring, $notify, $email, $categoryid=null)
/*
	Add a question (application level) - create record, update appropriate counts, index it, send notifications.
	If question is follow-on from an answer, $followanswer should contain answer database record, otherwise null.
	See qa-app-posts.php for a higher-level function which is easier to use.
*/
	{
		require_once QA_INCLUDE_DIR.'qa-app-options.php';
		require_once QA_INCLUDE_DIR.'qa-app-emails.php';
		require_once QA_INCLUDE_DIR.'qa-db-selects.php';

		$postid=qa_db_post_create('Q', @$followanswer['postid'], $userid, isset($userid) ? null : $cookieid,
			@$_SERVER['REMOTE_ADDR'], $title, $content, $format, $tagstring, qa_combine_notify_email($userid, $notify, $email), $categoryid);
		
		qa_db_posts_calc_category_path($postid);
		qa_db_category_path_qcount_update(qa_db_post_get_category_path($postid));
		qa_post_index($postid, 'Q', $postid, $title, $text, $tagstring);
		qa_db_hotness_update($postid);
		qa_db_points_update_ifuser($userid, 'qposts');
		qa_db_qcount_update();
		qa_db_unaqcount_update();
		
		if (isset($followanswer['notify']) && !qa_post_is_by_user($followanswer, $userid, $cookieid)) {
			require_once QA_INCLUDE_DIR.'qa-app-emails.php';
			require_once QA_INCLUDE_DIR.'qa-app-format.php';
			require_once QA_INCLUDE_DIR.'qa-util-string.php';
			
			$blockwordspreg=qa_get_block_words_preg();
			$sendtitle=qa_block_words_replace($title, $blockwordspreg);
			$sendtext=qa_viewer_text($followanswer['content'], $followanswer['format'], array('blockwordspreg' => $blockwordspreg));
			
			qa_send_notification($followanswer['userid'], $followanswer['notify'], @$followanswer['handle'], qa_lang('emails/a_followed_subject'), qa_lang('emails/a_followed_body'), array(
				'^q_handle' => isset($handle) ? $handle : qa_lang('main/anonymous'),
				'^q_title' => $sendtitle,
				'^a_content' => $sendtext,
				'^url' => qa_path(qa_q_request($postid, $sendtitle), null, qa_opt('site_url')),
			));
		}
		
		if (qa_opt('notify_admin_q_post'))
			qa_send_notification(null, qa_opt('feedback_email'), null, qa_lang('emails/q_posted_subject'), qa_lang('emails/q_posted_body'), array(
				'^q_handle' => isset($handle) ? $handle : qa_lang('main/anonymous'),
				'^q_title' => $title, // don't censor title or content since we want the admin to see bad words
				'^q_content' => $text,
				'^url' => qa_path(qa_q_request($postid, $title), null, qa_opt('site_url')),
			));
			
		qa_report_event('q_post', $userid, $handle, $cookieid, array(
			'postid' => $postid,
			'parentid' => @$followanswer['postid'],
			'title' => $title,
			'content' => $content,
			'format' => $format,
			'text' => $text,
			'tags' => $tagstring,
			'categoryid' => $categoryid,
			'notify' => $notify,
			'email' => $email,
		));
		
		return $postid;
	}

	
	function qa_array_filter_by_keys($inarray, $keys)
/*
	Return an array containing the elements of $inarray whose key is in $keys
*/
	{
		$outarray=array();

		foreach ($keys as $key)
			if (isset($inarray[$key]))
				$outarray[$key]=$inarray[$key];
				
		return $outarray;
	}

	
	function qa_suspend_post_indexing($suspend=true)
/*
	Suspend the indexing (and unindexing) of posts via qa_post_index(...) and qa_post_unindex(...)
	if $suspend is true, otherwise reinstate it. A counter is kept to allow multiple calls.
*/
	{
		global $qa_post_indexing_suspended;
		
		$qa_post_indexing_suspended+=($suspend ? 1 : -1);
	}
	
	
	function qa_post_index($postid, $type, $questionid, $title, $text, $tagstring, $skipcounts=false)
/*
	Add post $postid (which comes under $questionid) of $type (Q/A/C) to the database index, with $title, $text
	and $tagstring. Set $skipcounts to true to not update counts - useful during recalculationss.
*/
	{
		global $qa_post_indexing_suspended;
		
		if ($qa_post_indexing_suspended>0)
			return;
			
	//	Get words from each textual element
	
		$titlewords=array_unique(qa_string_to_words($title));
		$contentcount=array_count_values(qa_string_to_words($text));
		$tagwords=array_unique(qa_string_to_words($tagstring));
		$wholetags=array_unique(qa_tagstring_to_tags($tagstring));
		
	//	Map all words to their word IDs
		
		$words=array_unique(array_merge($titlewords, array_keys($contentcount), $tagwords, $wholetags));
		$wordtoid=qa_db_word_mapto_ids_add($words);
		
	//	Add to title words index
		
		$titlewordids=qa_array_filter_by_keys($wordtoid, $titlewords);
		qa_db_titlewords_add_post_wordids($postid, $titlewordids);
	
	//	Add to content words index (including word counts)
	
		$contentwordidcounts=array();
		foreach ($contentcount as $word => $count)
			if (isset($wordtoid[$word]))
				$contentwordidcounts[$wordtoid[$word]]=$count;

		qa_db_contentwords_add_post_wordidcounts($postid, $type, $questionid, $contentwordidcounts);
		
	//	Add to tag words index
	
		$tagwordids=qa_array_filter_by_keys($wordtoid, $tagwords);
		qa_db_tagwords_add_post_wordids($postid, $tagwordids);
	
	//	Add to whole tags index

		$wholetagids=qa_array_filter_by_keys($wordtoid, $wholetags);
		qa_db_posttags_add_post_wordids($postid, $wholetagids);
		
	//	Update counts cached in database
		
		if (!$skipcounts) {
			qa_db_word_titlecount_update($titlewordids);
			qa_db_word_contentcount_update(array_keys($contentwordidcounts));
			qa_db_word_tagwordcount_update($tagwordids);
			qa_db_word_tagcount_update($wholetagids);
			qa_db_tagcount_update();
		}
	}

		
	function qa_answer_validate($content, $format, $text, $notify, $email)
/*
	Return $errors fields for any invalid aspect of user-entered answer
*/
	{
		require_once QA_INCLUDE_DIR.'qa-app-options.php';

		$errors=array();
		
		qa_length_validate($errors, 'content', $content, 0, QA_DB_MAX_CONTENT_LENGTH); // for storage
		qa_length_validate($errors, 'content', $text, qa_opt('min_len_a_content'), null); // for display
		qa_notify_validate($errors, $notify, $email);
		
		return $errors;
	}

	
	function qa_answer_create($userid, $handle, $cookieid, $content, $format, $text, $notify, $email, $question)
/*
	Add an answer (application level) - create record, update appropriate counts, index it, send notifications.
	$question should contain database record for the question this is an answer to.
	See qa-app-posts.php for a higher-level function which is easier to use.
*/
	{
		$postid=qa_db_post_create('A', $question['postid'], $userid, isset($userid) ? null : $cookieid,
			@$_SERVER['REMOTE_ADDR'], null, $content, $format, null, qa_combine_notify_email($userid, $notify, $email), $question['categoryid']);
		
		qa_db_posts_calc_category_path($postid);
		
		if (!$question['hidden']) // don't index answer if parent question is hidden
			qa_post_index($postid, 'A', $question['postid'], null, $text, null);
		
		qa_db_post_acount_update($question['postid']);
		qa_db_hotness_update($question['postid']);
		qa_db_points_update_ifuser($userid, 'aposts');
		qa_db_acount_update();
		qa_db_unaqcount_update();
		
		if (isset($question['notify']) && !qa_post_is_by_user($question, $userid, $cookieid)) {
			require_once QA_INCLUDE_DIR.'qa-app-emails.php';
			require_once QA_INCLUDE_DIR.'qa-app-options.php';
			require_once QA_INCLUDE_DIR.'qa-util-string.php';
			
			$blockwordspreg=qa_get_block_words_preg();
			$sendtitle=qa_block_words_replace($question['title'], $blockwordspreg);
			$sendtext=qa_block_words_replace($text, $blockwordspreg);

			qa_send_notification($question['userid'], $question['notify'], @$question['handle'], qa_lang('emails/q_answered_subject'), qa_lang('emails/q_answered_body'), array(
				'^a_handle' => isset($handle) ? $handle : qa_lang('main/anonymous'),
				'^q_title' => $sendtitle,
				'^a_content' => $sendtext,
				'^url' => qa_path(qa_q_request($question['postid'], $sendtitle), null, qa_opt('site_url'), null, qa_anchor('A', $postid)),
			));
		}
		
		qa_report_event('a_post', $userid, $handle, $cookieid, array(
			'postid' => $postid,
			'parentid' => $question['postid'],
			'content' => $content,
			'format' => $format,
			'text' => $text,
			'categoryid' => $question['categoryid'],
			'notify' => $notify,
			'email' => $email,
		));
		
		return $postid;
	}

	
	function qa_comment_validate($content, $format, $text, $notify, $email)
/*
	Return $errors fields for any invalid aspect of user-entered comment
*/
	{
		require_once QA_INCLUDE_DIR.'qa-app-options.php';

		$errors=array();
		
		qa_length_validate($errors, 'content', $content, 0, QA_DB_MAX_CONTENT_LENGTH); // for storage
		qa_length_validate($errors, 'content', $text, qa_opt('min_len_c_content'), null); // for display
		qa_notify_validate($errors, $notify, $email);
			
		return $errors;
	}

	
	function qa_comment_create($userid, $handle, $cookieid, $content, $format, $text, $notify, $email, $question, $answer, $commentsfollows)
/*
	Add a comment (application level) - create record, update appropriate counts, index it, send notifications.
	$question should contain database record for the question this is part of (as direct or comment on Q's answer).
	If this is a comment on an answer, $answer should contain database record for the answer, otherwise null.
	$commentsfollows should contain database records for all previous comments on the same question or answer,
	but it can also contain other records that are ignored.
	See qa-app-posts.php for a higher-level function which is easier to use.
*/
	{
		require_once QA_INCLUDE_DIR.'qa-app-emails.php';
		require_once QA_INCLUDE_DIR.'qa-app-options.php';
		require_once QA_INCLUDE_DIR.'qa-app-format.php';
		require_once QA_INCLUDE_DIR.'qa-util-string.php';

		$parent=isset($answer) ? $answer : $question;
		
		$postid=qa_db_post_create('C', $parent['postid'], $userid, isset($userid) ? null : $cookieid,
			@$_SERVER['REMOTE_ADDR'], null, $content, $format, null, qa_combine_notify_email($userid, $notify, $email), $question['categoryid']);
		
		qa_db_posts_calc_category_path($postid);
		
		if (!($question['hidden'] || @$answer['hidden'])) // don't index comment if parent or parent of parent is hidden
			qa_post_index($postid, 'C', $question['postid'], null, $text, null);
		
		qa_db_points_update_ifuser($userid, 'cposts');
		qa_db_ccount_update();
		
	//	$senttoemail and $senttouserid ensure each user or email gets only one notification about an added comment,
	//	even if they have several previous comments in the same thread and asked for notifications for the parent.
	//	Still, if a person posted some comments as a registered user and some others anonymously,
	//	they could get two emails about a subsequent comment. Shouldn't be much of a problem in practice.

		$senttoemail=array();
		$senttouserid=array();
		
		switch ($parent['basetype']) {
			case 'Q':
				$subject=qa_lang('emails/q_commented_subject');
				$body=qa_lang('emails/q_commented_body');
				$context=$parent['title'];
				break;
				
			case 'A':
				$subject=qa_lang('emails/a_commented_subject');
				$body=qa_lang('emails/a_commented_body');
				$context=qa_viewer_text($parent['content'], $parent['format']);
				break;
		}
		
		$blockwordspreg=qa_get_block_words_preg();
		$sendhandle=isset($handle) ? $handle : qa_lang('main/anonymous');
		$sendcontext=qa_block_words_replace($context, $blockwordspreg);
		$sendtext=qa_block_words_replace($text, $blockwordspreg);
		$sendtitle=qa_block_words_replace($question['title'], $blockwordspreg);
		$sendurl=qa_path(qa_q_request($question['postid'], $sendtitle), null,
			qa_opt('site_url'), null, qa_anchor($parent['basetype'], $parent['postid']));
			
		if (isset($parent['notify']) && !qa_post_is_by_user($parent, $userid, $cookieid)) {
			$senduserid=$parent['userid'];
			$sendemail=@$parent['notify'];
			
			if (qa_email_validate($sendemail))
				$senttoemail[$sendemail]=true;
			elseif (isset($senduserid))
				$senttouserid[$senduserid]=true;

			qa_send_notification($senduserid, $sendemail, @$parent['handle'], $subject, $body, array(
				'^c_handle' => $sendhandle,
				'^c_context' => $sendcontext,
				'^c_content' => $sendtext,
				'^url' => $sendurl,
			));
		}
		
		foreach ($commentsfollows as $comment)
			if (($comment['basetype']=='C') && ($comment['parentid']==$parent['postid']) && (!$comment['hidden'])) // find just those for this parent
				if (isset($comment['notify']) && !qa_post_is_by_user($comment, $userid, $cookieid)) {
					$senduserid=$comment['userid'];
					$sendemail=@$comment['notify'];
					
					if (qa_email_validate($sendemail)) {
						if (@$senttoemail[$sendemail])
							continue;
							
						$senttoemail[$sendemail]=true;
						
					} elseif (isset($senduserid)) {
						if (@$senttouserid[$senduserid])
							continue;
							
						$senttouserid[$senduserid]=true;
					}

					qa_send_notification($senduserid, $sendemail, @$comment['handle'], qa_lang('emails/c_commented_subject'), qa_lang('emails/c_commented_body'), array(
						'^c_handle' => $sendhandle,
						'^c_context' => $sendcontext,
						'^c_content' => $sendtext,
						'^url' => $sendurl,
					));
				}
				
		qa_report_event('c_post', $userid, $handle, $cookieid, array(
			'postid' => $postid,
			'parentid' => $parent['postid'],
			'parenttype' => $parent['basetype'],
			'questionid' => $question['postid'],
			'content' => $content,
			'format' => $format,
			'text' => $text,
			'categoryid' => $question['categoryid'],
			'notify' => $notify,
			'email' => $email,
		));
		
		return $postid;
	}
	

/*
	Omit PHP closing tag to help avoid accidental output
*/