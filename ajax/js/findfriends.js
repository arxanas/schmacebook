$(document).ready(function () {
	$("#find_friends_form").submit(function() {
		if ($("#friend_name").val() != "") {
			document.getElementById("friend_name").disabled = true;
			$("#results").html("Searching... <img src='./images/loading.gif' alt='' />");
			$.post("./ajax/php/findfriends.php", {
				"friend_name": $("#friend_name").val()
			}, function(data, a, b) {
				$("#results").html(data);
				document.getElementById("friend_name").disabled = false;
			});
		}
		return false;
	});
});
function send_request(id) {
	$("#match_"+id).html("Sending... <img src='./images/loading.gif' alt='' />");
	$.post("./ajax/php/findfriends.php", {"friend_id": id}, function (data, a, b) {
		$("#match_"+id).html(data);
	});
}
