<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-feed.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Handles all requests to RSS feeds, first checking if they should be available


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


	@ini_set('display_errors', 0); // we don't want to show PHP errors to RSS readers


	require_once QA_INCLUDE_DIR.'qa-app-options.php';


//	Functions used within this file

	function qa_feed_db_fail_handler($type, $errno=null, $error=null, $query=null)
/*
	Database failure handler function for RSS feeds - outputs HTTP and text errors
*/

	{
		header('HTTP/1.1 500 Internal Server Error');
		echo qa_lang_html('main/general_error');
		exit;
	}

	
	function qa_feed_not_found()
/*
	Common function called when a non-existent feed is requested - outputs HTTP and text errors
*/
	{
		header('HTTP/1.0 404 Not Found');
		echo qa_lang_html('misc/feed_not_found');
		exit;
	}

	
	function qa_feed_load_ifcategory($allkey, $catkey, $questionselectspec1=null, $questionselectspec2=null, $questionselectspec3=null, $questionselectspec4=null)
/*
	Common function to load appropriate set of questions for requested feed, check category exists, and set up page title
*/
	{
		global $categoryslugs, $countslugs, $categories, $categoryid, $title, $questions;
		
		@list($questions1, $questions2, $questions3, $questions4, $categories, $categoryid)=qa_db_select_with_pending(
			$questionselectspec1,
			$questionselectspec2,
			$questionselectspec3,
			$questionselectspec4,
			$countslugs ? qa_db_category_nav_selectspec($categoryslugs, false) : null,
			$countslugs ? qa_db_slugs_to_category_id_selectspec($categoryslugs) : null
		);

		if ($countslugs && !isset($categoryid))
			qa_feed_not_found();

		$questions=array_merge(
			is_array($questions1) ? $questions1 : array(),
			is_array($questions2) ? $questions2 : array(),
			is_array($questions3) ? $questions3 : array(),
			is_array($questions4) ? $questions4 : array()
		);

		if (isset($allkey) && isset($catkey))
			$title=isset($categoryid) ? qa_lang_sub($catkey, $categories[$categoryid]['title']) : qa_lang($allkey);
	}


//	Connect to database and get the type of feed and category requested (in some cases these are overridden later)

	qa_base_db_connect('qa_feed_db_fail_handler');

	end($qa_request_lc_parts);
	$lastkey=key($qa_request_lc_parts);
	
	if (isset($lastkey)) {
		$suffix=substr($qa_request_lc_parts[$lastkey], -4);
		if (($suffix=='.rss') || ($suffix=='.xml'))
			$qa_request_lc_parts[$lastkey]=substr($qa_request_lc_parts[$lastkey], 0, -4);
	}
	
	$feedtype=@$qa_request_lc_parts[1];
	$feedparams=array_slice($qa_request_lc_parts, 2);
	

//	Choose which option needs to be checked to determine if this feed can be requested, and stop if no matches

	$feedoption=null;
	$categoryslugs=$feedparams;
	
	switch ($feedtype) {
		case 'questions':
			$feedoption='feed_for_questions';
			break;
			
		case 'unanswered':
			$feedoption='feed_for_unanswered';
			$categoryslugs=null;
			break;
			
		case 'answers':
		case 'comments':
		case 'activity':
			$feedoption='feed_for_activity';
			break;
			
		case 'qa':
			$feedoption='feed_for_qa';
			break;
		
		case 'tag':
			if (strlen(@$feedparams[0])) {
				$feedoption='feed_for_tag_qs';
				$categoryslugs=null;
			}
			break;
			
		case 'search':
			if (strlen(@$feedparams[0])) {
				$feedoption='feed_for_search';
				$categoryslugs=null;
			}
			break;
	}
	
	$countslugs=@count($categoryslugs);

	if (!isset($feedoption))
		qa_feed_not_found();
	

//	Check that all the appropriate options are in place to allow this feed to be retrieved

	if (!(
		(qa_opt($feedoption)) &&
		($countslugs ? (qa_using_categories() && qa_opt('feed_per_category')) : true)
	))
		qa_feed_not_found();


//	Retrieve the appropriate questions and other information for this feed

	require_once QA_INCLUDE_DIR.'qa-db-selects.php';

	$sitetitle=qa_opt('site_title');
	$siteurl=qa_opt('site_url');
	$full=qa_opt('feed_full_text');
	$count=qa_opt('feed_number_items');
	$showurllinks=qa_opt('show_url_links');
	
	$linkrequest=$feedtype.($countslugs ? ('/'.implode('/', $categoryslugs)) : '');
	$linkparams=null;

	switch ($feedtype) {
		case 'questions':
			qa_feed_load_ifcategory('main/recent_qs_title', 'main/recent_qs_in_x',
				qa_db_qs_selectspec(null, 'created', 0, $categoryslugs, null, false, $full, $count)
			);
			break;
			
		case 'unanswered':
			qa_feed_load_ifcategory('main/unanswered_qs_title', 'main/unanswered_qs_in_x',
				qa_db_unanswered_qs_selectspec(null, 0, null, false, $full, $count)
			);
			break;
			
		case 'answers':
			qa_feed_load_ifcategory('main/recent_as_title', 'main/recent_as_in_x',
				qa_db_recent_a_qs_selectspec(null, 0, $categoryslugs, null, false, $full, $count)
			);
			break;

		case 'comments':
			qa_feed_load_ifcategory('main/recent_cs_title', 'main/recent_cs_in_x',
				qa_db_recent_c_qs_selectspec(null, 0, $categoryslugs, null, false, $full, $count)
			);
			break;
			
		case 'qa':
			qa_feed_load_ifcategory('main/recent_qs_as_title', 'main/recent_qs_as_in_x',
				qa_db_qs_selectspec(null, 'created', 0, $categoryslugs, null, false, $full, $count),
				qa_db_recent_a_qs_selectspec(null, 0, $categoryslugs, null, false, $full, $count)
			);
			break;
		
		case 'activity':
			qa_feed_load_ifcategory('main/recent_activity_title', 'main/recent_activity_in_x',
				qa_db_qs_selectspec(null, 'created', 0, $categoryslugs, null, false, $full, $count),
				qa_db_recent_a_qs_selectspec(null, 0, $categoryslugs, null, false, $full, $count),
				qa_db_recent_c_qs_selectspec(null, 0, $categoryslugs, null, false, $full, $count),
				qa_db_recent_edit_qs_selectspec(null, 0, $categoryslugs, null, true, $full, $count)
			);
			break;
			
		case 'tag':
			$tag=$feedparams[0];

			qa_feed_load_ifcategory(null, null,
				qa_db_tag_recent_qs_selectspec(null, $tag, 0, $full, $count)
			);
			
			$title=qa_lang_sub('main/questions_tagged_x', $tag);
			$linkrequest='tag/'.$tag;
			break;
			
		case 'search':
			require_once QA_INCLUDE_DIR.'qa-util-string.php';
			
			$query=$feedparams[0];

			$words=qa_string_to_words($query);

			qa_feed_load_ifcategory(null, null,
				qa_db_search_posts_selectspec(null, $words, $words, $words, $words, trim($query), 0, $full, $count)
			);
		
			$title=qa_lang_sub('main/results_for_x', $query);
			$linkrequest='search';
			$linkparams=array('q' => $query);
			break;
	}


//	Remove duplicate questions (perhaps referenced in an answer and a comment) and cut down to size
	
	require_once QA_INCLUDE_DIR.'qa-app-format.php';
	require_once QA_INCLUDE_DIR.'qa-util-string.php';

	if ($feedtype!='search') // leave search results sorted by relevance
		$questions=qa_any_sort_and_dedupe($questions);
	
	$questions=array_slice($questions, 0, $count);
	$blockwordspreg=qa_get_block_words_preg();


//	Disconnect as quickly as possible to free up resources

	qa_base_db_disconnect();


//	Prepare the XML output

	$lines=array();

	$lines[]='<?xml version="1.0" encoding="UTF-8"?>';
	$lines[]='<rss version="2.0">';
	$lines[]='<channel>';

	$lines[]='<title>'.qa_html($sitetitle.' - '.$title).'</title>';
	$lines[]='<link>'.qa_path_html($linkrequest, $linkparams, $siteurl).'</link>';
	$lines[]='<description>Powered by Question2Answer</description>';
	
	foreach ($questions as $question) {

	//	Determine whether this is a question, answer or comment, and act accordingly
	
		$options=array('blockwordspreg' => @$blockwordspreg, 'showurllinks' => $showurllinks);
		
		$anchor=null;

		if (isset($question['opostid'])) {
			if ($question['obasetype']!='Q')
				$anchor=qa_anchor($question['obasetype'], $question['opostid']);
				
			$time=$question['otime'];
				
			if ($full)
				$htmlcontent=qa_viewer_html($question['ocontent'], $question['oformat'], $options);
		
		} else {
			$time=$question['created'];
			
			if ($full)
				$htmlcontent=qa_viewer_html($question['content'], $question['format'], $options);
		}
			
		switch (@$question['obasetype']) {
			case 'A':
				$titleprefix=@$question['oedited'] ? qa_lang('misc/feed_a_edited_prefix') : qa_lang('misc/feed_a_prefix');
				break;
				
			case 'C':
				$titleprefix=@$question['oedited'] ? qa_lang('misc/feed_c_edited_prefix') : qa_lang('misc/feed_c_prefix');
				break;
				
			default:
				$titleprefix=@$question['oedited'] ? qa_lang('misc/feed_edited_prefix') : '';
				break;
		}
						
		if (isset($blockwordspreg))
			$question['title']=qa_block_words_replace($question['title'], $blockwordspreg);
		
		$urlhtml=qa_path_html(qa_q_request($question['postid'], $question['title']), null, $siteurl, null, $anchor);
		
	//	Build the inner XML structure for each item
		
		$lines[]='<item>';
		$lines[]='<title>'.qa_html($titleprefix.$question['title']).'</title>';
		$lines[]='<link>'.$urlhtml.'</link>';

		if ($full && isset($htmlcontent))
			$lines[]='<description>'.qa_html($htmlcontent).'</description>'; // qa_html() a second time to put HTML code inside XML wrapper
			
		if (isset($question['categoryname']))
			$lines[]='<category>'.qa_html($question['categoryname']).'</category>';
			
		$lines[]='<guid isPermaLink="true">'.$urlhtml.'</guid>';
		$lines[]='<pubDate>'.qa_html(gmdate('r', $time)).'</pubDate>';
		$lines[]='</item>';
	}
	
	$lines[]='</channel>';
	$lines[]='</rss>';

//	Output the XML - and we're done!
	
	header('Content-type: text/xml; charset=utf-8');
	echo implode("\n", $lines);
	

/*
	Omit PHP closing tag to help avoid accidental output
*/