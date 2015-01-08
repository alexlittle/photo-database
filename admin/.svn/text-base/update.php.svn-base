<?php
require_once("../config.php");
require_once("../lib/photolib.php");
getConnection();

$LOCID = "519";
$ADDTAGS = "july, alcala, whiteboard";
$DATE = "2009-07-30";

if($LOCID != ""){
	if ($DATE != ""){
		$mydate = strtotime($DATE);
		$sql = "UPDATE photo SET photodate='".$mydate."' WHERE locid='".$LOCID."'";
		if (!mysql_query($sql,$conn)){
  			echo "error in updating date: ".$sql. "<br/>";
  			return;
  		} else {
  			echo "date updated to: ".$DATE. "<br/>";
  		}
		
	}
	
	if ($ADDTAGS != ""){
		$photos = getPhotosForLocation($LOCID);
		foreach($photos as $photo){
  			addTags($photo->photoid,$ADDTAGS);
  			echo "added tags to: ".$photo->photoid. "<br/>";
  			
  		}
	}
}



?>