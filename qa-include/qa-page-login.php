<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-page-login.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Controller for login page


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


//	Check we're not using Q2A's single-sign on integration and that we're not logged in
	
	if (QA_FINAL_EXTERNAL_USERS)
		qa_fatal_error('User login is handled by external code');
		
	if (isset($qa_login_userid))
		qa_redirect('');
		

//	Process submitted form after checking we haven't reached rate limit
	
	require_once QA_INCLUDE_DIR.'qa-app-limits.php';

	$passwordsent=qa_get('ps');

	if (qa_limits_remaining(null, 'L')) {
		if (qa_clicked('dologin')) {
			require_once QA_INCLUDE_DIR.'qa-db-users.php';
			require_once QA_INCLUDE_DIR.'qa-db-selects.php';
		
			$inemailhandle=qa_post_text('emailhandle');
			$inpassword=qa_post_text('password');
			$inremember=qa_post_text('remember');
			
			$errors=array();
			
			if (strpos($inemailhandle, '@')===false) // handles can't contain @ symbols
				$matchusers=qa_db_user_find_by_handle($inemailhandle);
			else
				$matchusers=qa_db_user_find_by_email($inemailhandle);
	
			if (count($matchusers)==1) { // if matches more than one (should be impossible), don't log in
				$inuserid=$matchusers[0];
				$userinfo=qa_db_select_with_pending(qa_db_user_account_selectspec($inuserid, true));
				
				if (strtolower(qa_db_calc_passcheck($inpassword, $userinfo['passsalt'])) == strtolower($userinfo['passcheck'])) { // login and redirect
					require_once QA_INCLUDE_DIR.'qa-app-users.php';
	
					qa_set_logged_in_user($inuserid, $userinfo['handle'], $inremember ? true : false);
					
					$topath=qa_get('to');
					
					if (isset($topath))
						qa_redirect_raw($qa_root_url_relative.$topath); // path already provided as URL fragment
					elseif ($passwordsent)
						qa_redirect('account');
					else
						qa_redirect('');
	
				} else
					$errors['password']=qa_lang('users/password_wrong');
	
			} else
				$errors['emailhandle']=qa_lang('users/user_not_found');
				
			qa_limits_increment(null, 'L'); // only get here if we didn't log in successfully

		} else
			$inemailhandle=qa_get('e');
		
	} else
		$pageerror=qa_lang('users/login_limit');

	
//	Prepare content for theme
	
	$qa_content=qa_content_prepare();

	$qa_content['title']=qa_lang_html('users/login_title');
	
	$qa_content['error']=@$pageerror;

	if (empty($inemailhandle) || isset($errors['emailhandle']))
		$forgotpath=qa_path('forgot');
	else
		$forgotpath=qa_path('forgot', array('e' => $inemailhandle));
	
	$forgothtml='<A HREF="'.qa_html($forgotpath).'">'.qa_lang_html('users/forgot_link').'</A>';
	
	$qa_content['form']=array(
		'tags' => 'METHOD="POST" ACTION="'.qa_self_html().'"',
		
		'style' => 'tall',
		
		'ok' => $passwordsent ? qa_lang_html('users/password_sent') : null,
		
		'fields' => array(
			'email_handle' => array(
				'label' => qa_lang_html('users/email_handle_label'),
				'tags' => 'NAME="emailhandle" ID="emailhandle"',
				'value' => qa_html(@$inemailhandle),
				'error' => qa_html(@$errors['emailhandle']),
			),
			
			'password' => array(
				'type' => 'password',
				'label' => qa_lang_html('users/password_label'),
				'tags' => 'NAME="password" ID="password"',
				'value' => qa_html(@$inpassword),
				'error' => empty($errors['password']) ? '' : (qa_html(@$errors['password']).' - '.$forgothtml),
				'note' => $passwordsent ? qa_lang_html('users/password_sent') : $forgothtml,
			),
			
			'remember' => array(
				'type' => 'checkbox',
				'label' => qa_lang_html('users/remember_label'),
				'tags' => 'NAME="remember"',
				'value' => @$inremember ? true : false,
			),
		),
		
		'buttons' => array(
			'login' => array(
				'label' => qa_lang_html('users/login_button'),
			),
		),
		
		'hidden' => array(
			'dologin' => '1',
		),
	);
	
	$modulenames=qa_list_modules('login');
	
	foreach ($modulenames as $tryname) {
		$module=qa_load_module('login', $tryname);
		
		if (method_exists($module, 'login_html')) {
			ob_start();
			$module->login_html(qa_opt('site_url').qa_get('to'), 'login');
			$html=ob_get_clean();
			
			if (strlen($html))
				@$qa_content['custom'].='<BR>'.$html.'<BR>';
		}
	}

	$qa_content['focusid']=(isset($inemailhandle) && !isset($errors['emailhandle'])) ? 'password' : 'emailhandle';
	

	return $qa_content;


/*
	Omit PHP closing tag to help avoid accidental output
*/