<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-ajax-subcats.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Server-side response to Ajax subcategory listing requests


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

	require_once QA_INCLUDE_DIR.'qa-db-selects.php';
	

	function qa_ajax_subcats_db_fail_handler()
	{
		echo "QA_AJAX_RESPONSE\n0\nA database error occurred.";
		exit;
	}

	qa_base_db_connect('qa_ajax_subcats_db_fail_handler');
	
	$categoryid=qa_post_text('categoryid');
	if (!strlen($categoryid))
		$categoryid=null;
	
	$categories=qa_db_select_with_pending(qa_db_category_sub_selectspec($categoryid));
	
	echo "QA_AJAX_RESPONSE\n1";
	
	foreach ($categories as $category)
		echo "\n".$category['categoryid'].'/'.$category['title'];

	qa_base_db_disconnect();
	

/*
	Omit PHP closing tag to help avoid accidental output
*/