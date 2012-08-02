<script type="text/javascript">
	function isIE() {
		return navigator.appName == "Microsoft Internet Explorer" ? true : false;
	}
	$(document).ready(function () {
		$("#footer").html($("#footer").html() + "<div id=\"ie\">It looks like you're using Internet Explorer. Try a faster, more compatible browser instead. [<a href=\"http://google.com/chrome\">Chrome</a>|<a href=\"http://getfirefox.com/\">Firefox</a>]</div>");
	});
</script>