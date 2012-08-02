<?php
require_once("db.php");
class User {
	public $loggedin;
	//array won't be created inside __construct for some reason
	private $properties = array("username" => false, "password" => false, "first_name" => false, "last_name" => false, "email" => false, "activated" => false, "profile_picture_url" => false, "date_joined" => false, "id" => false, "friends" => false);
	private $mail_working;
	function __construct() {
		$this->loggedin = false;
		$this->mail_working = true; //mail server is working again
		$this->isloggedin();
	}
	public function __get($var_name) {
		//apparently __get applies to methods too
		if (method_exists(__CLASS__, $var_name)) {
			return $this->$var_name();
		} else if ($var_name == "profile_picture_url") {
			return ($this->properties["profile_picture_url"]) ? $this->properties["profile_picture_url"] : "./profile_pictures/images/default.jpg";
		} else if (array_key_exists($var_name, $this->properties)) {
			return $this->properties[$var_name];
		} else {
			$this->error($var_name);
		}
	}
	public function __set($var_name, $value){
		if (method_exists(__CLASS__, $var_name)) {
			echo "<strong>Warning:</strong> Cannot edit functions.";
		} else {
			switch ($var_name) {
				case "id":
					echo "<strong>Warning:</strong> Cannot edit id.";
					break;
				case "friends":
					echo "<strong>Warning:</strong> Cannot edit friends like this.";
					break;
				default:
					if (array_key_exists($var_name, $this->properties)) {
						$this->properties[$var_name] = $value;
						if (is_string($value)) {
							mysql_query("UPDATE users SET ".$var_name."=\"".$value."\";");
						} else {
							mysql_query("UPDATE users SET ".$var_name."=".$value.";");
						}
					} else {
						$this->error($var_name);
					}
			}
		}
	} 
	public function login ($username, $password, $sanitized=false, $alreadymd5=false) {
		if ($sanitized) {
			$username = escape($username);
			$password = $alreadymd5 ? escape($password) : md5(escape($password));
		} else if (!$alreadymd5) {
			$password = md5($password);
		}
		$sql = mysql_query("SELECT * FROM `users` WHERE username=\"".$username."\" AND password=\"".$password."\";");
		if (!mysql_num_rows($sql))
			return false;
		foreach ($this->properties as $key => $value) {
			switch ($key) {
				case "friends":
					$this->properties["friends"] = $this->get_friends();
					break;
				default:
					$this->properties[$key] = mysql_result($sql, 0, $key);
			}
		}
		setcookie("username", $username, time()+3600);
		setcookie("password", $password, time()+3600);
		return true;
	}
	public function logout () {
		setcookie("username", " ", 1);
		setcookie("password", " ", 1);
	}
	public function isloggedin () {
		if ($this->loggedin) {
			return $this->loggedin;
		} else {
			$username = isset($_COOKIE["username"]) ? escape($_COOKIE["username"]) : false;
			//password is already md5'd
			$password = isset($_COOKIE["password"]) ? escape($_COOKIE["password"]) : false;
			//if they exist, then...
			if ($username !== false && $password !== false) {
				$this->properties["username"] = $username;
				$this->properties["password"] = $password;
				return $this->loggedin = $this->login($username, $password, true, true); //mysql_num_rows(mysql_query("SELECT id FROM `users` WHERE username=\"".$username."\" AND password=\"".$password."\";")) ? true : false;
			} else { // ...otherwise kill them both
				setcookie("username", "", 1);
				setcookie("password", "", 1);
			}
		}
	}
	public function register ($property_values, $sanitized = false) {
		$property_values["password"] = md5($property_values["password"]);
		$sql = "INSERT INTO users (";
		$column_names = "";
		$column_values = "";
		//create sql statement
		if (!isset($property_values["activated"]))
			$property_values["activated"] = 0;
		foreach ($property_values as $key => $value) {
			//don't want these values
			if ($key != "confirm" && $key != "date_joined" && $key != "id" && $key != "activated") {
				$column_names .= "".($sanitized ? $key : escape($key)).", ";
				$column_values .= "\"".($sanitized ? $value : escape($value))."\", ";
			}
		}
		//add parts to query; manual addition of date_joined here
		$sql .= $column_names."date_joined) VALUES (".$column_values."NOW());";
		mysql_query($sql) or die(mysql_error());
		$sql = "SELECT id FROM users WHERE username=\"".$property_values["username"]."\";";
		$sql = mysql_query($sql);
		$id = mysql_result($sql, 0);
		$activation_url = "http://arxanas.com/schmacebook/activate.php?id=".$id."&code=".$property_values["password"];
		$email_site = explode("@", $property_values["email"]);
		$email_site = $email_site[1];
		$mail_success_msg = "An email has been sent to ".unescape($property_values["email"])." for activation. Please go to <a target=\"_blank\" href=\"http://".$email_site."\">".$email_site."</a> to complete the registration process.<br /><br />If the email doesn't arrive within ten minutes, make sure to check your spam/junk folder.";
		$mail_failure_msg = "Oh no! Looks like our mail server isn&#39;t working, so here&#39;s what you would have gotten had it worked:<br /><br />Welcome to Schmacebook, ".unescape($property_values["first_name"])."! To get started, just <a href=\"".$activation_url."\">click here!</a>";
		if ($this->mail_working) {
			//send activation email!
			$to = $property_values["email"];
			$from = "no-reply@sch.arxanas.com";
			$subject = "Registration at Schmacebook";
			$message = "<html><head><title>Activate at Schmacebook</title></head><body>"."<strong>Do not reply to this automatically-generated email!</strong><br />"."Welcome to Schmacebook, ".unescape($property_values["first_name"])."! To get started, just <a href=\"".$activation_url."\">click here!</a><br /><br />If the link is not working, try manually typing this link into your browser address bar: ".$activation_url."</body></html>";
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";		
			$headers .= "From: ".$from;
			return (mail($to, $subject, $message, $headers) !== false) ? $mail_success_msg : $mail_failure_msg;
		} else {
			//return message output
			return $mail_failure_msg;
		}
	}
	public function activate ($id, $code) {
		$sql = "SELECT * FROM users WHERE id=".$id." AND password=\"".$code."\";";
		$sql = mysql_query($sql);
		if (mysql_num_rows($sql)) {
			$sql = "UPDATE users SET activated=1 WHERE id=".$id.";";
			$sql = mysql_query($sql);
			return "<span class=\"green\">You have been successfully activated!</span><br /><a href=\"./index.php\">Click here to log in</a>.";
		} else {
			return "<span class=\"red\">An error occurred. Please check the URL.";
		}
	}
	public function get_friends () {
		$sql = mysql_query("SELECT * FROM friends WHERE friender_id=".$this->__get("id")." OR friendee_id=".$this->__get("id").";");
		$friends = array();
		for ($i=0; $i<mysql_num_rows; $i++) {
			$friender_id = mysql_result($sql, "friender_id");
			if ($friender_id == $this->__get("id")) {
				array_push($friends, $friender_id);
			} else {
				array_push($friends, mysql_result($sql, "friendee_id"));
			}
		}
		return $friends;
	}
	public function get_friend_requests () {
		$sql = mysql_query("SELECT * FROM friend_requests WHERE friender_id=".$this->__get("id")." OR friendee_id=".$this->__get("id").";");
		$friend_requests = array();
		for ($i=0; $i<mysql_num_rows; $i++) {
			$friender_id = mysql_result($sql, "friender_id");
			if ($friender_id == $this->__get("id")) {
				array_push($friend_requests, $friender_id);
			} else {
				array_push($friend_requests, mysql_result($sql, "friendee_id"));
			}
		}
		return $friend_requests;
	}
	public function clearcache () {
		$this->properties = array("username" => false, "password" => false, "first_name" => false, "last_name" => false, "email" => false, "activated" => false, "profile_picture_url" => false, "date_joined" => false, "id" => false);
		$this->loggedin = null;
		$this->isloggedin();
	}
	public function post_status ($content, $to=0) {
		return mysql_query("INSERT INTO posts (`content`, `time_posted`, `author_id`, `to`) VALUES (\"$content\", NOW(), ".$this->__get("id").", $to);"); 
	}
	private function error ($var_name) {
		die("<strong>Fatal error:</strong> Property ".$var_name." does not exist!");
	}
}
$user = new User();
?>