<?php
ob_start();
require_once("../../classes/user.php");
if (!$user->loggedin) exit;
$to = isset($_POST["to"]) ? escape($_POST["to"]) : false;
if ($to === false) exit;
$to_user = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE id=".$to.";"));
if (!in_array($to_user["id"], $user->friends) && $to_user["id"] != $user->id) exit;
$posts = mysql_query("SELECT * FROM `posts` WHERE `to`=".$to." ORDER BY post_id DESC LIMIT 0,20;");
if (mysql_num_rows($posts)) {
	$content = "<form method=\"post\" action=\"#\"><table id=\"posts\">";
	for ($i=0; $i<mysql_num_rows($posts); $i++) {
		$this_post = mysql_fetch_assoc($posts);
		
		// Likes/Dislike handling
		$sql = mysql_query("SELECT * FROM likes WHERE post_id=".$this_post["post_id"].";");
		$likes = array();
		$dislikes = array();
		// get all like/dislike id's
		if (mysql_num_rows($sql)) {
			for ($j=0; $j<mysql_num_rows($sql); $j++) {
				$this_like = mysql_fetch_assoc($sql);
				if ($this_like["like_value"] == 1) {
					array_push($likes, $this_like["user_id"]);
				} else if ($this_like["like_value"] == 0) {
					array_push($dislikes, $this_like["user_id"]);
				}
			}
		}
		
		// assemble like html
		$like_html = "";
		$dislike_html ="";
		if ($likes) {
			$like_html = "<img src=\"./images/like.png\" alt=\"Likes\" />&nbsp;";
			foreach ($likes as $key => $value) {
				$sql = mysql_query("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM users WHERE id=".$value.";");
				$liker_name = mysql_result($sql, 0, 0);
				if ($key) {
					$like_html .= ", ";
				}
				$like_html .= "<a href=\"wall.php?u=".$value."\">".$liker_name."</a>";
			}
			$like_html .= "</span><br />";
		}
		if ($dislikes) {
			$dislike_html = "<span class='text-left'><img src=\"./images/dislike.png\" alt=\"Dislikes\" />&nbsp;";
			foreach ($dislikes as $key => $value) {
				$sql = mysql_query("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM users WHERE id=".$value.";");
				$disliker_name = mysql_result($sql, 0, 0);
				if ($key) {
					$dislike_html .= ", ";
				}
				$dislike_html .= "<a href=\"wall.php?u=".$value."\">".$disliker_name."</a>";
			}
			$dislike_html .= "</span><br />";
		}
		
		// get last few comments for previews
		$comments = mysql_query("SELECT * FROM `comments` WHERE post_id=".$this_post["post_id"]." ORDER BY comment_id DESC LIMIT 0,3;");
		$comment_html = "";
		if (mysql_num_rows($comments)) {
			for ($k=mysql_num_rows($comments)-1; $k>=0; $k--) {
				// same weird backwards thing
				$this_comment = array ("content" => mysql_result($comments, $k, 0), "time_posted" => mysql_result($comments, $k, 1), "author_id" => mysql_result($comments, $k, 2), "post_id" => mysql_result($comments, $k, 3), "comment_id" => mysql_result($comments, $k, 4));
				if ($k == mysql_num_rows($comments)-1) {
					$comment_html .= "<tbody id=\"comments_preview_".$this_comment["post_id"]."\">";
				}
				$comment_author_name = mysql_result(mysql_query("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM `users` WHERE id=".$this_comment["author_id"].";"), 0, 0);
				
				$comment_html .= "<tr class='comment'><td><span class='right'>".$user->profile_picture_html($this_comment["author_id"], 36)."</span></td>";
				$comment_html .= "<td><strong><a href=\"wall.php?u=".$this_comment["author_id"]."\">".$comment_author_name."</a></strong>";
				if ($this_comment["author_id"] == $user->id || $this_post["author_id"] == $user->id || $this_comment["post_id"] == $this_post["to"] || $user->admin) {
					$comment_html .= "<a href=\"javascript: delete_comment(".$this_comment["comment_id"].");\" class='delete'></a>";
				}
				$comment_html .= "<span class='gray'>at ".$this_comment["time_posted"]."</span><br />";
				$comment_html .= "<span class='text-left'>".$this_comment["content"]."</span></td></tr>";
			}
			$comment_html .= "</tbody>";
		}
		
		// get actual posts
		$post_author_name = mysql_result(mysql_query("SELECT CONCAT(first_name, ' ' , last_name) FROM `users` WHERE id=".$this_post["author_id"].";"), 0, 0);
		$content .= "<tr><td>".$user->profile_picture_html($this_post["author_id"])."<br /></td><td><strong><a href=\"wall.php?u=".$this_post["author_id"]."\">".$post_author_name."</a></strong>";
		if ($this_post["author_id"] == $user->id || $this_post["author_id"] == $this_post["to"] || $user->admin) {
			$content .= "<a href=\"javascript: delete_post(".$this_post["post_id"].");\" class='delete'></a>";
		}
		$content .= "<span class='gray'>at ".$this_post["time_posted"]."</span><br />".$this_post["content"]."<br />";
		$content .= "<span class='text-left'>".$like_html.$dislike_html."</span>";
		$content .= "<span class='text-left' id=\"like_".$this_post["post_id"]."\">";
		if (in_array($user->id, $likes)) {
			$content .= "<a href=\"javascript: unlike_post(".$this_post["post_id"].");\">Unlike";
		} else {
			$content .= "<a href=\"javascript: like_post(".$this_post["post_id"].");\">Like";
		}
		$content .= "</a></span>";
		
		$content .= "<span class='text-left' id=\"dislike_".$this_post["post_id"]."\">&nbsp;&bull;&nbsp;";
		if (in_array($user->id, $dislikes)) {
			$content .= "<a href=\"javascript: undislike_post(".$this_post["post_id"].");\">Undislike";
		} else {
			$content .= "<a href=\"javascript: dislike_post(".$this_post["post_id"].");\">Dislike";
		}
		$content .= "</a></span>";
		$content .= "<span class='text-left' id=\"comment_toggle_".$this_post["post_id"]."\">&nbsp;&bull;&nbsp;<a href=\"javascript: show_comments(".$this_post["post_id"].");\">Show all comments</a></span>";
		
		$content .= $comment_html."</td></tr>";
		$content .= "<tr class='comment'><td></td><td><span class='text-left' id=\"comment_row_".$this_post["post_id"]."\"><a href=\"javascript: generate_comment_box(".$this_post["post_id"].");\">Comment</a></span></td></tr>";
		$content = preg_replace("/\<br \/\>+/", "<br />", $content);
	}
	$content .= "</table></form>";
	echo $content;
} else {
	echo "No posts to show.";
}
$user->logout();
?>