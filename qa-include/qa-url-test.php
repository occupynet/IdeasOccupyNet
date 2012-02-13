<?php
	
/*
	Question2Answer (c) Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-url-test.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Sits in an iframe and shows a green page with word 'OK'


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

	if (qa_gpc_to_string(@$_GET['param'])==QA_URL_TEST_STRING) {
		require_once QA_INCLUDE_DIR.'qa-app-admin.php';
	
		echo '<HTML><BODY STYLE="margin:0; padding:0;">';
		echo '<TABLE WIDTH="100%" HEIGHT="100%" CELLSPACING="0" CELLPADDING="0">';
		echo '<TR VALIGN="middle"><TD ALIGN="center" STYLE="border-style:solid; border-width:1px; background-color:#fff; ';
		echo qa_admin_url_test_html();
		echo 'TD></TR></TABLE>';
		echo '</BODY></HTML>';
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/