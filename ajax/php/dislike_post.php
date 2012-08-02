<?php
ob_start();
require_once("../../classes/user.php");
if ($user->loggedin) {
	$id = escape($_POST["id"]);
	if (mysql_num_rows(mysql_query("SELECT * FROM posts WHERE post_id=".$id.";"))) {
		mysql_query("DELETE FROM likes WHERE post_id=".$id." AND user_id=".$user->id." AND like_value=1;");
		mysql_query("INSERT INTO likes (post_id, user_id, like_value) VALUES (".$id.", ".$user->id.", 0);");
		echo $id;
	}
} else {
	echo "Not logged in!";
} 

$user->logout();
ob_end_flush();
?>