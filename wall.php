<?php
ob_start();
require_once("./classes/user.php");
if (!$user->loggedin) {
	require_once("./classes/redirect.php");
	new Redirect();
} else {
	$u = (isset($_GET["u"])) ? (escape($_GET["u"])) : $user->id;
	$profile = isset($_POST["profile_txt"]) ? escape(htmlentities($_POST["profile_txt"])) : false;
	if ($profile !== false) {
		mysql_query("UPDATE users SET profile=\"".$profile."\" WHERE id=".$user->id.";");
	}
	$altuser = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE id=".$u.";"));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?php echo $altuser["first_name"]; ?>'s Wall</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="stylesheet.css" />
	<script type="text/javascript" src="./js/jquery.js"></script>
	<script type="text/javascript" src="./js/detectbrowser.js"></script>
	<script type="text/javascript" src="./js/wall.js"></script>
	<script type="text/javascript" src="./ajax/js/wall.js"></script>
	<?php if (in_array($altuser["id"], $user->friends) || $altuser["id"] == $user->id) { ?>
	<script type="text/javascript">
	<!--
	var user_id = <?php echo $altuser["id"]; ?>;
	$(document).ready(function() {
		get_posts(<?php echo $altuser["id"]; ?>);
		post_inverval = setInterval("get_posts();", 30000);
	});
	$(document).ready (function () {
		$("#status_txt").val("");
		$("#status_form").submit(function () {
			submit_status(<?php echo $altuser["id"]; ?>);
			return false;
		});
	});
	//-->
	</script>
	<?php } ?>
</head>
<body>
	<?php require_once("./php/header.php"); ?>
	<div id="content"><p>
		<table id="inner_content" border="0">
			<tr>
				<td><?php require_once("./php/leftbar.php"); ?></td>
				<td id="content_middle">
					<div>
						<strong><?php echo $altuser["first_name"]." ".$altuser["last_name"]; ?>&#39;s Profile</strong><span class='right'><span id="edit_profile"><?php if ($altuser["id"] == $user->id) echo "<a href=\"javascript: edit_profile(".$altuser["id"].");\">Edit</a> | "; ?></span><a href="pm.php?u=<?php echo $altuser["id"]; ?>">PM</a></span>
						<table><tr>
							<td><?php echo $user->profile_picture_html($altuser["id"]); ?></td>
							<td>
								<span id="profile_<?php echo $altuser["id"]; ?>"><?php echo ($altuser["profile"] !== false && $altuser["profile"] != "") ? $altuser["profile"] : $altuser["first_name"]." ".$altuser["last_name"]." doesn&#39;t have a profile yet."; ?></span>
							</td>
					</tr></table><hr />
					</div>
					<div class="centered">
						<?php if ($altuser["id"] != $user->id && in_array($altuser["id"], $user->friends)) { ?>
							Say something:
							<table><tr>
								<td><?php echo $user->profile_picture_html($user->id); ?></td>
								<td><form id="status_form" action="" method="post">
									<p>
										<textarea onkeyup="expandRows(this);" rows="1" cols="40" id="status_txt"></textarea><br />
										<input type="submit" class="btn" id="status_submit_btn" value="Share" />
									</p>
								</form></td>
							</tr></table>
						<?php } ?>
						<?php if (in_array($altuser["id"], $user->friends) || $altuser["id"] == $user->id) { ?>
						<span id="post_container">Loading <img src="./images/loading.gif" alt="" /></span>
						<?php } ?>
					</div>
				</td>
				<td><?php require_once("./php/rightbar.php"); ?></td>
			</tr>
		</table>
	</p></div>
	<?php require_once("php/footer.php"); ?>
</body>
</html>
<?php 
}
ob_end_flush();
?>