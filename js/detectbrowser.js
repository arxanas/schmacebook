var useragent = navigator.userAgent;
var browser = "ff";
if (useragent.match("Firefox")) {
	browser = "ff";
} else if (useragent.match("MSIE")) {
	browser = "ie";
} else if (useragent.match("Chrome")) {
	browser = "gc";
} else if (useragent.match("Safari")) { 
	browser = "sf";
} else if (useragent.match("Opera")) {
	browser = "op";
}