<?php
require_once("../../classes/user.php");
require_once("../../classes/nbbc.php");
if (!$user->loggedin) exit;
$content = (isset($_POST["content"]) && $_POST["content"] != "") ? trim($_POST["content"]) : die;
$id = (isset($_POST["id"]) && $_POST["id"] != "") ? escape($_POST["id"]) : die;
if (strlen($content)>500) {
	die("_".$id);
}
ob_start();
if ($user->loggedin) {
	mysql_query("INSERT INTO `comments` (content, time_posted, author_id, post_id) VALUES (\"".mysql_real_escape_string($bbcode->Parse(unescape($content)))."\", NOW(), ".$user->id.", ".$id.");");
}
$user->logout();
echo $id;
ob_end_flush();
?>