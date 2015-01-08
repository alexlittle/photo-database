<?php
	require_once("../config.php");

	$tempdir = $CONFIG->THUMB_DIR;
    $imagefile = $_GET['img'];
	if (isset($_GET['width'])){
		$newwidth = $_GET['width'];
	} else {
		$newwidth = 200;
	}
	if (isset($_GET['height'])){
		$newheight = $_GET['height'];
	} else {;
		$newheight = "auto";
	}
    $imagepath = $CONFIG->IMAGE_ROOT.$imagefile;

   
	// create hash of file name
	$hash = md5($imagefile.$newwidth.$newheight);
	
	//see if file exists
	if (file_exists($tempdir.$hash)){
		header('Content-type: image/jpeg');
	    //write to file to cache for next time
	    $image = imagecreatefromjpeg($tempdir.$hash);
	    imagejpeg($image);
	    die();
	} 
    // Load image
    $image = imagecreatefromjpeg($imagepath);

    if ($image == false) {
        die ('invalid image');
    }

    // Get original width and height
    $width = imagesx($image);
    $height = imagesy($image);
    if (isset($_GET['height'])){
    	$new_width = floatval($newwidth);
    	$new_height = floatval($newheight);
	} else {
		$new_width = floatval($newwidth);
		$new_height = $height * ($new_width/$width);
	}

    
    // Resample
    $image_resized = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

    echo "hello";
    // Display resized image
    header('Content-type: image/jpeg');
    
    //write to file to cache for next time
    imagejpeg($image_resized,$tempdir.$hash);
    imagejpeg($image_resized);
    die();

?> 
