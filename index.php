<?php
ob_start();
require_once("./classes/user.php");
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Schmacebook<?php echo $new_pms; ?></title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="./stylesheet.css" />
	<link rel="shortcut icon" href="./favicon.ico" />
	<style type="text/css">
		#register {
			display: none;
		}
		#register_form table, #login_form table {
			margin-left: auto;
			margin-right: auto;
		}
	</style>
	<script type="text/javascript" src="./js/jquery.js"></script>
	<?php if ($user->loggedin) { ?>
	<script type="text/javascript" src="./js/detectbrowser.js"></script>
	<script type="text/javascript" src="./js/index.js"></script>
	<script type="text/javascript" src="./ajax/js/index.js"></script>
	<?php } else { ?>
	<script type="text/javascript" src="./ajax/js/register.js"></script>
	<script type="text/javascript" src="./js/register.js"></script>
	<?php } ?>
</head>
<body>
	<?php require_once("php/header.php"); ?>
	<div id="content">
		<?php if ($user->loggedin) { ?>
			<table id="inner_content" border="0">
				<tr>
					<td valign="top">
						<?php require_once("./php/leftbar.php"); ?>
					</td>
					<td id="content_middle" valign="top">
						<div class="centered">
							What&#39;s on your mind?
							<table><tr>
								<td><?php echo $user->profile_picture_html($user->id); ?></td>
								<td><form id="status_form" action="" method="post">
									<p>
										<textarea onkeyup="expandRows(this);" rows="1" cols="40" id="status_txt"></textarea><br />
										<input type="submit" class="btn" id="status_submit_btn" value="Share" />
									</p>
								</form></td>
							</tr></table>
							<span id="post_container">Loading <img src="./images/loading.gif" alt="" /></span>
						</div>
					</td>
					<td valign="top">
						<?php require_once("./php/rightbar.php"); ?>
					</td>
				</tr>
			</table>
		<?php } else { echo $user->loggedin; ?>
		<object>
			<form id="rl_form" action="" method="post">
				<p>
					<input type="radio" name="rl_option" id="rl_login" checked="checked" /><label for="rl_login">Log in</label>
					<input type="radio" name="rl_option" id="rl_register" /><label for="rl_register">Register</label>
				</p>
			</form>
		</object>
		<div id="login">
			<hr />
			<object>
				<form id="login_form" action="login.php" method="post">
					<table>
						<tr><td>Username:</td><td><input type="text" id="username_txt" name="username_txt" value="" /></td></tr>
						<tr><td>Password:</td><td><input type="password" id="password_txt" name="password_txt" value="" /></td></tr>
						<tr><td></td><td><input type="submit" class="btn" value="Log in!" /></td></tr>
					</table>
				</form>
				<p class="centered">Make sure that cookies are enabled!</p>
			</object>
		</div>
		<div id="register">
			<hr />		
			<noscript>
				<p><strong>Warning:</strong> Javascript must be enabled in your browser!</p><hr />
			</noscript>
			<object>
				<form id="register_form" action="register.php" method="post">
					<table>
						<tr><td>Username:  </td><td><input type="text"     id="r_username_txt"   name="r_username_txt"   class="icon_txt" value="" /></td></tr>
						<tr><td>Password:  </td><td><input type="password" id="r_password_txt"   name="r_password_txt"   class="icon_txt" value="" /></td></tr>
						<tr><td>Confirm:   </td><td><input type="password" id="r_confirm_txt"    name="r_confirm_txt"    class="icon_txt" value="" /></td></tr>
						<tr><td>Email:     </td><td><input type="text"     id="r_email_txt"      name="r_email_txt"      class="icon_txt" value="" /></td></tr>
						<tr><td>First name:</td><td><input type="text"     id="r_first_name_txt" name="r_first_name_txt" class="icon_txt" value="" /></td></tr>
						<tr><td>Last name: </td><td><input type="text"     id="r_last_name_txt"  name="r_last_name_txt"  class="icon_txt" value="" /></td></tr>
						<tr><td></td><td><input type="submit" class="btn" value="Register" /></td><td></td></tr>
					</table>
				</form>
			</object>
			<div id="r_errors">
				
			</div>
		</div>
		<?php } ?>
	</div>
	<?php require_once("php/footer.php"); ?>
</body>
</html>
<?php ob_end_flush(); ?>