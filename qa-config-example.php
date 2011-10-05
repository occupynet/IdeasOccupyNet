<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-config-example.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: After renaming, use this to set up database details and other stuff


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
	======================================================================
	  THE 4 DEFINITIONS BELOW ARE REQUIRED AND MUST BE SET BEFORE USING!
	======================================================================
*/

	define('QA_MYSQL_HOSTNAME', '127.0.0.1'); // try '127.0.0.1' or 'localhost' if MySQL on same server
	define('QA_MYSQL_USERNAME', 'your-mysql-username');
	define('QA_MYSQL_PASSWORD', 'your-mysql-password');
	define('QA_MYSQL_DATABASE', 'your-mysql-db-name');
	
/*
	Ultra-concise installation instructions:
	
	1. Create a MySQL database.
	2. Create a MySQL user with full permissions for that database.
	3. Rename this file to qa-config.php.
	4. Set the above four definitions and save.
	5. Place all the Question2Answer files on your server.
	6. Open the appropriate URL, and follow the instructions.

	More detailed installation instructions here: http://www.question2answer.org/
*/

/*
	======================================================================
	 OPTIONAL CONSTANT DEFINITIONS, INCLUDING SUPPORT FOR SINGLE SIGN-ON
	======================================================================

	QA_MYSQL_TABLE_PREFIX will be added to table names, to allow multiple datasets
	in a single MySQL database, or to include the QA tables in an existing database.
*/

	define('QA_MYSQL_TABLE_PREFIX', 'qa_');
	
/*
	If you wish, you can define QA_MYSQL_USERS_PREFIX separately from QA_MYSQL_TABLE_PREFIX.
	If so, it is used instead of QA_MYSQL_TABLE_PREFIX as the prefix for tables containing
	information about QA user accounts (not including users' activity and points). To share a
	single user base between multiple QA sites, use the same values for all the QA_MYSQL_*
	constants in each site's qa-config.php file, with the exception of QA_MYSQL_TABLE_PREFIX.

	define('QA_MYSQL_USERS_PREFIX', 'qa_users_');
*/

/*
	If you wish, you can define QA_COOKIE_DOMAIN so that any cookies created by QA are assigned
	to a specific domain name, instead of the full domain name of the request by default. This is
	useful if you're running multiple QA sites on subdomains with a shared user base. 
	
	define('QA_COOKIE_DOMAIN', '.example.com'); // be sure to keep the leading period
*/

/*
	If you wish, you can define an array $QA_CONST_PATH_MAP to modify the URLs used in your Q2A site.
	The key of each array element should be the standard part of the path, e.g. 'questions',
	and the value should be the replacement for that standard part, e.g. 'topics'. If you edit this
	file in UTF-8 encoding you can also use non-ASCII characters in these URLs.
	
	$QA_CONST_PATH_MAP=array(
		'questions' => 'topics',
		'categories' => 'sections',
		'users' => 'contributors',
		'user' => 'contributor',
	);
*/

/*
	Flags for using external code - set to true if you're replacing default functions
	
	QA_EXTERNAL_LANG to use your language translation logic in qa-external/qa-external-lang.php
	QA_EXTERNAL_USERS to use your user identification code in qa-external/qa-external-users.php
	QA_EXTERNAL_EMAILER to use your email sending function in qa-external/qa-external-emailer.php
*/
	
	define('QA_EXTERNAL_USERS', false);
	define('QA_EXTERNAL_LANG', false);
	define('QA_EXTERNAL_EMAILER', false);

/*
	Out-of-the-box WordPress 3.x integration - to integrate with your WordPress site and user
	database, define QA_WORDPRESS_INTEGRATE_PATH as the full path to the WordPress directory
	containing wp-load.php. You do not need to set the QA_MYSQL_* constants above since these
	will be taken from WordPress automatically. See online documentation for more details.
	
	define('QA_WORDPRESS_INTEGRATE_PATH', '/PATH/TO/WORDPRESS');
*/

/*
	Some settings to help optimize your QA site's performance.
	
	If QA_HTML_COMPRESSION is true, HTML web pages will be output using Gzip compression, if
	the user's browser indicates this is supported. This will increase the performance of your
	site, but will make debugging harder if PHP does not complete execution.
	
	QA_MAX_LIMIT_START is the maximum start parameter that can be requested. As this gets
	higher, queries tend to get slower, since MySQL must examine more information. Very high
	start numbers are usually only requested by search engine robots anyway.
	
	If a word is used QA_IGNORED_WORDS_FREQ times or more in a particular way, it is ignored
	when searching or finding related questions. This saves time by ignoring words which are so
	common that they are probably not worth matching on.

	Set QA_OPTIMIZE_LOCAL_DB to true if your web server and MySQL are running on the same box.
	When viewing a page on your site, this will use several simple MySQL queries instead of one
	complex one, which makes sense since there is no latency for localhost access.
	
	Set QA_PERSISTENT_CONN_DB to true to use persistent database connections. Only use this if
	you are absolutely sure it is a good idea under your setup - generally it is not.
	For more information: http://www.php.net/manual/en/features.persistent-connections.php
	
	Set QA_DEBUG_PERFORMANCE to true to show detailed performance profiling information.
*/

	define('QA_HTML_COMPRESSION', true);
	define('QA_MAX_LIMIT_START', 19999);
	define('QA_IGNORED_WORDS_FREQ', 10000);
	define('QA_OPTIMIZE_LOCAL_DB', false);
	define('QA_PERSISTENT_CONN_DB', false);
	define('QA_DEBUG_PERFORMANCE', false);
	
/*
	And lastly... if you want to, you can define any constant from qa-db-maxima.php in this
	file to override the default setting. Just make sure you know what you're doing!
*/
	

/*
	Omit PHP closing tag to help avoid accidental output
*/