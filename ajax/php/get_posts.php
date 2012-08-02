<?php
//$sql = mysql_query("SELECT * FROM `posts` ORDER BY id LIMIT 0, 20;");
/*
Proposed structure:
Get friend list.
Get last post. If it's from a friend, then add it to post array. If 20 posts, then stop. If not, repeat.
*/
$posts = array();
?> 