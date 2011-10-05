<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-external-example/qa-external-emailer.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Example of how to use your own email sending function


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

/*
	==============================================================
	THIS FILE ALLOWS YOU TO DEFINE YOUR OWN EMAIL SENDING FUNCTION
	==============================================================

	It is used if QA_EXTERNAL_EMAILER is set to true in qa-config.php.
*/

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../');
		exit;
	}


	function qa_send_email($params)
/*
	This is your custom email sending function - $params is an array with the elements below.
	Return true if delivery (or at least queueing) was successful, false if not.
	
	'fromemail' => email of sender (should also be used for Return-Path)
	'fromname' => name of sender (should also be used for Return-Path)
	'toemail' => email of 'to' recipient
	'toname' => name of 'to' recipient
	'subject' => subject line of message (in UTF-8)
	'body' => body text of message (in UTF-8)
	'html' => true if body is HTML, false if body is plain text
	
	For an example that uses the PHPMailer library, see qa-include/qa-util-emailer.php.
*/
	{
		return false;
	}
	

/*
	Omit PHP closing tag to help avoid accidental output
*/