<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-plugin/xml-sitemap/qa-xml-sitemap.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Page module class for XML sitemap plugin


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

	class qa_xml_sitemap {
		
		var $directory;
		var $urltoroot;
		
		function load_module($directory, $urltoroot)
		{
			$this->directory=$directory;
			$this->urltoroot=$urltoroot;
		}
		
		function suggest_requests()
		{	
			return array(
				array(
					'title' => 'XML Sitemap',
					'request' => 'sitemap.xml',
					'nav' => null, // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
				),
			);
		}
		
		function match_request($request)
		{
			if ($request=='sitemap.xml')
				return true;

			return false;
		}
		
		function process_request($request)
		{
			@ini_set('display_errors', 0); // we don't want to show PHP errors inside XML

			$siteurl=qa_opt('site_url');
			
			header('Content-type: text/xml; charset=utf-8');
			
			echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
			echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
		
		//	Question pages
		
			$nextpostid=0;
			
			while (1) {
				$questions=qa_db_read_all_assoc(qa_db_query_sub(
					"SELECT postid, BINARY title AS title, upvotes, downvotes FROM ^posts WHERE postid>=# AND type='Q' ORDER BY postid LIMIT 100",
					$nextpostid
				));
				
				if (!count($questions))
					break;

				foreach ($questions as $question) {
					$priority=0.5; // between 0 and 1 depending on up/down votes (0.5 if net votes are zero)
					$netvotes=$question['upvotes']-$question['downvotes'];
					
					if ($netvotes!=0) {
						$absvotes=abs($netvotes);
						$signvotes=$netvotes/$absvotes;
						$priority+=$signvotes*0.5/(1+(1/$absvotes));
					}
					
					echo "\t<url>\n".
						"\t\t<loc>".qa_path_html(qa_q_request($question['postid'], $question['title']), null, $siteurl)."</loc>\n".
						"\t\t<priority>".$priority."</priority>\n".
						"\t</url>\n";
						
					$nextpostid=max($nextpostid, $question['postid']+1);
				}
			}
		
		//	Tag pages
			
			if (qa_using_tags()) {
				$nextwordid=0;
			
				while (1) {
					$tagwords=qa_db_read_all_assoc(qa_db_query_sub(
						"SELECT wordid, BINARY word AS word, tagcount FROM ^words WHERE wordid>=# AND tagcount>0 ORDER BY wordid LIMIT 100",
						$nextwordid
					));
					
					if (!count($tagwords))
						break;
						
					foreach ($tagwords as $tagword) {
						$priority=0.5/(1+(1/$tagword['tagcount'])); // between 0.25 and 0.5 depending on tag frequency
						
						echo "\t<url>\n".
							"\t\t<loc>".qa_path_html('tag/'.$tagword['word'], null, $siteurl)."</loc>\n".
							"\t\t<priority>".$priority."</priority>\n".
							"\t</url>\n";
							
						$nextwordid=max($nextwordid, $tagword['wordid']+1);
					}				
				}
			}
			
		//	User pages
		
			if (!QA_FINAL_EXTERNAL_USERS) {
				$nextuserid=0;
				
				while (1) {
					$users=qa_db_read_all_assoc(qa_db_query_sub(
						"SELECT userid, BINARY handle AS handle FROM ^users WHERE userid>=# ORDER BY userid LIMIT 100",
						$nextuserid
					));
					
					if (!count($users))
						break;
					
					foreach ($users as $user) {
						$priority=0.25;
						
						echo "\t<url>\n".
							"\t\t<loc>".qa_path_html('user/'.$user['handle'], null, $siteurl)."</loc>\n".
							"\t\t<priority>".$priority."</priority>\n".
							"\t</url>\n";
							
						$nextuserid=max($nextuserid, $user['userid']+1);
					}
				}
			}
			
			echo "</urlset>\n";
			
			return null;
		}
	
	};
	

/*
	Omit PHP closing tag to help avoid accidental output
*/