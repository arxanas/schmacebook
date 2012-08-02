<?php
ob_start(); 
require_once("../../classes/user.php");
require_once("../../classes/redirect.php");
if (!$user->loggedin) exit;
$post_id = isset($_POST["id"]) && $_POST["id"] != "" ? escape($_POST["id"]) : false;
if ($post_id) {
	$post_author_id = mysql_result(mysql_query("SELECT author_id FROM posts WHERE post_id=".$post_id.";"), 0, 0);
	if ($post_author_id == $user->id || $user->admin || mysql_num_rows(mysql_query("SELECT `to` FROM posts WHERE `to`=".$post_author_id.";"))) {
		mysql_query("DELETE FROM comments WHERE post_id=".$post_id.";");
		mysql_query("DELETE FROM posts WHERE post_id=".$post_id.";");
	}
}
$user->logout();
require_once("../../classes/redirect.php");
ob_end_flush();
?>