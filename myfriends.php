<?php
ob_start();
require_once("./classes/user.php");
if (!$user->loggedin) {
	require_once("./classes/redirect.php");
	new Redirect();
	exit;
}
if ($user->friends) {
	$myfriends_html = "<table><tr>";
	foreach ($user->friends as $key => $value) {
		$sql = mysql_query("SELECT profile_picture_url, first_name, last_name FROM `users` WHERE id=".$value);
		$sql2 = mysql_fetch_assoc($sql);
		// two friends per row
		if (!($key % 2)) {
			$myfriends_html .="</tr><tr>";
		}
		$full_name = $sql2["first_name"]." ".$sql2["last_name"];
		$myfriends_html .= "<td><img src=\"".(($sql2["profile_picture_url"]) ? ($sql2["profile_picture_url"]) : $user->default_profile_picture_url)."\" alt=\"".$fullname."\" width=\"64\" height=\"64\" /></td>";
		$myfriends_html .= "<td><strong><a href=\"wall.php?u=".$value."\">".$full_name."</a></strong><br /><a href=\"pm.php?u=".$value."\">PM</a><br /><a class='red_link' href=\"unfriend.php?u=".$value."\">Unfriend</a></td>";
	}
	$myfriends_html .= "</tr></table>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>My Friends</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="stylesheet.css" />
	<script type="text/javascript" src="./js/jquery.js"></script>
</head>
<body>
	<?php require_once("./php/header.php"); ?>
	<div id="content"><p>
		<table id="inner_content" border="0">
			<tr>
				<td><?php require_once("./php/leftbar.php"); ?></td>
				<td id="content_middle">
						My friends<hr />
						<?php if ($user->friends) {
							echo $myfriends_html;
						} else {
							echo "You have no friends! <a href=\"findfriends.php\">Find your friends here!</a>";
						}?>
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