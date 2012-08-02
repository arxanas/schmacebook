<?php
ob_start();
require_once("./classes/user.php");
$feedback = isset($_POST["feedback_txt"]) ? $_POST["feedback_txt"] : false;
if (!$user->loggedin) {
	require_once("./classes/redirect.php");
	new Redirect();
	exit;
}
if ($feedback !== false) {
	$to = "arxanas@gmail.com";
	$subject = "Feedback from ".$user->first_name." ".$user->last_name;
	$headers = "From: no-reply@schmacebook.net";
	$message = $user->first_name." ".$user->last_name." said this:\n".$feedback;
	mail($to, $subject, $message, $headers);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Submit Feedback</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="stylesheet.css" />
	<script type="text/javascript" src="./js/jquery.js"></script>
	<script type="text/javascript" src="./js/index.js"></script>
</head>
<body>
	<?php require_once("./php/header.php"); ?>
	<div id="content"><p>
		<table id="inner_content" border="0">
			<tr>
				<td><?php require_once("./php/leftbar.php"); ?></td>
				<td id="content_middle">
					<div class='centered'>
						<form action="feedback.php" method="post">
							<?php if ($feedback !== false) {
								echo "Thank you for your feedback!";
							}  else { ?>
							Submit your feedback, bug reports, and death threats:<br />
							<textarea onkeyup="expandRows(this);" name="feedback_txt" id="feedback_txt" rows="1" cols="40"></textarea><br />
							<input type="submit" class="btn" value="Submit Feedback" />
							<?php } ?>
						</form>
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
ob_end_flush();
?>