<?php
ob_start();
require_once("./classes/user.php");
$username = $_POST["username_txt"];
$password = $_POST["password_txt"];
if ($user->login($username, $password)) {
	require_once("./classes/redirect.php");
	new Redirect();
} else { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Schmacebook</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="stylesheet.css" />
	<style type="text/css">
		table {
			margin-left: auto;
			margin-right: auto;
		}
	</style>
</head>
<body>
	<?php require_once("php/header.php"); ?>
	<div id="content">
		<div id="login">
			<p class="centered red">Username or password was not correct.</p>
			<hr />
			<p class="centered">Make sure that cookies are enabled!</p>
			<object>
				<form action="login.php" method="post">
					<table>
						<tr><td>Username:</td><td><input type="text" id="username_txt" name="username_txt" value="" /></td></tr>
						<tr><td>Password:</td><td><input type="password" id="password_txt" name="password_txt" value="" /></td></tr>
						<tr><td></td><td><input type="submit" class="btn" value="Log in!" /></td></tr>
					</table>
				</form>
			</object>
		</div>
	</div>
	<?php require_once("php/footer.php"); ?>
</body>
</html>
<?php }
ob_end_flush(); ?>