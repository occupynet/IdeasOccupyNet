<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-app-captcha.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Wrapper functions and utilities for reCAPTCHA


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


	function qa_captcha_possible()
/*
	Return true if it will be possible to present a captcha to the user, false otherwise
*/
	{
		$options=qa_get_options(array('recaptcha_public_key', 'recaptcha_private_key'));
		
		return function_exists('fsockopen') && strlen(trim($options['recaptcha_public_key'])) && strlen(trim($options['recaptcha_private_key']));
	}
	

	function qa_captcha_error()
/*
	Return string of error to display in admin interface if captchas are not possible, null otherwise
*/
	{
		if (qa_captcha_possible())
			return null;
		
		elseif (!function_exists('fsockopen'))
			return qa_lang_html('admin/recaptcha_fsockopen');
		
		else {
			require_once QA_INCLUDE_DIR.'qa-recaptchalib.php';
	
			$url=recaptcha_get_signup_url(@$_SERVER['HTTP_HOST'], qa_opt('site_title'));
			
			return strtr(
				qa_lang_html('admin/recaptcha_get_keys'),
				
				array(
					'^1' => '<A HREF="'.qa_html($url).'">',
					'^2' => '</A>',
				)
			);
		}
	}

	
	function qa_captcha_html($error)
/*
	Return the html to display for a captcha, pass $error as returned by earlier qa_captcha_validate()
*/
	{
		require_once QA_INCLUDE_DIR.'qa-recaptchalib.php';

		return recaptcha_get_html(qa_opt('recaptcha_public_key'), $error, qa_is_https_probably());
	}

	
	function qa_captcha_validate($form, &$errors)
/*
	Check if captcha correct based on fields submitted in $form and set $errors['captcha'] accordingly
*/
	{
		if (qa_captcha_possible()) {
			require_once QA_INCLUDE_DIR.'qa-recaptchalib.php';
			
			if ( (!empty($form['recaptcha_challenge_field'])) && (!empty($form['recaptcha_response_field'])) ) {
				$answer=recaptcha_check_answer(
					qa_opt('recaptcha_private_key'),
					@$_SERVER['REMOTE_ADDR'],
					@$form['recaptcha_challenge_field'],
					@$form['recaptcha_response_field']
				);
				
				if (!$answer->is_valid)
					$errors['captcha']=@$answer->error;

			} else
				$errors['captcha']=true; // empty error but still set it
		}
	}

	
	function qa_set_up_captcha_field(&$qa_content, &$fields, $errors, $note=null)
/*
	Prepare $qa_content for showing a captcha, adding the element to $fields,
	given previous $errors, and a $note to display
*/
	{
		if (qa_captcha_possible()) {
			$fields['captcha']=array(
				'type' => 'custom',
				'label' => qa_lang_html('misc/captcha_label'),
				'html' => qa_captcha_html(@$errors['captcha']),
				'error' => isset($errors['captcha']) ? qa_lang_html('misc/captcha_error') : null,
				'note' => $note,
			);
			
			$language=qa_opt('site_language');
			if (strpos('|en|nl|fr|de|pt|ru|es|tr|', '|'.$language.'|')===false) // supported as of 3/2010
				$language='en';
			
			$qa_content['script_lines'][]=array(
				"var RecaptchaOptions = {",
				"\ttheme:'white',",
				"\tlang:".qa_js($language),
				"}",
			);
		}
	}
	

/*
	Omit PHP closing tag to help avoid accidental output
*/