<?php
ob_start();
require_once("./classes/user.php");
require_once("./classes/redirect.php");
$user->logout();
new Redirect();
ob_end_flush();
?>