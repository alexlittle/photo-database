<?php
/*
 * Created on 7 Dec 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once "../config.php";
  require_once "../lib/photolib.php";
  getConnection();
  
   if(isset($_GET["id"])){
	$picid = $_GET["id"];
   }
   
   if(isset($_POST["photofile"])){
   	$photofile = trim($_POST["photofile"]);
   } 
 
 
 if(isset($_POST["photolocation"])){
 	$photolocation = trim($_POST["photolocation"]);
 }
 
 if(isset($_POST["photodate"])){
 	$photodate = trim($_POST["photodate"]);
 } 
 
 if(isset($_POST["tags"])){
 	$tags = trim($_POST["tags"]);
 } 
 
 if(isset($_POST["photofile"])){
 	$pic = edit($picid,$photofile,$photolocation,$photodate,$tags);
 	header("Location:loc.php?locid=".$pic->locid."#".$pic->photoid);
 	echo "updated <a href='loc.php?locid=".$pic->locid."'>return to location</a>";
 }
 
 if(isset($_GET["id"])){
	$pic = getPhoto($picid);
   }
   
 cleanUpDB();
header('Content-Type:text/html; charset=UTF-8');
?>

<html>


<body>

<?php echo displayImage($pic);?>

<form method="post" action="">
<table>
<tr>
	<td>Filename</td>
	<td><input type="text" value="<?php print($pic->photofile);?>" name="photofile"/></td>
</tr>
<tr>
	<td>location</td>
	<td><input type="text" value="<?php print($pic->photolocation);?>" name="photolocation" size="40"/></td>
</tr>

<tr>
	<td>date</td>
	<td><input type="text" value="<?php print(date('d-M-Y',$pic->photodate));?>" name="photodate"/></td>
</tr>

<tr>
	<td>tags</td>
	<td><textarea name="tags" cols="50" rows="3"><?php print($pic->tags);?></textarea></td>
</tr>
</table>
<input type="submit" value="post"/>
</form>

</body>


</html>
