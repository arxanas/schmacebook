<?php
ob_start();
require_once("classes/user.php");
$vars = array ("username" => "", "password" => "", "confirm" => "", "first_name" => "", "last_name" => "", "email" => "");
$errors = array();
foreach ($vars as $key => $value) {
	$vars[$key] = escape($_POST["r_".$key."_txt"]);
	//if a value wasn't given
	if ($vars[$key] == "") {
		array_push($errors, ucwords(str_replace("_", " ", $key))." was not supplied!");
	} else {
		//is username taken?
		if ($key == "username") {
			$sql = mysql_query("SELECT * FROM users WHERE username=\"".$vars[$key]."\";");
			if (mysql_num_rows($sql) != 0) {
				array_push($errors, "Username is already taken!");
			}
		}
		// password/confirmation matching
		if ($key == "confirm" && $vars["confirm"] !== $vars["password"]) {
			array_push($errors, "Password and confirmation do not match!");
		}
		if ($key == "email" && !preg_match("/^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/", $vars[$key])) {
			array_push($errors, "Email is not valid!");
		}
		if ($key == "first_name" && !$vars[$key]) {
			array_push($errors, "First name is blank!");
		}
		if ($key == "last_name" && !$vars[$key]) {
			array_push($errors, "Last name is blank!");
		}
	}
}
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
		}
		#register{
			display: none;
		}
		table {
			margin-left: auto;
			margin-right: auto;
		}
	</style>
	<?php if ($errors) { ?>
	<script type="text/javascript" src="./js/jquery.js"></script>
	<script type="text/javascript" src="./ajax/js/register.js"></script>
	<script type="text/javascript" src="./js/register.js"></script>
	<?php } ?>
</head>
<body>
	<?php require_once("php/header.php") ?>
	<div id="content">
		<?php if ($errors) {?>
		<div id="register">
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
				<table><tr><td>
				<span class="red">The following errors occurred: </span>
                <?php
        			//assemble error message
        			$error_message = "<ul>";
        			foreach ($errors as $value) {
        				$error_message .= "<li>".$value."</li>";
        			}
        			echo $error_message."</ul>";
                	?>
                </div>
				</td></tr></table>
		</div>
		<?php } else {
			echo $user->register($vars);
		} ?>
	</div>
</body>
</html>
<?php ob_end_flush(); ?>