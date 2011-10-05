<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-util-sort.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: A useful general-purpose 'sort by' function


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


	function qa_sort_by(&$array, $by1, $by2=null)
/*
	Sort the $array of inner arrays by sub-element $by1 of each inner array, and optionally then by sub-element $by2
*/
	{
		global $qa_sort_by1, $qa_sort_by2;
		
		$qa_sort_by1=$by1;
		$qa_sort_by2=$by2;
		
		uasort($array, 'qa_sort_by_fn');
	}


	function qa_sort_by_fn($a, $b)
/*
	Function used in uasort to implement qa_sort_by()
*/
	{
		global $qa_sort_by1, $qa_sort_by2;
		
		$compare=qa_sort_cmp($a[$qa_sort_by1], $b[$qa_sort_by1]);

		if (($compare==0) && $qa_sort_by2)
			$compare=qa_sort_cmp($a[$qa_sort_by2], $b[$qa_sort_by2]);

		return $compare;
	}


	function qa_sort_cmp($a, $b)
/*
	General comparison function for two values, textual or numeric
*/
	{
		if (is_numeric($a) && is_numeric($b)) // straight subtraction won't work for floating bits
			return ($a==$b) ? 0 : (($a<$b) ? -1 : 1);

		else {
			require_once QA_INCLUDE_DIR.'qa-util-string.php';
		
			return strcasecmp($a, $b); // doesn't do UTF-8 right but it will do for now
		}
	}
	

/*
	Omit PHP closing tag to help avoid accidental output
*/