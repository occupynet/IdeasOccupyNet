<?php

/*
	Question2Answer (c) Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-app-users-edit.php
	Version: See define()s at top of qa-include/qa-base.php
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

	@define('QA_MIN_PASSWORD_LEN', 4);
	@define('QA_NEW_PASSWORD_LEN', 8); // when resetting password


	function qa_handle_email_filter(&$handle, &$email, $olduser=null)
/*
	Return $errors fields for any invalid aspect of user-entered $handle (username) and $email. Works by calling through
	to all filter modules and also rejects existing values in database unless they belongs to $olduser (if set).
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-users.php';
		
		$errors=array();
		
		$filtermodules=qa_load_modules_with('filter', 'filter_handle');
		
		foreach ($filtermodules as $filtermodule) {
			$error=$filtermodule->filter_handle($handle, $olduser);
			if (isset($error)) {
				$errors['handle']=$error;
				break;
			}
		}

		if (!isset($errors['handle'])) { // first test through filters, then check for duplicates here
			$handleusers=qa_db_user_find_by_handle($handle);
			if (count($handleusers) && ( (!isset($olduser['userid'])) || (array_search($olduser['userid'], $handleusers)===false) ) )
				$errors['handle']=qa_lang('users/handle_exists');
		}
		
		$filtermodules=qa_load_modules_with('filter', 'filter_email');
		
		$error=null;
		foreach ($filtermodules as $filtermodule) {
			$error=$filtermodule->filter_email($email, $olduser);
			if (isset($error)) {
				$errors['email']=$error;
				break;
			}
		}

		if (!isset($errors['email'])) {
			$emailusers=qa_db_user_find_by_email($email);
			if (count($emailusers) && ( (!isset($olduser['userid'])) || (array_search($olduser['userid'], $emailusers)===false) ) )
				$errors['email']=qa_lang('users/email_exists');
		}
		
		return $errors;
	}
	
	
	function qa_handle_make_valid($handle)
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

			$filtermodules=qa_load_modules_with('filter', 'filter_handle');
			foreach ($filtermodules as $filtermodule)
				$filtermodule->filter_handle($handle, null); // filter first without worrying about errors, since our goal is to get a valid one
			
			$haderror=false;
			
			foreach ($filtermodules as $filtermodule) {
				$error=$filtermodule->filter_handle($handle, null); // now check for errors after we've filtered
				if (isset($error))
					$haderror=true;
			}
			
			if (!$haderror) {
				$handleusers=qa_db_user_find_by_handle($tryhandle);
				if (!count($handleusers))
					return $tryhandle;
			}
		}
		
		qa_fatal_error('Could not create a valid and unique handle from: '.$handle);
	}


	function qa_password_validate($password, $olduser=null)
/*
	Return an array with a single element (key 'password') if user-entered $password is valid, otherwise an empty array.
	Works by calling through to all filter modules.
*/
	{
		$error=null;
		$filtermodules=qa_load_modules_with('filter', 'validate_password');
		
		foreach ($filtermodules as $filtermodule) {
			$error=$filtermodule->validate_password($password, $olduser);
			if (isset($error))
				break;
		}
		
		if (!isset($error)) {
			$minpasslen=max(QA_MIN_PASSWORD_LEN, 1);
			if (qa_strlen($password)<$minpasslen)
				$error=qa_lang_sub('users/password_min', $minpasslen);
		}		

		if (isset($error))
			return array('password' => $error);

		return array();
	}

	
	function qa_create_new_user($email, $password, $handle, $level=QA_USER_LEVEL_BASIC, $confirmed=false)
/*
	Create a new user (application level) with $email, $password, $handle and $level.
	Set $confirmed to true if the email address has been confirmed elsewhere.
	Handles user points, notification and optional email confirmation.
*/
	{
		if (qa_to_override(__FUNCTION__)) { $args=func_get_args(); return qa_call_override(__FUNCTION__, $args); }
		
		require_once QA_INCLUDE_DIR.'qa-db-users.php';
		require_once QA_INCLUDE_DIR.'qa-db-points.php';
		require_once QA_INCLUDE_DIR.'qa-app-options.php';
		require_once QA_INCLUDE_DIR.'qa-app-emails.php';
		require_once QA_INCLUDE_DIR.'qa-app-cookies.php';

		$userid=qa_db_user_create($email, $password, $handle, $level, qa_remote_ip_address());
		qa_db_points_update_ifuser($userid, null);
		
		if ($confirmed)
			qa_db_user_set_flag($userid, QA_USER_FLAGS_EMAIL_CONFIRMED, true);
			
		if (qa_opt('show_notice_welcome'))
			qa_db_user_set_flag($userid, QA_USER_FLAGS_WELCOME_NOTICE, true);
		
		$custom=qa_opt('show_custom_welcome') ? trim(qa_opt('custom_welcome')) : '';
		
		if (qa_opt('confirm_user_emails') && ($level<QA_USER_LEVEL_EXPERT) && !$confirmed) {
			$confirm=strtr(qa_lang('emails/welcome_confirm'), array(
				'^url' => qa_get_new_confirm_url($userid, $handle)
			));
			
			if (qa_opt('confirm_user_required'))
				qa_db_user_set_flag($userid, QA_USER_FLAGS_MUST_CONFIRM, true);
				
		} else
			$confirm='';
		
		qa_send_notification($userid, $email, $handle, qa_lang('emails/welcome_subject'), qa_lang('emails/welcome_body'), array(
			'^password' => isset($password) ? $password : qa_lang('users/password_to_set'),
			'^url' => qa_opt('site_url'),
			'^custom' => strlen($custom) ? ($custom."\n\n") : '',
			'^confirm' => $confirm,
		));
		
		qa_report_event('u_register', $userid, $handle, qa_cookie_get(), array(
			'email' => $email,
			'level' => $level,
		));
		
		return $userid;
	}
	
	
	function qa_delete_user($userid)
/*
	Delete $userid and all their votes and flags. Their posts will become anonymous.
	Handles recalculations of votes and flags for posts this user has affected.
*/
	{
		if (qa_to_override(__FUNCTION__)) { $args=func_get_args(); return qa_call_override(__FUNCTION__, $args); }
		
		require_once QA_INCLUDE_DIR.'qa-db-votes.php';
		require_once QA_INCLUDE_DIR.'qa-db-users.php';
		require_once QA_INCLUDE_DIR.'qa-db-post-update.php';
		require_once QA_INCLUDE_DIR.'qa-db-points.php';
		
		$postids=qa_db_uservoteflag_user_get($userid); // posts this user has flagged or voted on, whose counts need updating
		
		qa_db_user_delete($userid);
		
		foreach ($postids as $postid) { // hoping there aren't many of these - saves a lot of new SQL code...
			qa_db_post_recount_votes($postid);
			qa_db_post_recount_flags($postid);
		}
		
		$postuserids=qa_db_posts_get_userids($postids);
			
		foreach ($postuserids as $postuserid)
			qa_db_points_update_ifuser($postuserid, array('avoteds','qvoteds', 'upvoteds', 'downvoteds'));
	}

	
	function qa_send_new_confirm($userid)
/*
	Set a new email confirmation code for the user and send it out
*/
	{
		if (qa_to_override(__FUNCTION__)) { $args=func_get_args(); return qa_call_override(__FUNCTION__, $args); }
		
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
		if (qa_to_override(__FUNCTION__)) { $args=func_get_args(); return qa_call_override(__FUNCTION__, $args); }
		
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
		if (qa_to_override(__FUNCTION__)) { $args=func_get_args(); return qa_call_override(__FUNCTION__, $args); }
		
		require_once QA_INCLUDE_DIR.'qa-db-users.php';
		require_once QA_INCLUDE_DIR.'qa-app-cookies.php';
		
		qa_db_user_set_flag($userid, QA_USER_FLAGS_EMAIL_CONFIRMED, true);
		qa_db_user_set_flag($userid, QA_USER_FLAGS_MUST_CONFIRM, false);
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
		if (qa_to_override(__FUNCTION__)) { $args=func_get_args(); return qa_call_override(__FUNCTION__, $args); }
		
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
		if (qa_to_override(__FUNCTION__)) { $args=func_get_args(); return qa_call_override(__FUNCTION__, $args); }
		
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
		if (qa_to_override(__FUNCTION__)) { $args=func_get_args(); return qa_call_override(__FUNCTION__, $args); }
		
		require_once QA_INCLUDE_DIR.'qa-util-image.php';
		
		$imagedata=qa_image_constrain_data($imagedata, $width, $height, qa_opt('avatar_store_size'));
		
		if (isset($imagedata)) {
			require_once QA_INCLUDE_DIR.'qa-db-blobs.php';

			$newblobid=qa_db_blob_create($imagedata, 'jpeg', null, $userid, null, qa_remote_ip_address());
			
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