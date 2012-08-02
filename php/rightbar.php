<?php
require_once("./classes/user.php");
if ($user->friends) {
	// choose a random friend!
	$suggested_friends_html = "<table>";
	foreach ($user->friends as $key => $altuser) {
		// get all other friends into a NOT thing for the sql
		$sql = "SELECT * FROM `friends` WHERE (friender_id=".$altuser." OR friendee_id=".$altuser.")";
		foreach ($user->friends as $key => $value) {
			if ($value != $altuser) {
				$sql .= " AND friender_id!=".$value." AND friendee_id!=".$value;
			}
		}
		foreach ($user->friend_requests as $key => $value) {
			$sql .= " AND friender_id!=".$value." AND friendee_id!=".$value;
		}
		$sql .= " AND friender_id!=".$user->id." AND friendee_id!=".$user->id." LIMIT 0,10;";
		$sql = mysql_query($sql);
		if (mysql_num_rows($sql)) {
			for ($i=0; $i<mysql_num_rows($sql); $i++) {
				$sql2 = mysql_fetch_assoc($sql);
				if ($sql2["friender_id"] != $altuser) {
					$id = $sql2["friender_id"];	
				} else if ($sql2["friendee_id"] != $altuser) {
					$id = $sql2["friendee_id"];
				}
				// this could probably be done without querying the database
				$temp_suggested_friends_html .= "<tr id=\"suggestion_row_".$id."\"><td>".$user->profile_picture_html($id)."</td>";
				$temp_suggested_friends_html .= "<td><strong>".mysql_result(mysql_query("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM `users` WHERE id=".$id.";"), 0, "full_name")."</strong><br /><span id='suggestion_".$id."'><a href='javascript: send_suggestion_request(".$id.");'>Send Friend Request</a></span></td></tr>";
				if (!stristr($suggested_friends_html, $temp_suggested_friends_html)) {
					$suggested_friends_html .= $temp_suggested_friends_html;
				}
			}
		}
	}
	$suggested_friends_html .= "</table>";
}
?>
<script type="text/javascript" src="./ajax/js/rightbar.js"></script>
<div id="content_right">
	<?php if ($suggested_friends_html != "<table></table>") {
		echo "Suggested Friends<hr />";
		echo $suggested_friends_html;
	} else {
		echo "No suggested friends at this time.";
	}?>
</div>