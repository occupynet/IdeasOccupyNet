<?php
	
/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-app-limits.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Monitoring and rate-limiting user actions (application level)


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


	function qa_limits_remaining($userid, $actioncode)
/*
	Return how many more times user $userid and/or the requesting IP can perform $actioncode this hour,
	where $actioncode is Q/A/C/V/L/U/F/M for posting a question, answer, comment, voting or logging in,
	uploading a file, flagging a post, or sending a private message.
*/
	{
		require_once QA_INCLUDE_DIR.'qa-app-options.php';
		require_once QA_INCLUDE_DIR.'qa-db-limits.php';

		$period=(int)(qa_opt('db_time')/3600);
		$dblimits=qa_db_limits_get($userid, @$_SERVER['REMOTE_ADDR'], $actioncode);
		
		switch ($actioncode) {
			case 'Q':
				$userlimit=qa_opt('max_rate_user_qs');
				$iplimit=qa_opt('max_rate_ip_qs');
				break;
				
			case 'A':
				$userlimit=qa_opt('max_rate_user_as');
				$iplimit=qa_opt('max_rate_ip_as');
				break;
				
			case 'C':
				$userlimit=qa_opt('max_rate_user_cs');
				$iplimit=qa_opt('max_rate_ip_cs');
				break;

			case 'V':
				$userlimit=qa_opt('max_rate_user_votes');
				$iplimit=qa_opt('max_rate_ip_votes');
				break;
				
			case 'L':
				$userlimit=1; // not really relevant
				$iplimit=qa_opt('max_rate_ip_logins');
				break;
				
			case 'U':
				$userlimit=qa_opt('max_rate_user_uploads');
				$iplimit=qa_opt('max_rate_ip_uploads');
				break;
				
			case 'F':
				$userlimit=qa_opt('max_rate_user_flags');
				$iplimit=qa_opt('max_rate_ip_flags');
				break;
				
			case 'M':
				$userlimit=qa_opt('max_rate_user_messages');
				$iplimit=qa_opt('max_rate_ip_messages');
				break;
		}
		
		return max(0, min(
			$userlimit-((@$dblimits['user']['period']==$period) ? $dblimits['user']['count'] : 0),
			$iplimit-((@$dblimits['ip']['period']==$period) ? $dblimits['ip']['count'] : 0)
		));
	}
	
	
	function qa_is_ip_blocked()
/*
	Return whether the requesting IP address has been blocked from write operations
*/
	{
		$blockipclauses=qa_block_ips_explode(qa_opt('block_ips_write'));
		
		foreach ($blockipclauses as $blockipclause)
			if (qa_block_ip_match(@$_SERVER['REMOTE_ADDR'], $blockipclause))
				return true;
				
		return false;
	}

	
	function qa_block_ips_explode($blockipstring)
/*
	Return an array of the clauses within $blockipstring, each of which can contain hyphens or asterisks
*/
	{
		$blockipstring=preg_replace('/\s*\-\s*/', '-', $blockipstring); // special case for 'x.x.x.x - x.x.x.x'
	
		return preg_split('/[^0-9\.\-\*]/', $blockipstring, -1, PREG_SPLIT_NO_EMPTY);
	}

	
	function qa_block_ip_match($ip, $blockipclause)
/*
	Returns whether the ip address $ip is matched by the clause $blockipclause, which can contain a hyphen or asterisk
*/
	{
		if (long2ip(ip2long($ip))==$ip) {
			if (preg_match('/^(.*)\-(.*)$/', $blockipclause, $matches)) {
				if ( (long2ip(ip2long($matches[1]))==$matches[1]) && (long2ip(ip2long($matches[2]))==$matches[2]) ) {
					$iplong=sprintf('%u', ip2long($ip));
					$end1long=sprintf('%u', ip2long($matches[1]));
					$end2long=sprintf('%u', ip2long($matches[2]));
					
					return (($iplong>=$end1long) && ($iplong<=$end2long)) || (($iplong>=$end2long) && ($iplong<=$end1long));
				}
	
			} elseif (strlen($blockipclause))
				return preg_match('/^'.str_replace('\\*', '[0-9]+', preg_quote($blockipclause, '/')).'$/', $ip) > 0;
					// preg_quote misses hyphens but that is OK here
		}
			
		return false;
	}
	
	
	function qa_report_write_action($userid, $cookieid, $action, $questionid, $answerid, $commentid)
/*
	Called after a database write $action performed by a user identified by $userid and/or $cookieid,
	relating to $questionid, $answerid and/or $commentid.
*/
	{
		switch ($action) {
			case 'q_post':
			case 'q_claim':
				qa_limits_increment($userid, 'Q');
				break;
			
			case 'a_post':
			case 'a_claim':
				qa_limits_increment($userid, 'A');
				break;
				
			case 'c_post':
			case 'c_claim':
			case 'a_to_c':
				qa_limits_increment($userid, 'C');
				break;
			
			case 'q_vote_up':
			case 'q_vote_down':
			case 'q_vote_nil':
			case 'a_vote_up':
			case 'a_vote_down':
			case 'a_vote_nil':
				qa_limits_increment($userid, 'V');
				break;
				
			case 'q_flag':
			case 'a_flag':
			case 'c_flag':
				qa_limits_increment($userid, 'F');
				break;
		}

		if (isset($userid)) {
			require_once QA_INCLUDE_DIR.'qa-app-users.php';
			
			qa_user_report_action($userid, $action, $questionid, $answerid, $commentid);
		}
		
		if (isset($cookieid)) {
			require_once QA_INCLUDE_DIR.'qa-app-cookies.php';

			qa_cookie_report_action($cookieid, $action, $questionid, $answerid, $commentid);
		}
	}

	
	function qa_limits_increment($userid, $actioncode)
/*
	Take note for rate limits that user $userid and/or the requesting IP just performed $actioncode
*/
	{
		require_once QA_INCLUDE_DIR.'qa-db-limits.php';

		$period=(int)(qa_opt('db_time')/3600);
		
		if (isset($userid))
			qa_db_limits_user_add($userid, $actioncode, $period, 1);
		
		qa_db_limits_ip_add(@$_SERVER['REMOTE_ADDR'], $actioncode, $period, 1);
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/