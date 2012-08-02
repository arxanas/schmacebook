function username_check () {
	window.username_ok = false;
	$("r_username_txt").removeClass("notok");
	$("r_username_txt").removeClass("ok");
	if ($("#r_username_txt").val() != "") {
		$("#r_username_txt").addClass("loading");
		$.post("http://arxanas.com/schmacebook/ajax/php/register.php", {
			"mode": "username",
			"param": $("#r_username_txt").val()
		}, function(data, textstatus, xmlhttp){
			$("#r_username_txt").removeClass("loading");
			if (data == "1" && $("#r_username_txt").val() != "") {
				$("#r_username_txt").removeClass("notok");
				$("#r_username_txt").addClass("ok");
				window.username_ok = true;
			}
			else {
				$("#r_username_txt").removeClass("ok");
				$("#r_username_txt").addClass("notok");
				window.username_ok = false;
			}
		});
		return window.username_ok;
	} else {
		$("#r_username_txt").addClass("notok");
		return false;
	}
}