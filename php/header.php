<?php
if ($user->loggedin) {
	// get unread pms
	$sql = mysql_query("SELECT * FROM private_messages WHERE receiver_id=".$user->id." AND `read`=0;");
	if (mysql_num_rows($sql)) {
		$new_pms = " (".mysql_num_rows($sql).")";
	} else {
		$new_pms = "";
	}
}
?>
	<div id="header_bar">
		<div id="header_content">
			<div id="header_left">
				Schmacebook
			</div>
			<?php if ($user->loggedin) { ?>
			<div id="nav">
					<ul>
						<li><a href="feedback.php">Feedback</a></li>
						<li><a href="account.php">Account</a></li>
						<li><a href="pm.php">Inbox<?php echo $new_pms; ?></a></li>
						<li><a href="myfriends.php">Friends</a></li>
						<li><a href="wall.php">Wall</a></li>
						<li><a href="index.php">Home</a></li>

					</ul>
				<?php } else { ?>
			<div id="header_right">
					Not logged in!
				<?php } ?>
			</div>
		</div>
	</div>