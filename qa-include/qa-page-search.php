<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-page-search.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Controller for search page


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


//	Perform the search if appropriate

	if (strlen(qa_get('q'))) {
		require_once QA_INCLUDE_DIR.'qa-db-selects.php';
		require_once QA_INCLUDE_DIR.'qa-util-string.php';

		$inquery=trim(qa_get('q'));
		$words=qa_string_to_words($inquery);
		$retrieve=2*QA_DB_RETRIEVE_QS_AS+1; // get enough results to be able to give some idea of how many pages of search results there are
		
		$questions=qa_db_select_with_pending(
			qa_db_search_posts_selectspec($qa_login_userid, $words, $words, $words, $words, $inquery, $qa_start, false, $retrieve)
		);
		
		$pagesize=qa_opt('page_size_search');
		$gotcount=count($questions);
		$questions=array_slice($questions, 0, $pagesize);
		$usershtml=qa_userids_handles_html($questions);
		
		qa_report_event('search', $qa_login_userid, qa_get_logged_in_handle(), $qa_cookieid, array(
			'query' => $inquery,
			'start' => $qa_start,
		));
	}


//	Prepare content for theme

	$qa_content=qa_content_prepare(true);

	if (strlen(qa_get('q')))
		$qa_content['search']['value']=qa_html($inquery);
	
	if (isset($questions)) {
		if (count($questions))
			$qa_content['title']=qa_lang_html_sub('main/results_for_x', qa_html($inquery));
		else
			$qa_content['title']=qa_lang_html_sub('main/no_results_for_x', qa_html($inquery));
			
		$qa_content['q_list']['form']=array(
			'tags' => 'METHOD="POST" ACTION="'.qa_self_html().'"',
		);
		
		$qa_content['q_list']['qs']=array();
		foreach ($questions as $question) {
			$fields=qa_post_html_fields($question, $qa_login_userid, $qa_cookieid, $usershtml, null, qa_post_html_defaults('Q'));
				
			$fields['url']=qa_path_html(qa_q_request($question['postid'], $question['title']),
				null, null, null, qa_search_max_match_anchor($question));

			$qa_content['q_list']['qs'][]=$fields;
		}

		$qa_content['page_links']=qa_html_page_links($qa_request, $qa_start, $pagesize, $qa_start+$gotcount,
			qa_opt('pages_prev_next'), array('q' => $inquery), $gotcount>=$retrieve);
		
		if (qa_opt('feed_for_search'))
			$qa_content['feed']=array(
				'url' => qa_path_html(qa_feed_request('search/'.$inquery)),
				'label' => qa_lang_html_sub('main/results_for_x', qa_html($inquery)),
			);

	} else
		$qa_content['title']=qa_lang_html('main/search_title');
	
	if (empty($qa_content['page_links']))
		$qa_content['suggest_next']=qa_html_suggest_qs_tags(qa_using_tags());

		
	return $qa_content;


/*
	Omit PHP closing tag to help avoid accidental output
*/