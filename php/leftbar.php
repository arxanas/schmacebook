<script type="text/javascript" src="./ajax/js/leftbar.js"></script>
</script>
<div id="content_left">
		<a href="findfriends.php">Find friends</a><br />
		<?php
		$sql = mysql_query("SELECT * FROM friend_requests WHERE friendee_id=".$user->id);
		if (mysql_num_rows($sql)) {
			echo "<hr /><span id='request_container'>Requests (<span id='request_count'>".mysql_num_rows($sql)."</span>)</span><br /><table>";
			for ($i=0; $i<mysql_num_rows($sql); $i++) {
				$altuser = mysql_fetch_assoc($sql);
				$name = mysql_result(mysql_query("SELECT CONCAT(first_name, ' ', last_name) FROM `users` WHERE id=".$altuser["friender_id"].";"), 0, 0);
				echo "<tr id=\"friend_request_row_".$altuser["friender_id"]."\"><td>".$user->profile_picture_html($altuser["friender_id"])."</td>";
				echo "<td><strong>".$name."</strong><br /><span id=\"friend_request_".$altuser["friender_id"]."\"><a class=\"green_link\" href=\"javascript: accept_request(".$altuser["friender_id"].");\">Accept</a><br /><a class=\"red_link\" href=\"javascript: deny_request(".$altuser["friender_id"].");\">Deny</a></span></td></tr>";
			}
			echo "</table>";
		} else {
			echo "No friend requests at this time.";
		}
		?>				
</div>