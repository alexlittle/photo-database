<?php
require_once "./config.php";
require_once "./lib/photolib.php";

function cacheThumbnails($size){
	global $CONFIG;
	$photos = getAllPhotos();

	foreach($photos as $p){
		$imagefile = $p->location.$p->photofile;
		printf("<a href='%s' target='_blank'>",$imagefile);
		printf("<img src='lib/thumbnail.php?img=%s&width=%d&height=%d'/>",$imagefile,$size,$size);
		printf("</a>");
		flush_buffers();
	}
}

function cacheColours(){
	global $CONFIG;
	$photos = getAllPhotos();
	foreach($photos as $p){
		$imagefile = $p->location.$p->photofile;
		$hash = md5($imagefile."1"."1");
		if (file_exists($CONFIG->THUMB_DIR.$hash)){
			$image = imagecreatefromjpeg($CONFIG->THUMB_DIR.$hash);
			$rgb = imagecolorat($image, 0, 0);
			$rgbarray = imagecolorsforindex($image, $rgb);
			print_r($rgbarray);
			savePhotoColour($p->photoid,$rgbarray['red'],$rgbarray['green'],$rgbarray['blue'],$rgbarray['alpha'],$rgb);
		}
		flush_buffers();
	}
}

function createComposite($inputPhoto){
	if(file_exists($inputPhoto)){
		$image = imagecreatefromjpeg($inputPhoto);
		$img_x = imagesx($image);
		$img_y = imagesy($image);
		$used = array();
		array_push($used,0);
		// create a new image

		$img_new = imagecreatetruecolor($img_x*PIXEL_SIZE,$img_y*PIXEL_SIZE);
		for($j=0; $j<$img_y;$j++){
				
			for($i=0; $i<$img_x;$i++){

				$rgb =  imagecolorat($image, $i, $j);
				$rgbarray = imagecolorsforindex($image, $rgb);
				
				//$usedStr = join(',',$used);
				//$p = getClosestUnusedPhoto($rgbarray['red'],$rgbarray['green'],$rgbarray['blue'],$usedStr);
				//array_push($used,$p->photoid);
				
				$p = getClosestPhoto($rgbarray['red'],$rgbarray['green'],$rgbarray['blue']);
				
				$imagefile = urlencode($p->location.$p->photofile);
				$thumb_file = sprintf("http://localhost/photodb/lib/thumbnail.php?img=%s&width=%d&height=%d",$imagefile,PIXEL_SIZE,PIXEL_SIZE);

				if (fopen($thumb_file, "r")){
					$thumb_image = imagecreatefromjpeg($thumb_file);
					@imagecopy($img_new,$thumb_image,$i*PIXEL_SIZE,$j*PIXEL_SIZE,0,0,PIXEL_SIZE,PIXEL_SIZE);
				}
			}
		}
		header('Content-type: image/jpeg');
		imagejpeg($img_new);
	} else {
		echo "Input image file not found: ".$inputPhoto;
	}

}



