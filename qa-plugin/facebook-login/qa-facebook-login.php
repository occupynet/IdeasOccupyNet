<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-plugin/facebook-login/qa-facebook-login.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Login module class for Facebook login plugin


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

	class qa_facebook_login {
		
		var $directory;
		var $urltoroot;
		
		function load_module($directory, $urltoroot)
		{
			$this->directory=$directory;
			$this->urltoroot=$urltoroot;
		}
		
		function check_login()
		{
			// Based on sample code: http://developers.facebook.com/docs/guides/web
			
			$testfacebook=false;
			
			foreach ($_COOKIE as $key => $value)
				if (substr($key, 0, 4)=='fbs_')
					$testfacebook=true;
					
			if (!$testfacebook) // to save making a database query for qa_opt() if there's no point
				return;
			
			$app_id=qa_opt('facebook_app_id');
			$app_secret=qa_opt('facebook_app_secret');
			
			if (!(strlen($app_id) && strlen($app_secret)))
				return;
			
			if (isset($_COOKIE['fbs_'.$app_id])) {
				$args = array();
				parse_str(trim($_COOKIE['fbs_'.$app_id], '\\"'), $args);
				ksort($args);

				$payload = '';
				foreach ($args as $key => $value)
					if ($key != 'sig')
						$payload.=$key.'='.$value;
						
				if (md5($payload.qa_opt('facebook_app_secret'))==$args['sig']) {
					$rawuser=qa_retrieve_url('https://graph.facebook.com/me?access_token='.$args['access_token'].'&fields=email,name,verified,location,website,about,picture');
					
					if (strlen($rawuser)) {
						require_once $this->directory.'JSON.php';
						
						$json=new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
						$user=$json->decode($rawuser);
						
						if (is_array($user))
							qa_log_in_external_user('facebook', $args['uid'], array(
								'email' => @$user['email'],
								'handle' => @$user['name'],
								'confirmed' => @$user['verified'],
								'name' => @$user['name'],
								'location' => @$user['location']['name'],
								'website' => @$user['website'],
								'about' => @$user['about'],
								'avatar' => strlen(@$user['picture']) ? qa_retrieve_url($user['picture']) : null,
							));

					}
				}
			}
		}
		
		function match_source($source)
		{
			return $source=='facebook';
		}
		
		function login_html($tourl, $context)
		{
			$app_id=qa_opt('facebook_app_id');

			if (!strlen($app_id))
				return;

?>		
<div id="fb-root"></div>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script>
	FB.init({appId: <?php echo qa_js($app_id)?>, status: true, cookie: true, xfbml: true});
	FB.Event.subscribe('auth.sessionChange', function(response) {
		window.location=<?php echo qa_js($tourl)?>;
	});
</script>
<fb:login-button perms="email,user_about_me,user_location,user_website"></fb:login-button>
<?php

		}
		
		function logout_html($tourl)
		{
			$app_id=qa_opt('facebook_app_id');

			if (!strlen($app_id))
				return;
				
			if (isset($_COOKIE['fbs_'.$app_id])) { // check we still have a Facebook cookie ...

?>		
<span id="fb-root"></span>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script>
	FB.init({appId: <?php echo qa_js($app_id)?>, status: true, cookie: true, xfbml: true});
	FB.Event.subscribe('auth.sessionChange', function(response) {
		window.location=<?php echo qa_js($tourl)?>;
	});
</script>
<fb:login-button autologoutlink="true"></fb:login-button>
<?php

			} else // ... if not, show a standard logout link, since sometimes the redirect to Q2A's logout page doesn't complete
				echo '<A HREF="'.qa_html($tourl).'">'.qa_lang_html('main/nav_logout').'</A>';
		}
		
		function admin_form()
		{
			$saved=false;
			
			if (qa_clicked('facebook_save_button')) {
				qa_opt('facebook_app_id', qa_post_text('facebook_app_id_field'));
				qa_opt('facebook_app_secret', qa_post_text('facebook_app_secret_field'));
				$saved=true;
			}
			
			$ready=strlen(qa_opt('facebook_app_id')) && strlen(qa_opt('facebook_app_secret'));
			
			return array(
				'ok' => $saved ? 'Facebook application details saved' : null,
				
				'fields' => array(
					array(
						'label' => 'Your Facebook App ID:',
						'value' => qa_html(qa_opt('facebook_app_id')),
						'tags' => 'NAME="facebook_app_id_field"',
					),

					array(
						'label' => 'Your Facebook App Secret:',
						'value' => qa_html(qa_opt('facebook_app_secret')),
						'tags' => 'NAME="facebook_app_secret_field"',
						'error' => $ready ? null : 'To use Facebook Login, please <A HREF="http://developers.facebook.com/setup/" TARGET="_blank">set up a Facebook application</A>.',
					),
				),
				
				'buttons' => array(
					array(
						'label' => 'Save Changes',
						'tags' => 'NAME="facebook_save_button"',
					),
				),
			);
		}
		
	};
	

/*
	Omit PHP closing tag to help avoid accidental output
*/