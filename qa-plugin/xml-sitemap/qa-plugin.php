<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-plugin/xml-sitemap/qa-plugin.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Initiates XML sitemap plugin


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
	Plugin Name: XML Sitemap
	Plugin URI: 
	Plugin Description: Generates sitemap.xml file for submission to search engines
	Plugin Version: 1.0
	Plugin Date: 2010-10-31
	Plugin Author: Question2Answer
	Plugin Author URI: http://www.question2answer.org/
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.3
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}


	qa_register_plugin_module('page', 'qa-xml-sitemap.php', 'qa_xml_sitemap', 'XML Sitemap');
	

/*
	Omit PHP closing tag to help avoid accidental output
*/