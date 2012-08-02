var post_interval_disable_count = 0;
var like_pause = 500; // milliseconds
function submit_status() {
	if (document.getElementById("status_txt").value) {
		document.getElementById("status_txt").disabled = true;
		document.getElementById("status_submit_btn").disabled = true;
		$.post("./ajax/php/send_post.php", {
			"content": document.getElementById("status_txt").value
		}, function(data, a, b) {
			if (data === "0") {
				alert("Post is too long!");
			} else {
				$("#status_txt").val("");
			}
			document.getElementById("status_txt").disabled = false;
			document.getElementById("status_submit_btn").disabled = false;
			get_posts();
			expandRows(document.getElementById("status_txt"));
		});
		return false;
	}
}

function submit_comment(id) {
	if (document.getElementById("comment_" + id).value) {
		document.getElementById("comment_" + id).disabled = true;
		document.getElementById("comment_submit_btn_" + id).disabled = true;
		$.post("./ajax/php/send_comment.php", {
			"content": document.getElementById("comment_" + id).value,
			"id": id
		}, function(id, b, c) {
			if (id.match("_")) {
				alert("Comment is too long!");
				id = id.replace("_", "");
			}
			document.getElementById("comment_" + id).disabled = false;
			document.getElementById("comment_submit_btn_" + id).disabled = true;
			cancel_comment_box(id);
			get_posts();
		});
	}
}

function get_posts() {
	if (post_interval_disable_count == 0) {
		$.post("./ajax/php/get_posts.php", {}, function(data, a, b) {
			$("#post_container").html(data);
		});
	}
}

function show_comments(id) {
	window.temp_comment_id = id;
	post_interval_disable_count++;
	$.post("./ajax/php/get_comments.php", {
		"post_id": id
	}, function(data, a, b) {
		$("#comments_preview_" + window.temp_comment_id).html(data);
		$("#comment_toggle_" + window.temp_comment_id).html("&nbsp;&bull;&nbsp;<a href=\"javascript: hide_comments(" + window.temp_comment_id + ");\">Show less comments</a>");
	});
}

function hide_comments(id) {
	post_interval_disable_count--;
	window.temp_comment_id = id;
	$.post("./ajax/php/get_comments.php", {
		"post_id": id,
		"preview": "true"
	}, function(data, a, b) {
		$("#comments_preview_" + window.temp_comment_id).html(data);
		$("#comment_toggle_" + window.temp_comment_id).html("&nbsp;&bull;&nbsp;<a href=\"javascript: show_comments(" + window.temp_comment_id + ");\">Show all comments</a>");
	});
}

function like_post(id) {
	$("#like_" + id).html("<img src=\"./images/loading.gif\" alt=\"Loading\" />");
	post_interval_disable_count++;
	$.post("./ajax/php/like_post.php", {
		"id": id
	}, function(id, b, c) {
		$("#like_" + id).html("<span class='green'>Liked!</span>");
		post_interval_disable_count--;
		setTimeout("get_posts();", like_pause);
	});
}

function unlike_post(id) {
	$("#like_" + id).html("<img src=\"./images/loading.gif\" alt=\"Loading\" />");
	post_interval_disable_count++;
	$.post("./ajax/php/unlike_post.php", {
		"id": id
	}, function(id, b, c) {
		$("#like_" + id).html("<span class='green'>Unliked!</span>");
		post_interval_disable_count--;
		setTimeout("get_posts();", like_pause);
	});
}

function dislike_post(id) {
	$("#dislike_" + id).html("<img src=\"./images/loading.gif\" alt=\"Loading\" />");
	post_interval_disable_count++;
	$.post("./ajax/php/dislike_post.php", {
		"id": id
	}, function(a, b, c) {
		$("#dislike_" + id).html("&nbsp;&bull;&nbsp;<span class='green'>Disliked!</span>");
		post_interval_disable_count--;
		setTimeout("get_posts();", like_pause);
	});
}

function undislike_post(id) {
	$("#dislike_" + id).html("<img src=\"./images/loading.gif\" alt=\"Loading\" />");
	post_interval_disable_count++;
	$.post("./ajax/php/undislike_post.php", {
		"id": id
	}, function(a, b, c) {
		$("#dislike_" + id).html("&nbsp;&bull;&nbsp;<span class='green'>Undisliked!</span>");
		post_interval_disable_count--;
		setTimeout("get_posts();", like_pause);
	});
}

function delete_post(id) {
	$.post("./ajax/php/delete_post.php", {
		"id": id
	}, function () {
		get_posts();
	});
}
function delete_comment(id) {
	$.post("./ajax/php/delete_comment.php", {
		"id": id
	}, function () {
		get_posts();
	});
}
$(document).ready(function() {
	get_posts();
	post_inverval = setInterval("get_posts();", 30000);
});