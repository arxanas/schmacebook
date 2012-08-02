function send_suggestion_request(id) {
	$("#suggestion_"+id).html("Sending... <img src='./images/loading.gif' alt='' />");
	$.post("./ajax/php/findfriends.php", {"friend_id": id}, function (data, a, b) {
		$("#suggestion_"+id).fadeOut(500, function () {
			$("#suggestion_"+id).html(data);
			$("#suggestion_"+id).fadeIn(500, function () {
				$("#suggestion_row_"+id).slideUp(500);
			});
		});
	});
}