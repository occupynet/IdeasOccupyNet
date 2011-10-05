<?php
	
/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-app-post-update.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Changing questions, answer and comments (application level)


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

	require_once QA_INCLUDE_DIR.'qa-app-post-create.php';
	require_once QA_INCLUDE_DIR.'qa-db-post-create.php';
	require_once QA_INCLUDE_DIR.'qa-db-post-update.php';
	require_once QA_INCLUDE_DIR.'qa-db-points.php';
	require_once QA_INCLUDE_DIR.'qa-db-hotness.php';

	
	function qa_question_set_content($oldquestion, $title, $content, $format, $text, $tagstring, $notify, $userid, $handle, $cookieid)
/*
	Change the fields of a question (application level) to $title, $content, $format, $tagstring and $notify,
	and reindex based on $text. Pass the question's database record before changes in $oldquestion and details
	of the user doing this in $userid, $handle and $cookieid. Reports event as appropriate.
	See qa-app-posts.php for a higher-level function which is easier to use.
*/
	{
		qa_post_unindex($oldquestion['postid']);
		
		$setupdated=strcmp($oldquestion['title'], $title) || strcmp($oldquestion['content'], $content) || strcmp($oldquestion['format'], $format);
		
		qa_db_post_set_content($oldquestion['postid'], $title, $content, $format, $tagstring, $notify,
			$setupdated ? $userid : null, $setupdated ? @$_SERVER['REMOTE_ADDR'] : null);
		
		if (!$oldquestion['hidden'])
			qa_post_index($oldquestion['postid'], 'Q', $oldquestion['postid'], $title, $text, $tagstring);

		qa_report_event('q_edit', $userid, $handle, $cookieid, array(
			'postid' => $oldquestion['postid'],
			'title' => $title,
			'content' => $content,
			'format' => $format,
			'text' => $text,
			'tags' => $tagstring,
			'oldtitle' => $oldquestion['title'],
			'oldcontent' => $oldquestion['content'],
			'oldformat' => $oldquestion['format'],
			'oldtags' => $oldquestion['tags'],
		));
	}

	
	function qa_question_set_selchildid($userid, $handle, $cookieid, $oldquestion, $selchildid, $answers)
/*
	Set the selected answer (application level) of $oldquestion to $selchildid. Pass details of the user doing this
	in $userid, $handle and $cookieid, and the database records for all answers to the question in $answers.
	Handles user points values and notifications.
	See qa-app-posts.php for a higher-level function which is easier to use.
*/
	{
		$oldselchildid=$oldquestion['selchildid'];
		
		qa_db_post_set_selchildid($oldquestion['postid'], isset($selchildid) ? $selchildid : null);
		qa_db_points_update_ifuser($oldquestion['userid'], 'aselects');
		
		if (isset($oldselchildid))
			if (isset($answers[$oldselchildid])) {
				qa_db_points_update_ifuser($answers[$oldselchildid]['userid'], 'aselecteds');
			
				qa_report_event('a_unselect', $userid, $handle, $cookieid, array(
					'parentid' => $oldquestion['postid'],
					'postid' => $oldselchildid,
				));
			}

		if (isset($selchildid)) {
			$answer=$answers[$selchildid];
			
			qa_db_points_update_ifuser($answer['userid'], 'aselecteds');

			if (isset($answer['notify']) && !qa_post_is_by_user($answer, $userid, $cookieid)) {
				require_once QA_INCLUDE_DIR.'qa-app-emails.php';
				require_once QA_INCLUDE_DIR.'qa-app-options.php';
				require_once QA_INCLUDE_DIR.'qa-util-string.php';
				require_once QA_INCLUDE_DIR.'qa-app-format.php';
				
				$blockwordspreg=qa_get_block_words_preg();
				$sendtitle=qa_block_words_replace($oldquestion['title'], $blockwordspreg);
				$sendcontent=qa_viewer_text($answer['content'], $answer['format'], array('blockwordspreg' => $blockwordspreg));

				qa_send_notification($answer['userid'], $answer['notify'], @$answer['handle'], qa_lang('emails/a_selected_subject'), qa_lang('emails/a_selected_body'), array(
					'^s_handle' => isset($handle) ? $handle : qa_lang('main/anonymous'),
					'^q_title' => $sendtitle,
					'^a_content' => $sendcontent,
					'^url' => qa_path(qa_q_request($oldquestion['postid'], $sendtitle), null, qa_opt('site_url'), null, qa_anchor('A', $selchildid)),
				));
			}

			qa_report_event('a_select', $userid, $handle, $cookieid, array(
				'parentid' => $oldquestion['postid'],
				'postid' => $selchildid,
			));
		}
	}

	
	function qa_question_set_hidden($oldquestion, $hidden, $userid, $handle, $cookieid, $answers, $commentsfollows)
/*
	Set the hidden status (application level) of $oldquestion to $hidden. Pass details of the user doing this
	in $userid, $handle and $cookieid, the database records for all answers to the question in $answers,
	and the database records for all comments on the question or the question's answers in $commentsfollows
	($commentsfollows can also contain records for follow-on questions which are ignored).
	Handles indexing, user points, cached counts and event reports.
	See qa-app-posts.php for a higher-level function which is easier to use.
*/
	{
		qa_post_unindex($oldquestion['postid']);
		
		foreach ($answers as $answer)
			qa_post_unindex($answer['postid']);
		
		foreach ($commentsfollows as $comment)
			if ($comment['basetype']=='C')
				qa_post_unindex($comment['postid']);
			
		qa_db_post_set_type($oldquestion['postid'], $hidden ? 'Q_HIDDEN' : 'Q', $userid, @$_SERVER['REMOTE_ADDR']);
		qa_db_category_path_qcount_update(qa_db_post_get_category_path($oldquestion['postid']));
		qa_db_points_update_ifuser($oldquestion['userid'], array('qposts', 'aselects'));
		qa_db_qcount_update();
		qa_db_unaqcount_update();
		
		if (!$hidden) {
			require_once QA_INCLUDE_DIR.'qa-app-format.php';
			
			qa_post_index($oldquestion['postid'], 'Q', $oldquestion['postid'], $oldquestion['title'],
				qa_viewer_text($oldquestion['content'], $oldquestion['format']), $oldquestion['tags']);

			foreach ($answers as $answer)
				if (!$answer['hidden']) // even if question visible, don't index hidden answers
					qa_post_index($answer['postid'], $answer['type'], $oldquestion['postid'], null,
						qa_viewer_text($answer['content'], $answer['format']), null);
					
			foreach ($commentsfollows as $comment)
				if ($comment['basetype']=='C')
					if (!($comment['hidden'] || @$answers[$comment['parentid']]['hidden'])) // don't index comment if it or its parent is hidden
						qa_post_index($comment['postid'], $comment['type'], $oldquestion['postid'], null,
							qa_viewer_text($comment['content'], $comment['format']), null);
		}

		qa_report_event($hidden ? 'q_hide' : 'q_reshow', $userid, $handle, $cookieid, array(
			'postid' => $oldquestion['postid'],
		));
	}

	
	function qa_question_set_category($oldquestion, $categoryid, $userid, $handle, $cookieid, $answers, $commentsfollows)
/*
	Sets the category (application level) of $oldquestion to $categoryid. Pass details of the user doing this
	in $userid, $handle and $cookieid, the database records for all answers to the question in $answers,
	and the database records for all comments on the question or the question's answers in $commentsfollows
	($commentsfollows can also contain records for follow-on questions which are ignored).
	Handles cached counts and event reports and will reset category IDs and paths for all answers and comments.
	See qa-app-posts.php for a higher-level function which is easier to use.
*/
	{
		$oldpath=qa_db_post_get_category_path($oldquestion['postid']);
		
		qa_db_post_set_category($oldquestion['postid'], $categoryid);
		qa_db_posts_calc_category_path($oldquestion['postid']);
		
		$newpath=qa_db_post_get_category_path($oldquestion['postid']);
		
		qa_db_category_path_qcount_update($oldpath);
		qa_db_category_path_qcount_update($newpath);

		$otherpostids=array();
		foreach ($answers as $answer)
			$otherpostids[]=$answer['postid'];
			
		foreach ($commentsfollows as $comment)
			if ($comment['basetype']=='C')
				$otherpostids[]=$comment['postid'];
				
		qa_db_posts_set_category_path($otherpostids, $newpath);

		qa_report_event('q_move', $userid, $handle, $cookieid, array(
			'postid' => $oldquestion['postid'],
			'categoryid' => $categoryid,
			'oldcategoryid' => $oldquestion['categoryid'],
		));
	}
	
	
	function qa_question_delete($oldquestion, $userid, $handle, $cookieid)
/*
	Permanently delete a question (application level) from the database. The question must not have any
	answers or comments on it. Pass details of the user doing this in $userid, $handle and $cookieid.
	Handles unindexing, votes, points, cached counts and event reports.
	See qa-app-posts.php for a higher-level function which is easier to use.
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-votes.php';
		
		if (!$oldquestion['hidden'])
			qa_fatal_error('Tried to delete a non-hidden question');
		
		$useridvotes=qa_db_uservote_post_get($oldquestion['postid']);
		$oldpath=qa_db_post_get_category_path($oldquestion['postid']);
		
		qa_post_unindex($oldquestion['postid']);
		qa_db_post_delete($oldquestion['postid']); // also deletes any related voteds due to cascading
		
		qa_db_category_path_qcount_update($oldpath);
		qa_db_points_update_ifuser($oldquestion['userid'], array('qposts', 'aselects', 'qvoteds', 'upvoteds', 'downvoteds'));
		
		foreach ($useridvotes as $voteruserid => $vote)
			qa_db_points_update_ifuser($voteruserid, ($vote>0) ? 'qupvotes' : 'qdownvotes');
				// could do this in one query like in qa_db_users_recalc_points() but this will do for now - unlikely to be many votes
		
		qa_db_qcount_update();
		qa_db_unaqcount_update();

		qa_report_event('q_delete', $userid, $handle, $cookieid, array(
			'postid' => $oldquestion['postid'],
		));
	}


	function qa_question_set_userid($oldquestion, $userid, $handle, $cookieid)
/*
	Set the author (application level) of $oldquestion to $userid and also pass $handle and $cookieid
	of user. Updates points and reports events as appropriate.
*/
	{
		qa_db_post_set_userid($oldquestion['postid'], $userid);

		qa_db_points_update_ifuser($oldquestion['userid'], array('qposts', 'aselects', 'qvoteds', 'upvoteds', 'downvoteds'));
		qa_db_points_update_ifuser($userid, array('qposts', 'aselects', 'qvoteds', 'upvoteds', 'downvoteds'));
		
		qa_report_event('q_claim', $userid, $handle, $cookieid, array(
			'postid' => $oldquestion['postid'],
		));
	}

	
	function qa_post_unindex($postid)
/*
	Remove post $postid from our index and update appropriate word counts
*/
	{
		global $qa_post_indexing_suspended;

		if ($qa_post_indexing_suspended>0)
			return;
			
		$titlewordids=qa_db_titlewords_get_post_wordids($postid);
		qa_db_titlewords_delete_post($postid);
		qa_db_word_titlecount_update($titlewordids);

		$contentwordids=qa_db_contentwords_get_post_wordids($postid);
		qa_db_contentwords_delete_post($postid);
		qa_db_word_contentcount_update($contentwordids);
		
		$tagwordids=qa_db_tagwords_get_post_wordids($postid);
		qa_db_tagwords_delete_post($postid);
		qa_db_word_tagwordcount_update($tagwordids);

		$wholetagids=qa_db_posttags_get_post_wordids($postid);
		qa_db_posttags_delete_post($postid);
		qa_db_word_tagcount_update($wholetagids);
	}

	
	function qa_answer_set_content($oldanswer, $content, $format, $text, $notify, $userid, $handle, $cookieid, $question)
/*
	Change the fields of an answer (application level) to $content, $format and $notify, and reindex based on $text.
	Pass the answer's database record before changes in $oldanswer, the question's in $question, and details of the
	user doing this in $userid, $handle and $cookieid. Handle indexing and event reports as appropriate.
	See qa-app-posts.php for a higher-level function which is easier to use.
*/
	{
		qa_post_unindex($oldanswer['postid']);
		
		$setupdated=strcmp($oldanswer['content'], $content) || strcmp($oldanswer['format'], $format);
		
		qa_db_post_set_content($oldanswer['postid'], $oldanswer['title'], $content, $format, $oldanswer['tags'], $notify,
			$setupdated ? $userid : null, $setupdated ? @$_SERVER['REMOTE_ADDR'] : null);
		
		if (!($oldanswer['hidden'] || $question['hidden'])) // don't index if answer or its question is hidden
			qa_post_index($oldanswer['postid'], 'A', $question['postid'], null, $text, null);

		qa_report_event('a_edit', $userid, $handle, $cookieid, array(
			'postid' => $oldanswer['postid'],
			'parentid' => $oldanswer['parentid'],
			'content' => $content,
			'format' => $format,
			'text' => $text,
			'oldcontent' => $oldanswer['content'],
			'oldformat' => $oldanswer['format'],
		));
	}

	
	function qa_answer_set_hidden($oldanswer, $hidden, $userid, $handle, $cookieid, $question, $commentsfollows)
/*
	Set the hidden status (application level) of $oldanswer to $hidden. Pass details of the user doing this
	in $userid, $handle and $cookieid, the database record for the question in $question, and the database
	records for all comments on the answer in $commentsfollows ($commentsfollows can also contain other
	records which are ignored). Handles indexing, user points, cached counts and event reports.
	See qa-app-posts.php for a higher-level function which is easier to use.
*/
	{
		qa_post_unindex($oldanswer['postid']);
		
		foreach ($commentsfollows as $comment)
			if ( ($comment['basetype']=='C') && ($comment['parentid']==$oldanswer['postid']) )
				qa_post_unindex($comment['postid']);
		
		qa_db_post_set_type($oldanswer['postid'], $hidden ? 'A_HIDDEN' : 'A', $userid, @$_SERVER['REMOTE_ADDR']);
		qa_db_points_update_ifuser($oldanswer['userid'], array('aposts', 'aselecteds'));
		qa_db_post_acount_update($question['postid']);
		qa_db_hotness_update($question['postid']);
		qa_db_acount_update();
		qa_db_unaqcount_update();
		
		if (!($hidden || $question['hidden'])) { // even if answer visible, don't index if question is hidden
			require_once QA_INCLUDE_DIR.'qa-app-format.php';
			
			qa_post_index($oldanswer['postid'], 'A', $question['postid'], null,
				qa_viewer_text($oldanswer['content'], $oldanswer['format']), null);
			
			foreach ($commentsfollows as $comment)
				if ( ($comment['basetype']=='C') && ($comment['parentid']==$oldanswer['postid']) )
					if (!$comment['hidden']) // and don't index hidden comments
						qa_post_index($comment['postid'], $comment['type'], $question['postid'], null,
							qa_viewer_text($comment['content'], $comment['format']), null);
		}

		qa_report_event($hidden ? 'a_hide' : 'a_reshow', $userid, $handle, $cookieid, array(
			'postid' => $oldanswer['postid'],
			'parentid' => $oldanswer['parentid'],
		));
	}

	
	function qa_answer_delete($oldanswer, $question, $userid, $handle, $cookieid)
/*
	Permanently delete an answer (application level) from the database. The answer must not have any comments or
	follow-on questions. Pass the database record for the question in $question and details of the user doing this
	in $userid, $handle and $cookieid. Handles unindexing, votes, points, cached counts and event reports.
	See qa-app-posts.php for a higher-level function which is easier to use.
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-votes.php';
		
		if (!$oldanswer['hidden'])
			qa_fatal_error('Tried to delete a non-hidden answer');
		
		$useridvotes=qa_db_uservote_post_get($oldanswer['postid']);
		
		qa_post_unindex($oldanswer['postid']);
		qa_db_post_delete($oldanswer['postid']); // also deletes any related voteds due to cascading
		
		if ($question['selchildid']==$oldanswer['postid']) {
			qa_db_post_set_selchildid($question['postid'], null);
			qa_db_points_update_ifuser($question['userid'], 'aselects');
		}
		
		qa_db_points_update_ifuser($oldanswer['userid'], array('aposts', 'aselecteds', 'avoteds', 'upvoteds', 'downvoteds'));
		
		foreach ($useridvotes as $userid => $vote)
			qa_db_points_update_ifuser($userid, ($vote>0) ? 'aupvotes' : 'adownvotes');
				// could do this in one query like in qa_db_users_recalc_points() but this will do for now - unlikely to be many votes
		
		qa_db_post_acount_update($question['postid']);
		qa_db_hotness_update($question['postid']);
		qa_db_acount_update();
		qa_db_unaqcount_update();

		qa_report_event('a_delete', $userid, $handle, $cookieid, array(
			'postid' => $oldanswer['postid'],
			'parentid' => $oldanswer['parentid'],
		));
	}
	
	
	function qa_answer_set_userid($oldanswer, $userid, $handle, $cookieid)
/*
	Set the author (application level) of $oldanswer to $userid and also pass $handle and $cookieid
	of user. Updates points and reports events as appropriate.
*/
	{
		qa_db_post_set_userid($oldanswer['postid'], $userid);

		qa_db_points_update_ifuser($oldanswer['userid'], array('aposts', 'aselecteds', 'avoteds', 'upvoteds', 'downvoteds'));
		qa_db_points_update_ifuser($userid, array('aposts', 'aselecteds', 'avoteds', 'upvoteds', 'downvoteds'));

		qa_report_event('a_claim', $userid, $handle, $cookieid, array(
			'postid' => $oldanswer['postid'],
			'parentid' => $oldanswer['parentid'],
		));
	}

	
	function qa_comment_set_content($oldcomment, $content, $format, $text, $notify, $userid, $handle, $cookieid, $question, $answer)
/*
	Change the fields of a comment (application level) to $content, $format and $notify, and reindex based on $text.
	Pass the comment's database record before changes in $oldcomment, details of the user doing this in  $userid,
	$handle and $cookieid, the antecedent question in $question and the answer's database record in $answer if this
	is a comment on an answer, otherwise null. Handles unindexing and event reports.
	See qa-app-posts.php for a higher-level function which is easier to use.
*/
	{
		qa_post_unindex($oldcomment['postid']);
		
		$setupdated=strcmp($oldcomment['content'], $content) || strcmp($oldcomment['format'], $format);
		
		qa_db_post_set_content($oldcomment['postid'], $oldcomment['title'], $content, $format, $oldcomment['tags'], $notify,
			$setupdated ? $userid : null, $setupdated ? @$_SERVER['REMOTE_ADDR'] : null);

		if (!($oldcomment['hidden'] || $question['hidden'] || @$answer['hidden']))
			qa_post_index($oldcomment['postid'], 'C', $question['postid'], null, $text, null);

		qa_report_event('c_edit', $userid, $handle, $cookieid, array(
			'postid' => $oldcomment['postid'],
			'parentid' => $oldcomment['parentid'],
			'parenttype' => isset($answer) ? $answer['basetype'] : $question['basetype'],
			'questionid' => $question['postid'],
			'content' => $content,
			'format' => $format,
			'text' => $text,
			'oldcontent' => $oldcomment['content'],
			'oldformat' => $oldcomment['format'],
		));
	}

	
	function qa_answer_to_comment($oldanswer, $parentid, $content, $format, $text, $notify, $userid, $handle, $cookieid, $question, $answers, $commentsfollows)
/*
	Convert an answer to a comment (application level) and set its fields to $content, $format and $notify.
	Pass the answer's database record before changes in $oldanswer, the new comment's $parentid to be, details of the
	user doing this in $userid, $handle and $cookieid, the antecedent question's record in $question, the records for
	all answers to that question in $answers, and the records for all comments on the (old) answer and questions
	following from the (old) answer in $commentsfollows ($commentsfollows can also contain other records which are ignored).
	Handles indexing (based on $text), user points, cached counts and event reports.
*/
	{
		$parent=isset($answers[$parentid]) ? $answers[$parentid] : $question;
			
		qa_post_unindex($oldanswer['postid']);
		
		qa_db_post_set_type($oldanswer['postid'], $oldanswer['hidden'] ? 'C_HIDDEN' : 'C', $userid, @$_SERVER['REMOTE_ADDR']);
		qa_db_post_set_parent($oldanswer['postid'], $parentid, $userid, @$_SERVER['REMOTE_ADDR']);
		qa_db_post_set_content($oldanswer['postid'], $oldanswer['title'], $content, $format, $oldanswer['tags'], $notify, $userid, @$_SERVER['REMOTE_ADDR']);
		
		foreach ($commentsfollows as $commentfollow)
			if ($commentfollow['parentid']==$oldanswer['postid']) // do same thing for comments and follows
				qa_db_post_set_parent($commentfollow['postid'], $parentid, null, null);

		qa_db_points_update_ifuser($oldanswer['userid'], array('aposts', 'aselecteds', 'cposts'));

		qa_db_post_acount_update($question['postid']);
		qa_db_hotness_update($question['postid']);
		qa_db_acount_update();
		qa_db_ccount_update();
		qa_db_unaqcount_update();
	
		if (!($oldanswer['hidden'] || $question['hidden'] || $parent['hidden'])) { // only index if none of the things it depends on are hidden
			require_once QA_INCLUDE_DIR.'qa-app-format.php';
			qa_post_index($oldanswer['postid'], 'C', $question['postid'], null, $text, null);
		}

		qa_report_event('a_to_c', $userid, $handle, $cookieid, array(
			'postid' => $oldanswer['postid'],
			'parentid' => $parentid,
			'parenttype' => $parent['basetype'],
			'questionid' => $question['postid'],
			'content' => $content,
			'format' => $format,
			'text' => $text,
			'oldcontent' => $oldanswer['content'],
			'oldformat' => $oldanswer['format'],
		));
	}

	
	function qa_comment_set_hidden($oldcomment, $hidden, $userid, $handle, $cookieid, $question, $answer)
/*
	Set the hidden status (application level) of $oldcomment to $hidden. Pass the antecedent question's record in $question,
	details of the user doing this in $userid, $handle and $cookieid, and the answer's database record in $answer if this
	is a comment on an answer, otherwise null. Handles indexing, user points, cached counts and event reports.
	See qa-app-posts.php for a higher-level function which is easier to use.
*/
	{
		qa_post_unindex($oldcomment['postid']);
		
		qa_db_post_set_type($oldcomment['postid'], $hidden ? 'C_HIDDEN' : 'C', $userid, @$_SERVER['REMOTE_ADDR']);
		qa_db_points_update_ifuser($oldcomment['userid'], array('cposts'));
		qa_db_ccount_update();
		
		if (!($hidden || $question['hidden'] || @$answer['hidden'])) { // only index if none of the things it depends on are hidden
			require_once QA_INCLUDE_DIR.'qa-app-format.php';
			qa_post_index($oldcomment['postid'], 'C', $question['postid'], null,
				qa_viewer_text($oldcomment['content'], $oldcomment['format']), null);
		}

		qa_report_event($hidden ? 'c_hide' : 'c_reshow', $userid, $handle, $cookieid, array(
			'postid' => $oldcomment['postid'],
			'parentid' => $oldcomment['parentid'],
			'parenttype' => isset($answer) ? $answer['basetype'] : $question['basetype'],
			'questionid' => $question['postid'],
		));
	}

	
	function qa_comment_delete($oldcomment, $question, $answer, $userid, $handle, $cookieid)
/*
	Permanently delete a comment in $oldcomment (application level) from the database. Pass the database question in $question
	and the answer's database record in $answer if this is a comment on an answer, otherwise null. Pass details of the user
	doing this in $userid, $handle and $cookieid. Handles unindexing, points, cached counts and event reports.
	See qa-app-posts.php for a higher-level function which is easier to use.
*/
	{
		if (!$oldcomment['hidden'])
			qa_fatal_error('Tried to delete a non-hidden comment');
		
		qa_post_unindex($oldcomment['postid']);
		qa_db_post_delete($oldcomment['postid']);
		qa_db_points_update_ifuser($oldcomment['userid'], array('cposts'));
		qa_db_ccount_update();

		qa_report_event('c_delete', $userid, $handle, $cookieid, array(
			'postid' => $oldcomment['postid'],
			'parentid' => $oldcomment['parentid'],
			'parenttype' => isset($answer) ? $answer['basetype'] : $question['basetype'],
			'questionid' => $question['postid'],
		));
	}

	
	function qa_comment_set_userid($oldcomment, $userid, $handle, $cookieid)
/*
	Set the author (application level) of $oldcomment to $userid and also pass $handle and $cookieid
	of user. Updates points and reports events as appropriate.
*/
	{
		qa_db_post_set_userid($oldcomment['postid'], $userid);
		
		qa_db_points_update_ifuser($oldcomment['userid'], array('cposts'));
		qa_db_points_update_ifuser($userid, array('cposts'));

		qa_report_event('c_claim', $userid, $handle, $cookieid, array(
			'postid' => $oldcomment['postid'],
			'parentid' => $oldcomment['parentid'],
		));
	}
	

/*
	Omit PHP closing tag to help avoid accidental output
*/