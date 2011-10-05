/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-content/qa-votes.js
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: JS to handle Ajax voting


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

function qa_vote_click(elem, oldvote)
{
	var ens=elem.name.split('_');
	var postid=ens[1];
	var vote=parseInt(ens[2]);
	var anchor=ens[3];
	
	qa_ajax_post('vote', {postid:postid, vote:vote},
		function(lines) {
			if (lines[0]=='1') {
				document.getElementById('voting_'+postid).innerHTML=lines.slice(1).join("\n");

			} else if (lines[0]=='0') {
				var mess=document.getElementById('errorbox');
				
				if (!mess) {
					var mess=document.createElement('div');
					mess.id='errorbox';
					mess.className='qa-error';
					mess.innerHTML=lines[1];
				}
				
				var postelem=document.getElementById(anchor);
				postelem.parentNode.insertBefore(mess, postelem);
			
			} else {
				alert('Unexpected response from server - please try again.');
			}

		}
	);
	
	return false;
}

function qa_ajax_post(operation, params, callback)
{
	jQuery.extend(params, {qa:'ajax', qa_operation:operation, qa_root:qa_root, qa_request:qa_request});
	
	jQuery.post(qa_root, params, function(response) {
		var header='QA_AJAX_RESPONSE';
		var headerpos=response.indexOf(header);
		
		if (headerpos>=0)
			callback(response.substr(headerpos+header.length).replace(/^\s+/, '').split("\n"));
		else
			callback([]);

	}, 'text').error(function() { callback([]) });
}