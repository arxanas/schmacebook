<?php
ob_start();
require_once("./classes/user.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Schmacebook</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="./stylesheet.css" />
	<link rel="shortcut icon" href="./favicon.ico" />
	<style type="text/css">
		.centered {
			text-align: center;
			margin-left: auto;
			margin-right: auto;
			width: auto;
		}
		#register {
			display: none;
		}
		table {
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
						What&#39;s on your mind?
						<form id="status_form" action="" method="post">
							<textarea onkeyup="expandRows(this);" rows="1" cols="36" id="status_txt"></textarea><br />
							<span class="right"><input type="submit" class="btn" value="Share" /></span>
						</form>
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
				<form action="login.php" method="post">
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