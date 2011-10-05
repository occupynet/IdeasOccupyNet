<?php
	
/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-page-message.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Controller for private messaging page


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

	require_once QA_INCLUDE_DIR.'qa-db-selects.php';
	require_once QA_INCLUDE_DIR.'qa-app-users.php';
	require_once QA_INCLUDE_DIR.'qa-app-format.php';
	

	$handle=@$pass_subrequests[0];
	if (!strlen($handle))
		qa_redirect('users');


//	Check we're not using Q2A's single-sign on integration and that we're logged in

	if (QA_FINAL_EXTERNAL_USERS)
		qa_fatal_error('User accounts are handled by external code');
	
	if (!isset($qa_login_userid)) {
		$qa_content=qa_content_prepare();
		$qa_content['error']=qa_insert_login_links(qa_lang_html('misc/message_must_login'), $qa_request);
		return $qa_content;
	}


//	Find the user profile and questions and answers for this handle
	
	$useraccount=qa_db_select_with_pending(qa_db_user_account_selectspec($handle, false));


//	Check the user exists and work out what can and can't be set (if not using single sign-on)
	
	if ( (!is_array($useraccount)) || ($useraccount['flags'] & QA_USER_FLAGS_NO_MESSAGES) )
		return include QA_INCLUDE_DIR.'qa-page-not-found.php';
	

//	Process sending a message to user

	$messagesent=false;
	
	if (qa_post_text('domessage')) {
	
	//	Check that we haven't been blocked on volume
	
		$errorhtml=null;
		
		switch (qa_user_permit_error(null, 'M')) {
			case 'limit':
				$errorhtml=qa_lang_html('misc/message_limit');
				break;
				
			case false:
				break;
				
			default:
				$errorhtml=qa_lang_html('users/no_permission');
				break;
		}
	
		if (isset($errorhtml)) {
			$qa_content=qa_content_prepare();
			$qa_content['error']=$errorhtml;
			return $qa_content;
		}
	

	//	Proceed...
	
		$inmessage=qa_post_text('message');
		
		if (empty($inmessage))
			$errors['message']=qa_lang('misc/message_empty');
		
		if (empty($errors)) {
			require_once QA_INCLUDE_DIR.'qa-app-emails.php';

			$fromhandle=qa_get_logged_in_handle();
			$canreply=!(qa_get_logged_in_flags() & QA_USER_FLAGS_NO_MESSAGES);
			
			$more=strtr(qa_lang($canreply ? 'emails/private_message_reply' : 'emails/private_message_info'), array(
				'^f_handle' => $fromhandle,
				'^url' => qa_path($canreply ? ('message/'.$fromhandle) : ('user/'.$fromhandle), null, qa_opt('site_url')),
			));

			$subs=array(
				'^message' => $inmessage,
				'^f_handle' => $fromhandle,
				'^f_url' => qa_path('user/'.$fromhandle, null, qa_opt('site_url')),
				'^more' => $more,
				'^a_url' => qa_path_html('account', null, qa_opt('site_url')),
			);
			
			if (qa_send_notification($useraccount['userid'], $useraccount['email'], $useraccount['handle'],
					qa_lang('emails/private_message_subject'), qa_lang('emails/private_message_body'), $subs))
				$messagesent=true;
			else
				$page_error=qa_lang_html('main/general_error');

			qa_limits_increment($qa_login_userid, 'M');
			
			qa_report_event('u_message', $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, array(
				'userid' => $useraccount['userid'],
				'handle' => $useraccount['handle'],
				'message' => $inmessage,
			));
		}
	}


//	Prepare content for theme
	
	$qa_content=qa_content_prepare();
	
	$qa_content['title']=qa_lang_html('misc/private_message_title');

	$qa_content['error']=@$page_error;

	$qa_content['form']=array(
		'tags' => 'METHOD="POST" ACTION="'.qa_self_html().'"',
		
		'style' => 'tall',
		
		'fields' => array(
			'message' => array(
				'type' => $messagesent ? 'static' : '',
				'label' => qa_lang_html_sub('misc/message_for_x', qa_get_one_user_html($handle, false)),
				'tags' => 'NAME="message" ID="message"',
				'value' => qa_html(@$inmessage, $messagesent),
				'rows' => 16,
				'note' => qa_lang_html_sub('misc/message_explanation', qa_html(qa_opt('site_title'))),
				'error' => qa_html(@$errors['message']),
			),
		),
		
		'buttons' => array(
			'send' => array(
				'label' => qa_lang_html('main/send_button'),
			),
		),
		
		'hidden' => array(
			'domessage' => '1',
		),
	);
	
	$qa_content['focusid']='message';

	if ($messagesent) {
		$qa_content['form']['ok']=qa_lang_html('misc/message_sent');
		unset($qa_content['form']['fields']['message']['note']);
		unset($qa_content['form']['buttons']);
	}


	return $qa_content;


/*
	Omit PHP closing tag to help avoid accidental output
*/