<?php
require_once("../qa-include/qa-base.php");
require_once (QA_INCLUDE_DIR."qa-app-users.php");

//Checking if user have permission. Superadmin level.
if(qa_get_logged_in_level() != 120)
 die("You don't have permission. Get out here!");

$result_users = qa_db_query_raw('SELECT userid,email from qa_users');
while ($row_users = mysql_fetch_array($result_users, MYSQL_NUM)){
 $result_mailing = qa_db_query_raw('SELECT COUNT(*) from qa_mailing where userid='.$row_users[0]);
 $row_mailing = mysql_fetch_array($result_mailing, MYSQL_NUM);
 if($row_mailing[0] == 0)
  qa_db_query_raw("INSERT INTO qa_mailing (userid,email,subscriber) VALUES (".$row_users[0].",\"".$row_users[1]."\",1)");
 else
  qa_db_query_raw("UPDATE qa_mailing SET email=\"".$row_users[1]."\" WHERE userid=".$row_users[0]);
}

header('Location: http://'.$_SERVER['SERVER_NAME'].'/qa-external/qa-mailing.php?action=sync'); 
?>
