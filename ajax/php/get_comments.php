<?php
ob_start();
require_once("../../classes/user.php");
if (!$user->loggedin) exit;
$post_id = (isset($_POST["post_id"])) ? escape($_POST["post_id"]) : false;
$preview = (isset($_POST["preview"])) ? true : false;
if ($user->loggedin && $post_id) {
	// make sure that the user is actually qualified to see these comments
	$sql = mysql_query("SELECT `author_id` FROM `posts` WHERE post_id=".$post_id.";");
	if (mysql_num_rows($sql)) {
		$author_id = mysql_result($sql, 0, 0);
		if (!in_array($author_id, $user->friends) && ($author_id != $user->id)) {
			die ("Not authorized! (error 010)");
		}
	} else {
		die ("Not authorized! (error 011)");
	}
	// get the comments!
	if ($preview) {
		$sql = mysql_query("SELECT * FROM `comments` WHERE post_id=".$post_id." ORDER BY comment_id DESC LIMIT 0,3;");
		$comment_html = "";
		if (mysql_num_rows($sql)) {
			for ($i=mysql_num_rows($sql)-1; $i>=0; $i--) {
				$sql2 = array ("content" => mysql_result($sql, $i, 0), "time_posted" => mysql_result($sql, $i, 1), "author_id" => mysql_result($sql, $i, 2), "post_id" => mysql_result($sql, $i, 3), "comment_id" => mysql_result($sql, $i, 4));
				$full_name = mysql_result(mysql_query("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM `users` WHERE id=".$sql2["author_id"].";"), 0, 0);
				
				$comment_html .= "<tr class='comment'><td><span class='right'>".$user->profile_picture_html($sql2["author_id"], 36)."</span></td>";
				$comment_html .= "<td><a href=\"wall.php?u=".$sql2["author_id"]."\">".$full_name."</a> <span class='gray'>at ".$sql2["time_posted"]."</span><br />";
				$comment_html .= "<span class='text-left'>".$sql2["content"]."</span></td></tr>";
			}
		}
	} else {
		$sql = mysql_query("SELECT * FROM `comments` WHERE post_id=".$post_id." ORDER BY comment_id ASC;");
		$comment_html = "";
		if (mysql_num_rows($sql)) {
			for ($i=0; $i<mysql_num_rows($sql); $i++) {
				$sql2 = mysql_fetch_assoc($sql);
				$full_name = mysql_result(mysql_query("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM `users` WHERE id=".$sql2["author_id"].";"), 0, 0);
	
				$comment_html .= "<tr class='comment'><td><span class='right'>".$user->profile_picture_html($sql2["author_id"], 36)."</span></td>";
				$comment_html .= "<td><a href=\"wall.php?u=".$sql2["author_id"]."\">".$full_name."</a> <span class='gray'>at ".$sql2["time_posted"]."</span><br />";
				$comment_html .= "<span class='text-left'>".$sql2["content"]."</span></td></tr>";
	
			}
		}
	}
	echo $comment_html;
} else {
	if (!$post_id) {
		echo "Post handler not provided!";
	}
	if (!$user->loggedin) {
		echo "Not logged in!";
	}
	die;
}
$user->logout();
ob_end_flush();
?>