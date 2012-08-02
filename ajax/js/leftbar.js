function accept_request (id) {
	$("#friend_request_"+id).html("<img src='./images/loading.gif' alt='' />");
	$.post("./ajax/php/leftbar.php", {"mode": "accept", "id": id}, function (data, a, b) {
		$("#friend_request_"+id).fadeOut(500, function () {
			$("#friend_request_"+id).html(data);
			$("#friend_request_"+id).fadeIn(500, function () {
				$("#friend_request_row_"+id).slideUp(500);
			});
		});
		$("#request_count").fadeOut(500, function () {
			if (parseInt($("#request_count").text()) - 1) {
				$("#request_count").text(parseInt($("#request_count").text()) - 1);
			} else {
				$("#request_container").fadeOut(500);
			}
			$("#request_count").fadeIn(500);
		});	
	});
}
function deny_request (id) {
	$("#friend_request_"+id).html("<img src='./images/loading.gif' alt='' />");
	$.post("./ajax/php/leftbar.php", {"mode": "deny", "id": id}, function (data, a, b) {
		$("#friend_request_row_"+id).fadeOut(500, function () {
			$("#friend_request_row_"+id).html(data);
			$("#friend_request_row_"+id).fadeIn(500, function () {
				$("#friend_request_row_"+id).slideUp(500);
			});
		});
		$("#request_count").fadeOut(500, function () {
			if (parseInt($("#request_count").text()) - 1) {
				$("#request_count").text(parseInt($("#request_count").text()) - 1);
			} else {
				$("#request_container").fadeOut(500);
			}
			$("#request_count").fadeIn(500);
		});	
	});
}
