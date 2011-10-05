<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-page-unanswered.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Controller for page listing recent questions without answers


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

	require_once QA_INCLUDE_DIR.'qa-db-selects.php';
	require_once QA_INCLUDE_DIR.'qa-app-format.php';
	require_once QA_INCLUDE_DIR.'qa-app-q-list.php';
	

//	Get list of unanswered questions

	$questions=qa_db_select_with_pending(
		qa_db_unanswered_qs_selectspec($qa_login_userid, $qa_start)
	);
	
	
//	Prepare and return content for theme

	return qa_q_list_page_content(
		$questions, qa_opt('page_size_una_qs'), $qa_start, qa_opt('cache_unaqcount'),
		qa_lang_html('main/unanswered_qs_title'), qa_lang_html('main/no_una_questions_found'),
		null, null, false, null, qa_opt('feed_for_unanswered') ? 'unanswered' : null, qa_html_suggest_qs_tags(qa_using_tags())
	);


/*
	Omit PHP closing tag to help avoid accidental output
*/