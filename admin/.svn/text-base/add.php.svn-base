<?php

require_once "../config.php";
  require_once "../lib/photolib.php";
  getConnection();
  
   if(isset($_POST["photofile"])){
   	$photofile = trim($_POST["photofile"]);
   } else {
 	$photofile = ".jpg";
 }
 
 
 if(isset($_POST["photolocation"])){
 	$photolocation = trim($_POST["photolocation"]);
 } else {
 	$photolocation = "/photos/";
 }
 
 if(isset($_POST["photodate"])){
 	$photodate = trim($_POST["photodate"]);
 } else {
 	$photodate =  date('d-M-Y');
 }
 
 if(isset($_POST["tags"])){
 	$tags = trim($_POST["tags"]);
 } else {
 	$tags = "";
 }
  if(isset($_POST["photofile"])){
 	addToDatabase($photofile,$photolocation,$photodate,$tags);
 }
 if(isset($_POST["photofile"])){
 	$photofile = trim($_POST["photofile"]);
	/*$temp = split(" ",$photofile);

 	$temp2 = split(".jpg",$temp[3]);
 	$newid = $temp2[0]+1;
 	if(strlen($newid)==1){
 		$newid = "00".$newid;
 	}
	if(strlen($newid)==2){
 		$newid = "0".$newid;
 	}
	if(strlen($newid)==3){
 		$newid = "0".$newid;
 	}
 	$photofile = $temp[0]." ".$temp[1]." ".$temp[2]." ".$newid.".jpg";*/
 	$temp = split("_",$photofile);
 	$temp2 = split(".jpg",$temp[1]);
 	$newid = $temp2[0]+1;
 	if(strlen($newid)==1){
 		$newid = "000".$newid;
 	}
	if(strlen($newid)==2){
 		$newid = "00".$newid;
 	}
	if(strlen($newid)==3){
 		$newid = "0".$newid;
 	}
 	$photofile = $temp[0]."_".$newid.".jpg";
 } else {
 	$photofile = ".jpg";
 }

 cleanUpDB();
?>

<html>


<body>


<form method="post" action="add.php">
<table>
<tr>
	<td>Filename</td>
	<td><input type="text" value="<?php print($photofile);?>" name="photofile"/></td>
</tr>
<tr>
	<td>location</td>
	<td><input type="text" value="<?php print($photolocation);?>" name="photolocation" size="60"/></td>
</tr>

<tr>
	<td>date</td>
	<td><input type="text" value="<?php print($photodate);?>" name="photodate"/></td>
</tr>

<tr>
	<td>tags</td>
	<td><textarea name="tags" cols="50" rows="3"><?php print($tags);?></textarea></td>
</tr>
</table>
<input type="submit" value="post"/>
</form>

</body>


</html>
