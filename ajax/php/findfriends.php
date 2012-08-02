<?php
require_once("../../classes/user.php");
if (isset($_POST["friend_name"]) && $_POST["friend_name"]!="") {
	$sql = mysql_query("SELECT * FROM users WHERE CONCAT(first_name, \" \", last_name) LIKE \"%".escape($_POST["friend_name"])."%\" AND `id`!=".$user->id." ORDER BY first_name LIMIT 0,20;");
	echo "<table>";
	if (mysql_num_rows($sql)) {
		for ($i=0; $i<mysql_num_rows($sql); $i++) {
			$altuser = mysql_fetch_assoc($sql);
			echo "<tr><td><img src=\"".($altuser["profile_picture_url"] ? $altuser["profile_picture_url"] : $user->default_profile_picture_url)."\" alt=\"".$altuser["first_name"]."'s Profile Picture\" width=\"64\" height=\"64\" /></td>";
			echo "<td><strong>".$altuser["first_name"]." ".$altuser["last_name"]."</strong><br /><span id=\"match_".$altuser["id"]."\"><a href=\"javascript: send_request(".$altuser["id"].");\">Send Friend Request</a></span></td></tr>";
		}
	} else {
		echo "No matches found.";
	}
	echo "</table>";
} else if (isset($_POST["friend_id"]) && $_POST["friend_id"]!="")  {
	if ($user->id == $_POST["friend_id"]) {
		echo "<span class='red'>This is you!</span>";
	} else if (mysql_num_rows(mysql_query("SELECT * FROM friend_requests WHERE friendee_id=".$_POST["friend_id"].";"))) {
		echo "<span class='red'>Request already sent!</span>";
		
		// WORK ON THIS
	} else if (false) { //mysql_num_rows($sql = mysql_query("SELECT * FROM friend_requests WHERE friendee_id=".$_POST["friend_id"].";"))) {
		mysql_query("DELETE FROM friend_requests WHERE friendee_id=".$_POST["friend_id"].";");
		mysql_query("INSERT INTO friends (friender_id) VALUES (")
	} else if (!mysql_num_rows(mysql_query("SELECT * FROM `users` WHERE id=".$_POST["friend_id"].";"))) {
		echo "<span class='red'>User does not exist!</span>";
	} else {
		mysql_query("INSERT INTO friend_requests (friender_id, friendee_id) VALUES (".$user->id.", ".$_POST["friend_id"].");");
		echo "<span class='green'>Request sent!";
	}
}
?>