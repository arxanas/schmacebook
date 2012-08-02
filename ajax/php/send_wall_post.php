<?php
require_once("../../classes/user.php");
require_once("../../classes/nbbc.php");
if (!$user->loggedin) exit;
$content = (isset($_POST["content"]) && $_POST["content"] != "") ? trim(unescape($_POST["content"])) : die("No content provided!");
$to = (isset($_POST["to"]) && $_POST["to"] != "") ? escape($_POST["to"]) : die ("No user provided!"); 
if (strlen($content)>500) {
	die("_".$id);
}
ob_start();
if ($user->loggedin) {
	mysql_query("INSERT INTO `posts` (content, time_posted, author_id, `to`) VALUES (\"".mysql_real_escape_string($bbcode->Parse($content))."\", NOW(), ".$user->id.", ".$to.");");
}
$user->logout();
ob_end_flush();
?>