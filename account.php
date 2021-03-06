<?php
ob_start();
require_once("./classes/user.php");
if (!$user->loggedin) {
	require_once("./classes/redirect.php");
	new Redirect();
} else {
	$errors = array();
	foreach ($_POST as $key => $value) {
		$_POST["key"] = escape($_POST["key"]);
	}
	$input = false;
	
	if ($_POST["old_password_txt"] && $_POST["new_password_txt"] && $_POST["confirm_txt"]) {
		if ($user->password != md5($_POST["old_password_txt"])) {
			array_push($errors, "Old password did not match.");
		} else if ($_POST["new_password_txt"] != $_POST["confirm_txt"]) {
			array_push($errors, "New password and confirmation did not match.");
		} else {
			$user->password = md5($_POST["new_password_txt"]);
			$user->login($user->username, $user->password, true, true);
		}
		$input = true;
	} else if ($_POST["old_password_txt"] || $_POST["new_password_txt"] || $_POST["confirm_txt"]) {
		$input = false;
	}
	if ($_POST["first_name_txt"]) {
		$user->first_name = escape(htmlentities($_POST["first_name_txt"]));
		$input = true;
	}
	if ($_POST["last_name_txt"]) {
		$user->last_name = escape(htmlentities($_POST["last_name_txt"]));
		$input = true;
	}
	if ($_POST["email_txt"]) {
		if (!preg_match("/^[A-Za-z0-9._%-]+@([A-Za-z0-9._%-]+\.)+[A-Za-z]{2,4}$/", $_POST["email_txt"])) {
			array_push($errors, "Email is not valid.");
		} else {
			$user->email = escape(htmlentities($_POST["email_txt"]));
		}
		$input = true;
	}
	if (isset($_POST["remove_profile_picture"])) {
		$user->profile_picture_url = $user->default_profile_picture_url;
		if ($user->profile_picture_url != $user->default_profile_picture_url)
			unlink($user->profile_picture_url);
	} else if((!empty($_FILES["profile_picture_file"])) && ($_FILES["profile_picture_file"]["error"] == 0)) {
		// no real mime type getter available :(
		if (stristr($_FILES["profile_picture_file"]["type"], "image")) {
			$target_path = "./profile_pictures/images/";
			$extension = explode(".", $_FILES["profile_picture_file"]["name"]);
			$extension = strtolower($extension[count($extension)-1]);
			$allowed_extensions = array("bmp", "jpg", "jpeg", "gif", "png");
			if (in_array($extension, $allowed_extensions)) {
				$target_path = $target_path.md5($user->username).".".$extension;
				if ($user->profile_picture_url != $user->default_profile_picture_url)
					unlink($user->profile_picture_url);
				$user->profile_picture_url = $target_path;
				move_uploaded_file($_FILES["profile_picture_file"]["tmp_name"], $target_path) || array_push($errors, "File could not be saved; please try again.");;
			} else {
				array_push($errors, "Picture type not recognized!");
			}
		}
		$input = true;
	}
	if (isset($_POST["email_notifications"])) {
		$user->email_on_friend_request = isset($_POST["email_on_friend_request"]) ? 1 : 0;
		$user->email_on_pm = isset($_POST["email_on_pm"]) ? 1 : 0;
	}
	if ($errors) {
		$error_message = "<span class='red'>The following errors occurred:</span><br /><ul>";
		foreach ($errors as $key => $value) {
			$error_message .= "<li>".$value."</li>";
		}
		$error_message .= "</ul>";
	} else if ($input) {
		$error_message = "<script type='text/javascript'>$(document).ready(function () { setTimeout(\"\$('#changes_saved').slideUp(500);\", 3000); });</script><span class='green' id='changes_saved'>Changes saved!</span>";
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Account Settings</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="stylesheet.css" />
	<style type="text/css">
		#first_name_txt, #last_name_txt {
			width: 75px;
		}
		#logout {
			float: right;
		}
		#save_changes_btn {
			text-align: center;
		}
	</style>
	<script type="text/javascript" src="./js/jquery.js"></script>
</head>
<body>
	<?php require_once("./php/header.php"); ?>
	<div id="content"><p>
		<table id="inner_content" border="0">
			<tr>
				<td><?php require_once("./php/leftbar.php"); ?></td>
				<td id="content_middle">
						<strong>Account Settings</strong><span id="logout"><a href="logout.php">Log out</a></span><hr />
						<?php echo $error_message; ?>
						<form enctype="multipart/form-data" action="account.php" method="post">
							<p>
								<strong>Name:</strong> <input type="text" name="first_name_txt" id="first_name_txt" value="<?php echo $user->first_name ?>" /> <input type="text" name="last_name_txt" id="last_name_txt" value="<?php echo $user->last_name ?>" /><br /><br />
								<strong>Email:</strong> <input type="text" name="email_txt" id="email_txt" value="<?php echo $user->email; ?>" /><br /><br />
								<strong>Change Password</strong><br />
								<object>
									<table>
										<tr><td>Old password:</td><td><input type="password" name="old_password_txt" id="old_password_txt"/></td></tr>
										<tr><td>New password:</td><td><input type="password" name="new_password_txt" id="new_password_txt"/></td></tr>
										<tr><td>Confirm:</td><td><input type="password" name="confirm_txt" id="confirm_txt"/></td></tr>
									</table>
								</object><br /><br />
								<strong>Email Notifications</strong> (don't work with k12.spsd.net addresses!)<br />
								<input type="hidden" name="email_notifications" value="email_notifications" />
								<input type="checkbox" value="email_on_friend_request" name="email_on_friend_request" id="email_on_friend_request" <?php if ($user->email_on_friend_request == 1) echo "checked=\"checked\""; ?> /><label for="email_on_friend_request"> Email when you get a friend request</label><br />
								<input type="checkbox" value="email_on_pm" name="email_on_pm" id="email_on_pm" <?php if ($user->email_on_pm == 1) echo "checked=\"checked\""; ?> /><label for="email_on_pm"> Email when you get a PM</label><br /><br />
								<strong>Profile Picture</strong>
								<table>
									<tr>
										<td><img src="<?php echo ($user->profile_picture_url); ?>" alt="<?php echo $user->first_name; ?>'s Profile Picture" height="64" width="64" /></td>
										<td><input type="hidden" name="MAX_FILE_SIZE" value="102400" /><input name="profile_picture_file" id="profile_picture_file" type="file" /><br />
										<input type="checkbox" value="remove_profile_picture" name="remove_profile_picture" />Remove Profile Picture<br />
										There is some sort of restriction on your picture upload that I can&#39;t control; if it fails, try a smaller picture.</td>
									</tr>
								</table>
								<div id="save_changes_btn">
									<input type="submit" class="btn" value="Save Changes" />
								</div>
							</p>
						</form>
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