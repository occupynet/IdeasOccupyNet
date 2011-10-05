<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-page-register.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Controller for register page


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

	require_once QA_INCLUDE_DIR.'qa-app-captcha.php';
	require_once QA_INCLUDE_DIR.'qa-db-users.php';


//	Check we're not using single-sign on integration, that we're not logged in, and we're not blocked

	if (QA_FINAL_EXTERNAL_USERS)
		qa_fatal_error('User registration is handled by external code');
		
	if (isset($qa_login_userid))
		qa_redirect('');
	
	if (qa_opt('suspend_register_users')) {
		$qa_content=qa_content_prepare();
		$qa_content['error']=qa_lang_html('users/register_suspended');
		return $qa_content;
	}
	
	if (qa_user_permit_error()) {
		$qa_content=qa_content_prepare();
		$qa_content['error']=qa_lang_html('users/no_permission');
		return $qa_content;
	}

	
//	Process submitted form

	if (qa_clicked('doregister')) {
		require_once QA_INCLUDE_DIR.'qa-app-users-edit.php';
		
		$inemail=qa_post_text('email');
		$inpassword=qa_post_text('password');
		$inhandle=qa_post_text('handle');
		
		$errors=array_merge(
			qa_handle_email_validate($inhandle, $inemail),
			qa_password_validate($inpassword)
		);
		
		if (qa_opt('captcha_on_register'))
			qa_captcha_validate($_POST, $errors);
	
		if (empty($errors)) { // register and redirect
			$userid=qa_create_new_user($inemail, $inpassword, $inhandle);
			qa_set_logged_in_user($userid, $inhandle);

			$topath=qa_get('to');
			
			if (isset($topath))
				qa_redirect_raw($qa_root_url_relative.$topath); // path already provided as URL fragment
			else
				qa_redirect('');
		}
	}


//	Prepare content for theme

	$qa_content=qa_content_prepare();

	$qa_content['title']=qa_lang_html('users/register_title');

	$qa_content['form']=array(
		'tags' => 'METHOD="POST" ACTION="'.qa_self_html().'"',
		
		'style' => 'tall',
		
		'fields' => array(
			'handle' => array(
				'label' => qa_lang_html('users/handle_label'),
				'tags' => 'NAME="handle" ID="handle"',
				'value' => qa_html(@$inhandle),
				'error' => qa_html(@$errors['handle']),
			),
			
			'password' => array(
				'type' => 'password',
				'label' => qa_lang_html('users/password_label'),
				'tags' => 'NAME="password" ID="password"',
				'value' => qa_html(@$inpassword),
				'error' => qa_html(@$errors['password']),
			),

			'email' => array(
				'label' => qa_lang_html('users/email_label'),
				'tags' => 'NAME="email" ID="email"',
				'value' => qa_html(@$inemail),
				'note' => qa_opt('email_privacy'),
				'error' => qa_html(@$errors['email']),
			),
		),
		
		'buttons' => array(
			'register' => array(
				'label' => qa_lang_html('users/register_button'),
			),
		),
		
		'hidden' => array(
			'doregister' => '1',
		),
	);
	
	if (qa_opt('captcha_on_register'))
		qa_set_up_captcha_field($qa_content, $qa_content['form']['fields'], @$errors);
	
	$modulenames=qa_list_modules('login');
	
	foreach ($modulenames as $tryname) {
		$module=qa_load_module('login', $tryname);
		
		if (method_exists($module, 'login_html')) {
			ob_start();
			$module->login_html(qa_opt('site_url').qa_get('to'), 'register');
			$html=ob_get_clean();
			
			if (strlen($html))
				@$qa_content['custom'].='<BR>'.$html.'<BR>';
		}
	}

	$qa_content['focusid']=isset($errors['handle']) ? 'handle'
		: (isset($errors['password']) ? 'password'
			: (isset($errors['email']) ? 'email' : 'handle'));

			
	return $qa_content;
	

/*
	Omit PHP closing tag to help avoid accidental output
*/