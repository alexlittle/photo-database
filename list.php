<?php
/*
 * Created on 15 Dec 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once "config.php";
  require_once "lib/photolib.php";
  getConnection();
  	$cloud = getCloud();
	$max = getMaxTagCount();
  cleanUpDB();
header('Content-Type:text/html; charset=UTF-8');
?>


<html>


<body>

<h1>Tag List</h1>
<?php 
foreach ( $cloud as $tag){	
	echo "<li>";
	echo "<a href='search.php?terms=".$tag->tagtext."'>";
	echo $tag->tagtext;
	echo "</a> (".$tag->tagcount.")";
	echo "</li>";
}
	
?>
</body>
