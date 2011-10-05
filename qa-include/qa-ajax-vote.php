<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-ajax-vote.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Server-side response to Ajax voting requests


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

	require_once QA_INCLUDE_DIR.'qa-app-users.php';
	require_once QA_INCLUDE_DIR.'qa-app-cookies.php';
	require_once QA_INCLUDE_DIR.'qa-app-votes.php';
	require_once QA_INCLUDE_DIR.'qa-app-format.php';
	require_once QA_INCLUDE_DIR.'qa-app-options.php';
	require_once QA_INCLUDE_DIR.'qa-db-selects.php';
	

	function qa_ajax_vote_db_fail_handler()
	{
		echo "QA_AJAX_RESPONSE\n0\nA database error occurred.";
		exit;
	}

	qa_base_db_connect('qa_ajax_vote_db_fail_handler');

	$postid=qa_post_text('postid');
	$qa_login_userid=qa_get_logged_in_userid();
	$qa_cookieid=qa_cookie_get();

	$post=qa_db_select_with_pending(qa_db_full_post_selectspec($qa_login_userid, $postid));

	$voteerror=qa_vote_error_html($post, $qa_login_userid, $qa_request);
	
	if ($voteerror===false) {
		qa_vote_set($post, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, qa_post_text('vote'));
		
		$post=qa_db_select_with_pending(qa_db_full_post_selectspec($qa_login_userid, $postid));
		
		$fields=qa_post_html_fields($post, $qa_login_userid, $qa_cookieid, array(), null, array(
			'voteview' => qa_opt('votes_separated') ? 'updown' : 'net')
		);
		
		$themeclass=qa_load_theme_class(qa_opt('site_theme'), 'voting', null, null);

		echo "QA_AJAX_RESPONSE\n1\n";
		$themeclass->voting_inner_html($fields);

	} else
		echo "QA_AJAX_RESPONSE\n0\n".$voteerror;

	qa_base_db_disconnect();
	

/*
	Omit PHP closing tag to help avoid accidental output
*/