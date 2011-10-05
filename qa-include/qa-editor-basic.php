<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-editor-basic.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Basic editor module for plain text editing


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


	class qa_editor_basic {
		
		function load_module($localdir, $htmldir)
		{
		}
		
		function calc_quality($content, $format)
		{
			if ($format=='')
				return 1.0;
			elseif ($format=='html')
				return 0.2;
			else
				return 0;
		}

		function get_field(&$qa_content, $content, $format, $fieldname, $rows, $autofocus)
		{
			if ($autofocus)
				$qa_content['focusid']=$fieldname;
			
			return array(
				'type' => 'textarea',
				'tags' => 'NAME="'.$fieldname.'" ID="'.$fieldname.'"',
				'value' => qa_html($content),
				'rows' => $rows,
			);
		}
		
		function read_post($fieldname)
		{
			return array(
				'format' => '',
				'content' => qa_post_text($fieldname),
			);
		}
	
	};
	

/*
	Omit PHP closing tag to help avoid accidental output
*/