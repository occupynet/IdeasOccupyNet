<?php
require_once("../qa-include/qa-base.php");
require_once (QA_INCLUDE_DIR."qa-app-users.php");

//Checking if user have permission. Superadmin level.
if(qa_get_logged_in_level() != 120)
 die("You don't have permission. Get out here!");

$result = qa_db_query_raw('SELECT email from qa_mailing where subscriber=1');
while ($row = mysql_fetch_array($result, MYSQL_NUM)){
 $sent = mail($row[0],$_POST['subject'],$_POST['notification'],'From: noreply.propongo@tomalaplaza.net' . "\r\n"."Content-type: text/html\r\n");
}

header('Location: http://'.$_SERVER['SERVER_NAME'].'/qa-external/qa-mailing.php?action=mailed'); 
?>
