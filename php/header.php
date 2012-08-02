	<div id="header_bar">
		<div id="header_content">
			<div id="header_left">
				Schmacebook
			</div>
			<div id="header_right">
				<?php if ($user->loggedin) { ?>
					<ul>
						<li><a href="account.php">Account</a></li>
						<li><a href="wall.php">Wall</a></li>
						<li><a href="index.php">Home</a></li>

					</ul>
				<?php } else { ?>
					Not logged in!
				<?php } ?>
			</div>
		</div>
	</div>