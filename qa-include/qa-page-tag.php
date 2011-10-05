<?php
	
/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-page-tag.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Controller for page for a specific tag


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
	require_once QA_INCLUDE_DIR.'qa-app-format.php';
	
	$tag=@$pass_subrequests[0]; // picked up from qa-page.php
	if (!strlen($tag))
		qa_redirect('tags');


//	Find the questions with this tag

	list($questions, $qcount)=qa_db_select_with_pending(
		qa_db_tag_recent_qs_selectspec($qa_login_userid, $tag, $qa_start),
		qa_db_tag_count_qs_selectspec($tag)
	);
	
	$pagesize=qa_opt('page_size_tag_qs');
	$questions=array_slice($questions, 0, $pagesize);
	$usershtml=qa_userids_handles_html($questions);


//	Prepare content for theme
	
	$qa_content=qa_content_prepare(true);

	$qa_content['title']=qa_lang_html_sub('main/questions_tagged_x', qa_html($tag));
	
	if (!count($questions))
		$qa_content['q_list']['title']=qa_lang_html('main/no_questions_found');

	$qa_content['q_list']['form']=array(
		'tags' => 'METHOD="POST" ACTION="'.qa_self_html().'"',
	);

	$qa_content['q_list']['qs']=array();
	foreach ($questions as $postid => $question)
		$qa_content['q_list']['qs'][]=qa_post_html_fields($question, $qa_login_userid, $qa_cookieid, $usershtml,
			null, qa_post_html_defaults('Q'));
		
	$qa_content['page_links']=qa_html_page_links($qa_request, $qa_start, $pagesize, $qcount, qa_opt('pages_prev_next'));

	if (empty($qa_content['page_links']))
		$qa_content['suggest_next']=qa_html_suggest_qs_tags(true);

	if (qa_opt('feed_for_tag_qs'))
		$qa_content['feed']=array(
			'url' => qa_path_html(qa_feed_request('tag/'.$tag)),
			'label' => qa_lang_html_sub('main/questions_tagged_x', qa_html($tag)),
		);

		
	return $qa_content;


/*
	Omit PHP closing tag to help avoid accidental output
*/