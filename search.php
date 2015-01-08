
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
  if (isset($_POST['submit']) && isset($_POST['photoids']) && $_POST['photoids']!=""){
	if($_POST['tagstoadd'] != ""){
		$count=count($_POST['photoids']);
		for($i=0;$i<$count;$i++){
			echo "adding to: ".$_POST['photoids'][$i]."<br/>";
			addTags($_POST['photoids'][$i],$_POST['tagstoadd']);
		}
			
	}
	if($_POST['tagstoremove'] != ""){
		$count=count($_POST['photoids']);
		for($i=0;$i<$count;$i++){
			echo "removing from: ".$_POST['photoids'][$i]."<br/>";
			removeTags($_POST['photoids'][$i],$_POST['tagstoremove']);
		}
			
	}
	if($_POST['newdate'] != ""){
		$count=count($_POST['photoids']);
		for($i=0;$i<$count;$i++){
			updateDate($_POST['photoids'][$i],$_POST['newdate']);
		}
			
	}
}
  

if (isset($_POST['delete']) && isset($_POST['photoids']) && $_POST['photoids']!=""){
	echo "Deleting photos now...<br/>";
	$count=count($_POST['photoids']);
	for($i=0;$i<$count;$i++){
		echo "deleting: ".$_POST['photoids'][$i];
		deletePhoto($_POST['photoids'][$i]);
		echo "... deleted,<br/>";
	}
	
}
  if(isset($_GET["terms"])){
  	$pics = search($_GET["terms"]);
  }
  cleanUpDB();

    header('Content-Type:text/html; charset=UTF-8');
?>


<html>
<head>
<script type="text/javascript">
		function selectall() {
			var arr = new Array();
	        arr = document.getElementsByName('photoids[]');
	        for(var i = 0; i < arr.length; i++){
	            var obj = document.getElementsByName('photoids[]').item(i);
	            obj.checked ='checked';
	        }
		}
	
		function confirmDelete() {
			var response = confirm("are you sure?");
			if (response) {
				return true;
			} else {
				return false;
			}
		}
	</script>

</head>

<body>

<a href="cloud.php">back</a>

<form action="" method="post">
<table>
<tr>
	<td onclick="selectall()">X</td>
	<td>PhotoFile</td>
	<td>thumb</td>
	<td>tags</td>
</tr>
<?php 
foreach ( $pics as $pic){
	echo "<tr>";
	echo "<td><input type='checkbox' name='photoids[]' value='".$pic->photoid."'/></td>";
	echo "<td><a href='admin/edit.php?id=$pic->photoid'>";
	echo $pic->photofile;
	echo "</a></td>";
	echo "<td>";
	echo displayImage($pic,".");
	echo "</td>";
	echo "<td>";
	echo $pic->tags;
	echo "</td>";
	echo "</tr>";
}
	
?>
</table>
With Selected:<br/>
Add tags: <input name="tagstoadd" type="text"></input><br/>
Remove tags: <input name="tagstoremove" type="text"></input><br/>
Change Date: <input name="newdate" type="text"></input><br/>
<input type="submit" value="submit" name="submit"></input>
<input type="submit" value="delete" name="delete" onClick="return confirmDelete()"></input>

</form>

</body>
