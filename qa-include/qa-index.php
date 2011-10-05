<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-index.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: The Grand Central of Q2A - most requests come through here


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

//	Try our best to set base path here just in case it wasn't set in index.php (pre version 1.0.1)

	if (!defined('QA_BASE_DIR'))
		define('QA_BASE_DIR', dirname(empty($_SERVER['SCRIPT_FILENAME']) ? dirname(__FILE__) : $_SERVER['SCRIPT_FILENAME']).'/');


//	If this is an Ajax request, branch off here

	if (@$_POST['qa']=='ajax') {
		require 'qa-ajax.php';
		return;
	}


//	If this is a direct blob request, branch off here

	if (@$_GET['qa']=='blob') {
		require 'qa-blob.php';
		return;
	}


//	Load the QA base file which sets up a bunch of crucial functions
	
	require 'qa-base.php';

	
//	Determine the request and root of the installation, and the requested start position used by many pages
	
	$relativedepth=0;
	$rootpath=strtr(dirname($_SERVER['PHP_SELF']), '\\', '/');
	
	if (isset($_GET['qa-rewrite'])) { // URLs rewritten by .htaccess
		$qa_used_url_format=QA_URL_FORMAT_NEAT;
		$requestparts=explode('/', qa_gpc_to_string($_GET['qa-rewrite']));
		unset($_GET['qa-rewrite']);
		$relativedepth=count($requestparts);
		
		// Workaround for fact that Apache unescapes characters while rewriting, based on assumption that $_GET['qa-rewrite'] has
		// right path depth, which is true do long as there are only escaped characters in the last part of the path
		if (!empty($_SERVER['REQUEST_URI'])) {
			$origpath=$_SERVER['REQUEST_URI'];
			$_GET=array();
			
			$questionpos=strpos($origpath, '?');
			if (is_numeric($questionpos)) {
				$params=explode('&', substr($origpath, $questionpos+1));
				
				foreach ($params as $param)
					if (preg_match('/^([^\=]*)(\=(.*))?$/', $param, $matches))
						$_GET[urldecode($matches[1])]=qa_string_to_gpc(urldecode(@$matches[3]));

				$origpath=substr($origpath, 0, $questionpos);
			}
			
			$requestparts=array_slice(explode('/', urldecode($origpath)), -count($requestparts));
		}
		
	} elseif (isset($_GET['qa'])) {
		if (strpos($_GET['qa'], '/')===false) {
			$qa_used_url_format=( (empty($_SERVER['REQUEST_URI'])) || (strpos($_SERVER['REQUEST_URI'], '/index.php')!==false) )
				? QA_URL_FORMAT_SAFEST : QA_URL_FORMAT_PARAMS;
			$requestparts=array(qa_gpc_to_string($_GET['qa']));
			
			for ($part=1; $part<10; $part++)
				if (isset($_GET['qa_'.$part])) {
					$requestparts[]=qa_gpc_to_string($_GET['qa_'.$part]);
					unset($_GET['qa_'.$part]);
				}
		
		} else {
			$qa_used_url_format=QA_URL_FORMAT_PARAM;
			$requestparts=explode('/', qa_gpc_to_string($_GET['qa']));
		}
		
		unset($_GET['qa']);
	
	} else {
		$phpselfunescaped=strtr($_SERVER['PHP_SELF'], '+', ' '); // seems necessary, and plus does not work with this scheme
		$indexpath='/index.php/';
		$indexpos=strpos($phpselfunescaped, $indexpath);
		
		if (is_numeric($indexpos)) {
			$qa_used_url_format=QA_URL_FORMAT_INDEX;
			$requestparts=explode('/', substr($phpselfunescaped, $indexpos+strlen($indexpath)));
			$relativedepth=1+count($requestparts);
			$rootpath=substr($phpselfunescaped, 0, $indexpos);
	
		} else {
			$qa_used_url_format=null; // at home page so can't identify path type
			$requestparts=array();
		}
	}
	
	foreach ($requestparts as $part => $requestpart) // remove any blank parts
		if (!strlen($requestpart))
			unset($requestparts[$part]);
			
	reset($requestparts);
	$key=key($requestparts);
	
	if (isset($QA_CONST_PATH_MAP)) {
		$replacement=array_search(@$requestparts[$key], $QA_CONST_PATH_MAP);
		
		if ($replacement!==false)
			$requestparts[$key]=$replacement;
	}

	$qa_request=implode('/', $requestparts);
	$qa_request_lc=strtolower($qa_request);

	$qa_request_parts=explode('/', $qa_request);
	$qa_request_lc_parts=explode('/', $qa_request_lc);

	$qa_root_url_relative=($relativedepth>1) ? str_repeat('../', $relativedepth-1) : './';
	$qa_root_url_inferred='http://'.@$_SERVER['HTTP_HOST'].$rootpath;
	
	if (substr($qa_root_url_inferred, -1)!='/')
		$qa_root_url_inferred.='/';
	
	
//	Check for install or url test pages

	if ($qa_request_lc=='install')
		require QA_INCLUDE_DIR.'qa-install.php';
		
	elseif ($qa_request_lc==('url/test/'.QA_URL_TEST_STRING))
		require QA_INCLUDE_DIR.'qa-url-test.php';
	
	elseif ($qa_request_lc_parts[0]=='image')
		require QA_INCLUDE_DIR.'qa-image.php';
	
	else {

	//	Enable gzip compression for output (needs to come early)

		if (defined('QA_HTML_COMPRESSION') ? QA_HTML_COMPRESSION : true) // on by default
			if ($qa_request_lc!='admin/recalc') // not for lengthy processes
				if (extension_loaded('zlib') && !headers_sent())
					ob_start('ob_gzhandler');
					
	//	Route to appropriate file based on whether this is a feed request or normal page
			
		if ($qa_request_lc_parts[0]=='feed')
			require QA_INCLUDE_DIR.'qa-feed.php';
		else
			require QA_INCLUDE_DIR.'qa-page.php';
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/