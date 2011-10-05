/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-content/qa-admin.js
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: JS for admin pages to handle Ajax-triggered recalculations


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

var qa_recalc_running=0;

window.onbeforeunload=function(event)
{
	if (qa_recalc_running>0) {
		event=event||window.event;
		var message=qa_warning_recalc;
		event.returnValue=message;
		return message;
	}
}

function qa_recalc_click(state, elem, value, noteid)
{
	if (elem.qa_recalc_running) {
		elem.qa_recalc_stopped=true;
	
	} else {
		elem.qa_recalc_running=true;
		elem.qa_recalc_stopped=false;
		qa_recalc_running++;
		
		document.getElementById(noteid).innerHTML='';
		elem.qa_original_value=elem.value;
		if (value)
			elem.value=value;
		
		qa_recalc_update(elem, state, noteid);
	}
	
	return false;
}

function qa_recalc_update(elem, state, noteid)
{
	if (state)
		qa_ajax_post('recalc', {state:state},
			function(lines) {
				if (lines[0]=='1') {
					if (lines[2])
						document.getElementById(noteid).innerHTML=lines[2];
					
					if (elem.qa_recalc_stopped)
						qa_recalc_cleanup(elem);
					else
						qa_recalc_update(elem, lines[1], noteid);
				
				} else if (lines[0]=='0') {
					document.getElementById(noteid).innerHTML=lines[2];
					qa_recalc_cleanup(elem);
				
				} else {
					alert('Unexpected response from server - please try again.');
					qa_recalc_cleanup(elem);
				}
			}
		);

	else
		qa_recalc_cleanup(elem);
}

function qa_recalc_cleanup(elem)
{
	elem.value=elem.qa_original_value;
	elem.qa_recalc_running=null;
	qa_recalc_running--;
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