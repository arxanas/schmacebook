<?php
class Redirect {
	private $url;
	public function __construct ($url = "http://arxanas.com/schmacebook/") {
		$this->redir($url);
	}
	public function __set ($name, $value = "http://arxanas.com/schmacebook/") {
		if ($name == "url") {
			$this->url = $value;
			$this->redir($this->url);
		}
	}
	public function __get ($name) {
		if ($name == "url") {
			return $this->url;
		}
	}
	private function redir ($url = "http://arxanas.com/schmacebook/") {
		header("Location: ".$url);
		echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Schmacebook</title>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<link rel="stylesheet" href="stylesheet.css" />
	<script type="text/javascript">
		<!--
		window.location == '.$url.';
		//-->
	</script>
</head>
<body>
	<p>
		Redirecting... <a href="'.$url.'">Click here</a> if nothing happens.
	</p>
</body>
</html>';
	}
}
?>