<?php
	
/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-page-admin-plugins.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Controller for admin page listing plugins and showing their options


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

	require_once QA_INCLUDE_DIR.'qa-app-admin.php';

	
//	Check admin privileges (do late to allow one DB query)

	if (!qa_admin_check_privileges($qa_content))
		return $qa_content;
		
		
//	Prepare content for theme
	
	$qa_content=qa_content_prepare();

	$qa_content['title']=qa_lang_html('admin/admin_title').' - '.qa_lang_html('admin/plugins_title');
	
	$qa_content['error']=qa_admin_page_error();
	
	if (count($qa_plugin_files)) {
		$qa_content['form']=array(
			'style' => 'tall',
			
			'fields' => array(
				'plugins' => array(
					'type' => 'custom',
					'label' => qa_lang_html('admin/installed_plugins'),
					'html' => '',
				),		
			),
		);
		
		$metafields=array(
			'name' => 'Plugin Name',
			'uri' => 'Plugin URI',
			'description' => 'Plugin Description',
			'version' => 'Plugin Version',
			'date' => 'Plugin Date',
			'author' => 'Plugin Author',
			'author_uri' => 'Plugin Author URI',
			'license' => 'Plugin License',
			'min_q2a' => 'Plugin Minimum Question2Answer Version',
		);
			
		foreach ($qa_plugin_files as $pluginfile) {
			$contents=file_get_contents($pluginfile);
			$metadata=array();
	
			foreach ($metafields as $key => $fieldname)
				if (preg_match('/'.str_replace(' ', '[ \t]*', preg_quote($fieldname, '/')).':[ \t]*([^\n\f]*)[\n\f]/i', $contents, $matches))
					$metadata[$key]=trim($matches[1]);
					
			if (strlen(@$metadata['name']))
				$namehtml=qa_html($metadata['name']);
			else
				$namehtml=qa_lang_html('admin/unnamed_plugin');
				
			if (strlen(@$metadata['uri']))
				$namehtml='<A HREF="'.qa_html($metadata['uri']).'">'.$namehtml.'</A>';
			
			$namehtml='<B>'.$namehtml.'</B>';
				
			if (strlen(@$metadata['version']))
				$namehtml.=' '.qa_html($metadata['version']);
				
			if (strlen(@$metadata['author'])) {
				$authorhtml=qa_html($metadata['author']);
				
				if (strlen(@$metadata['author_uri']))
					$authorhtml='<A HREF="'.qa_html($metadata['author_uri']).'">'.$authorhtml.'</A>';
					
				$authorhtml=qa_lang_html_sub('main/by_x', $authorhtml);
				
			} else
				$authorhtml='';
				
			if (strlen(@$metadata['description']))
				$deschtml=qa_html($metadata['description']).'<BR>';
			else
				$deschtml='';
				
			$pluginhtml=$namehtml.' '.$authorhtml.'<BR>'.$deschtml.'<SMALL STYLE="color:#666">'.qa_html(dirname($pluginfile).'/').'</SMALL>';
				
			if (is_numeric($metadata['min_q2a']) && ((float)QA_VERSION>0) && $metadata['min_q2a']>(float)QA_VERSION)
				$pluginhtml='<STRIKE STYLE="color:#999">'.$pluginhtml.'</STRIKE><BR><SPAN STYLE="color:#f00">'.
					qa_lang_html_sub('admin/requires_q2a_version', qa_html($metadata['min_q2a'])).'</SPAN>';
				
			$qa_content['form']['fields'][]=array(
				'type' => 'custom',
				'html' => $pluginhtml,
			);
		}
	}
	
	$formadded=false;
	
	$moduletypes=qa_list_module_types();
	
	foreach ($moduletypes as $type) {
		$modulenames=qa_list_modules($type);
		
		foreach ($modulenames as $name) {
			$module=qa_load_module($type, $name);
			
			if (method_exists($module, 'admin_form')) {
				$form=$module->admin_form($qa_content);

				if (!isset($form['title']))
					$form['title']=qa_html($name);
				
				$identifierhtml=qa_html(md5($type.'/'.$name));
				
				$form['title']='<A NAME="'.$identifierhtml.'">'.$form['title'].'</A>';
					
				if (!isset($form['tags']))
					$form['tags']='METHOD="POST" ACTION="'.qa_self_html().'#'.$identifierhtml.'"';
				
				if (!isset($form['style']))
					$form['style']='tall';
					
				$qa_content['form_'.$type.'_'.$name]=$form;
				$formadded=true;
			}
		}
	}
		
	if (!$formadded)
		$qa_content['suggest_next']=qa_lang_html('admin/no_plugin_options');
	

	$qa_content['navigation']['sub']=qa_admin_sub_navigation();
	
	return $qa_content;
	

/*
	Omit PHP closing tag to help avoid accidental output
*/