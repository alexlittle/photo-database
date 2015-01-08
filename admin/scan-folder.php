<?php
require_once "../config.php";
require_once("../lib/photolib.php");
getConnection();

// remember trailing slashes
if(isset($_POST['submit'])){
	$SUBMITTED = $_POST['submit'];
    $BASE_DB_PATH = $_POST['BASE_DB_PATH'];
    $BASE_DIR = '/home/alex/data'.$BASE_DB_PATH;
    $DEFAULT_DATE = $_POST['DEFAULT_DATE'];
    $DEFAULT_TAGS = $_POST['DEFAULT_TAGS']; 
} else {
	$SUBMITTED = false;
	$date =  date('Y-m-d');
    $BASE_DB_PATH = sprintf('/photos/%d/',date('Y'));
    $BASE_DIR = '/home/alex/data'.$BASE_DB_PATH;
    $DEFAULT_DATE = $date;
    $DEFAULT_TAGS = "";
}

?>
<form name="scan" action="" method="post">
Base DB Path: <input type="text" name="BASE_DB_PATH" size="40" value="<? echo $BASE_DB_PATH;?>"><br/>
Default Date: <input type="text" name="DEFAULT_DATE" value="<? echo $DEFAULT_DATE;?>"><br/>
Default Tags: <input type="text" name="DEFAULT_TAGS" size="40" value="<? echo $DEFAULT_TAGS;?>"><br/>
<input type="submit" name="submit"/>
</form>

<?
if ($SUBMITTED){
    $dir_handle = @opendir($BASE_DIR) or die ("can't open $BASE_DIR");

    while ($file = readdir($dir_handle)) {

	    if($file == "." || $file == ".." || $file == "index.php" || substr ($file, -4) == ".avi" )
            continue;

        $locid = getLocId($BASE_DB_PATH); 
	    echo $BASE_DB_PATH .': '.$locid.'<br/><ul>';
	    if (substr ($file, -4) == ".jpg"){
            echo '<li>'.$file.": ";
            addToDatabase($file,$BASE_DB_PATH, $DEFAULT_DATE,$DEFAULT_TAGS);
            echo '</li>';
        }

	    echo '</ul>';

            /*$current_db_dir = $BASE_DB_PATH.$file.'/';
            $current_base_dir = $BASE_DIR.$file.'/';
            $locid = getLocId($current_db_dir);    
            
            echo $current_db_dir .': '.$locid.'<br/><ul>';
            $photodir_handle = @opendir($current_base_dir) or die ("can't open $current_base_dir");
            while ($photo = readdir($photodir_handle)) {
            	if (substr ($photo, -4) == ".jpg"){
            		echo '<li>'.$photo.": ";
            		addToDatabase($photo,$current_db_dir, $DEFAULT_DATE,$DEFAULT_TAGS);
            		echo '</li>';
            	}
            	
            }
            
            echo '</ul>';*/
    }
}
?>
