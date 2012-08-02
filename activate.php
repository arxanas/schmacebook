<?php
ob_start();
require_once("./classes/user.php");
$id = $_GET["id"];
$code = $_GET["code"];
$errors = (!$id || !$code) ? true : false;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Schmacebook</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="./stylesheet.css" />
	<link rel="shortcut icon" href="./favicon.ico" />
</head>
<body>
	<?php require_once("./php/header.php"); ?>
	<div id="content">
		<?php if ($errors) { ?>
			<span class="red">
				An error occurred. Please check the URL.
			</span>
		<?php } else {
			echo $user->activate($id, $code);
		} ?>
	</div>
	<?php require_once("./php/footer.php"); ?>
</body>
</html>
<?php ob_end_flush(); ?>