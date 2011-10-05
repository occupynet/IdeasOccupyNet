<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-plugin/activity-count-widget/qa-activity-count.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Widget module class for activity count plugin


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

	class qa_activity_count {
		
		function allow_template($template)
		{
			return true;
		}
		
		function allow_region($region)
		{
			return ($region=='side');
		}
		
		function output_widget($region, $place, $themeobject, $template, $request, $qa_content)
		{
			$themeobject->output('<DIV STYLE="font-size:150%; line-height:150%;">');
			$this->output_count($themeobject, qa_opt('cache_qcount'), 'main/1_question', 'main/x_questions');
			$themeobject->output('<BR/>');
			$this->output_count($themeobject, qa_opt('cache_acount'), 'main/1_answer', 'main/x_answers');
			$themeobject->output('<BR/>');
			$this->output_count($themeobject, qa_opt('cache_ccount'), 'main/1_comment', 'main/x_comments');
			$themeobject->output('<BR/>');
			$this->output_count($themeobject, qa_opt('cache_userpointscount'), 'main/1_user', 'main/x_users');
			$themeobject->output('</DIV>');
		}
		
		function output_count($themeobject, $value, $langsingular, $langplural)
		{
			if ($value==1)
				$themeobject->output(qa_lang_html_sub($langsingular, '<B>1</B>', '1'));
			else
				$themeobject->output(qa_lang_html_sub($langplural, '<B>'.number_format($value).'</B>'));
		}
	
	};
	

/*
	Omit PHP closing tag to help avoid accidental output
*/