<?php
	
/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-page-admin-hidden.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Controller for admin page showing hidden questions, answers and comments


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

	require_once QA_INCLUDE_DIR.'qa-app-admin.php';
	require_once QA_INCLUDE_DIR.'qa-db-selects.php';
	require_once QA_INCLUDE_DIR.'qa-app-format.php';

	
//	Find recently hidden questions, answers, comments

	list($hiddenquestions, $hiddenanswers, $hiddencomments)=qa_db_select_with_pending(
		qa_db_qs_selectspec($qa_login_userid, 'created', 0, null, null, true),
		qa_db_recent_a_qs_selectspec($qa_login_userid, 0, null, null, true),
		qa_db_recent_c_qs_selectspec($qa_login_userid, 0, null, null, true)
	);
	
	
//	Check admin privileges (do late to allow one DB query)

	if (!qa_admin_check_privileges($qa_content))
		return $qa_content;
		
		
//	Combine sets of questions and get information for users

	$questions=qa_any_sort_and_dedupe(array_merge($hiddenquestions, $hiddenanswers, $hiddencomments));
	
	$usershtml=qa_userids_handles_html(qa_any_get_userids_handles($questions));


//	Prepare content for theme
	
	$qa_content=qa_content_prepare();

	$qa_content['title']=qa_lang_html('admin/recent_hidden_title');
	
	$qa_content['error']=qa_admin_page_error();
	
	$qa_content['q_list']['qs']=array();
	
	if (count($questions)) {
		foreach ($questions as $question) {
			$htmloptions=qa_post_html_defaults('Q');
			$htmloptions['voteview']=false;
			$htmloptions['tagsview']=false;
			$htmloptions['answersview']=false;

			$htmlfields=qa_any_to_q_html_fields($question, $qa_login_userid, $qa_cookieid, $usershtml, null, $htmloptions);
			
			if (isset($htmlfields['what_url'])) // link directly to relevant content
				$htmlfields['url']=$htmlfields['what_url'];

			$qa_content['q_list']['qs'][]=$htmlfields;
		}

	} else
		$qa_content['title']=qa_lang_html('admin/no_hidden_found');
		

	$qa_content['navigation']['sub']=qa_admin_sub_navigation();
	
	return $qa_content;
	

/*
	Omit PHP closing tag to help avoid accidental output
*/