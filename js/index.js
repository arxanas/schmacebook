$(document).ready (function () {
	$("#status_txt").val("");
	$("#status_form").submit(function () {
		submit_status();
		return false;
	});
});
function expandRows (textarea_elem) {
	var current_rows = textarea_elem.value.split('\n');
	var new_rows = current_rows.length;
	for (i in current_rows) {
		var cols = 36;
		if (browser == "gc") {
			cols = 41;
		}
		if (current_rows[i].length >= cols) {
			new_rows += Math.floor(current_rows[i].length/textarea_elem.cols);
			Math.floor(current_rows[i].length/textarea_elem.cols);
		}
	}
	textarea_elem.rows = new_rows>1 ? new_rows : 1;
}