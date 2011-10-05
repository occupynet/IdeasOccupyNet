<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-external-example/qa-external-lang.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Example of how to use your own language translation layer


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
	=======================================================================
	THIS FILE ALLOWS YOU TO USE YOUR EXISTING LANGUAGE TRANSLATION SOLUTION
	=======================================================================

	It is used if QA_EXTERNAL_LANG is set to true in qa-config.php.
*/

	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../');
		exit;
	}


	function qa_lang($identifier)
/*
	Provide the appropriate translation for the phrase labelled $identifier.

	If you cannot provide a translation, you can return qa_lang_base($identifier)
	which will use the default translation code for the engine.
*/
	{
		$gottranslation=false;
		
		if ($gottranslation)
			return 'the translation';
		else
			return qa_lang_base($identifier);
	}
	

/*
	Omit PHP closing tag to help avoid accidental output
*/