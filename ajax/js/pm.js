function delete_pm (pm_id) {
	$("#pm_delete_"+pm_id).html("<span class='red><img src=\"./images/loading.gif\" alt=\"\" /></span>");
	$.post("./ajax/php/delete_pm.php", {"pm_id": pm_id}, function (pm_id, a, b) {
		$("#pm_delete_"+pm_id).html("<span class='green'>Deleted!</span>");
		setTimeout("$('#pm_"+pm_id+"').slideUp(500);", 2500);
	});
}
