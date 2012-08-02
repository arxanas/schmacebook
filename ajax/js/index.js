function submit_status () {
	document.getElementById("status_txt").disabled = true;
	$.post("./ajax/php/send_post.php", {"content": $("#status_txt").val()}, function () {
		document.getElementById("status_txt").disabled = false;
		$("#status_txt").val("");
	});
}
