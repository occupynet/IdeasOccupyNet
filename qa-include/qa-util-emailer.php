<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-util-emailer.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Wrapper for email sending function


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

	if (QA_EXTERNAL_EMAILER) {
	
		require_once QA_EXTERNAL_DIR.'qa-external-emailer.php';
	
	} else {
	
		function qa_send_email($params)
	/*
		Send the email based on the $params array - the following keys are required (some can be empty):
		fromemail, fromname, toemail, toname, subject, body, html
	*/
		{
			require_once QA_INCLUDE_DIR.'qa-class.phpmailer.php';
			
			$mailer=new PHPMailer();
			$mailer->CharSet='utf-8';
			
			$mailer->From=$params['fromemail'];
			$mailer->Sender=$params['fromemail'];
			$mailer->FromName=$params['fromname'];
			$mailer->AddAddress($params['toemail'], $params['toname']);
			$mailer->Subject=$params['subject'];
			$mailer->Body=$params['body'];

			if ($params['html'])
				$mailer->IsHTML(true);
				
			return $mailer->Send();
		}
		
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/