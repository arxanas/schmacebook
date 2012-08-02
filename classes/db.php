<?php
//database connection
$username = "arxanas_schbook";
$password = "Ww0#4456&!wlVZJ5";
$host = "localhost";
$database = "arxanas_schmacebook";
mysql_connect($host, $username, $password) or die(mysql_error());
mysql_select_db($database) or die(mysql_error());

function escape ($str) {
	//magic_quotes are on
	return $str;
}
function unescape ($str) {
	return stripslashes($str);
}

//prevent caching, which is bad
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
?>