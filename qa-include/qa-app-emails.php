<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-app-emails.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Wrapper functions for sending email notifications to users


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

	require_once QA_INCLUDE_DIR.'qa-app-options.php';


	$qa_notifications_suspended=0;
	
	
	function qa_suspend_notifications($suspend=true)
/*
	Suspend the sending of all email notifications via qa_send_notification(...) if $suspend is
	true, otherwise reinstate it. A counter is kept to allow multiple calls.
*/
	{
		global $qa_notifications_suspended;
		
		$qa_notifications_suspended+=($suspend ? 1 : -1);
	}
	
	
	function qa_send_notification($userid, $email, $handle, $subject, $body, $subs)
/*
	Send email to person with $userid and/or $email and/or $handle (null/invalid values
	are ignored or retrieved from user database as appropriate). Email uses $subject
	and $body, after substituting each key in $subs with its corresponding value, plus
	applying some standard substitutions such as ^site_title, ^handle and ^email.
*/
	{
		global $qa_notifications_suspended;
		
		if ($qa_notifications_suspended>0)
			return false;
		
		require_once QA_INCLUDE_DIR.'qa-db-selects.php';
		require_once QA_INCLUDE_DIR.'qa-util-emailer.php';
		require_once QA_INCLUDE_DIR.'qa-util-string.php';
		
		if (isset($userid)) {
			$needemail=!qa_email_validate(@$email); // take from user if invalid, e.g. @ used in practice
			$needhandle=empty($handle);
			
			if ($needemail || $needhandle) {
				if (QA_FINAL_EXTERNAL_USERS) {
					if ($needhandle) {
						$handles=qa_get_public_from_userids(array($userid));
						$handle=@$handles[$userid];
					}
					
					if ($needemail)
						$email=qa_get_user_email($userid);
				
				} else {
					$useraccount=qa_db_select_with_pending(
						qa_db_user_account_selectspec($userid, true)
					);
					
					if ($needhandle)
						$handle=@$useraccount['handle'];
	
					if ($needemail)
						$email=@$useraccount['email'];
				}
			}
		}
			
		if (isset($email) && qa_email_validate($email)) {
			$subs['^site_title']=qa_opt('site_title');
			$subs['^handle']=$handle;
			$subs['^email']=$email;
			$subs['^open']="\n";
			$subs['^close']="\n";
		
			return qa_send_email(array(
				'fromemail' => qa_opt('from_email'),
				'fromname' => qa_opt('site_title'),
				'toemail' => $email,
				'toname' => $handle,
				'subject' => strtr($subject, $subs),
				'body' => (empty($handle) ? '' : $handle.",\n\n").strtr($body, $subs),
				'html' => false,
			));
		
		} else
			return false;
	}
	

/*
	Omit PHP closing tag to help avoid accidental output
*/