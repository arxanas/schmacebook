<?php
require_once("../../classes/user.php");
require_once("../../classes/nbbc.php");
if (!$user->loggedin) exit;
$content = (isset($_POST["content"]) && $_POST["content"] != "") ? trim(unescape($_POST["content"])) : die("No content provided!");
if (strlen($content)>500) {
	die("0");
}
ob_start();
if ($user->loggedin) {
	mysql_query("INSERT INTO `posts` (content, time_posted, author_id, `to`) VALUES (\"".mysql_real_escape_string($bbcode->Parse($content))."\", NOW(), ".$user->id.", 0);");
}
$user->logout();
ob_end_flush();
?>