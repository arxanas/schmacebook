<?php
ob_start();
require_once("./classes/user.php");
$mode = 0;
$to = false;
$to = (isset($_GET["u"]) && $_GET["u"] != "") ? escape($_GET["u"]) : $to;
$to = (isset($_POST["u"]) && $_POST["u"] != "") ? escape($_POST["u"]) : $to;
$pm = (isset($_POST["pm_txt"]) && $_POST["pm_txt"] != "") ? $_POST["pm_txt"] : false;
$subject = (isset($_POST["subject_txt"]) && $_POST["subject_txt"] != "") ? escape(htmlentities($_POST["subject_txt"])) : false;
$m = (isset($_GET["m"]) && $_GET["m"] != "") ? escape($_GET["m"]) : false;
$r = (isset($_GET["r"]) && $_GET["r"] != "") ? escape($_GET["r"]) : false;
if ($r !== false) {
	$sql = mysql_query("SELECT * FROM private_messages WHERE pm_id=".$r.";");
	$to = mysql_result($sql, 0, "sender_id");
	$subject = "Re: ".mysql_result($sql, 0, "subject");
}
if (!$user->loggedin) {
	require_once("./classes/redirect.php");
	new Redirect();
	exit;
} else if ($to !== false && $pm !== false) {
	// send private message
	$mode = 1;
	$subject = ($subject == "" || !$subject) ? "(no subject)" : $subject;
	if ($user->id != $to) {
		mysql_query("INSERT INTO private_messages (subject, content, sender_id, receiver_id, time_sent, `read`) VALUES (\"".$subject."\", \"".$pm."\", ".$user->id.", ".$to.", NOW(), 0);");
		require_once("./classes/nbbc.php");
		$pm = $bbcode->Parse(unescape($pm));
		// if they want pm notifications, do so
		$sql = mysql_query("SELECT email_on_pm, email FROM users WHERE id=".$to.";");
		if (mysql_result($sql, 0, 0)) {
			$email_to = mysql_result($sql, 0, 1);
			$email_from = "no-reply@schmacebook.net";
			$email_subject = "PM from ".$user->first_name." ".$user->last_name;
			$email_message = "<html><head><title>".$email_subject."</title></head><body>"."<strong>Do not reply to this automatically-generated email!</strong><br /><br /><strong>".$email_subject."<hr />".$pm."</body></html>";
			$email_headers  = 'MIME-Version: 1.0' . "\r\n";
			$email_headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";		
			$email_headers .= "From: ".$email_from;
			mail($email_to, $email_subject, $email_message, $email_headers); // can't do much if the mail sending fails
		}
	}
} else if ($to !== false) {
	// private message composition
	$mode = 2;
	$full_name = mysql_result(mysql_query("SELECT CONCAT(first_name, ' ', last_name) AS full_name FROM `users` WHERE id=".$to.";"), 0, 0);
} else if ($m !== false) {
	// view private message
	$mode = 3;
	$sql = mysql_query("SELECT * FROM `private_messages` WHERE pm_id=".$m." AND receiver_id=".$user->id.";");
	$pm_html = "";
	if (mysql_num_rows($sql)) {
		$pm_html = "<table id=\"pms\">";
		$sql2 = mysql_fetch_assoc($sql);
		$sql3 = mysql_fetch_assoc(mysql_query("SELECT * FROM `users` WHERE id=".$sql2["sender_id"]));
		
		// make message 'read'
		mysql_query("UPDATE `private_messages` SET `read`=1 WHERE pm_id=".$sql2["pm_id"].";");
		
		if (mysql_num_rows(mysql_query("SELECT `read` FROM private_messages WHERE pm_id=".$sql2["pm_id"]." AND `read`=1;"))) {
			$pm_html .= "<tr class='read-pm left'><td>".$user->profile_picture_html($sql2["sender_id"])."</td>";
		} else {
			$pm_html .= "<tr class='left'><td>".$user->profile_picture_html($sql2["sender_id"])."</td>";
		}
		$pm_html .= "<td><strong><a href=\"wall.php?u=".$sql2["sender_id"]."\">".$sql3["first_name"]." ".$sql3["last_name"]."</a></strong> <span class='gray'>at ".$sql2["time_sent"]."</span><br />";
		$pm_html .= "<em>".$sql2["subject"]."</em><br />";
		$pm_html .= $sql2["content"]."<br /><br /><a href=\"pm.php?r=".$sql2["pm_id"]."\">Reply</a> | <a href=\"pm.php\">Inbox</a>";
		$pm_html .= "</td></tr></table>";
	} else {
		$pm_html = "<div class='centered'>This private message was not sent to you!</div>";
	}
} else {
	// view ALL private messages
	$mode = 4;
	$sql = mysql_query("SELECT * FROM private_messages WHERE receiver_id=".$user->id." ORDER BY pm_id DESC;");
	if (mysql_num_rows($sql)) {
		$pm_html = "<table id=\"pms\">";
		for ($i=0; $i<mysql_num_rows($sql); $i++) {
			$sql2 = mysql_fetch_assoc($sql);
			$sql3 = mysql_fetch_assoc(mysql_query("SELECT * FROM `users` WHERE id=".$sql2["sender_id"]));
			// if it's read
			if (mysql_num_rows(mysql_query("SELECT `read` FROM private_messages WHERE pm_id=".$sql2["pm_id"]." AND `read`=1;"))) {
				$pm_html .= "<tr id=\"pm_".$sql2["pm_id"]."\" class='read-pm left'><td>".$user->profile_picture_html($sql2["sender_id"])."</td>";
			} else {
				$pm_html .= "<tr id=\"pm_".$sql2["pm_id"]."\" class='left'><td>".$user->profile_picture_html($sql2["sender_id"])."</td>";
			}
			$pm_html .= "<td><strong><a href=\"wall.php?u=".$sql2["sender_id"]."\">".$sql3["first_name"]." ".$sql3["last_name"]."</a></strong> <span class='gray'>at ".$sql2["time_sent"]."</span><br />";
			$pm_html .= "<em><a href=\"pm.php?m=".$sql2["pm_id"]."\">".$sql2["subject"]."</a></em><br />";
			$pm_html .= "<a href=\"pm.php?r=".$sql2["pm_id"]."\">Reply</a> | <span id=\"pm_delete_".$sql2["pm_id"]."\"><a class='red_link' href=\"javascript: delete_pm(".$sql2["pm_id"].");\">Delete</a></span></td>";
		}
		$pm_html .= "</table>";
	} else {
		$pm_html = "<div class='centered'>You have no private messages.</div>";
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Private Messages</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="stylesheet.css" />
	<style type="text/css">
		#subject_txt {
			width: 99%
		}
		textarea {
			width: 100%;
		}
	</style>
	<script type="text/javascript" src="./js/jquery.js"></script>
	<script type="text/javascript" src="./js/index.js"></script>
	<script type="text/javascript" src="./ajax/js/pm.js"></script>
</head>
<body>
	<?php require_once("./php/header.php"); ?>
	<div id="content"><p>
		<table id="inner_content" border="0">
			<tr>
				<td><?php require_once("./php/leftbar.php"); ?></td>
				<td id="content_middle">
					<?php if ($mode == 1) { ?>
						<div class='centered'>
							<?php if ($user->id != $to) { ?>
								Message sent!<br /><a href="pm.php">Inbox</a>
							<?php } else { ?>
								You can&#39;t send a message to yourself!<br /><a href="pm.php">Inbox</a>
							<?php } ?>
						</div>
					<?php } else if ($mode == 2) { ?>
						<div class="centered">
							<form action="pm.php" method="post">
								To: <strong><?php echo $full_name; ?></strong><hr /><input type="hidden" name="u" id="u" value="<?php echo $to; ?>" />
								<strong>Subject:</strong> <input type="text" name="subject_txt" id="subject_txt" size="40" value="<?php echo $subject; ?>"><br />
								<strong>Message: </strong> <textarea name="pm_txt" id="pm_txt" rows="1" cols="40" onkeyup="expandRows(this);"></textarea><br />
								<input type="submit" class="btn" value="Send Message" />
							</form>
						</div>
					<?php } else if ($mode == 3) {
							echo $pm_html;
						} else if ($mode == 4) {
						 echo $pm_html;
					} ?>
				</td>
				<td><?php require_once("./php/rightbar.php"); ?></td>
			</tr>
		</table>
	</p></div>
	<?php require_once("php/footer.php"); ?>
</body>
</html>
<?php
ob_end_flush();
?>