<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-base.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Sets up Q2A environment, plus many globally useful functions


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

//	Be ultra-strict about error checking

	error_reporting(E_ALL);
	

//	Set the version to be used for internal reference and a suffix for .js and .css requests, and other constants

	define('QA_VERSION', '1.4');
	define('QA_CATEGORY_DEPTH', 4); // you can't change this number!
	

//	Basic PHP configuration checks and unregister globals

	if ( ((float)phpversion()) < 4.3 )
		qa_fatal_error('This requires PHP 4.3 or later');

	@ini_set('magic_quotes_runtime', 0);
	
	@setlocale(LC_CTYPE, 'C'); // prevent strtolower() et al affecting non-ASCII characters (appears important for IIS)
	
	if (function_exists('date_default_timezone_set') && function_exists('date_default_timezone_get'))
		@date_default_timezone_set(@date_default_timezone_get()); // prevent PHP notices where default timezone not set
		
	if (ini_get('register_globals')) {
		$checkarrays=array('_ENV', '_GET', '_POST', '_COOKIE', '_SERVER', '_FILES', '_REQUEST', '_SESSION');
		$keyprotect=array_flip(array_merge($checkarrays, array('GLOBALS')));
		
		foreach ($checkarrays as $checkarray)
			if ( isset(${$checkarray}) && is_array(${$checkarray}) )
				foreach (${$checkarray} as $checkkey => $checkvalue)
					if (isset($keyprotect[$checkkey]))
						qa_fatal_error('My superglobals are not for overriding');
					else
						unset($GLOBALS[$checkkey]);
	}
	

//	Try our best to set base path here just in case it wasn't set in index.php or qa-index.php - won't work with symbolic links

	if (!defined('QA_BASE_DIR'))
		define('QA_BASE_DIR', dirname(dirname(__FILE__)).'/');


//	Define directories of important files in local disk space, load up configuration
	
	define('QA_EXTERNAL_DIR', QA_BASE_DIR.'qa-external/');
	define('QA_INCLUDE_DIR', QA_BASE_DIR.'qa-include/');
	define('QA_LANG_DIR', QA_BASE_DIR.'qa-lang/');
	define('QA_THEME_DIR', QA_BASE_DIR.'qa-theme/');
	define('QA_PLUGIN_DIR', QA_BASE_DIR.'qa-plugin/');

	if (!file_exists(QA_BASE_DIR.'qa-config.php'))
		qa_fatal_error('The config file could not be found. Please read the instructions in qa-config-example.php.');
	
	require_once QA_BASE_DIR.'qa-config.php';
	require_once QA_INCLUDE_DIR.'qa-db.php';
	
	@define('QA_COOKIE_DOMAIN', ''); // default if not set

	
//	Handle WordPress integration

	if (defined('QA_WORDPRESS_INTEGRATE_PATH') && strlen(QA_WORDPRESS_INTEGRATE_PATH)) {
		define('QA_FINAL_WORDPRESS_INTEGRATE_PATH', QA_WORDPRESS_INTEGRATE_PATH.((substr(QA_WORDPRESS_INTEGRATE_PATH, -1)=='/') ? '' : '/'));

		$wordpressfile=QA_FINAL_WORDPRESS_INTEGRATE_PATH.'wp-load.php';
		if (!is_readable($wordpressfile))
			qa_fatal_error('Could not find wp-load.php file for WordPress integration - please check QA_WORDPRESS_INTEGRATE_PATH in qa-config.php');

		require_once $wordpressfile;

		define('QA_FINAL_MYSQL_HOSTNAME', DB_HOST);
		define('QA_FINAL_MYSQL_USERNAME', DB_USER);
		define('QA_FINAL_MYSQL_PASSWORD', DB_PASSWORD);
		define('QA_FINAL_MYSQL_DATABASE', DB_NAME);
		define('QA_FINAL_EXTERNAL_USERS', true);
		
		// Undo WordPress's addition of magic quotes to various things (leave $_COOKIE as is since WP code might need that)
		foreach ($_GET as $key => $value)
			$_GET[$key]=strtr(stripslashes($value), array('\\\'' => '\'', '\"' => '"')); // also compensate for WordPress's .htaccess file

		foreach ($_POST as $key => $value)
			$_POST[$key]=stripslashes($value);
			
		$_SERVER['PHP_SELF']=stripslashes($_SERVER['PHP_SELF']);

	} else {
		define('QA_FINAL_MYSQL_HOSTNAME', QA_MYSQL_HOSTNAME);
		define('QA_FINAL_MYSQL_USERNAME', QA_MYSQL_USERNAME);
		define('QA_FINAL_MYSQL_PASSWORD', QA_MYSQL_PASSWORD);
		define('QA_FINAL_MYSQL_DATABASE', QA_MYSQL_DATABASE);
		define('QA_FINAL_EXTERNAL_USERS', QA_EXTERNAL_USERS);
	}	


//	General HTML/JS functions

	function qa_html($string, $multiline=false)
/*
	Return HTML representation of $string, work well with blocks of text if $multiline is true
*/
	{
		$html=htmlspecialchars((string)$string);
		
		if ($multiline) {
			$html=preg_replace('/\r\n?/', "\n", $html);
			$html=preg_replace('/(?<=\s) /', '&nbsp;', $html);
			$html=str_replace("\t", '&nbsp; &nbsp; ', $html);
			$html=nl2br($html);
		}
		
		return $html;
	}

	
	function qa_sanitize_html($html, $linksnewwindow=false)
/*
	Return $html after ensuring it is safe, i.e. removing Javascripts and the like - uses htmLawed library
	Links open in a new window if $linksnewwindow is true
*/
	{
		require_once 'qa-htmLawed.php';
		
		global $qa_sanitize_html_newwindow;
		
		$qa_sanitize_html_newwindow=$linksnewwindow;
		
		$safe=htmLawed($html, array(
			'safe' => 1,
			'elements' => '*+embed+object',
			'schemes' => 'href: aim, feed, file, ftp, gopher, http, https, irc, mailto, news, nntp, sftp, ssh, telnet; *:file, http, https; style: !; classid:clsid',
			'keep_bad' => 0,
			'anti_link_spam' => array('/.*/', ''),
			'hook_tag' => 'qa_sanitize_html_hook_tag',
		));
		
		return $safe;
	}
	
	
	function qa_sanitize_html_hook_tag($element, $attributes)
/*
	htmLawed hook function used to process tags in qa_sanitize_html(...)
*/
	{
		global $qa_sanitize_html_newwindow;

		if ( ($element=='param') && (trim(strtolower(@$attributes['name']))=='allowscriptaccess') )
			$attributes['name']='allowscriptaccess_denied';
			
		if ($element=='embed')
			unset($attributes['allowscriptaccess']);
			
		if (($element=='a') && isset($attributes['href']) && $qa_sanitize_html_newwindow)
			$attributes['target']='_blank';
		
		$html='<'.$element;
		foreach ($attributes as $key => $value)
			$html.=' '.$key.'="'.$value.'"';
			
		return $html.'>';
	}
	
	
	function qa_js($value, $forcequotes=false)
/*
	Return JavaScript representation of $value, putting in quotes if non-numeric or if $forcequote is true
*/
	{
		if (is_numeric($value) && !$forcequotes)
			return $value;
		else
			return "'".strtr($value, array(
				"'" => "\\'",
				'\\' => '\\\\',
				"\n" => "\\n",
				"\r" => "\\n",
			))."'";
	}

	
	function qa_gpc_to_string($string)
/*
	Return string for incoming GET/POST/COOKIE value, stripping slashes if appropriate
*/
	{
		return get_magic_quotes_gpc() ? stripslashes($string) : $string;
	}
	

	function qa_string_to_gpc($string)
/*
	Return string with slashes added, if appropriate for later removal by qa_gpc_to_string()
*/
	{
		return get_magic_quotes_gpc() ? addslashes($string) : $string;
	}


	function qa_get($field)
/*
	Return string for incoming GET field, or null if it's not defined
*/
	{
		return isset($_GET[$field]) ? qa_gpc_to_string($_GET[$field]) : null;
	}


	function qa_post_text($field)
/*
	Return string for incoming POST field, or null if it's not defined.
	While we're at it, trim() surrounding white space and converted to Unix line endings.
*/
	{
		return isset($_POST[$field]) ? preg_replace('/\r\n?/', "\n", trim(qa_gpc_to_string($_POST[$field]))) : null;
	}

	
	function qa_clicked($name)
/*
	Return true if form button $name was clicked (as TYPE=SUBMIT/IMAGE) to create this page request.
*/
	{
		return isset($_POST[$name]) || isset($_POST[$name.'_x']);
	}

	
	function qa_is_http_post()
/*
	Return true if we are responding to an HTTP POST request
*/
	{
		return ($_SERVER['REQUEST_METHOD']=='POST') || !empty($_POST);
	}

	
	function qa_is_https_probably()
/*
	Return true if we appear to be responding to a secure HTTP request (but hard to be sure)
*/
	{
		return (@$_SERVER['HTTPS'] && ($_SERVER['HTTPS']!='off')) || (@$_SERVER['SERVER_PORT']==443);
	}
	
	
	function qa_is_human_probably()
/*
	Return true if it appears the page request is coming from a human using a web browser, rather than a search engine
	or other bot. Based on a whitelist of terms in user agents, this can easily be tricked by a scraper or bad bot.
*/
	{
		$useragent=@$_SERVER['HTTP_USER_AGENT'];
		
		if (strlen($useragent)) {
			$humanagents=array('MSIE', 'Firefox', 'Chrome', 'Safari', 'Opera', 'Gecko', 'MIDP', 'PLAYSTATION', 'Teleca',
			'BlackBerry', 'UP.Browser', 'Polaris', 'MAUI_WAP_Browser', 'iPad', 'iPhone', 'iPod');
			
			foreach ($humanagents as $humanagent)
				if (strpos($useragent, $humanagent)!==false)
					return true;
					
			return false;
			
		} else
			return true; // if server doesn't provide user agent, assume it's a human
	}

	
//	Language support

	function qa_lang_base($identifier)
/*
	Return the translated string for $identifier, unless we're using external translation logic.
	This will retrieve the 'site_language' option so make sure you've already loaded/set that if
	loading an option now will cause a problem (see issue in qa_default_option()). The part of
	$identifier before the slash (/) replaces the * in the qa-lang-*.php file references, and the
	part after the / is the key of the array element to be taken from that file's returned result.
*/
	{
		$languagecode=qa_opt('site_language');
		
		list($group, $label)=explode('/', $identifier, 2);
		
		if (strlen($languagecode)) {
			global $qa_lang_custom;
		
			if (!isset($qa_lang_custom[$group])) { // only load each language file once
				$directory=QA_LANG_DIR.$languagecode.'/';
				
				$phrases=@include $directory.'qa-lang-'.$group.'.php'; // can tolerate missing file or directory
				
				$qa_lang_custom[$group]=is_array($phrases) ? $phrases : array();
			}
			
			if (isset($qa_lang_custom[$group][$label]))
				return $qa_lang_custom[$group][$label];
		}
		
		global $qa_lang_default;
		
		if (!isset($qa_lang_default[$group])) // only load each default language file once
			$qa_lang_default[$group]=include_once QA_INCLUDE_DIR.'qa-lang-'.$group.'.php';
		
		if (isset($qa_lang_default[$group][$label]))
			return $qa_lang_default[$group][$label];
			
		return '['.$identifier.']'; // as a last resort, return the identifier to help in development
	}


	if (QA_EXTERNAL_LANG) {

		require QA_EXTERNAL_DIR.'qa-external-lang.php';

	} else {

		function qa_lang($identifier)
		{
			return qa_lang_base($identifier);
		}

	}

	
	function qa_lang_sub($identifier, $textparam, $symbol='^')
/*
	Return the translated string for $identifier, with $symbol substituted for $textparam
*/
	{
		return str_replace($symbol, $textparam, qa_lang($identifier));
	}
	

	function qa_lang_html($identifier)
/*
	Return the translated string for $identifier, converted to HTML
*/
	{
		return qa_html(qa_lang($identifier));
	}

	
	function qa_lang_html_sub($identifier, $htmlparam, $symbol='^')
/*
	Return the translated string for $identifier converted to HTML, with $symbol *then* substituted for $htmlparam
*/
	{
		return str_replace($symbol, $htmlparam, qa_lang_html($identifier));
	}
	

	function qa_lang_html_sub_split($identifier, $htmlparam, $symbol='^')
/*
	Return an array containing the translated string for $identifier converted to HTML, then split into three,
	with $symbol substituted for $htmlparam in the 'data' element, and obvious 'prefix' and 'suffix' elements
*/
	{
		$html=qa_lang_html($identifier);

		$symbolpos=strpos($html, $symbol);
		if (!is_numeric($symbolpos))
			qa_fatal_error('Missing '.$symbol.' in language string '.$identifier);
			
		return array(
			'prefix' => substr($html, 0, $symbolpos),
			'data' => $htmlparam,
			'suffix' => substr($html, $symbolpos+1),
		);
	}

	
//	Path generation

	define('QA_URL_FORMAT_INDEX', 0); // http://.../index.php/questions/123/why-is-the-sky-blue
	define('QA_URL_FORMAT_NEAT', 1); // http://.../questions/123/why-is-the-sky-blue
	define('QA_URL_FORMAT_PARAM', 3); // http://.../?qa=questions/123/why-is-the-sky-blue
	define('QA_URL_FORMAT_PARAMS', 4); // http://.../?qa=questions&qa_1=123&qa_2=why-is-the-sky-blue
	define('QA_URL_FORMAT_SAFEST', 5); // http://.../index.php?qa=questions&qa_1=123&qa_2=why-is-the-sky-blue
	define('QA_URL_TEST_STRING', '$&-_~#%\\@^*()=!()][`\';:|".{},<>?# π§½Жש'); // tests escaping, spaces, quote slashing and unicode - but not + and /

	function qa_path($request, $params=null, $rooturl=null, $neaturls=null, $anchor=null)
/*
	Return the relative URI path for $request, with optional parameters $params and $anchor.
	Slashes in $request will not be urlencoded, but any other characters will.
	If $neaturls is set, use that, otherwise retrieve the option. If $rooturl is set, take
	that as the root of the QA site, otherwise use $qa_root_url_relative set elsewhere.
*/
	{
		global $qa_root_url_relative, $QA_CONST_PATH_MAP;
		
		if (!isset($neaturls)) {
			require_once QA_INCLUDE_DIR.'qa-app-options.php';
			$neaturls=qa_opt('neat_urls');
		}
		
		if (!isset($rooturl))
			$rooturl=$qa_root_url_relative;
		
		$url=$rooturl.( (empty($rooturl) || (substr($rooturl, -1)=='/') ) ? '' : '/');
		$paramsextra='';
		
		$requestparts=explode('/', $request);
		
		if (isset($QA_CONST_PATH_MAP[$requestparts[0]]))
			$requestparts[0]=$QA_CONST_PATH_MAP[$requestparts[0]];
		
		foreach ($requestparts as $index => $requestpart)
			$requestparts[$index]=urlencode($requestpart);
		$requestpath=implode('/', $requestparts);
		
		switch ($neaturls) {
			case QA_URL_FORMAT_INDEX:
				if (!empty($request))
					$url.='index.php/'.$requestpath;
				break;
				
			case QA_URL_FORMAT_NEAT:
				$url.=$requestpath;
				break;
				
			case QA_URL_FORMAT_PARAM:
				if (!empty($request))
					$paramsextra='?qa='.$requestpath;
				break;
				
			default:
				$url.='index.php';
			
			case QA_URL_FORMAT_PARAMS:
				if (!empty($request))
					foreach ($requestparts as $partindex => $requestpart)
						$paramsextra.=(strlen($paramsextra) ? '&' : '?').'qa'.($partindex ? ('_'.$partindex) : '').'='.$requestpart;
				break;
		}
		
		if (isset($params))
			foreach ($params as $key => $value)
				$paramsextra.=(strlen($paramsextra) ? '&' : '?').urlencode($key).'='.urlencode($value);
		
		return $url.$paramsextra.( empty($anchor) ? '' : '#'.urlencode($anchor) );
	}

	
	function qa_q_request($questionid, $title)
/*
	Return the request for question $questionid, and make it search-engine friendly based on $title.
	Keep the title bit to a length of just over 50 characters, not including hyphens.
	To do this, we remove shorter words, which are generally less meaningful.
*/
	{
		require_once QA_INCLUDE_DIR.'qa-util-string.php';
	
		$words=qa_string_to_words($title, true, false, false);

		$wordlength=array();
		foreach ($words as $index => $word)
			$wordlength[$index]=qa_strlen($word);

		$remaining=qa_opt('q_urls_title_length');
		
		if (array_sum($wordlength)>$remaining) {
			arsort($wordlength, SORT_NUMERIC); // sort with longest words first
			
			foreach ($wordlength as $index => $length) {
				if ($remaining>0)
					$remaining-=$length;
				else
					unset($words[$index]);
			}
		}
		
		$title=implode('-', $words);
		if (qa_opt('q_urls_remove_accents'))
			$title=qa_string_remove_accents($title);
		
		return (int)$questionid.'/'.$title;
	}

	
	function qa_feed_request($feed)
/*
	Return the request for the specified $feed
*/
	{
		return 'feed/'.$feed.'.rss';
	}
	
	
	function qa_anchor($basetype, $postid)
/*
	Return the HTML anchor that should be used for post $postid with $basetype (Q/A/C)
*/
	{
		return strtolower($basetype).$postid; // used to be $postid only but this violated HTML spec
	}
	
	
	function qa_path_html($request, $params=null, $rooturl=null, $neaturls=null, $anchor=null)
/*
	Return HTML representation of relative URI path for $request - see qa_path() for other parameters
*/
	{
		return qa_html(qa_path($request, $params, $rooturl, $neaturls, $anchor));
	}

	
	function qa_path_form_html($request, $params=null, $rooturl=null, $neaturls=null, $anchor=null)
/*
	Return HTML for hidden fields to insert into a <FORM METHOD="GET"...> on the page.
	This is needed because any parameters on the URL will be lost when the form is submitted.
*/
	{
		$path=qa_path($request, $params, $rooturl, $neaturls, $anchor);
		$formhtml='';
		
		$questionpos=strpos($path, '?');
		if (is_numeric($questionpos)) {
			$params=explode('&', substr($path, $questionpos+1));
			
			foreach ($params as $param)
				if (preg_match('/^([^\=]*)(\=(.*))?$/', $param, $matches))
					$formhtml.='<INPUT TYPE="hidden" NAME="'.qa_html(urldecode($matches[1])).'" VALUE="'.qa_html(urldecode(@$matches[3])).'"/>';
		}
		
		return $formhtml;
	}
	
	
	function qa_redirect($request, $params=null, $rooturl=null, $neaturls=null, $anchor=null)
/*
	Redirect the user's web browser to $request and then we're done - see qa_path() for other parameters
*/
	{
		header('Location: '.qa_path($request, $params, $rooturl, $neaturls, $anchor));
		exit;
	}
	
	
	function qa_redirect_raw($url)
/*
	Redirect the user's web browser to page $path which is already a URL
*/
	{
		header('Location: '.$url);
		exit;
	}
	

//	General utilities

	function qa_retrieve_url($url)
/*
	Return the contents of remote $url, using file_get_contents() if possible, otherwise curl functions
*/
	{
		$contents=@file_get_contents($url);
		
		if ((!strlen($contents)) && function_exists('curl_exec')) { // try curl as a backup (if allow_url_fopen not set)
			$curl=curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			$contents=@curl_exec($curl);
			curl_close($curl);
		}
		
		return $contents;
	}


//	Database connection

	function qa_base_db_connect($failhandler=null)
/*
	Connect to the database with optional $failhandler
*/
	{
		qa_db_connect($failhandler);
	}

	
	function qa_base_db_disconnect()
/*
	Disconnect from the database
*/
	{
		qa_db_disconnect();
	}
	
	
	function qa_opt($name, $value=null)
/*
	Shortcut to get or set an option value without specifying database
*/
	{
		require_once QA_INCLUDE_DIR.'qa-app-options.php';
		
		if (isset($value))
			qa_set_option($name, $value);	
		
		$options=qa_get_options(array($name));

		return $options[$name];
	}
	
	
//	Error handling

	function qa_fatal_error($message)
/*
	Display $message in the browser, write it to server error log, and then stop abruptly
*/
	{
		echo '<FONT COLOR="red">'.qa_html($message).'</FONT>';
		@error_log('Question2Answer fatal error: '.$message);
		exit;
	}
	
	
//	Module (and plugin) management
	
	$qa_modules=array();
	$qa_layers=array();


	function qa_register_module($type, $include, $class, $name, $directory=QA_INCLUDE_DIR, $urltoroot=null)
/*
	Register a module of $type named $name, whose class named $class is defined in file $include (or null if no include necessary)
	If this module comes from a plugin, pass in the local plugin $directory and the $urltoroot relative url for that directory 
*/
	{
		global $qa_modules;
		
		if (isset($qa_modules[$type][$name]))
			qa_fatal_error('A '.$type.' module named '.$name.' already exists. Please check there are no duplicate plugins.');
		
		$qa_modules[$type][$name]=array(
			'directory' => $directory,
			'urltoroot' => $urltoroot,
			'include' => $include,
			'class' => $class,
		);
	}
	
	
	function qa_register_plugin_module($type, $include, $class, $name)
/*
	Register a plugin module of $type named $name, whose class named $class is defined in file $include (or null if no include necessary)
	This function relies on some global variable values and can only be called from a plugin's qa-plugin.php file
*/
	{
		global $qa_plugin_directory, $qa_plugin_urltoroot;
		
		if (empty($qa_plugin_directory) || empty($qa_plugin_urltoroot))
			qa_fatal_error('qa_register_plugin_module() can only be called from a plugin qa-plugin.php file');

		qa_register_module($type, $include, $class, $name, $qa_plugin_directory, $qa_plugin_urltoroot);
	}

	
	function qa_register_layer($include, $name, $directory=QA_INCLUDE_DIR, $urltoroot=null)
/*
	Register a layer named $name, defined in file $include. If this layer comes from a plugin (as all currently do),
	pass in the local plugin $directory and the $urltoroot relative url for that directory 
*/
	{
		global $qa_layers;
		
		if (isset($qa_layers[$name]))
			qa_fatal_error('A layer named '.$name.' already exists. Please check there are no duplicate plugins.');
			
		$qa_layers[$name]=array(
			'directory' => $directory,
			'urltoroot' => $urltoroot,
			'include' => $include,
		);
	}

	
	function qa_register_plugin_layer($include, $name)
/*
	Register a plugin layer named $name, defined in file $include. Can only be called from a plugin's qa-plugin.php file
*/
	{
		global $qa_plugin_directory, $qa_plugin_urltoroot;
		
		if (empty($qa_plugin_directory) || empty($qa_plugin_urltoroot))
			qa_fatal_error('qa_register_plugin_layer() can only be called from a plugin qa-plugin.php file');

		qa_register_layer($include, $name, $qa_plugin_directory, $qa_plugin_urltoroot);
	}
	
	
	function qa_list_module_types()
/*
	Return an array of all the module types for which at least one module has been registered
*/
	{
		global $qa_modules;
		
		return array_keys($qa_modules);
	}

	
	function qa_list_modules($type)
/*
	Return an array of information about registered modules of $type
*/
	{
		global $qa_modules;
		
		return is_array(@$qa_modules[$type]) ? array_keys($qa_modules[$type]) : array();
	}
	
	
	function qa_load_module($type, $name)
/*
	Return an instantiated class for module of $type named $name, whose functions can be called
*/
	{
		global $qa_modules, $qa_root_url_relative;
		
		$module=@$qa_modules[$type][$name];
		
		if (is_array($module)) {
			if (isset($module['object']))
				return $module['object'];
			
			if (strlen(@$module['include']))
				require_once $module['directory'].$module['include'];
			
			if (strlen(@$module['class'])) {
				$object=new $module['class'];
				
				if (method_exists($object, 'load_module'))
					$object->load_module($module['directory'], $qa_root_url_relative.$module['urltoroot']);
				
				$qa_modules[$type][$name]['object']=$object;
				return $object;
			}
		}
		
		return null;
	}
	
	
	$qa_event_reports_suspended=0;
	
	
	function qa_suspend_event_reports($suspend=true)
/*
	Suspend the reporting of events to event modules via qa_report_event(...) if $suspend is
	true, otherwise reinstate it. A counter is kept to allow multiple calls.
*/
	{
		global $qa_event_reports_suspended;
		
		$qa_event_reports_suspended+=($suspend ? 1 : -1);
	}
	
	
	function qa_report_event($event, $userid, $handle, $cookieid, $params=array())
/*
	Send a notification of event $event by $userid, $handle and $cookieid to all event modules, with extra $params
*/
	{
		global $qa_event_reports_suspended;
		
		if ($qa_event_reports_suspended>0)
			return;
		
		require_once QA_INCLUDE_DIR.'qa-app-cookies.php';
		
		$modulenames=qa_list_modules('event');
		
		foreach ($modulenames as $tryname) {
			$trymodule=qa_load_module('event', $tryname);
			
			if (method_exists($trymodule, 'process_event'))
				$trymodule->process_event($event, $userid, $handle, $cookieid, $params);
		}
	}
	

//	Register default editor and viewer modules and other modules and layers via plugin qa-plugin.php files

	qa_register_module('editor', 'qa-editor-basic.php', 'qa_editor_basic', '');
	qa_register_module('viewer', 'qa-viewer-basic.php', 'qa_viewer_basic', '');
	
	$qa_plugin_files=glob(QA_PLUGIN_DIR.'*/qa-plugin.php');
	
	foreach ($qa_plugin_files as $pluginfile)
		if (file_exists($pluginfile)) {
			if (preg_match('/Plugin[ \t]*Minimum[ \t]*Question2Answer[ \t]*Version\:[ \t]*([0-9\.]+)\s/i', file_get_contents($pluginfile), $matches))
				if ( ((float)QA_VERSION>0) && ($matches[1]>(float)QA_VERSION) )
					continue; // skip plugin which requires a later version
			
			$qa_plugin_directory=dirname($pluginfile).'/';
			$qa_plugin_urltoroot=substr($qa_plugin_directory, strlen(QA_BASE_DIR));
			
			@include_once $pluginfile;
			
			unset($qa_plugin_directory);
			unset($qa_plugin_urltoroot);
		}
	

/*
	Omit PHP closing tag to help avoid accidental output
*/