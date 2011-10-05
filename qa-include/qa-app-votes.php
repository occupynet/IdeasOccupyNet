<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-app-votes.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Handling incoming votes (application level)


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


	function qa_vote_error_html($post, $userid, $topage)
/*
	Check if $userid can vote on $post, on the page $topage.
	Return an HTML error to display if there was a problem, or false if it's OK.
*/
	{
		require_once QA_INCLUDE_DIR.'qa-app-users.php';
		
		if (
			is_array($post) &&
			( ($post['basetype']=='Q') || ($post['basetype']=='A') ) &&
			qa_opt(($post['basetype']=='Q') ? 'voting_on_qs' : 'voting_on_as') &&
			( (!isset($post['userid'])) || (!isset($userid)) || ($post['userid']!=$userid) )
		) {
			
			switch (qa_user_permit_error(($post['basetype']=='Q') ? 'permit_vote_q' : 'permit_vote_a', 'V')) {
				case 'login':
					return qa_insert_login_links(qa_lang_html('main/vote_must_login'), $topage);
					break;
					
				case 'confirm':
					return qa_insert_login_links(qa_lang_html('main/vote_must_confirm'), $topage);
					break;
					
				case 'limit':
					return qa_lang_html('main/vote_limit');
					break;
					
				default:
					return qa_lang_html('users/no_permission');
					break;
					
				case false:
					return false;
			}
		
		} else
			return qa_lang_html('main/vote_not_allowed'); // voting option should not have been presented (but could happen due to options change)
	}

	
	function qa_vote_set($post, $userid, $handle, $cookieid, $vote)
/*
	Actually set (application level) the $vote (-1/0/1) by $userid (with $handle and $cookieid) on $postid.
	Handles user points, recounting and event reports as appropriate.
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-points.php';
		require_once QA_INCLUDE_DIR.'qa-db-hotness.php';
		require_once QA_INCLUDE_DIR.'qa-db-votes.php';
		require_once QA_INCLUDE_DIR.'qa-app-limits.php';
		
		$vote=(int)min(1, max(-1, $vote));
		$oldvote=(int)qa_db_uservote_get($post['postid'], $userid);

		qa_db_uservote_set($post['postid'], $userid, $vote);
		qa_db_post_recount_votes($post['postid']);
		
		$postisanswer=($post['basetype']=='A');
		
		$columns=array();
		
		if ( ($vote>0) || ($oldvote>0) )
			$columns[]=$postisanswer ? 'aupvotes' : 'qupvotes';

		if ( ($vote<0) || ($oldvote<0) )
			$columns[]=$postisanswer ? 'adownvotes' : 'qdownvotes';
			
		qa_db_points_update_ifuser($userid, $columns);
		
		qa_db_points_update_ifuser($post['userid'], array($postisanswer ? 'avoteds' : 'qvoteds', 'upvoteds', 'downvoteds'));
		
		if ($post['basetype']=='Q')
			qa_db_hotness_update($post['postid']);
		
		if ($vote<0)
			$action=$postisanswer ? 'a_vote_down' : 'q_vote_down';
		elseif ($vote>0)
			$action=$postisanswer ? 'a_vote_up' : 'q_vote_up';
		else
			$action=$postisanswer ? 'a_vote_nil' : 'q_vote_nil';
		
		qa_report_write_action($userid, null, $action, $postisanswer ? null : $post['postid'], $postisanswer ? $post['postid'] : null, null);

		qa_report_event($action, $userid, $handle, $cookieid, array(
			'postid' => $post['postid'],
			'vote' => $vote,
			'oldvote' => $oldvote,
		));
	}
	
	
	function qa_flag_error_html($post, $userid, $topage)
/*
	Check if $userid can flag $post, on the page $topage.
	Return an HTML error to display if there was a problem, or false if it's OK.
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-selects.php';
		require_once QA_INCLUDE_DIR.'qa-app-options.php';
		require_once QA_INCLUDE_DIR.'qa-app-users.php';

		if (
			is_array($post) &&
			qa_opt('flagging_of_posts') &&
			( (!isset($post['userid'])) || (!isset($userid)) || ($post['userid']!=$userid) )
		) {
		
			switch (qa_user_permit_error('permit_flag', 'F')) {
				case 'login':
					return qa_insert_login_links(qa_lang_html('question/flag_must_login'), $topage);
					break;
					
				case 'confirm':
					return qa_insert_login_links(qa_lang_html('question/flag_must_confirm'), $topage);
					break;
					
				case 'limit':
					return qa_lang_html('question/flag_limit');
					break;
					
				default:
					return qa_lang_html('users/no_permission');
					break;
					
				case false:
					return false;
			}
		
		} else
			return qa_lang_html('question/flag_not_allowed'); // flagging option should not have been presented
	}
	

	function qa_flag_set_tohide($post, $userid, $handle, $cookieid, $question)
/*
	Set (application level) a flag by $userid (with $handle and $cookieid) on $post which belongs to $question.
	Handles recounting, admin notifications and event reports as appropriate.
	Returns true if the post should now be hidden because it has accumulated enough flags.
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-votes.php';
		require_once QA_INCLUDE_DIR.'qa-app-limits.php';
		
		qa_db_userflag_set($post['postid'], $userid, true);
		qa_db_post_recount_flags($post['postid']);
		
		switch ($post['basetype']) {
			case 'Q':
				$action='q_flag';
				break;
				
			case 'A':
				$action='a_flag';
				break;

			case 'C':
				$action='c_flag';
				break;
		}
		
		qa_report_write_action($userid, null, $action, ($post['basetype']=='Q') ? $post['postid'] : null,
			($post['basetype']=='A') ? $post['postid'] : null, ($post['basetype']=='C') ? $post['postid'] : null);

		qa_report_event($action, $userid, $handle, $cookieid, array(
			'postid' => $post['postid'],
		));
		
		$post=qa_db_select_with_pending(qa_db_full_post_selectspec(null, $post['postid']));
		
		$flagcount=$post['flagcount'];
		$notifycount=$flagcount-qa_opt('flagging_notify_first');
		
		if ( ($notifycount>=0) && (($notifycount % qa_opt('flagging_notify_every'))==0) ) {
			require_once QA_INCLUDE_DIR.'qa-app-emails.php';
			require_once QA_INCLUDE_DIR.'qa-app-format.php';
			
			$anchor=($post['basetype']=='Q') ? null : qa_anchor($post['basetype'], $post['postid']);
			
			qa_send_notification(null, qa_opt('feedback_email'), null, qa_lang('emails/flagged_subject'), qa_lang('emails/flagged_body'), array(
				'^p_handle' => isset($post['handle']) ? $post['handle'] : qa_lang('main/anonymous'),
				'^flags' => ($flagcount==1) ? qa_lang_html_sub('main/1_flag', '1', '1') : qa_lang_html_sub('main/x_flags', $flagcount),
				'^p_context' => trim(@$post['title']."\n\n".qa_viewer_text($post['content'], $post['format'])),
				'^url' => qa_path(qa_q_request($question['postid'], $question['title']), null, qa_opt('site_url'), null, $anchor),
			));
		}
		
		if ( ($flagcount>=qa_opt('flagging_hide_after')) && !$post['hidden'] )
			return true;
		
		return false;
	}


	function qa_flag_clear($post, $userid, $handle, $cookieid)
/*
	Clear (application level) a flag on $post by $userid (with $handle and $cookieid).
	Handles recounting and event reports as appropriate.
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-votes.php';
		require_once QA_INCLUDE_DIR.'qa-app-limits.php';
		
		qa_db_userflag_set($post['postid'], $userid, false);
		qa_db_post_recount_flags($post['postid']);
		
		switch ($post['basetype']) {
			case 'Q':
				$action='q_unflag';
				break;
				
			case 'A':
				$action='a_unflag';
				break;

			case 'C':
				$action='c_unflag';
				break;
		}
		
		qa_report_write_action($userid, null, $action, ($post['basetype']=='Q') ? $post['postid'] : null,
			($post['basetype']=='A') ? $post['postid'] : null, ($post['basetype']=='C') ? $post['postid'] : null);

		qa_report_event($action, $userid, $handle, $cookieid, array(
			'postid' => $post['postid'],
		));
	}
	
	
	function qa_flags_clear_all($post, $userid, $handle, $cookieid)
/*
	Clear (application level) all flags on $post by $userid (with $handle and $cookieid).
	Handles recounting and event reports as appropriate.
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-votes.php';
		require_once QA_INCLUDE_DIR.'qa-app-limits.php';
		
		qa_db_userflags_clear_all($post['postid']);
		qa_db_post_recount_flags($post['postid']);

		switch ($post['basetype']) {
			case 'Q':
				$action='q_clearflags';
				break;
				
			case 'A':
				$action='a_clearflags';
				break;

			case 'C':
				$action='c_clearflags';
				break;
		}

		qa_report_write_action($userid, null, $action, ($post['basetype']=='Q') ? $post['postid'] : null,
			($post['basetype']=='A') ? $post['postid'] : null, ($post['basetype']=='C') ? $post['postid'] : null);

		qa_report_event($action, $userid, $handle, $cookieid, array(
			'postid' => $post['postid'],
		));
	}

/*
	Omit PHP closing tag to help avoid accidental output
*/