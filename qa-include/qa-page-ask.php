<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-page-ask.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Controller for ask-a-question page


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


	require_once QA_INCLUDE_DIR.'qa-app-format.php';
	require_once QA_INCLUDE_DIR.'qa-db-selects.php';


//	Check whether this is a follow-on question and get some info we need from the database

	$infollow=qa_get('follow');
	$incategoryid=qa_get_category_field_value('category');
	if (!isset($incategoryid))
		$incategoryid=qa_get('cat');			
	
	@list($categories, $followanswer, $completetags)=qa_db_select_with_pending(
		qa_db_category_nav_selectspec($incategoryid, true),
		isset($infollow) ? qa_db_full_post_selectspec($qa_login_userid, $infollow) : null,
		qa_db_popular_tags_selectspec(0, QA_DB_RETRIEVE_COMPLETE_TAGS)
	);
	
	if (!isset($categories[$incategoryid]))
		$incategoryid=null;
	
	if (@$followanswer['basetype']!='A')
		$followanswer=null;
		

//	Check for permission error

	$permiterror=qa_user_permit_error('permit_post_q', qa_is_http_post() ? 'Q' : null); // only check rate limit later on

	if ($permiterror) {
		$qa_content=qa_content_prepare();
		
		switch ($permiterror) {
			case 'login':
				$qa_content['error']=qa_insert_login_links(qa_lang_html('question/ask_must_login'), $qa_request, isset($infollow) ? array('follow' => $infollow) : null);
				break;
				
			case 'confirm':
				$qa_content['error']=qa_insert_login_links(qa_lang_html('question/ask_must_confirm'), $qa_request, isset($infollow) ? array('follow' => $infollow) : null);
				break;
				
			case 'limit':
				$qa_content['error']=qa_lang_html('question/ask_limit');
				break;
				
			default:
				$qa_content['error']=qa_lang_html('users/no_permission');
				break;
		}
		
		return $qa_content;
	}
	

//	Process input
	
	$usecaptcha=qa_user_use_captcha('captcha_on_anon_post');
	$intitle=qa_post_text('title'); // allow title and tags to be posted by an external form
	$intags=qa_get_tags_field_value('tags');

	if (qa_clicked('doask')) {
		require_once QA_INCLUDE_DIR.'qa-app-post-create.php';
		require_once QA_INCLUDE_DIR.'qa-util-string.php';
		
		$innotify=qa_post_text('notify') ? true : false;
		$inemail=qa_post_text('email');
			
		qa_get_post_content('editor', 'content', $ineditor, $incontent, $informat, $intext);
			
		$tagstring=qa_tags_to_tagstring($intags);

		$errors=qa_question_validate($intitle, $incontent, $informat, $intext, $tagstring, $innotify, $inemail);			
		
		if (qa_using_categories() && count($categories) && (!qa_opt('allow_no_category')) && !isset($incategoryid))
			$errors['category']=qa_lang_html('question/category_required');
		
		if ($usecaptcha) {
			require_once 'qa-app-captcha.php';
			qa_captcha_validate($_POST, $errors);
		}
		
		if (empty($errors)) {
			if (!isset($qa_login_userid))
				$qa_cookieid=qa_cookie_get_create(); // create a new cookie if necessary

			$questionid=qa_question_create($followanswer, $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid,
				$intitle, $incontent, $informat, $intext, $tagstring, $innotify, $inemail, $incategoryid);
			
			qa_report_write_action($qa_login_userid, $qa_cookieid, 'q_post', $questionid, null, null);
			qa_redirect(qa_q_request($questionid, $intitle)); // our work is done here
		}
	}


//	Prepare content for theme

	$qa_content=qa_content_prepare(false, array_keys(qa_category_path($categories, @$incategoryid)));
	
	$qa_content['title']=qa_lang_html(isset($followanswer) ? 'question/ask_follow_title' : 'question/ask_title');

	$editorname=isset($ineditor) ? $ineditor : qa_opt('editor_for_qs');
	$editor=qa_load_editor(@$incontent, @$informat, $editorname);

	$qa_content['form']=array(
		'tags' => 'NAME="ask" METHOD="POST" ACTION="'.qa_self_html().'"',
		
		'style' => 'tall',
		
		'fields' => array(
			'follows' => array(),
			
			'title' => array(
				'label' => qa_lang_html('question/q_title_label'),
				'tags' => 'NAME="title" ID="title" AUTOCOMPLETE="off"',
				'value' => qa_html(@$intitle),
				'error' => qa_html(@$errors['title']),
			),
			
			'similar' => array(
				'type' => 'custom',
				'html' => '<SPAN ID="similar"></SPAN>',
			),
			
			'category' => array(
				'label' => qa_lang_html('question/q_category_label'),
				'error' => qa_html(@$errors['category']),
			),
			
			'content' => array_merge(
				$editor->get_field($qa_content, @$incontent, @$informat, 'content', 12, false),
				array(
					'label' => qa_lang_html('question/q_content_label'),
					'error' => qa_html(@$errors['content']),
				)
			),
			
			'tags' => array(
				'error' => qa_html(@$errors['tags']),
			),
			
		),
		
		'buttons' => array(
			'ask' => array(
				'label' => qa_lang_html('question/ask_button'),
			),
		),
		
		'hidden' => array(
			'editor' => qa_html($editorname),
			'doask' => '1',
		),
	);
			
	if (qa_opt('do_ask_check_qs') || qa_opt('do_example_tags')) {
		$qa_content['script_rel'][]='qa-content/qa-ask.js?'.QA_VERSION;
		$qa_content['form']['fields']['title']['tags'].=' onChange="qa_title_change(this.value);"';
		
		if (strlen(@$intitle))
			$qa_content['script_onloads'][]='qa_title_change('.qa_js($intitle).');';
	}
	
	if (qa_using_categories() && count($categories)) {
		qa_set_up_category_field($qa_content, $qa_content['form']['fields']['category'], 'category',
			$categories, $incategoryid, true, qa_opt('allow_no_sub_category'));
		
		if (!qa_opt('allow_no_category')) // don't auto-select a category even though one is required
			$qa_content['form']['fields']['category']['options']['']='';

	} else
		unset($qa_content['form']['fields']['category']);
	
	if (qa_using_tags())
		qa_set_up_tag_field($qa_content, $qa_content['form']['fields']['tags'], 'tags',
			isset($intags) ? $intags : array(), array(), qa_opt('do_complete_tags') ? array_keys($completetags) : array(), qa_opt('page_size_ask_tags'));
	else
		unset($qa_content['form']['fields']['tags']);
	
	qa_set_up_notify_fields($qa_content, $qa_content['form']['fields'], 'Q', qa_get_logged_in_email(),
		isset($innotify) ? $innotify : qa_opt('notify_users_default'), @$inemail, @$errors['email']);
	
	if ($usecaptcha) {
		require_once 'qa-app-captcha.php';
		qa_set_up_captcha_field($qa_content, $qa_content['form']['fields'], @$errors,
			qa_insert_login_links(qa_lang_html(isset($qa_login_userid) ? 'misc/captcha_confirm_fix' : 'misc/captcha_login_fix')));
	}
			
	if (isset($followanswer)) {
		$viewer=qa_load_viewer($followanswer['content'], $followanswer['format']);
		
		$qa_content['form']['fields']['follows']=array(
			'type' => 'static',
			'label' => qa_lang_html('question/ask_follow_from_a'),
			'value' => $viewer->get_html($followanswer['content'], $followanswer['format'], array('blockwordspreg' => qa_get_block_words_preg())),
		);

	} else
		unset($qa_content['form']['fields']['follows']);
		
	$qa_content['focusid']='title';

	
	return $qa_content;


/*
	Omit PHP closing tag to help avoid accidental output
*/