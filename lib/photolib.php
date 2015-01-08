<?php
/*
 * Created on 7 Dec 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 $conn = false;
   
    function getConnection()
    {
        global $conn, $CONFIG;
        if( $conn )
            return $conn;
        $conn = mysqli_connect( $CONFIG->DB_HOST, $CONFIG->DB_USER, $CONFIG->DB_PASS, $CONFIG->DB_NAME) or die('Could not connect to server.' );
		mysql_query("SET NAMES utf8");
        return $conn;
    }
   
    function cleanUpDB()
    {
        global $conn;
        if( $conn != false )
            mysql_close($conn);
        $conn = false;
    } 
    
    function addToDatabase($file,$loc, $date,$tags){
    	global $conn,$CONFIG;
    	$mydate = strtotime($date);

    	$image = $CONFIG->IMAGE_ROOT.$loc.$file;
    	$exifdate = 0;
    	$exif = exif_read_data($image, 0, true);

    	foreach ($exif as $key => $section) {
    		if ($key == "FILE"){
	    		foreach ($section as $name => $val) {
	    			if($name == "FileDateTime"){
	    				$exifdate = $val;
	    			}
	    		}
    		}
    	}
    	
    	if($exifdate != 0){
    		$mydate = $exifdate;
    	}
    	
		$locid = getLocId($loc);
    	$sql = "INSERT INTO photo (photofile, locid, photodate) VALUES ('".$file."',".$locid.", ".$mydate.")";
    	if (!$conn->query($sql)){
  			echo "already added that one!";
  			if($exifdate != 0){
	  			//get id and update date
	  			$ssql = sprintf("SELECT photoid from photo WHERE photofile='%s' and locid=%d",$file,$locid);
	  			$res = $conn->query($sql);
	  			while($o= mysql_fetch_object($res)){
	  				$usql = sprintf("UPDATE photo SET photodate='%s' WHERE photoid=%d",$exifdate,$o->photoid);
	  				$conn->query($usql);
	  				echo "updated date<br/>";
	  				addTags($o->photoid,date('Y,F',$exifdate));
	  				addTags($o->photoid,$tags);
	  			}
  			}
  			return;
  		}
  		
  		//get the most recent photoid
  		$newid = mysql_insert_id();
  		if($exifdate != 0){
  			addTags($newid,date('Y,F',$exifdate));
  		}
  		addTags($newid,$tags);
    }
    
    function addTags($photoid,$tags){
    	global $conn;
   	 	//get the tagsid array
  		$tagsIdArray = processTags($tags);
  		
  		foreach($tagsIdArray as $tagId){
  			$sql = "INSERT INTO phototag (photoid,tagid) VALUES (".$photoid.",".$tagId.")";
	    	if (!mysql_query($sql,$conn)){
	  			echo('Couldn\'t add tag: '.$tagId);
	  		}
  		}
    	
    }
    
    function removeTags($photoid,$tags){
    	global $conn;
   	 	//get the tagsid array
  		$tagsIdArray = processTags($tags);
  		
  		foreach($tagsIdArray as $tagId){
  			$sql = "DELETE FROM phototag WHERE photoid=".$photoid." AND tagid=".$tagId;
	    	if (!mysql_query($sql,$conn)){
	  			echo('Couldn\'t remove tag: '.$tagId);
	  		}
  		}
    	
    }
    
    function processTags($tags){
    	$tagIdArr = array();
    	//split tags and loop through
    	$tagsArr = preg_split("/,/",$tags);
    	foreach ( $tagsArr as $tag){
    		//getTag id
    		$tagid = getTagID($tag);
    		if ($tagid != 0){
    			array_push($tagIdArr,$tagid);
    		}
    	}
    	
    	$newArr = array_unique($tagIdArr);
    	return $newArr;
    }
    
    function getTagId($tag){
    	global $conn;
    	$tagId = 0;
    	//$tag = strtolower(trim($tag));
    	$tag = trim($tag);
    	if($tag ==""){
    		return $tagId;
    	}
    	$sql = "SELECT tagid FROM tag WHERE tagtext = '".$tag."'";
    	$result = mysql_query($sql,$conn);
  		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
		   	$tagId = $row['tagid'];
		} 
		if($tagId == 0){
			$sql = "INSERT INTO tag (tagtext) VALUES ('".$tag."')";
			if (!mysql_query($sql,$conn)){
  				die('Error: ' . mysql_error());
  			}
			$sql = "SELECT MAX(tagid) AS newid FROM tag";
			$result = mysql_query($sql,$conn);
	  		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			   	$tagId = $row['newid'];
			}
		}
    	return $tagId;
    }
    
    function getLocations(){
    	global $conn;
    	$sql = "SELECT COUNT(*) AS photocount, l.location,l.locid FROM photo p 
		INNER JOIN location l ON l.locid = p.locid
		GROUP BY l.location,l.locid 		ORDER BY location DESC;";
    	$locArr = array();
    	$result = mysql_query($sql,$conn);
	  		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	  			$loc = arrayToObject($row);
			   	array_push($locArr,$loc);
			}
    	
    	return $locArr;
    }
    
    function getPhotosForLocation($loc){
    	global $conn;
    	$sql = "SELECT p.photoid, p.photofile,p.photodate,l.location AS photolocation FROM photo p 
		INNER JOIN location l ON l.locid = p.locid
		WHERE l.locid = ".$loc." ORDER BY photofile ASC";
    	$picArr = array();
    	$result = mysql_query($sql,$conn);
  		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
  			$pic = arrayToObject($row);
  			$pic->tags = getTagsAsString($row['photoid']);
		   	array_push($picArr,$pic);
		}
    	return $picArr;
    }
    
    function getPhoto($id){
    	global $conn;
    	$sql = "SELECT p.photoid, p.photofile,l.location AS photolocation,p.photodate,p.locid FROM photo p INNER JOIN location l ON l.locid = p.locid WHERE photoid = ".$id;
    	$pic = new stdClass();
    	$result = mysql_query($sql,$conn);
	  		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	  			$pic = arrayToObject($row);
			}
    	
    	//get tags
    	$pic->tags = getTagsAsString($pic->photoid);
    	return $pic;
    	
    }
    
    function getTagsAsString($id,$withlinks=false){
   		global $conn;
   		$tagstr = "";
    	$sql = "SELECT t.tagtext FROM phototag pt INNER JOIN tag t ON t.tagid = pt.tagid WHERE pt.photoid = ".$id;
    	$tagArr = array();
    	$result = mysql_query($sql,$conn);
	  		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
				if($withlinks){
					array_push($tagArr,'<a href="search.php?terms='.$row['tagtext'].'">'.$row['tagtext'].'</a>');
				} else {
	  				array_push($tagArr,$row['tagtext']);
				}
			}	
    	$tagstr = join($tagArr,", ");
    	return $tagstr;
    	
    }
    
     function edit($id,$file,$loc,$date,$tags){
    	global $conn;
    	$mydate = strtotime($date);
    	$locid = getLocId($loc);
    	$sql = "UPDATE photo SET ".
				"photofile = '".$file."', ".
				"locid = ".$locid.", ".
				"photodate = ".$mydate.
				" WHERE photoid =".$id;
    	if (!mysql_query($sql,$conn)){
  			echo "error updating";
  			return;
  		}
  		
  		//get the most recent photoid
  		$sql = "DELETE FROM phototag WHERE photoid =".$id;
  		if (!mysql_query($sql,$conn)){
  			echo "error updating";
  			return;
  		}
  		
  		//get the tagsid array
  		$tagsIdArray = processTags($tags);
  		
  		foreach($tagsIdArray as $tagId){
  			$sql = "INSERT INTO phototag (photoid,tagid) VALUES (".$id.",".$tagId.")";
	    	if (!mysql_query($sql,$conn)){
	  			die('Error: ' . mysql_error());
	  		}
  		}

		return getPhoto($id);	
    }
    
    function getAllPhotos(){
    	global $conn;
    	
    	$sql = sprintf("SELECT p.photoid, l.location, p.photofile, propint FROM location l 
    						INNER JOIN photo p ON l.locid = p.locid 
    						INNER JOIN photoprops pp ON p.photoid = pp.photoid
					    	ORDER By propint DESC");
    	$photos = array();
    	$result = mysql_query($sql,$conn);
    	while($o = mysql_fetch_object($result)){
    		array_push($photos,$o);
    	}
    	return $photos;
    	
    }
    
    function getClosestPhoto($r,$g,$b){
    	global $conn;
    	$sql = sprintf("SELECT p.*, l.location
						FROM colours c
    					INNER JOIN photo p ON p.photoid = c.photoid
    					INNER JOIN location l ON l.locid = p.locid
						WHERE abs( r -%d ) + abs( g -%d ) + abs( b -%d ) = (
						SELECT min( abs( r -%d ) + abs( g -%d ) + abs( b -%d ) ) AS compcolour
						FROM colours ) ",$r,$g,$b,$r,$g,$b);
    	/*$sql = sprintf("SELECT * FROM photoprops pp
    						INNER JOIN photo p ON p.photoid = pp.photoid
    						INNER JOIN location l ON l.locid = p.locid
							WHERE propint = (SELECT %d-min(abs(%d-propint)) FROM photoprops)
							OR propint =  (SELECT %d+min(abs(%d-propint)) FROM photoprops)
							LIMIT 0,1",$colour,$colour,$colour,$colour);*/
    	$result = mysql_query($sql,$conn);
    	while($o = mysql_fetch_object($result)){
    		return $o;
    	}
    }
    
    function getClosestUnusedPhoto($r,$g,$b,$used){
    	global $conn;
    	$sql = sprintf("SELECT p.*, l.location
						FROM colours c
    					INNER JOIN photo p ON p.photoid = c.photoid
    					INNER JOIN location l ON l.locid = p.locid
						WHERE abs( r -%d ) + abs( g -%d ) + abs( b -%d ) = (
						SELECT min( abs( r -%d ) + abs( g -%d ) + abs( b -%d )) AS compcolour
						FROM colours WHERE photoid NOT IN (%s)) 
    					and p.photoid NOT IN (%s)",$r,$g,$b,$r,$g,$b,$used,$used);
    	$result = mysql_query($sql,$conn);
    	while($o = mysql_fetch_object($result)){
    		return $o;
    	}
    }
    
    function savePhotoProp($photoid, $propname, $propint=0, $propvalue=""){
    	global $conn;
    	$sql = sprintf("SELECT * FROM photoprops WHERE photoid=%d and propname='%s'",$photoid,$propname);
    	$result = mysql_query($sql,$conn);
    	while($row = mysql_fetch_object($result)){
    		//update
    		$updatesql = sprintf("UPDATE photoprops SET propint=%d, propvalue='%s' WHERE photoid=%d and propname='%s'",
    							$propint,$propvalue, $photoid, $propname);
    		mysql_query($updatesql,$conn);
    		return;
    	}
    	//else insert
    	$insertsql = sprintf("INSERT INTO photoprops (photoid,propname,propint,propvalue) VALUES (%d,'%s',%d,'%s')",
    		$photoid, $propname,$propint, $propvalue);
    	mysql_query($insertsql,$conn);
    }
    
    function savePhotoColour($photoid,$r,$g,$b,$alpha, $colourint){
    	global $conn;
    	$sql = sprintf("SELECT * FROM colours WHERE photoid=%d",$photoid);
    	$result = mysql_query($sql,$conn);
    	while($row = mysql_fetch_object($result)){
    		//update
    		$updatesql = sprintf("UPDATE colours SET r=%d,g=%d,b=%d,alpha=%d, colourint=%d WHERE photoid=%d",
    				$r,$g,$b,$alpha,$colourint, $photoid);
    		mysql_query($updatesql,$conn);
    		return;
    	}
    	//else insert
    	$insertsql = sprintf("INSERT INTO colours (photoid,r,g,b,alpha,colourint) VALUES (%d,%d,%d,%d,%d,%d)",
    			$photoid, $r,$g,$b,$alpha,$colourint);
    	mysql_query($insertsql,$conn);
    }
    
    function getCloud(){
    	global $conn;
    	$sql = "SELECT COUNT(*) AS tagcount , tagtext FROM phototag pt ".
    			"INNER JOIN tag t ON t.tagid = pt.tagid ".
				"GROUP BY tagtext ".
                //"HAVING count(*)>10 ".
				"ORDER BY tagtext ASC";
    	$cloud = array();
    	$result = mysql_query($sql,$conn);
		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
			$tag = arrayToObject($row);
			array_push($cloud,$tag);
		}
    	return $cloud;
    }
    
    function getMaxTagCount(){
    	global $conn;
    	$sql = "SELECT MAX(tagcount) as maxcount FROM " .
    			"(SELECT COUNT(*) AS tagcount , tagtext FROM phototag pt ".
    			"INNER JOIN tag t ON t.tagid = pt.tagid ".
				"GROUP BY tagtext) temp";
    	$result = mysql_query($sql,$conn);
    	$max = 0;
  		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
  			$max = $row['maxcount'];
		}
    	return $max;
    	
    }
    
    function search($terms){
    	global $conn;
	$tagIdArr = array();
    	//split tags and loop through
    	$termsArr = preg_split("/,/",$terms);
    	foreach ( $termsArr as $term){
    		//getTag id
    		$tagid = getTagID($term);
    		if ($tagid != 0){
    			array_push($tagIdArr,$tagid);
    		}
    	}
    	
	$tagIdstr = join($tagIdArr,",");
	
	$sql = "SELECT COUNT(*), p.photofile,p.photoid, l.location AS photolocation, p.photodate, p.locid FROM photo p
			INNER JOIN phototag pt ON pt.photoid=p.photoid
			INNER JOIN location l ON p.locid = l.locid
			WHERE tagid IN (".$tagIdstr.")
			GROUP BY p.photofile,p.photoid, l.location, p.photodate, p.locid
			HAVING COUNT(*) = ".count($tagIdArr)."
			ORDER BY photodate ASC, photofile ASC";
	$picArr = array();
	$result = mysql_query($sql,$conn);
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	    $pic = arrayToObject($row);
	    $pic->tags = getTagsAsString($row['photoid'],true);
	    array_push($picArr,$pic);
	}
    	
    	return $picArr;
}


function deletePhoto($photoid){
	global $conn;
	// delete the tag references	
	$sql = "DELETE FROM phototag WHERE photoid =".$photoid;
  	if (!mysql_query($sql,$conn)){
  		echo "...error deleting tags...";
  		return;
  	} else {
  		echo "...tags removed...";
  	}
  	
  	// delete the photo
$sql = "DELETE FROM photo WHERE photoid =".$photoid;
  	if (!mysql_query($sql,$conn)){
  		echo "...error deleting photo...";
  		return;
  	}else {
  		echo "...photo removed...";
  	}
}

function displayImage($pic,$rel=".."){
	$str = "<a href='".$pic->photolocation.$pic->photofile."' target='new'>";
	$str .= "<img src='".$rel."/lib/thumbnail.php?img=".$pic->photolocation.$pic->photofile."' border='0'/>";
	$str .= "</a>";
	return $str;
}

function arrayToObject($arr){
	$obj = new stdClass();
	foreach($arr as $k => $v) {
        	$obj->{$k} = $v;
	}
	return $obj;
}

function getLocId($loc){
    global $conn;
    $locId = 0;
    $loc = strtolower(trim($loc));
    if($loc ==""){
    	return $locId;
    }
    $sql = "SELECT locid FROM location WHERE location = '".$loc."'";
    $result = mysql_query($sql,$conn);
  	while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
	   	$locId = $row['locid'];
	} 
	if($locId == 0){
		$sql = "INSERT INTO location (location) VALUES ('".$loc."')";
		if (!mysql_query($sql,$conn)){
  			die('Error: ' . mysql_error());
  		}
		$sql = "SELECT MAX(locid) AS newid FROM location";
		$result = mysql_query($sql,$conn);
  		while($row = mysql_fetch_array($result, MYSQL_ASSOC)){
		   	$locId = $row['newid'];
		}
	}
    return $locId;
}

function updateDate($photoid,$date){
	global $conn;
	$mydate = strtotime($date);
	$sql = "UPDATE photo SET photodate='".$mydate."' WHERE photoid='".$photoid."'";
	if (!mysql_query($sql,$conn)){
  		echo "error in updating date: ".$sql. "<br/>";
  		return;
  	} else {
  		echo "date updated to: ".$date. "<br/>";
  	}	
	
}


function flush_buffers(){
	ob_end_flush();
	@ob_flush();
	@flush();
	ob_start();
}
?>
