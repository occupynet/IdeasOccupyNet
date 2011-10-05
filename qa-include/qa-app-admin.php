<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-app-admin.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Functions used in the admin center pages


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

	
	function qa_admin_check_privileges(&$qa_content)
/*
	Return true if user is logged in with admin privileges. If not, return false
	and set up $qa_content with the appropriate title and error message
*/
	{
		global $qa_login_userid, $qa_request;
		
		if (!isset($qa_login_userid)) {
			require_once QA_INCLUDE_DIR.'qa-app-format.php';
			
			$qa_content=qa_content_prepare();

			$qa_content['title']=qa_lang_html('admin/admin_title');
			$qa_content['error']=qa_insert_login_links(qa_lang_html('admin/not_logged_in'), $qa_request);
			
			return false;

		} elseif (qa_get_logged_in_level()<QA_USER_LEVEL_ADMIN) {
			$qa_content=qa_content_prepare();
			
			$qa_content['title']=qa_lang_html('admin/admin_title');
			$qa_content['error']=qa_lang_html('admin/no_privileges');
			
			return false;
		}
		
		return true;
	}
	
	
	function qa_admin_language_options()
/*
	Return a sorted array of available languages, [short code] => [long name]
*/
	{
		$codetolanguage=array(
			'ar' => 'Arabic - العربية',
			'bg' => 'Bulgarian - Български',
			'ca' => 'Catalan - Català',
			'cs' => 'Czech - Čeština',
			'da' => 'Danish - Dansk',
			'de' => 'German - Deutsch',
			'el' => 'Greek - Ελληνικά',
			'en-GB' => 'English (UK)',
			'es' => 'Spanish - Español',
			'et' => 'Estonian - Eesti',
			'fa' => 'Persian - فارسی',
			'fi' => 'Finnish - Suomi',
			'fr' => 'French - Français',
			'he' => 'Hebrew - עברית',
			'hr' => 'Croatian - Hrvatski',
			'hu' => 'Hungarian - Magyar',
			'id' => 'Indonesian - Bahasa Indonesia',
			'is' => 'Icelandic - Íslenska',
			'it' => 'Italian - Italiano',
			'ja' => 'Japanese - 日本語',
			'kh' => 'Khmer - ភាសាខ្មែរ',
			'ko' => 'Korean - 한국어',
			'lt' => 'Lithuanian - Lietuvių',
			'nl' => 'Dutch - Nederlands',
			'no' => 'Norwegian - Norsk',
			'pl' => 'Polish - Polski',
			'pt' => 'Portuguese - Português',
			'ro' => 'Romanian - Română',
			'ru' => 'Russian - Русский',
			'sk' => 'Slovak - Slovenčina',
			'sl' => 'Slovenian - Slovenščina',
			'sr' => 'Serbian - Српски',
			'sv' => 'Swedish - Svenska',
			'th' => 'Thai - ไทย',
			'tr' => 'Turkish - Türkçe',
			'uk' => 'Ukrainian - Українська',
			'vi' => 'Vietnamese - Tiếng Việt',
			'zh' => 'Chinese - 中文',
		);
		
		$options=array('' => 'English (US)');
		
		$directory=@opendir(QA_LANG_DIR);
		if (is_resource($directory)) {
			while (($code=readdir($directory))!==false)
				if (is_dir(QA_LANG_DIR.$code) && isset($codetolanguage[$code]))
					$options[$code]=$codetolanguage[$code];

			closedir($directory);
		}
		
		asort($options, SORT_STRING);
		
		return $options;
	}
	
	
	function qa_admin_theme_options()
/*
	Return a sorted array of available themes, [theme name] => [theme name]
*/
	{
		$options=array();

		$directory=@opendir(QA_THEME_DIR);
		if (is_resource($directory)) {
			while (($theme=readdir($directory))!==false)
				if ( (substr($theme, 0, 1)!='.') && (file_exists(QA_THEME_DIR.$theme.'/qa-theme.php') || file_exists(QA_THEME_DIR.$theme.'/qa-styles.css')) )
					$options[$theme]=$theme;

			closedir($directory);
		}
		
		asort($options, SORT_STRING);
		
		return $options;
	}
	
	
	function qa_admin_place_options()
/*
	Return an array of widget placement options, with keys matching the database value
*/
	{
		return array(
			'FT' => qa_lang_html('options/place_full_top'),
			'FH' => qa_lang_html('options/place_full_below_nav'),
			'FL' => qa_lang_html('options/place_full_below_content'),
			'FB' => qa_lang_html('options/place_full_below_footer'),
			'MT' => qa_lang_html('options/place_main_top'),
			'MH' => qa_lang_html('options/place_main_below_title'),
			'ML' => qa_lang_html('options/place_main_below_lists'),
			'MB' => qa_lang_html('options/place_main_bottom'),
			'ST' => qa_lang_html('options/place_side_top'),
			'SH' => qa_lang_html('options/place_side_below_sidebar'),
			'SL' => qa_lang_html('options/place_side_below_categories'),
			'SB' => qa_lang_html('options/place_side_last'),
		);
	}

	
	function qa_admin_page_size_options($maximum)
/*
	Return an array of page size options up to $maximum, [page size] => [page size]
*/
	{
		$rawoptions=array(5, 10, 15, 20, 25, 30, 40, 50, 60, 80, 100, 120, 150, 200, 250, 300, 400, 500, 600, 800, 1000);
		
		$options=array();
		foreach ($rawoptions as $rawoption) {
			if ($rawoption>$maximum)
				break;
				
			$options[$rawoption]=$rawoption;
		}
		
		return $options;
	}
	
	
	function qa_admin_match_options()
/*
	Return an array of options representing matching precision, [value] => [label]
*/
	{
		return array(
			5 => qa_lang_html('options/match_5'),
			4 => qa_lang_html('options/match_4'),
			3 => qa_lang_html('options/match_3'),
			2 => qa_lang_html('options/match_2'),
			1 => qa_lang_html('options/match_1'),
		);
	}

	
	function qa_admin_permit_options($widest, $narrowest, $doconfirms)
/*
	Return an array of options representing permission restrictions, [value] => [label]
	ranging from $widest to $narrowest. Set $doconfirms to whether email confirmations are on
*/
	{
		require_once QA_INCLUDE_DIR.'qa-app-options.php';
		
		$options=array(
			QA_PERMIT_ALL => qa_lang_html('options/permit_all'),
			QA_PERMIT_USERS => qa_lang_html('options/permit_users'),
			QA_PERMIT_CONFIRMED => qa_lang_html('options/permit_confirmed'),
			QA_PERMIT_POINTS => qa_lang_html('options/permit_points'),
			QA_PERMIT_POINTS_CONFIRMED => qa_lang_html('options/permit_points_confirmed'),
			QA_PERMIT_EXPERTS => qa_lang_html('options/permit_experts'),
			QA_PERMIT_EDITORS => qa_lang_html('options/permit_editors'),
			QA_PERMIT_MODERATORS => qa_lang_html('options/permit_moderators'),
			QA_PERMIT_ADMINS => qa_lang_html('options/permit_admins'),
			QA_PERMIT_SUPERS => qa_lang_html('options/permit_supers'),
		);
		
		foreach ($options as $key => $label)
			if (($key<$narrowest) || ($key>$widest))
				unset($options[$key]);
		
		if (!$doconfirms) {
			unset($options[QA_PERMIT_CONFIRMED]);
			unset($options[QA_PERMIT_POINTS_CONFIRMED]);
		}
			
		return $options;
	}

	
	function qa_admin_sub_navigation()
/*
	Return the sub navigation structure common to admin pages
*/
	{
		$navigation=array(
			'admin$' => array(
				'label' => qa_lang('admin/general_title'),
				'url' => qa_path_html('admin'),
			),
			
			'admin/emails' => array(
				'label' => qa_lang('admin/emails_title'),
				'url' => qa_path_html('admin/emails'),
			),
			
			'admin/user' => array(
				'label' => qa_lang('admin/users_title'),
				'url' => qa_path_html('admin/users'),
			),
			
			'admin/layout' => array(
				'label' => qa_lang('admin/layout_title'),
				'url' => qa_path_html('admin/layout'),
			),
			
			'admin/lists' => array(
				'label' => qa_lang('admin/lists_title'),
				'url' => qa_path_html('admin/lists'),
			),
			
			'admin/viewing' => array(
				'label' => qa_lang('admin/viewing_title'),
				'url' => qa_path_html('admin/viewing'),
			),
			
			'admin/posting' => array(
				'label' => qa_lang('admin/posting_title'),
				'url' => qa_path_html('admin/posting'),
			),
			
			'admin/categories' => array(
				'label' => qa_lang('admin/categories_title'),
				'url' => qa_path_html('admin/categories'),
			),
			
			'admin/permissions' => array(
				'label' => qa_lang('admin/permissions_title'),
				'url' => qa_path_html('admin/permissions'),
			),
			
			'admin/pages' => array(
				'label' => qa_lang('admin/pages_title'),
				'url' => qa_path_html('admin/pages'),
			),
			
			'admin/feeds' => array(
				'label' => qa_lang('admin/feeds_title'),
				'url' => qa_path_html('admin/feeds'),
			),
			
			'admin/points' => array(
				'label' => qa_lang('admin/points_title'),
				'url' => qa_path_html('admin/points'),
			),
			
			'admin/spam' => array(
				'label' => qa_lang('admin/spam_title'),
				'url' => qa_path_html('admin/spam'),
			),

			'admin/hidden' => array(
				'label' => qa_lang('admin/hidden_title'),
				'url' => qa_path_html('admin/hidden'),
			),
			
			'admin/stats' => array(
				'label' => qa_lang('admin/stats_title'),
				'url' => qa_path_html('admin/stats'),
			),
			
			'admin/plugins' => array(
				'label' => qa_lang('admin/plugins_title'),
				'url' => qa_path_html('admin/plugins'),
			),
		);
		
		if (!qa_using_categories())
			unset($navigation['admin/categories']);
			
		return $navigation;
	}
	
	
	function qa_admin_page_error()
/*
	Return the error that needs to displayed on all admin pages, or null if none
*/
	{
		@include_once QA_INCLUDE_DIR.'qa-db-install.php';
		
		if (defined('QA_DB_VERSION_CURRENT') && (qa_opt('db_version')<QA_DB_VERSION_CURRENT))
			return strtr(
				qa_lang_html('admin/upgrade_db'),
				
				array(
					'^1' => '<A HREF="'.qa_path_html('install').'">',
					'^2' => '</A>',
				)
			);
		else
			return null;
	}


	function qa_admin_url_test_html()
/*
	Return an HTML fragment to display for a URL test which has passed
*/
	{
		return '; font-size:9px; color:#060; font-weight:bold; font-family:arial,sans-serif; border-color:#060;">OK<';
	}


	function qa_admin_is_slug_reserved($requestpart)
/*
	Returns whether a URL path beginning with $requestpart is reserved by the engine or a plugin page module
*/
	{
		global $QA_CONST_ROUTING, $QA_CONST_PATH_MAP;
		
		$requestpart=trim(strtolower($requestpart));
		
		if (isset($QA_CONST_ROUTING[$requestpart]) || isset($QA_CONST_ROUTING[$requestpart.'/']) || is_numeric($requestpart))
			return true;
			
		if (isset($QA_CONST_PATH_MAP))
			foreach ($QA_CONST_PATH_MAP as $requestmap)
				if (trim(strtolower($requestmap)) == $requestpart)
					return true;
			
		switch ($requestpart) {
			case '':
			case 'qa':
			case 'feed':
			case 'install':
			case 'url':
			case 'image':
			case 'ajax':
				return true;
		}
		
		$modulenames=qa_list_modules('page');
		
		foreach ($modulenames as $tryname) {
			$trypage=qa_load_module('page', $tryname);

			if (method_exists($trypage, 'match_request') && $trypage->match_request($requestpart))
				return true;
		}
			
		return false;
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/