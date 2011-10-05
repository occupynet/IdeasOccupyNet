<?php
	
/*
	Question2Answer 1.4 (c) 2011, Gideon Greenspan

	http://www.question2answer.org/

	
	File: qa-include/qa-db-blobs.php
	Version: 1.4
	Date: 2011-06-13 06:42:43 GMT
	Description: Database-level access to blobs table for large chunks of data (e.g. images)


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


	function qa_db_blob_create($content, $format, $filename=null, $userid=null, $cookieid=null, $ip=null)
/*
	Create a new blob in the database with $content and $format, returning its blobid
*/
	{
		for ($attempt=0; $attempt<10; $attempt++) {
			$blobid=qa_db_random_bigint();
			
			if (qa_db_blob_exists($blobid))
				continue;

			qa_db_query_sub(
				'INSERT INTO ^blobs (blobid, format, content, filename, userid, cookieid, createip, created) VALUES (#, $, $, $, $, #, INET_ATON($), NOW())',
				$blobid, $format, $content, $filename, $userid, $cookieid, $ip
			);
		
			return $blobid;
		}
		
		return null;
	}
	
	
	function qa_db_blob_read($blobid)
/*
	Get the content of blob $blobid from the database
*/
	{
		return qa_db_read_one_assoc(qa_db_query_sub(
			'SELECT content, format, BINARY filename AS filename FROM ^blobs WHERE blobid=#',
			$blobid
		), true);
	}
	
	
	function qa_db_blob_delete($blobid)
/*
	Delete blob $blobid in the database
*/
	{
		qa_db_query_sub(
			'DELETE FROM ^blobs WHERE blobid=#',
			$blobid
		);
	}

	
	function qa_db_blob_exists($blobid)
/*
	Check if blob $blobid exists in the database
*/
	{
		return qa_db_read_one_value(qa_db_query_sub(
			'SELECT COUNT(*) FROM ^blobs WHERE blobid=#',
			$blobid
		)) > 0;
	}


/*
	Omit PHP closing tag to help avoid accidental output
*/