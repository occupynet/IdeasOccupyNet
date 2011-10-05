<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-app-cookies.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: User cookie management (application level) for tracking anonymous posts


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


	function qa_cookie_get()
/*
	Return the user identification cookie sent by the browser for this page request, or null if none
*/
	{
		return isset($_COOKIE['qa_id']) ? qa_gpc_to_string($_COOKIE['qa_id']) : null;
	}

	
	function qa_cookie_get_create()
/*
	Return user identification cookie sent by browser if valid, or create a new one if not.
	Either way, extend for another year (this is used when an anonymous post is created)
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-cookies.php';

		$cookieid=qa_cookie_get();
		
		if (isset($cookieid) && qa_db_cookie_exists($cookieid))
			; // cookie is valid
		else
			$cookieid=qa_db_cookie_create(@$_SERVER['REMOTE_ADDR']);
		
		setcookie('qa_id', $cookieid, time()+86400*365, '/', QA_COOKIE_DOMAIN);
		
		return $cookieid;
	}

	
	function qa_cookie_report_action($cookieid, $action, $questionid, $answerid, $commentid)
/*
	Called after a database write $action performed by a user identified by $cookieid,
	relating to $questionid, $answerid and/or $commentid
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-cookies.php';
		
		qa_db_cookie_written($cookieid, @$_SERVER['REMOTE_ADDR']);
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/