$(document).ready (function () {
	$("#status_txt").val("");
	$("#status_form").submit(function () {
		submit_status();
		return false;
	});
});
function expandRows (textarea_elem) {
	// split into newlines
	var current_rows = textarea_elem.value.split('\n');
	var new_rows = current_rows.length;
	for (i in current_rows) {
		var cols = textarea_elem.cols;
		// if there's more stuff in a line than can fit, add a row for each line of it
		if (current_rows[i].length >= cols) {
			new_rows += Math.floor(current_rows[i].length/textarea_elem.cols);
		}
	}
	textarea_elem.rows = new_rows>1 ? new_rows : 1;
}
function generate_comment_box (id) {
	$("#comment_row_"+id).html("<textarea rows=\"1\" cols=\"33\" id=\"comment_"+id+"\" onkeyup=\"expandRows(this);\"></textarea><br /><input type=\"button\" id=\"comment_submit_btn_"+id+"\" class=\"btn\" onclick=\"submit_comment("+id+");\" value=\"Comment\" />&nbsp;<input onclick=\"cancel_comment_box("+id+");\" type=\"button\" class=\"btn\" value=\"Cancel\" />");
	post_interval_disable_count++;
}
function cancel_comment_box (id) {
	$("#comment_row_"+id).html("<span class='text-left' id=\"comment_row_"+id+"\"><a href=\"javascript: generate_comment_box("+id+");\">Comment</a></span>");
	post_interval_disable_count--;
}