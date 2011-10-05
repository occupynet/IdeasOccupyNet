<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-ajax-recalc.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Server-side response to Ajax recalculation requests


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
	require_once QA_INCLUDE_DIR.'qa-app-recalc.php';
	

	function qa_ajax_recalc_db_fail_handler()
	{
		echo "QA_AJAX_RESPONSE\n0\n\nA database error occurred.";
		exit;
	}

	qa_base_db_connect('qa_ajax_recalc_db_fail_handler');
	
	if (qa_get_logged_in_level()>=QA_USER_LEVEL_ADMIN) {
		$state=qa_post_text('state');
		$stoptime=time()+3;
		
		while ( qa_recalc_perform_step($state) && (time()<$stoptime) )
			;
			
		$message=qa_recalc_get_message($state);
	
	} else {
		$state='';
		$message=qa_lang('admin/no_privileges');
	}
	
	qa_base_db_disconnect();
	
	echo "QA_AJAX_RESPONSE\n1\n".$state."\n".qa_html($message);


/*
	Omit PHP closing tag to help avoid accidental output
*/