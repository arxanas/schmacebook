//radio in jquery is a pain
function checker(objectNum, val) {
	//0 is login, 1 is register
	if (val == null) {
		return document.getElementById("rl_form").rl_option[objectNum].checked;
	} else {
		return document.getElementById("rl_form").rl_option[objectNum].checked = val;
	}
}

function confirm_check() {
	if ($("#r_password_txt").val() == "" || $("#r_confirm_txt").val() == "") {
		if ($("#r_password_txt").val() == "") {
			elementnotok("r_password_txt");
		}
		if ($("#r_confirm_txt").val() == "") {
			elementnotok("r_confirm_txt");
		}
		return false;
	} else 
		if ($("#r_password_txt").val() != $("#r_confirm_txt").val()) {
			elementnotok("r_password_txt");
			elementnotok("r_confirm_txt");
			return false;
		} else {
			elementok("r_password_txt");
			elementok("r_confirm_txt");
			return true;
		}
}

function email_check() {
	var email_regex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
	if (email_regex.test($("#r_email_txt").val())) {
		elementok("r_email_txt");
		return true;
	} else {
		elementnotok("r_email_txt");
		return false;
	}
}

function first_name_check() {
	if ($("#r_first_name_txt").val() != "") {
		elementok("r_first_name_txt");
		return true;
	} else {
		elementnotok("r_first_name_txt");
		return false;
	}
}

function last_name_check() {
	if ($("#r_last_name_txt").val() != "") {
		elementok("r_last_name_txt");
		return true;
	} else {
		elementnotok("r_last_name_txt");
		return false;
	}
}

function elementok(element_name) {
	$("#" + element_name).removeClass("notok");
	$("#" + element_name).addClass("ok");
}

function elementnotok(element_name) {
	$("#" + element_name).removeClass("ok");
	$("#" + element_name).addClass("notok");
}

$(document).ready(function() {
	if (document.URL.split("/")[document.URL.split("/").length - 1] != "register.php") {
		$("input[name='rl_option']").change(function() {
			//if it is login...
			if (checker(0)) {
				$("#register").slideUp(500);
				$("#login").slideDown(500);
			} else { //else if it is register
				$("#login").slideUp(500);
				$("#register").slideDown(500);
			}
		});
		checker(0, true);
		$("#register").slideUp(500);
	} else {
		$("#register").show(0);
	}
	$.ajaxSetup({
			async: false
		});
	$("#register_form").submit(function() {
		errors = new Array();
		if (!username_check()) {
			if ($("#r_username_txt").val() == "") {
				errors.push("Username is blank!");
			} else {
				errors.push("Username already taken!");
			}
		}
		if (!confirm_check()) {
			if ($("#r_password_txt").val() == "" || $("#r_confirm_txt").val() == "") {
				if ($("#r_password_txt").val() == "") {
					errors.push("Password is blank!");
				}
				if ($("#r_confirm_txt").val() == "") {
					errors.push("Confirmation is blank!");
				}
			} else {
				if ($("#r_password_txt").val() != $("#r_confirm_txt").val()) {
					errors.push("Password and confirmation don&#39;t match!");
				}
			}
		}
		if (!email_check()) {
			errors.push("Email is not valid!");
		}
		if (!first_name_check()) {
			errors.push("First name is blank!");
		}
		if (!last_name_check()) {
			errors.push("Last name is blank!");
		}
		if (errors.length) {
			window.errors = errors;
			$("#r_errors").slideUp(500, function() {
				errors = window.errors;
				if (errors.length > 0) {
					var error_string = "<table><tr><td><span class=\"red\">The following errors occurred:</span><br /><ul>";
					for (var i = 0; i < errors.length; i++) {
						error_string += "<li>" + errors[i] + "</li>";
					}
					error_string += "</ul></td></tr></table>";
					$("#r_errors").html(error_string);
				}
				$("#r_errors").slideDown(500);
			});
		}
		return (errors.length) ? false : true;
	});
});
