<?php

/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-db-users.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Database-level access to user management tables (if not using single sign-on)


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


	function qa_db_calc_passcheck($password, $salt)
/*
	Return the expected value for the passcheck column given the $password and password $salt
*/
	{
		return sha1(substr($salt, 0, 8).$password.substr($salt, 8));
	}
	

	function qa_db_user_create($email, $password, $handle, $level, $ip)
/*
	Create a new user in the database with $email, $password, $handle, privilege $level, and $ip address
*/
	{
		require_once QA_INCLUDE_DIR.'qa-util-string.php';
		
		$salt=isset($password) ? qa_random_alphanum(16) : null;
		
		qa_db_query_sub(
			'INSERT INTO ^users (created, createip, email, passsalt, passcheck, level, handle, loggedin, loginip) '.
			'VALUES (NOW(), COALESCE(INET_ATON($), 0), $, $, UNHEX($), #, $, NOW(), COALESCE(INET_ATON($), 0))',
			$ip, $email, $salt, isset($password) ? qa_db_calc_passcheck($password, $salt) : null, (int)$level, $handle, $ip
		);
		
		return qa_db_last_insert_id();
	}

        function qa_db_user_logical_delete($userid)
/*
        Delete the user in the database with $userid
*/
        {
                $count_guests=qa_db_read_one_value(qa_db_query_sub(
                               'SELECT COUNT(*) FROM ^users WHERE handle like "guest%"'
                              ));   
                $new_handle="guest".($count_guests + 1);

                qa_db_query_sub(
                        'UPDATE ^users SET createip=null,email=null,handle="'.$new_handle.'",avatarblobid=null,avatarwidth=null,avatarheight=null,passsalt=null,passcheck=null,level=null,loggedin=null,loginip=null,writeip=null,emailcode="",sessioncode="",sessionsource="" WHERE userid='.$userid
                );

                qa_db_query_sub(
                        'DELETE FROM ^userprofile WHERE userid='.$userid
                );

        }       
		
	function qa_db_user_find_by_email($email)
/*
	Return the ids of all users in the database which match $email (should be one or none)
*/
	{
		return qa_db_read_all_values(qa_db_query_sub(
			'SELECT userid FROM ^users WHERE email=$',
			$email
		));
	}


	function qa_db_user_find_by_handle($handle)
/*
	Return the ids of all users in the database which match $handle (=username), should be one or none
*/
	{
		return qa_db_read_all_values(qa_db_query_sub(
			'SELECT userid FROM ^users WHERE handle=$',
			$handle
		));
	}
	

	function qa_db_user_set($userid, $field, $value)
/*
	Set $field of $userid to $value in the database users table
*/
	{
		qa_db_query_sub(
			'UPDATE ^users SET '.qa_db_escape_string($field).'=$ WHERE userid=$',
			$value, $userid
		);
	}


	function qa_db_user_set_password($userid, $password)
/*
	Set the password of $userid to $password, and reset their salt at the same time
*/
	{
		require_once QA_INCLUDE_DIR.'qa-util-string.php';
		
		$salt=qa_random_alphanum(16);

		qa_db_query_sub(
			'UPDATE ^users SET passsalt=$, passcheck=UNHEX($) WHERE userid=$',
			$salt, qa_db_calc_passcheck($password, $salt), $userid
		);
	}
	

	function qa_db_user_set_flag($userid, $flag, $set)
/*
	Switch on the $flag bit of the flags column for $userid if $set is true, or switch off otherwise
*/
	{
		qa_db_query_sub(
			'UPDATE ^users SET flags=flags'.($set ? '|' : '&~').'# WHERE userid=$',
			$flag, $userid
		);
	}

	
	function qa_db_user_rand_emailcode()
/*
	Return a random string to be used for a user's emailcode column
*/
	{
		require_once QA_INCLUDE_DIR.'qa-util-string.php';
		
		return qa_random_alphanum(8);
	}
	

	function qa_db_user_rand_sessioncode()
/*
	Return a random string to be used for a user's sessioncode column (for browser session cookies)
*/
	{
		require_once QA_INCLUDE_DIR.'qa-util-string.php';
		
		return qa_random_alphanum(8);
	}

	
	function qa_db_user_profile_set($userid, $field, $value)
/*
	Set a row in the database user profile table to store $value for $field for $userid
*/
	{
		qa_db_query_sub(
			'REPLACE ^userprofile (title, content, userid) VALUES ($, $, $)',
			$field, $value, $userid
		);
	}

	
	function qa_db_user_logged_in($userid, $ip)
/*
	Note in the database that $userid just logged in from $ip address
*/
	{
		qa_db_query_sub(
			'UPDATE ^users SET loggedin=NOW(), loginip=COALESCE(INET_ATON($), 0) WHERE userid=$',
			$ip, $userid
		);
	}
	

	function qa_db_user_written($userid, $ip)
/*
	Note in the database that $userid just performed a write operation from $ip address
*/
	{
		qa_db_query_sub(
			'UPDATE ^users SET written=NOW(), writeip=COALESCE(INET_ATON($), 0) WHERE userid=$',
			$ip, $userid
		);
	}
	
	
	function qa_db_user_login_add($userid, $source, $identifier)
/*
	Add an external login in the database for $source and $identifier for user $userid
*/
	{
		qa_db_query_sub(
			'INSERT INTO ^userlogins (userid, source, identifier, identifiermd5) '.
			'VALUES ($, $, $, UNHEX($))',
			$userid, $source, $identifier, md5($identifier)
		);
	}
	

	function qa_db_user_login_find($source, $identifier)
/*
	Return some information about the user with external login $source and $identifier in the database, if a match is found
*/
	{
		return qa_db_read_all_assoc(qa_db_query_sub(
			'SELECT ^userlogins.userid, handle, email FROM ^userlogins LEFT JOIN ^users ON ^userlogins.userid=^users.userid '.
			'WHERE source=$ AND identifiermd5=UNHEX($) AND identifier=$',
			$source, md5($identifier), $identifier
		));
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/
