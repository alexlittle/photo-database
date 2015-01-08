<?php
	header('Content-type: text/html; charset: utf-8');

?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8" />

<?php
    require_once "config.php";
    require_once "lib/photolib.php";
    getConnection();
    $cloud = getCloud();
    $max = getMaxTagCount();
    cleanUpDB();
?>


</head>


<body>
<h1>Tag Cloud</h1>
<?php 
foreach ( $cloud as $tag){
	$size = ceil((($tag->tagcount/$max)*400) + 100);
	echo "<span style='font-size:".$size."%' title='".$tag->tagcount."'><a href='search.php?terms=".$tag->tagtext."'>";
	echo $tag->tagtext;
	echo "</a></span> ";
}
	
?>
</body>
</html>
