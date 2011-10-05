<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-page-hot.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Controller for page listing hot questions


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
	require_once QA_INCLUDE_DIR.'qa-app-q-list.php';
	

//	Get list of questions

	$questions=qa_db_select_with_pending(
		qa_db_qs_selectspec($qa_login_userid, 'hotness', $qa_start)
	);


//	Prepare and return content for theme

	$qa_content=qa_q_list_page_content(
		$questions, qa_opt('page_size_hot_qs'), $qa_start, qa_opt('cache_qcount'),
		qa_lang_html('main/hot_qs_title'), qa_lang_html('main/no_questions_found'),
		null, null, false, null, null, qa_html_suggest_ask()
	);


	return $qa_content;
	

/*
	Omit PHP closing tag to help avoid accidental output
*/