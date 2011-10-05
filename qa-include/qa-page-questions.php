<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-page-questions.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Controller for page listing recent questions


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
	require_once QA_INCLUDE_DIR.'qa-app-q-list.php';
	
	$categoryslugs=$pass_subrequests;
	$countslugs=count($categoryslugs);
	$sort=$countslugs ? null : qa_get('sort');


//	Get list of questions, plus category information

	switch ($sort) {
		case 'hot':
			$questionselect=qa_db_qs_selectspec($qa_login_userid, 'hotness', $qa_start);
			break;
		
		case 'votes':
			$questionselect=qa_db_qs_selectspec($qa_login_userid, 'netvotes', $qa_start);
			break;
			
		case 'answers':
			$questionselect=qa_db_qs_selectspec($qa_login_userid, 'acount', $qa_start);
			break;
			
		case 'views':
			$questionselect=qa_db_qs_selectspec($qa_login_userid, 'views', $qa_start);
			break;
		
		default:
			$questionselect=qa_db_qs_selectspec($qa_login_userid, 'created', $qa_start, $categoryslugs);
			break;
	}
	
	@list($questions, $categories, $categoryid)=qa_db_select_with_pending(
		$questionselect,
		qa_db_category_nav_selectspec($categoryslugs, false),
		$countslugs ? qa_db_slugs_to_category_id_selectspec($categoryslugs) : null
	);
	
	if ($countslugs) {
		if (!isset($categoryid))
			return include QA_INCLUDE_DIR.'qa-page-not-found.php';
	
		$categorytitlehtml=qa_html($categories[$categoryid]['title']);
		$nonetitle=qa_lang_html_sub('main/no_questions_in_x', $categorytitlehtml);

	} else
		$nonetitle=qa_lang_html('main/no_questions_found');
	
	$categorypathprefix=null; // only show category list and feed when sorting by date
	$feedpathprefix=null;
	$pagelinkparams=array('sort' => $sort);
	
	switch ($sort) {
		case 'hot':
			$sometitle=qa_lang_html('main/hot_qs_title');
			break;
			
		case 'votes':
			$sometitle=qa_lang_html('main/voted_qs_title');
			break;
			
		case 'answers':
			$sometitle=qa_lang_html('main/answered_qs_title');
			break;
		
		case 'views':
			$sometitle=qa_lang_html('main/viewed_qs_title');
			break;
		
		default:
			$pagelinkparams=array();
			$sometitle=$countslugs ? qa_lang_html_sub('main/recent_qs_in_x', $categorytitlehtml) : qa_lang_html('main/recent_qs_title');
			$categorypathprefix='questions/';
			$feedpathprefix=qa_opt('feed_for_questions') ? 'questions' : null;
			break;
	}

	
//	Prepare and return content for theme

	$qa_content=qa_q_list_page_content(
		$questions, qa_opt('page_size_qs'), $qa_start, $countslugs ? $categories[$categoryid]['qcount'] : qa_opt('cache_qcount'), $sometitle, $nonetitle,
		$categories, $categoryid, true, $categorypathprefix, $feedpathprefix,
		$countslugs ? qa_html_suggest_qs_tags(qa_using_tags()) : qa_html_suggest_ask($categoryid), $pagelinkparams
	);
	
	if (!$countslugs)
		$qa_content['navigation']['sub']=qa_qs_sub_navigation($sort);

	
	return $qa_content;


/*
	Omit PHP closing tag to help avoid accidental output
*/