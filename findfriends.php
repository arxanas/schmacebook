<?php
ob_start();
require_once("./classes/user.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Find Friends</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="./stylesheet.css" />
	<link rel="shortcut icon" href="./favicon.ico" />
	<style type="text/css">
		#friend_name {
			width: 70%;
		}
	</style>
	<script type="text/javascript" src="./js/jquery.js"></script>
	<script type="text/javascript" src="./ajax/js/findfriends.js"></script>
</head>
<body>
	<?php require_once("php/header.php"); ?>
	<div id="content">
		<?php if ($user->loggedin) { ?>
			<table id="inner_content" border="0">
				<tr>
					<td>
						<?php require_once("./php/leftbar.php"); ?>
					</td>
					<td id="content_middle">
						<form id="find_friends_form" action="findfriends.php" method="post">
							<p>Type a friend&#39;s name: <input type="text" name="friend_name" id="friend_name" value="<?php echo isset($_POST["friend_name"]) && $_POST["friend_name"] ? unescape($_POST["friend_name"]) : ""; ?>"/>
							<input type="submit" class="btn" value="Search" /></p>
						</form>
						<div id="results">
						
						</div>
					</td>
					<td>
						<?php require_once("./php/rightbar.php"); ?>
					</td>
				</tr>
			</table>
		<?php
		require_once("php/footer.php");
		} else { 
			require_once("./classes/redirect.php");
			new Redirect();
		} ?>
	</div>
</body>
</html>
<?php ob_end_flush(); ?>