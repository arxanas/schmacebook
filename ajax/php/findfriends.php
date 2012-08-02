<?php
ob_start();
require_once("./../../classes/user.php");
if (isset($_POST["friend_name"]) && $_POST["friend_name"]!="") {
$_POST["friend_name" ] = escape($_POST["friend_name"]);
	$sql = mysql_query("SELECT * FROM users WHERE CONCAT(first_name, \" \", last_name) LIKE \"%".escape($_POST["friend_name"])."%\" AND `id`!=".$user->id." ORDER BY `first_name`;");
	echo "<table>";
	if (mysql_num_rows($sql)) {
		for ($i=0; $i<mysql_num_rows($sql); $i++) {
			$altuser = mysql_fetch_assoc($sql);
			echo "<tr><td><img src=\"".($altuser["profile_picture_url"] ? $altuser["profile_picture_url"] : $user->default_profile_picture_url)."\" alt=\"".$altuser["first_name"]."'s Profile Picture\" width=\"64\" height=\"64\" /></td>";
			echo "<td><strong><a href=\"wall.php?u=".$altuser["id"]."\">".$altuser["first_name"]." ".$altuser["last_name"]."</a></strong><br /><span id=\"match_".$altuser["id"]."\"><a href=\"javascript: send_request(".$altuser["id"].");\">Send Friend Request</a></span><br /><a href=\"pm.php?u=".$altuser["id"]."\">PM</a></td></tr>";
		}
	} else {
		echo "No matches found.";
	}
	echo "</table>";
} else if (isset($_POST["friend_id"]) && $_POST["friend_id"]!="")  {
	//print_r($user->friends);
	$_POST["friend_id" ] = escape($_POST["friend_id"]);
	if ($user->id == $_POST["friend_id"]) {
		echo "<span class='red'>This is you!</span>";
		
	} else if (in_array($_POST["friend_id"], $user->friends)) {
		echo "<span class='red'>You are already friends!</span>";
		
	} else if (mysql_num_rows(mysql_query("SELECT * FROM friend_requests WHERE friender_id=".$user->id." AND friendee_id=".$_POST["friend_id"].";"))) {
		echo "<span class='red'>Request already sent!</span>";
		
	} else if (!mysql_num_rows(mysql_query("SELECT * FROM `users` WHERE id=".$_POST["friend_id"].";"))) {
		echo "<span class='red'>User does not exist!</span>";
		
	// If there exists a friend request with friender_id of the guy who you're friending, and a friendee_id of you,
	// then just add him as a friend
	} else if (mysql_num_rows(mysql_query("SELECT * FROM friend_requests WHERE friender_id=".$_POST["friend_id"]." AND friendee_id=".$user->id.";"))) {
		mysql_query("DELETE friend_requests WHERE friender_id=".$_POST["friend_id"]." AND friendee_id=".$user->id.";");
		mysql_query("INSERT INTO friends (friender_id, friendee_id) VALUES (".$_POST["friend_id"].", ".$user->id.";");
	} else {
		mysql_query("INSERT INTO friend_requests (friender_id, friendee_id) VALUES (".$user->id.", ".$_POST["friend_id"].");");
		// send email notification, if applicable
		$email_to = mysql_query("SELECT email FROM users WHERE id=\"".$_POST["friend_id"]."\" AND email_on_friend_request=1;");
		if (mysql_num_rows($email_to)) {
			$email_to = mysql_result($email_to, 0, 0);
			$email_from = "no-reply@schmacebook.net";
			$email_subject = "New friend request!";
			$email_message = "<html><head><title>New friend request</title></head><body>";
			$email_message .= "<strong>Do not reply to this automatically-generated email!</strong><br />";
			$email_message .= $user->first_name." ".$user->last_name." has sent you a friend request on Schmacebook!<br />";
			$email_message .= "<a href=\"http://schmacebook.net/\">Go to Schmacebook</a> to confirm or deny the request</body></html>";
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";		
			$headers .= "From: ".$email_from;
			mail($email_to, $email_subject, $email_message, $headers);
		}
		echo "<span class='green'>Request sent!</span>";
	}
}
// cookies are stored for the ajax directory, need to kill them!
$user->logout();
ob_end_flush();
?>