<?php
require_once("./classes/user.php");
require_once("./classes/redirect.php");
if (!$user->loggedin) {
	new Redirect();
} else if (isset($_GET["u"])) {
	$altuser = escape($_GET["u"]);
	mysql_query("DELETE FROM `friends` WHERE (friender_id=".$user->id." AND friendee_id=".$altuser.") OR (friendee_id=".$user->id." AND friender_id=".$altuser.");");
	new Redirect("myfriends.php");
}
?>