<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-plugin/qa-openid-login/qa-plugin.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Initiates Openid login plugin


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
	Plugin Name: Openid Login
	Plugin URI: 
	Plugin Description: Allows users to log in with Openid
	Plugin Version: 0.1
	Plugin Date: 2011-10-04
	Plugin Author: propongo
	Plugin Author URI: http://www.propongo.tomalaplaza.net/
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.4
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}


	qa_register_plugin_module('login', 'qa-openid-login.php', 'qa_openid_login', 'Openid Login');
	

/*
	Omit PHP closing tag to help avoid accidental output
*/
