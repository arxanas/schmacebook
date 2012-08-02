<div id="content_left">
		<a href="findfriends.php">Find friends</a>	
		<?php
		$sql = mysql_query("SELECT * FROM friend_requests WHERE friendee_id=".$user->id);
		if (mysql_num_rows($sql)) {
			echo "<hr />Requests (".mysql_num_rows($sql).")<br /><table>";
			for ($i=0; $i<mysql_num_rows($sql); $i++) {
				$altuser = mysql_fetch_assoc($sql);
				$altuser = mysql_fetch_assoc(mysql_query("SELECT first_name, last_name, profile_picture_url FROM users WHERE id=\"".$altuser["friender_id"]."\";"));
				echo "<tr id=\"friend_request_row_".$altuser["friender_id"]."\"><td><img width=\"64\" height=\"64\" src=\"".($altuser["profile_picture_url"] ? $altuser["profile_picture_url"] : $user->default_profile_picture_url)."\" alt=\"".$altuser["first_name"]." ".$altuser["last_name"]."\" /></td>";
				echo "<td><strong>".$altuser["first_name"]." ".$altuser["last_name"]."</strong><br /><span id=\"friend_request_".$altuser["id"]."\"><a class=\"green_link\" href=\"#\">Accept</a><br /><a class=\"red_link\" href=\"#\">Deny</a></span></td></tr>";
			}
			echo "</table>";
		}
		?>				
</div>