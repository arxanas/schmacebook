<?php
ob_start();
require_once("../../classes/user.php");
if ($user->loggedin) {
	$id = escape($_POST["id"]);
	mysql_query("DELETE FROM likes WHERE post_id=".$id." AND user_id=".$user->id." AND like_value=0;");
	echo $id;
} else {
	echo "Not logged in!";
} 

$user->logout();
ob_end_flush();
?>