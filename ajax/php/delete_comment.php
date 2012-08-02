<?php
ob_start(); 
require_once("../../classes/user.php");
require_once("../../classes/redirect.php");
if (!$user->loggedin) exit;
$comment_id = isset($_POST["id"]) && $_POST["id"] != "" ? escape($_POST["id"]) : false;
if ($comment_id) {
	$comment = mysql_fetch_assoc(mysql_query("SELECT * FROM comments WHERE comment_id=".$comment_id.";"));
	$comment_author_id = $comment["author_id"];
	$post = mysql_fetch_assoc(mysql_query("SELECT * FROM posts WHERE post_id=".$comment["post_id"].";"));
	if ($comment_author_id == $user->id || $user->admin || $post["author_id"] == $user->id || $comment["post_id"] == $post["to"]) {
		mysql_query("DELETE FROM comments WHERE comment_id=".$comment_id.";");
	}
}
$user->logout();
require_once("../../classes/redirect.php");
ob_end_flush();
?>