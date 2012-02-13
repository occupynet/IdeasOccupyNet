<?php

/*
	Question2Answer (c) Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-plugin/tag-cloud-widget/qa-tag-cloud.php
	Version: See define()s at top of qa-include/qa-base.php
	Description: Widget module class for tag cloud plugin


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

	class qa_tag_cloud {
		
		function option_default($option)
		{
			if ($option=='tag_cloud_count_tags')
				return 100;
			elseif ($option=='tag_cloud_font_size')
				return 24;
			elseif ($option=='tag_cloud_size_popular')
				return true;
		}

		
		function admin_form()
		{
			$saved=false;
			
			if (qa_clicked('tag_cloud_save_button')) {
				qa_opt('tag_cloud_count_tags', (int)qa_post_text('tag_cloud_count_tags_field'));
				qa_opt('tag_cloud_font_size', (int)qa_post_text('tag_cloud_font_size_field'));
				qa_opt('tag_cloud_size_popular', (int)qa_post_text('tag_cloud_size_popular_field'));
				$saved=true;
			}
			
			return array(
				'ok' => $saved ? 'Tag cloud settings saved' : null,
				
				'fields' => array(
					array(
						'label' => 'Maximum tags to show:',
						'type' => 'number',
						'value' => (int)qa_opt('tag_cloud_count_tags'),
						'suffix' => 'tags',
						'tags' => 'NAME="tag_cloud_count_tags_field"',
					),

					array(
						'label' => 'Starting font size:',
						'suffix' => 'pixels',
						'type' => 'number',
						'value' => (int)qa_opt('tag_cloud_font_size'),
						'tags' => 'NAME="tag_cloud_font_size_field"',
					),
					
					array(
						'label' => 'Font size represents tag popularity',
						'type' => 'checkbox',
						'value' => qa_opt('tag_cloud_size_popular'),
						'tags' => 'NAME="tag_cloud_size_popular_field"',
					),
				),
				
				'buttons' => array(
					array(
						'label' => 'Save Changes',
						'tags' => 'NAME="tag_cloud_save_button"',
					),
				),
			);
		}

		
		function allow_template($template)
		{
			$allow=false;
			
			switch ($template)
			{
				case 'activity':
				case 'qa':
				case 'questions':
				case 'hot':
				case 'ask':
				case 'categories':
				case 'question':
				case 'tag':
				case 'tags':
				case 'unanswered':
				case 'user':
				case 'users':
				case 'search':
				case 'admin':
				case 'custom':
					$allow=true;
					break;
			}
			
			return $allow;
		}

		
		function allow_region($region)
		{
			return ($region=='side');
		}
		

		function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
		{
			require_once QA_INCLUDE_DIR.'qa-db-selects.php';
			
			$populartags=qa_db_single_select(qa_db_popular_tags_selectspec(0, (int)qa_opt('tag_cloud_count_tags')));
			
			reset($populartags);
			$maxcount=current($populartags);
			
			$themeobject->output(
				'<H2 STYLE="margin-top:0; padding-top:0;">',
				qa_lang_html('main/popular_tags'),
				'</H2>'
			);
			
			$themeobject->output('<DIV STYLE="font-size:10px;">');
			
			$maxsize=qa_opt('tag_cloud_font_size');
			$scale=qa_opt('tag_cloud_size_popular');
			
			foreach ($populartags as $tag => $count) {
				$size=number_format(($scale ? ($maxsize*$count/$maxcount) : $maxsize), 1);
				
				if (($size>=5) || !$scale)
					$themeobject->output('<A HREF="'.qa_path_html('tag/'.$tag).'" STYLE="font-size:'.$size.'px; vertical-align:baseline;">'.qa_html($tag).'</A>');
			}
			
			$themeobject->output('</DIV>');
		}
	
	}
	

/*
	Omit PHP closing tag to help avoid accidental output
*/