<?php
	
/*
	Question2Answer (c) Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-page-admin-moderate.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Controller for admin page showing questions, answers and comments waiting for approval


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

	require_once QA_INCLUDE_DIR.'qa-app-admin.php';
	require_once QA_INCLUDE_DIR.'qa-db-selects.php';
	require_once QA_INCLUDE_DIR.'qa-app-format.php';

	
//	Find queued questions, answers, comments

	$userid=qa_get_logged_in_userid();
	
	list($queuedquestions, $queuedanswers, $queuedcomments)=qa_db_select_with_pending(
		qa_db_qs_selectspec($userid, 'created', 0, null, null, 'Q_QUEUED', true),
		qa_db_recent_a_qs_selectspec($userid, 0, null, null, 'A_QUEUED', true),
		qa_db_recent_c_qs_selectspec($userid, 0, null, null, 'C_QUEUED', true)
	);
	
	
//	Check admin privileges (do late to allow one DB query)

	if (qa_user_permit_error('permit_moderate')) {
		$qa_content=qa_content_prepare();
		$qa_content['error']=qa_lang_html('users/no_permission');
		return $qa_content;
	}
		
		
//	Check to see if any were approved/rejected here

	qa_admin_check_clicks();
	

//	Combine sets of questions

	$questions=qa_any_sort_by_date(array_merge($queuedquestions, $queuedanswers, $queuedcomments));
	

//	Get information for users

	$usershtml=qa_userids_handles_html(qa_any_get_userids_handles($questions));


//	Prepare content for theme
	
	$qa_content=qa_content_prepare();

	$qa_content['title']=qa_lang_html('admin/recent_approve_title');
	
	$qa_content['error']=qa_admin_page_error();
	
	$qa_content['q_list']=array(
		'form' => array(
			'tags' => 'METHOD="POST" ACTION="'.qa_self_html().'"',
		),
		
		'qs' => array(),
	);
	
	if (count($questions)) {
		foreach ($questions as $question) {
			$postid=qa_html(isset($question['opostid']) ? $question['opostid'] : $question['postid']);
			$elementid='p'.$postid;
			
			$htmloptions=qa_post_html_defaults('Q');
			$htmloptions['voteview']=false;
			$htmloptions['tagsview']=!isset($question['opostid']);
			$htmloptions['answersview']=false;
			$htmloptions['contentview']=true;
			$htmloptions['elementid']=$elementid;

			$htmlfields=qa_any_to_q_html_fields($question, $userid, qa_cookie_get(), $usershtml, null, $htmloptions);
			
			if (isset($htmlfields['what_url'])) // link directly to relevant content
				$htmlfields['url']=$htmlfields['what_url'];
			
			$htmlfields['form']=array(
				'style' => 'light',

				'buttons' => array(
					'approve' => array(
						'tags' => 'NAME="admin_'.$postid.'_approve" onclick="return qa_admin_click(this);"',
						'label' => qa_lang_html('question/approve_button'),
					),
	
					'reject' => array(
						'tags' => 'NAME="admin_'.$postid.'_reject" onclick="return qa_admin_click(this);"',
						'label' => qa_lang_html('question/reject_button'),
					),
				),
			);

			$qa_content['q_list']['qs'][]=$htmlfields;
		}

	} else
		$qa_content['title']=qa_lang_html('admin/no_approve_found');
		

	$qa_content['navigation']['sub']=qa_admin_sub_navigation();
	$qa_content['script_rel'][]='qa-content/qa-admin.js?'.QA_VERSION;
	
	
	return $qa_content;
	

/*
	Omit PHP closing tag to help avoid accidental output
*/