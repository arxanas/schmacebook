<?php
ob_start();
require_once("../../classes/user.php");
if (!$user->loggedin) exit;
$pm_id = (isset($_POST["pm_id"]) && $_POST["pm_id"] != "") ? escape($_POST["pm_id"]) : false;
if (!$user->loggedin) {
	require_once("../../classes/redirect.php");
	new Redirect();
	exit;
}
if ($pm_id !== false) {
	mysql_query("DELETE FROM private_messages WHERE receiver_id=".$user->id." AND pm_id=".$pm_id.";");
	echo $pm_id;
}
$user->logout();
ob_end_flush();
?>