<?php
/*
 * Created on 15 Dec 2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
require_once "../config.php";
  require_once "../lib/photolib.php";
  getConnection();
  $locs = getLocations();
  cleanUpDB();
?>


<html>


<body>



<table>
<tr>
	<td>Locations</td>
	<td>No pics</td>
</tr>
<?php 
foreach ( $locs as $loc){
	echo "<tr>";
	echo "<td><a href='loc.php?locid=$loc->locid'>";
	echo $loc->location;
	echo "</a></td>";
	
	echo "<td>";
	echo $loc->photocount;
	echo "</td>";
	echo "</tr>";
}
	
?>
</table>


</body>
