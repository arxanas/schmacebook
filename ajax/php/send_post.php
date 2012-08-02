<?php
require_once("../../classes/user.php");
$content = (isset($_POST["content"]) && $_POST["content"] != "") ? escape($_POST["content"]) : die("");
if ($user->loggedin) {
	$user->post_status($content);
}
?>