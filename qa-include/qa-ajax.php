<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-ajax.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Front line of response to Ajax requests, routing as appropriate


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

//	Output this header as early as possible

	header('Content-Type: text/plain; charset=utf-8');

//	Ensure no PHP errors are shown in the Ajax response

	@ini_set('display_errors', 0);

//	Load the QA base file which sets up a bunch of crucial functions

	require 'qa-base.php';

//	Get general Ajax parameters from the POST payload

	$qa_root_url_relative=qa_post_text('qa_root');
	$qa_request=qa_post_text('qa_request');
	$qa_operation=qa_post_text('qa_operation');

//	Perform the appropriate Ajax operation

	switch ($qa_operation) {
		case 'vote':
			require QA_INCLUDE_DIR.'qa-ajax-vote.php';
			break;
			
		case 'recalc':
			require QA_INCLUDE_DIR.'qa-ajax-recalc.php';
			break;
			
		case 'subcats':
			require QA_INCLUDE_DIR.'qa-ajax-subcats.php';
			break;
			
		case 'asktitle':
			require QA_INCLUDE_DIR.'qa-ajax-asktitle.php';
			break;
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/