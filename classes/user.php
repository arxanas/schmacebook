<?php
require_once("db.php");
class User {
	private $properties = array("id" => false, "loggedin" => false, "username" => false, "password" => false, "first_name" => false, "last_name" => false, "email" => false, "activated" => false, "profile_picture_url" => false, "date_joined" => false, "friends" => false, "friend_requests" => false);
	
	// some constants that aren't actually constants, but that's too bad.
	private $mail_working;
	private $login_time;
	public $default_profile_picture_url;
	function __construct() {
		// some constants
		$this->mail_working = true; // mail server is working again
		$this->login_time = 60*60*24; // one day
		$this->default_profile_picture_url = "./profile_pictures/images/default.jpg";
		// assume not logged in
		$this->properties["loggedin"] = false;
		// check for login
		$this->isloggedin();
	}
	public function __get($var_name) {
		// apparently __get applies to methods too
		// if this isn't here, then you can't run functions from outside the class
		if (method_exists(__CLASS__, $var_name)) {
			return $this->$var_name();
		} else if ($var_name == "profile_picture_url") {
			// if profile picture isn't set, just return the default one
			return ($this->properties["profile_picture_url"]) ? $this->properties["profile_picture_url"] : $this->default_profile_picture_url;
		} else if (array_key_exists($var_name, $this->properties)) {
			return $this->properties[$var_name];
		} else if (isset($this->$var_name)) {
			return $this->$var_name;
		} else {
			die("<strong>Fatal error:</strong> Property ".$var_name." does not exist!");
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
				echo "<strong>Warning:</strong> Cannot edit friends via setter.";
				break;
			default:
				if (array_key_exists($var_name, $this->properties)) {
					$this->properties[$var_name] = $value;
					if (is_string($value)) {
						mysql_query("UPDATE users SET ".$var_name."=\"".$value."\" WHERE id=".$this->properties["id"].";");
					} else {
						mysql_query("UPDATE users SET ".$var_name."=".$value." WHERE id=".$this->properties["id"].";");
					}
				} else {
					$this->$var_name = $value;
				}
			}
		}
	}
	public function isloggedin () {
		if ($this->properties["loggedin"]) {
			return $this->properties["loggedin"];
		} else {
			// escape the cookies!
			$username = isset($_COOKIE["username"]) ? escape($_COOKIE["username"]) : false;
			$password = isset($_COOKIE["password"]) ? escape($_COOKIE["password"]) : false;
			// if they exist
			if ($username !== false && $password !== false) {
				$this->properties["username"] = $username;
				$this->properties["password"] = $password;
				// renew the log in! You don't want to be logged out in the middle of a session
				return $this->properties["loggedin"] = $this->login($username, $password, true, true);
			} else {
				// no point keeping useless cookies
				$this->logout();
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
		// if a user with those credentials DOESN'T exist...
		$sql = mysql_query("SELECT * FROM `users` WHERE username=\"".$username."\" AND password=\"".$password."\";");
		if (!mysql_num_rows($sql))
			return false;
			
		// otherwise get all the properties from the database and set cookies
		$this->get_properties();
		setcookie("username", $username, time()+$this->login_time);
		setcookie("password", $password, time()+$this->login_time);
		return true;
	}
	public function logout () {
		setcookie("username", " ", 1);
		setcookie("password", " ", 1);
	}
	private function get_properties () {
		$properties = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE username=\"".$this->properties["username"]."\";"));
		foreach ($this->properties as $key => $value) {
			switch ($key) {
			// these properties are already set
			case "loggedin":
			case "username":
			case "password":
				break;
				
			// the friend stuff don't literally exist in the database, they have to be found manually
			case "friends":
				// due to the fact that I didn't think of any other, more efficient way,
				// friends are found by whether they're in either of the "friender_id"
				// OR the "friendee_id" slot
				$sql = mysql_query("SELECT * FROM friends WHERE friender_id=".$this->__get("id")." OR friendee_id=".$this->__get("id").";");
				$this->properties["friends"] = array();
				for ($i=0; $i<mysql_num_rows($sql); $i++) {
					// don't worry! the user id is set before the friends are found
					if (mysql_result($sql, "friender_id") == $this->properties["id"]) {
						// if the friender is you, then you need the OTHER person - the friendee
						array_push($this->properties["friends"], mysql_result($sql, "friendee_id"));
					} else {
						// if the friendee is you, then you need the friender
						array_push($this->properties["friends"], mysql_result($sql, "friender_id"));
					}
				}
				break;
			// friend requests work pretty much exactly the same
			case "friend_requests":
				$sql = mysql_query("SELECT * FROM friends WHERE friender_id=".$this->__get("id")." OR friendee_id=".$this->__get("id").";");
				$this->properties["friend_requests"] = array();
				for ($i=0; $i<mysql_num_rows($sql); $i++) {
					if (mysql_result($sql, "friender_id") == $this->properties["id"]) {
						array_push($this->properties["friend_requests"], mysql_result($sql, "friendee_id"));
					} else {
						array_push($this->properties["friend_requests"], mysql_result($sql, "friender_id"));
					}
				}
				break;
				
			//just pull everything else from the fetched row
			default:
				$this->properties[$key] = $properties[$key];
			}
		}
	}
	
	// since the user isn't logged in, registration and activation are a good deal
	// harder than just setting values
	public function register ($property_values, $sanitized = false) {
		// passwords are always md5'd
		$property_values["password"] = md5($property_values["password"]);
		
		// creation of sql statement to create user
		$sql = "INSERT INTO users (";
		$column_names = "";
		$column_values = "";
		
		if (!isset($property_values["activated"]))
			$property_values["activated"] = 0;
			
		foreach ($property_values as $key => $value) {
			//don't want these values
			if ($key != "confirm" && $key != "date_joined" && $key != "id" && $key != "activated") {
				$column_names .= "".($sanitized ? $key : escape($key)).", ";
				$column_values .= "\"".($sanitized ? $value : escape($value))."\", ";
			}
		}
		
		// add parts to query; manual addition of date_joined here
		$sql .= $column_names."date_joined) VALUES (".$column_values."NOW());";
		mysql_query($sql) or die(mysql_error());
		
		// after creation, get the user id
		$sql = "SELECT id FROM users WHERE username=\"".$property_values["username"]."\";";
		$sql = mysql_query($sql);
		$id = mysql_result($sql, 0);
		
		// start making the activation email
		$activation_url = "http://arxanas.com/schmacebook/activate.php?id=".$id."&code=".$property_values["password"];
		
		// get what's probably their email site (abc@gmail.com would be gmail.com, or foobar@asdfhjkl.example.com is
		// asdfhjkl.example.com)
		$email_site = explode("@", $property_values["email"]);
		$email_site = $email_site[1];
		
		$mail_success_msg = "An email has been sent to ".unescape($property_values["email"])." for activation. Please go to <a href=\"http://".$email_site."\">".$email_site."</a> to complete the registration process.<br /><br />If the email doesn't arrive within ten minutes, make sure to check your spam/junk folder.";
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
}
$user = new User();
?>