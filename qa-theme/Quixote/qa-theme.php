<?php

/*
	Question2Answer 1.3.3 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-theme/Candy/qa-theme.php
	Version: 1.3.3
	Date: 2011-03-16 12:46:02 GMT
	Description: Override something in base theme class for Candy theme


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

	class qa_html_theme extends qa_html_theme_base
	{
		function nav_user_search() // reverse the usual order
		{
			$this->search();
			$this->nav('user');
		}

                function q_view_content($q_view)
                {
                  
                        if (!empty($q_view['content']))
                                $this->output(
                                        '<DIV CLASS="qa-q-view-content">',
                                        $q_view['content'],
                                        '</DIV>'
                                );

                        // Sociable
                        $question_url = str_replace("..","","http://".$_SERVER['SERVER_NAME'].$q_view['url']);
                        $question_text = 
                        $fb_meta = '  <meta property="og:title" content="'.strip_tags($q_view["title"]).'"/>
                          <meta property="og:site_name" content="Occupy Ideas"/>
                          <meta property="og:url" content="'.$question_url.'"/>
                          <meta property="og:image" content="http://occupywallstreet.net/bloombergbucks/images/bloombuck.png"/>
                          <meta property="og:description" content="'.addslashes(strip_tags($q_view["content"])).'"/>';
                          
                        $share_facebook = $fb_meta."
                        <li><a href=\"http://www.facebook.com/sharer.php?u=".$question_url."\" target=\"_blank\" class=\"sharefacebook\" title=\"Share on Facebook\">Share on Facebook</a></li>";
                        
                        $share_twitter = "<li><a href=\"http://twitter.com/share?text=".addslashes(strip_tags($q_view["title"]))." %20%23occupyideas%20%23ows%20&url=".$question_url."\" target=\"_blank\" class=\"sharetwitter\" title=\"Share on Twitter\">Share on Twitter</a></li>";

                        $this->output(
                          '<DIV class="sociable">',
                            '<UL>',
                            $share_facebook." ".$share_twitter,
                            '</UL>',
		          '</DIV><br />' 
                        );
                }

                function main()
                                     {
                        $content=$this->content;
                         
                        // Adding class role if user page(user/%user%)
                        if(strpos($content['script_var']['qa_request'],'user/') === false)
                          $extraclass="";
                        else{
                          $extraclass = $content['form_profile']['fields']['level']['value'];                         
                          if(strpos($extraclass,'<A') === false){
                           $extraclass = " qa-main-".str_replace(' ','-',$extraclass);
                          }else{
                           $extraclass = " qa-main-".str_replace(' ','-',substr($extraclass,0,strpos($extraclass,'<A') - 3));
                          }
                        }


                        $this->output('<DIV CLASS="qa-main'.$extraclass.(@$this->content['hidden'] ? ' qa-main-hidden' : '').'">');

                        $this->widgets('main', 'top');

                        $this->page_title();
                        $this->page_error();

                        $this->widgets('main', 'high');

                        if (isset($content['main_form_tags']))
                                $this->output('<FORM '.$content['main_form_tags'].'>');

                        $this->main_parts($content);

                        if (isset($content['main_form_tags']))
                                $this->output('</FORM>');

                        $this->widgets('main', 'low');

                        $this->page_links();
                        $this->suggest_next();

                        $this->widgets('main', 'bottom');

                        $this->output('</DIV> <!-- END qa-main -->', '');
                }
           
                function q_list_item($question)
                {
                        // Adding class role
                        $this->output('<DIV CLASS="qa-q-list-item qa-q-list-item-'.str_replace(' ','-',@$question['who']['level']).rtrim(' '.@$question['classes']).'" '.@$question['tags'].'>');

                        $this->q_item_stats($question);
                        $this->q_item_main($question);
                        $this->q_item_clear();

                        $this->output('</DIV> <!-- END qa-q-list-item -->', '');
                }

                function q_item_stats($question)
                {
                        $this->output('<DIV CLASS="qa-q-item-stats">');

                        $this->voting($question);
                        $this->a_count($question);

                        $this->output('</DIV>');
                }

                function q_view_main($q_view)
                {
                        // Adding class role
                        $this->output('<DIV CLASS="qa-q-view-main qa-q-view-main-'.str_replace(' ','-',@$q_view['who']['level']).'">');

                        $this->q_view_content($q_view);
                        $this->q_view_follows($q_view);
                        $this->post_tags($q_view, 'qa-q-view');
                        $this->post_avatar($q_view, 'qa-q-view');
                        $this->post_meta($q_view, 'qa-q-view');
                        $this->q_view_buttons($q_view);
                        $this->c_list(@$q_view['c_list'], 'qa-q-view');
                        $this->form(@$q_view['a_form']);
                        $this->c_list(@$q_view['a_form']['c_list'], 'qa-a-item');
                        $this->form(@$q_view['c_form']);

                        $this->output('</DIV> <!-- END qa-q-view-main -->');
                }

                function a_list_item($a_item)
                {
                        // Adding class role
                        $extraclass=@$a_item['classes'].($a_item['hidden'] ? ' qa-a-list-item-hidden' : ($a_item['selected'] ? ' qa-a-list-item-selected' : ''));

                        $this->output('<DIV CLASS="qa-a-list-item qa-a-list-item-'.str_replace(' ','-',@$a_item['who']['level'])." ".$extraclass.'" '.@$a_item['tags'].'>');

                        $this->voting($a_item);
                        $this->a_item_main($a_item);
                        $this->a_item_clear();

                        $this->output('</DIV> <!-- END qa-a-list-item -->', '');
                }

                function c_list_item($c_item)
                {
                        // Adding class role
                        $extraclass=@$c_item['classes'].($c_item['hidden'] ? ' qa-c-item-hidden' : '');

                        $this->output('<DIV CLASS="qa-c-list-item qa-c-list-item-'.str_replace(' ','-',@$c_item['who']['level'])." ".$extraclass.'" '.@$c_item['tags'].'>');
                        $this->c_item_main($c_item);
                        $this->c_item_clear();
                        $this->output('</DIV> <!-- END qa-c-item -->');
                }

	}
	

/*
	Omit PHP closing tag to help avoid accidental output
*/
