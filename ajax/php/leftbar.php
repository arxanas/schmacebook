<?php
ob_start();
require_once("../../classes/user.php");
$mode = $_POST["mode"];
$id = escape($_POST["id"]);
if ($mode == "accept") {
	if (mysql_num_rows(mysql_query("SELECT * FROM friend_requests WHERE friender_id=".$id." AND friendee_id=".$user->id.";"))) {
		mysql_query("DELETE FROM friend_requests WHERE friender_id=".$id." AND friendee_id=".$user->id.";");
		mysql_query("INSERT INTO friends (friender_id, friendee_id) VALUES (".$id.", ".$user->id.");");
		echo "<td><span class='green'>Accepted!</span></td>";
	} else {
		echo "<td><span class='red'>Failure!</span></td>";
	}
} else if ($mode == "deny") {
	if (mysql_num_rows(mysql_query("SELECT * FROM friend_requests WHERE friender_id=".$id." AND friendee_id=".$user->id.";"))) {
		mysql_query("DELETE FROM friend_requests WHERE friender_id=".$id." AND friendee_id=".$user->id.";");
		echo "<td><span class='green'>Denied!</span></td>";
	} else {
		echo "<td><span class='red'>Failure!</span></td>";
	}
}
$user->logout();
ob_end_flush();
?>