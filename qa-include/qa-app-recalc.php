<?php
	
/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-app-recalc.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Managing database recalculations (clean-up operations) and status messages


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
	
/*
	A full list of redundant (non-normal) information in the database that can be recalculated:
	
	Recalculated in doreindexposts:
	===============================
	^titlewords (all): index of words in titles of posts
	^contentwords (all): index of words in content of posts
	^tagwords (all): index of words in tags of posts (a tag can contain multiple words)
	^posttags (all): index tags of posts
	^words (all): list of words used for indexes
	^options (title=cache_qcount|cache_acount|cache_ccount|cache_tagcount|cache_unaqcount): total Qs, As, Cs, tags, unanswered Qs
	
	Recalculated in dorecountposts:
	==============================
	^posts (upvotes, downvotes, netvotes, hotness, acount, flagcount): number of votes, hotness, answers, flags received by posts
	
	Recalculated in dorecalcpoints:
	===============================
	^userpoints (all): points calculation for all users
	^options (title=cache_userpointscount):
	
	Recalculated in dorecalccategories:
	===================================
	^posts (categoryid): assign to answers and comments based on their antecedent question
	^posts (catidpath1, catidpath2, catidpath3): hierarchical path to category ids (requires QA_CATEGORY_DEPTH=4)
	^categories (qcount): number of (visible) questions in each category
	^categories (backpath): full (backwards) path of slugs to that category
*/

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../');
		exit;
	}

	require_once QA_INCLUDE_DIR.'qa-db-recalc.php';
	require_once QA_INCLUDE_DIR.'qa-db-post-create.php';
	require_once QA_INCLUDE_DIR.'qa-db-points.php';
	require_once QA_INCLUDE_DIR.'qa-db-selects.php';
	require_once QA_INCLUDE_DIR.'qa-db-admin.php';
	require_once QA_INCLUDE_DIR.'qa-app-options.php';
	require_once QA_INCLUDE_DIR.'qa-app-post-create.php';
	require_once QA_INCLUDE_DIR.'qa-app-post-update.php';


	function qa_recalc_perform_step(&$state)
/*
	Advance the recalculation operation represented by $state by a single step.
	$state can also be the name of a recalculation operation on its own.
*/
	{
		$continue=false;
		
		@list($operation, $length, $next, $done)=explode(',', $state);
		
		switch ($operation) {
			case 'doreindexposts':
				qa_recalc_transition($state, 'doreindexposts_postcount');
				break;
				
			case 'doreindexposts_postcount':
				qa_db_qcount_update();
				qa_db_acount_update();
				qa_db_ccount_update();

				qa_recalc_transition($state, 'doreindexposts_reindex');
				break;
				
			case 'doreindexposts_reindex':
				$posts=qa_db_posts_get_for_reindexing($next, 10);
				
				if (count($posts)) {
					require_once QA_INCLUDE_DIR.'qa-app-format.php';

					$lastpostid=max(array_keys($posts));
					
					qa_db_prepare_for_reindexing($next, $lastpostid);
		
					foreach ($posts as $postid => $post)
						qa_post_index($postid, $post['type'], $post['questionid'], $post['title'],
							qa_viewer_text($post['content'], $post['format']), $post['tags'], true);
					
					$next=1+$lastpostid;
					$done+=count($posts);
					$continue=true;

				} else {
					qa_db_truncate_indexes($next);
					qa_recalc_transition($state, 'doreindexposts_wordcount');
				}
				break;
				
			case 'doreindexposts_wordcount':
				$wordids=qa_db_words_prepare_for_recounting($next, 1000);
				
				if (count($wordids)) {
					$lastwordid=max($wordids);
					
					qa_db_words_recount($next, $lastwordid);
					
					$next=1+$lastwordid;
					$done+=count($wordids);
					$continue=true;
			
				} else {
					qa_db_tagcount_update(); // this is quick so just do it here
					qa_recalc_transition($state, 'doreindexposts_complete');
				}
				break;
				
			case 'dorecountposts':
				qa_recalc_transition($state, 'dorecountposts_postcount');
				break;
				
			case 'dorecountposts_postcount':
				qa_db_qcount_update();
				qa_db_acount_update();
				qa_db_ccount_update();
				qa_db_unaqcount_update();

				qa_recalc_transition($state, 'dorecountposts_recount');
				break;
				
			case 'dorecountposts_recount':
				$postids=qa_db_posts_get_for_recounting($next, 1000);
				
				if (count($postids)) {
					$lastpostid=max($postids);
					
					qa_db_posts_recount($next, $lastpostid);
					
					$next=1+$lastpostid;
					$done+=count($postids);
					$continue=true;

				} else {
					qa_recalc_transition($state, 'dorecountposts_complete');
				}
				break;
			
			case 'dorecalcpoints':
				qa_recalc_transition($state, 'dorecalcpoints_usercount');
				break;
				
			case 'dorecalcpoints_usercount':
				qa_db_userpointscount_update(); // for progress update - not necessarily accurate
				qa_recalc_transition($state, 'dorecalcpoints_recalc');
				break;
				
			case 'dorecalcpoints_recalc':
				$userids=qa_db_users_get_for_recalc_points($next, 10);
				
				if (count($userids)) {
					$lastuserid=max($userids);
					
					qa_db_users_recalc_points($next, $lastuserid);
					
					$next=1+$lastuserid;
					$done+=count($userids);
					$continue=true;
				
				} else {
					qa_db_truncate_userpoints($next);
					qa_db_userpointscount_update(); // quick so just do it here
					qa_recalc_transition($state, 'dorecalcpoints_complete');
				}
				break;
				
			case 'dorecalccategories':
				qa_recalc_transition($state, 'dorecalccategories_postcount');
				break;
			
			case 'dorecalccategories_postcount':
				qa_db_acount_update();
				qa_db_ccount_update();
				
				qa_recalc_transition($state, 'dorecalccategories_postupdate');
				break;
				
			case 'dorecalccategories_postupdate':
				$postids=qa_db_posts_get_for_recategorizing($next, 100);
				
				if (count($postids)) {
					$lastpostid=max($postids);
					
					qa_db_posts_recalc_categoryid($next, $lastpostid);
					qa_db_posts_calc_category_path($next, $lastpostid);
					
					$next=1+$lastpostid;
					$done+=count($postids);
					$continue=true;
				
				} else {
					qa_recalc_transition($state, 'dorecalccategories_recount');
				}
				break;
			
			case 'dorecalccategories_recount':
				$categoryids=qa_db_categories_get_for_recalcs($next, 10);
				
				if (count($categoryids)) {
					$lastcategoryid=max($categoryids);
					
					foreach ($categoryids as $categoryid)
						qa_db_ifcategory_qcount_update($categoryid);
					
					$next=1+$lastcategoryid;
					$done+=count($categoryids);
					$continue=true;
				
				} else {
					qa_recalc_transition($state, 'dorecalccategories_backpaths');
				}
				break;
				
			case 'dorecalccategories_backpaths':
				$categoryids=qa_db_categories_get_for_recalcs($next, 10);

				if (count($categoryids)) {
					$lastcategoryid=max($categoryids);
					
					qa_db_categories_recalc_backpaths($next, $lastcategoryid);
					
					$next=1+$lastcategoryid;
					$done+=count($categoryids);
					$continue=true;
				
				} else {
					qa_recalc_transition($state, 'dorecalccategories_complete');
				}
				break;
				
			case 'dodeletehidden':
				qa_recalc_transition($state, 'dodeletehidden_comments');
				break;
				
			case 'dodeletehidden_comments':
				$posts=qa_db_posts_get_for_deleting('C', $next, 1);
				
				if (count($posts)) {
					$postid=$posts[0];
					
					$oldcomment=qa_db_single_select(qa_db_full_post_selectspec(null, $postid));
					$parent=qa_db_single_select(qa_db_full_post_selectspec(null, $oldcomment['parentid']));
					
					if ($parent['basetype']=='Q') {
						$question=$parent;
						$answer=null;
					} else {
						$question=qa_db_single_select(qa_db_full_post_selectspec(null, $parent['parentid']));
						$answer=$parent;
					}

					qa_comment_delete($oldcomment, $question, $answer, null, null, null);
					
					$next=1+$postid;
					$done++;
					$continue=true;
				
				} else
					qa_recalc_transition($state, 'dodeletehidden_answers');
				break;
			
			case 'dodeletehidden_answers':
				$posts=qa_db_posts_get_for_deleting('A', $next, 1);
				
				if (count($posts)) {
					$postid=$posts[0];
					
					$oldanswer=qa_db_single_select(qa_db_full_post_selectspec(null, $postid));
					$question=qa_db_single_select(qa_db_full_post_selectspec(null, $oldanswer['parentid']));
					qa_answer_delete($oldanswer, $question, null, null, null);
					
					$next=1+$postid;
					$done++;
					$continue=true;
				
				} else
					qa_recalc_transition($state, 'dodeletehidden_questions');
				break;

			case 'dodeletehidden_questions':
				$posts=qa_db_posts_get_for_deleting('Q', $next, 1);
				
				if (count($posts)) {
					$postid=$posts[0];
					
					$oldquestion=qa_db_single_select(qa_db_full_post_selectspec(null, $postid));
					qa_question_delete($oldquestion, null, null, null);
					
					$next=1+$postid;
					$done++;
					$continue=true;
				
				} else
					qa_recalc_transition($state, 'dodeletehidden_complete');
				break;

			default:
				$state='';
				break;
		}
		
		if ($continue)
			$state=$operation.','.$length.','.$next.','.$done;
		
		return $continue && ($done<$length);
	}
	

	function qa_recalc_transition(&$state, $operation)
/*
	Change the $state to represent the beginning of a new $operation
*/
	{
		$state=$operation.','.qa_recalc_stage_length($operation).',0,0';
	}

		
	function qa_recalc_stage_length($operation)
/*
	Return how many steps there will be in recalculation $operation
*/
	{
		switch ($operation) {
			case 'doreindexposts_reindex':
				$length=qa_opt('cache_qcount')+qa_opt('cache_acount')+qa_opt('cache_ccount');
				break;
			
			case 'doreindexposts_wordcount':
				$length=qa_db_count_words();
				break;
				
			case 'dorecalcpoints_recalc':
				$length=qa_opt('cache_userpointscount');
				break;
				
			case 'dorecountposts_recount':
			case 'dorecalccategories_postupdate':
				$length=qa_db_count_posts();
				break;
				
			case 'dorecalccategories_recount':
			case 'dorecalccategories_backpaths':
				$length=qa_db_count_categories();
				break;
			
			case 'dodeletehidden_comments':
				$length=count(qa_db_posts_get_for_deleting('C'));
				break;
				
			case 'dodeletehidden_answers':
				$length=count(qa_db_posts_get_for_deleting('A'));
				break;
				
			case 'dodeletehidden_questions':
				$length=count(qa_db_posts_get_for_deleting('Q'));
				break;
			
			default:
				$length=0;
				break;
		}
		
		return $length;
	}

	
	function qa_recalc_get_message($state)
/*
	Return a string which gives a user-viewable version of $state
*/
	{
		@list($operation, $length, $next, $done)=explode(',', $state);
		
		$done=(int)$done;
		$length=(int)$length;
		
		switch ($operation) {
			case 'doreindexposts_postcount':
			case 'dorecountposts_postcount':
			case 'dorecalccategories_postcount':
				$message=qa_lang('admin/recalc_posts_count');
				break;
				
			case 'doreindexposts_reindex':
				$message=strtr(qa_lang('admin/reindex_posts_reindexed'), array(
					'^1' => number_format($done),
					'^2' => number_format($length)
				));
				break;
				
			case 'doreindexposts_wordcount':
				$message=strtr(qa_lang('admin/reindex_posts_wordcounted'), array(
					'^1' => number_format($done),
					'^2' => number_format($length)
				));
				break;
				
			case 'dorecountposts_recount':
				$message=strtr(qa_lang('admin/recount_posts_recounted'), array(
					'^1' => number_format($done),
					'^2' => number_format($length)
				));
				break;
				
			case 'doreindexposts_complete':
				$message=qa_lang('admin/reindex_posts_complete');
				break;
				
			case 'dorecountposts_complete':
				$message=qa_lang('admin/recount_posts_complete');
				break;
				
			case 'dorecalcpoints_usercount':
				$message=qa_lang('admin/recalc_points_usercount');
				break;
				
			case 'dorecalcpoints_recalc':
				$message=strtr(qa_lang('admin/recalc_points_recalced'), array(
					'^1' => number_format($done),
					'^2' => number_format($length)
				));
				break;
				
			case 'dorecalcpoints_complete':
				$message=qa_lang('admin/recalc_points_complete');
				break;
				
			case 'dorecalccategories_postupdate':
				$message=strtr(qa_lang('admin/recalc_categories_updated'), array(
					'^1' => number_format($done),
					'^2' => number_format($length)
				));
				break;
				
			case 'dorecalccategories_recount':
				$message=strtr(qa_lang('admin/recalc_categories_recounting'), array(
					'^1' => number_format($done),
					'^2' => number_format($length)
				));
				break;
				
			case 'dorecalccategories_backpaths':
				$message=strtr(qa_lang('admin/recalc_categories_backpaths'), array(
					'^1' => number_format($done),
					'^2' => number_format($length)
				));
				break;
				
			case 'dorecalccategories_complete':
				$message=qa_lang('admin/recalc_categories_complete');
				break;
				
			case 'dodeletehidden_comments':
				$message=strtr(qa_lang('admin/hidden_comments_deleted'), array(
					'^1' => number_format($done),
					'^2' => number_format($length)
				));
				break;
				
			case 'dodeletehidden_answers':
				$message=strtr(qa_lang('admin/hidden_answers_deleted'), array(
					'^1' => number_format($done),
					'^2' => number_format($length)
				));
				break;
				
			case 'dodeletehidden_questions':
				$message=strtr(qa_lang('admin/hidden_questions_deleted'), array(
					'^1' => number_format($done),
					'^2' => number_format($length)
				));
				break;

			case 'dodeletehidden_complete':
				$message=qa_lang('admin/delete_hidden_complete');
				break;
			
			default:
				$message='';
				break;
		}
		
		return $message;
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/