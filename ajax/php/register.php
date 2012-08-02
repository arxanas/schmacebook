<?php
ob_start();
require_once("../../classes/db.php");
$mode = $_POST["mode"];
$param = $_POST["param"];
//print_r($_POST);
if ($mode == "username") {
	echo (!mysql_num_rows(mysql_query("SELECT * FROM users WHERE username=\"".$param."\";"))) ? 1 : 0;
}
ob_end_flush();
?>