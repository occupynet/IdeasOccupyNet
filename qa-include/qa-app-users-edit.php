<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-app-users-edit.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: User management (application level) for creating/modifying users


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


	define('QA_MIN_PASSWORD_LEN', 4);
	define('QA_NEW_PASSWORD_LEN', 8);


	function qa_handle_email_validate($handle, $email, $allowuserid=null)
/*
	Return $errors fields for any invalid aspect of user-entered $handle (username) and $email.
	Also rejects existing values in database unless they belongs to $allowuserid (if set).
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-users.php';
		require_once QA_INCLUDE_DIR.'qa-db-maxima.php';
		require_once QA_INCLUDE_DIR.'qa-util-string.php';
		
		$errors=array();
		
		if (empty($handle))
			$errors['handle']=qa_lang('users/handle_empty');

		elseif (preg_match('/[\\@\\+\\/]/', $handle))
			$errors['handle']=qa_lang_sub('users/handle_has_bad', '@ + /');
		
		elseif (qa_strlen($handle)>QA_DB_MAX_HANDLE_LENGTH)
			$errors['handle']=qa_lang_sub('main/max_length_x', QA_DB_MAX_HANDLE_LENGTH);
		
		else {
			$handleusers=qa_db_user_find_by_handle($handle);
			if (count($handleusers) && ( (!isset($allowuserid)) || (array_search($allowuserid, $handleusers)===false) ) )
				$errors['handle']=qa_lang('users/handle_exists');
		}
		
		if (empty($email))
			$errors['email']=qa_lang('users/email_required');
		
		elseif (!qa_email_validate($email))
			$errors['email']=qa_lang('users/email_invalid');
		
		elseif (qa_strlen($email)>QA_DB_MAX_EMAIL_LENGTH)
			$errors['email']=qa_lang_sub('main/max_length_x', QA_DB_MAX_EMAIL_LENGTH);
			
		else {
			$emailusers=qa_db_user_find_by_email($email);
			if (count($emailusers) && ( (!isset($allowuserid)) || (array_search($allowuserid, $emailusers)===false) ) )
				$errors['email']=qa_lang('users/email_exists');
		}
		
		return $errors;
	}
	
	
	function qa_handle_make_valid($handle, $allowuserid=null)
/*
	Make $handle valid and unique in the database - if $allowuserid is set, allow it to match that user only
*/
	{
		require_once QA_INCLUDE_DIR.'qa-util-string.php';
		require_once QA_INCLUDE_DIR.'qa-db-maxima.php';
		
		if (!strlen($handle))
			$handle=qa_lang('users/registered_user');

		$handle=preg_replace('/[\\@\\+\\/]/', ' ', $handle);

		for ($attempt=0; $attempt<=99; $attempt++) {
			$suffix=$attempt ? (' '.$attempt) : '';
			$tryhandle=qa_substr($handle, 0, QA_DB_MAX_HANDLE_LENGTH-strlen($suffix)).$suffix;
			$handleusers=qa_db_user_find_by_handle($tryhandle);
		
			if (!(count($handleusers) && ( (!isset($allowuserid)) || (array_search($allowuserid, $handleusers)===false) ) ))
				return $tryhandle;
		}
		
		qa_fatal_error('Could not create a unique handle');
	}


	function qa_profile_field_validate($field, $value, &$errors)
/*
	Return $errors fields for any invalid aspect of user-entered profile information
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-maxima.php';
		require_once QA_INCLUDE_DIR.'qa-util-string.php';
		
		if (qa_strlen($value)>QA_DB_MAX_PROFILE_CONTENT_LENGTH)
			$errors[$field]=qa_lang_sub('main/max_length_x', QA_DB_MAX_PROFILE_CONTENT_LENGTH);
	}


	function qa_password_validate($password)
/*
	Return $errors fields for any invalid aspect of user-entered password
*/
	{
		require_once QA_INCLUDE_DIR.'qa-util-string.php';

		$errors=array();

		$minpasslen=max(QA_MIN_PASSWORD_LEN, 1);
		
		if (qa_strlen($password)<$minpasslen)
			$errors['password']=qa_lang_sub('users/password_min', $minpasslen);
		
		return $errors;
	}

	
	function qa_create_new_user($email, $password, $handle, $level=QA_USER_LEVEL_BASIC, $confirmed=false)
/*
	Create a new user (application level) with $email, $password, $handle and $level.
	Set $confirmed to true if the email address has been confirmed elsewhere.
	Handles user points, notification and optional email confirmation.
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-users.php';
		require_once QA_INCLUDE_DIR.'qa-db-points.php';
		require_once QA_INCLUDE_DIR.'qa-app-options.php';
		require_once QA_INCLUDE_DIR.'qa-app-emails.php';
		require_once QA_INCLUDE_DIR.'qa-app-cookies.php';

		$userid=qa_db_user_create($email, $password, $handle, $level, @$_SERVER['REMOTE_ADDR']);
		qa_db_points_update_ifuser($userid, null);
		
		if ($confirmed)
			qa_db_user_set_flag($userid, QA_USER_FLAGS_EMAIL_CONFIRMED, true);
		
		$options=qa_get_options(array('custom_welcome', 'site_url', 'confirm_user_emails'));
		
		$custom=trim($options['custom_welcome']);
		
		if ($options['confirm_user_emails'] && ($level<QA_USER_LEVEL_EXPERT) && !$confirmed)
			$confirm=strtr(qa_lang('emails/welcome_confirm'), array(
				'^url' => qa_get_new_confirm_url($userid, $handle)
			));
		else
			$confirm='';
		
		qa_send_notification($userid, $email, $handle, qa_lang('emails/welcome_subject'), qa_lang('emails/welcome_body'), array(
			'^password' => isset($password) ? $password : qa_lang('users/password_to_set'),
			'^url' => $options['site_url'],
			'^custom' => empty($custom) ? '' : ($custom."\n\n"),
			'^confirm' => $confirm,
		));
		
		qa_report_event('u_register', $userid, $handle, qa_cookie_get(), array(
			'email' => $email,
			'level' => $level,
		));
		
		return $userid;
	}

	
	function qa_send_new_confirm($userid)
/*
	Set a new email confirmation code for the user and send it out
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-users.php';
		require_once QA_INCLUDE_DIR.'qa-db-selects.php';
		require_once QA_INCLUDE_DIR.'qa-app-emails.php';

		$userinfo=qa_db_select_with_pending(qa_db_user_account_selectspec($userid, true));
		
		if (!qa_send_notification($userid, $userinfo['email'], $userinfo['handle'], qa_lang('emails/confirm_subject'), qa_lang('emails/confirm_body'), array(
			'^url' => qa_get_new_confirm_url($userid, $userinfo['handle']),
		)))
			qa_fatal_error('Could not send email confirmation');
	}

	
	function qa_get_new_confirm_url($userid, $handle)
/*
	Set a new email confirmation code for the user and return the corresponding link
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-users.php';
		
		$emailcode=qa_db_user_rand_emailcode();
		qa_db_user_set($userid, 'emailcode', $emailcode);
		
		return qa_path('confirm', array('c' => $emailcode, 'u' => $handle), qa_opt('site_url'));
	}

	
	function qa_complete_confirm($userid, $email, $handle)
/*
	Complete the email confirmation process for the user
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-users.php';
		require_once QA_INCLUDE_DIR.'qa-app-cookies.php';
		
		qa_db_user_set_flag($userid, QA_USER_FLAGS_EMAIL_CONFIRMED, true);
		qa_db_user_set($userid, 'emailcode', ''); // to prevent re-use of the code

		qa_report_event('u_confirmed', $userid, $handle, qa_cookie_get(), array(
			'email' => $email,
		));
	}

	
	function qa_start_reset_user($userid)
/*
	Start the 'I forgot my password' process for $userid, sending reset code
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-users.php';
		require_once QA_INCLUDE_DIR.'qa-app-options.php';
		require_once QA_INCLUDE_DIR.'qa-app-emails.php';
		require_once QA_INCLUDE_DIR.'qa-db-selects.php';

		qa_db_user_set($userid, 'emailcode', qa_db_user_rand_emailcode());

		$userinfo=qa_db_select_with_pending(qa_db_user_account_selectspec($userid, true));

		if (!qa_send_notification($userid, $userinfo['email'], $userinfo['handle'], qa_lang('emails/reset_subject'), qa_lang('emails/reset_body'), array(
			'^code' => $userinfo['emailcode'],
			'^url' => qa_path('reset', array('c' => $userinfo['emailcode'], 'e' => $userinfo['email']), qa_opt('site_url')),
		)))
			qa_fatal_error('Could not send reset password email');
	}

	
	function qa_complete_reset_user($userid)
/*
	Successfully finish the 'I forgot my password' process for $userid, sending new password
*/
	{
		require_once QA_INCLUDE_DIR.'qa-util-string.php';
		require_once QA_INCLUDE_DIR.'qa-app-options.php';
		require_once QA_INCLUDE_DIR.'qa-app-emails.php';
		require_once QA_INCLUDE_DIR.'qa-app-cookies.php';
		require_once QA_INCLUDE_DIR.'qa-db-selects.php';
	
		$password=qa_random_alphanum(max(QA_MIN_PASSWORD_LEN, QA_NEW_PASSWORD_LEN));
		
		$userinfo=qa_db_select_with_pending(qa_db_user_account_selectspec($userid, true));
		
		if (!qa_send_notification($userid, $userinfo['email'], $userinfo['handle'], qa_lang('emails/new_password_subject'), qa_lang('emails/new_password_body'), array(
			'^password' => $password,
			'^url' => qa_opt('site_url'),
		)))
			qa_fatal_error('Could not send new password - password not reset');
		
		qa_db_user_set_password($userid, $password); // do this last, to be safe
		qa_db_user_set($userid, 'emailcode', ''); // so can't be reused

		qa_report_event('u_reset', $userid, $userinfo['handle'], qa_cookie_get(), array(
			'email' => $userinfo['email'],
		));
	}

	
	function qa_logged_in_user_flush()
/*
	Flush any information about the currently logged in user, so it is retrieved from database again
*/
	{
		global $qa_cached_logged_in_user;
		
		$qa_cached_logged_in_user=null;
	}
	
	
	function qa_set_user_avatar($userid, $imagedata, $oldblobid=null)
/*
	Set the avatar of $userid to the image in $imagedata, and remove $oldblobid from the database if not null
*/
	{
		require_once QA_INCLUDE_DIR.'qa-util-image.php';
		
		$imagedata=qa_image_constrain_data($imagedata, $width, $height, qa_opt('avatar_store_size'));
		
		if (isset($imagedata)) {
			require_once QA_INCLUDE_DIR.'qa-db-blobs.php';

			$newblobid=qa_db_blob_create($imagedata, 'jpeg', null, $userid, null, @$_SERVER['REMOTE_ADDR']);
			
			if (isset($newblobid)) {
				qa_db_user_set($userid, 'avatarblobid', $newblobid);
				qa_db_user_set($userid, 'avatarwidth', $width);
				qa_db_user_set($userid, 'avatarheight', $height);
				qa_db_user_set_flag($userid, QA_USER_FLAGS_SHOW_AVATAR, true);
				qa_db_user_set_flag($userid, QA_USER_FLAGS_SHOW_GRAVATAR, false);

				if (isset($oldblobid))
					qa_db_blob_delete($oldblobid);

				return true;
			}
		}
		
		return false;
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/