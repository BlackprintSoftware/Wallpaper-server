<?php
			  
$isExportingXML = false;
$color = $_GET['color'];
$tag = $_GET['tag'];	
$export_type = "";		  
			  
//CONNECT TO THE DATABASE
$connection = mysql_connect("localhost", "blackpri_david", "sw0rdfi5h");
if (!$connection)
{
	die('Could not connect: ' . mysql_error());
}

mysql_select_db("blackpri_wallpaper1", $connection);

//COMPOSE SQL QUERY

$table_id = 'wallpapers';

$sql = "SELECT * FROM " . $table_id;

//EXECUTE SQL QUERY

$dbresult = mysql_query($sql,$connection);

if ($isExportingXML)
{

	$doc = new DomDocument('1.0');
	$root = $doc->createElement('root');
	$root = $doc->appendChild($root);
	
	while($row = mysql_fetch_assoc($dbresult)) {
	
		$occ = $doc->createElement($table_id);
		$occ = $root->appendChild($occ);
		
		// add a child node for each field
		foreach ($row as $fieldname => $fieldvalue) {
		
			$child = $doc->createElement($fieldname);
			$child = $occ->appendChild($child);
			
			$value = $doc->createTextNode($fieldvalue);
			$value = $child->appendChild($value);
		
		}
	
	}
	
	$xml_string = $doc->saveXML();
	
	echo $xml_string;

}

//SHOW NO FILTERS
else if ( ($color == "") && ($tag == "") )
{
	echo "<li>blah</li>";
}

//FILTERED ON TAG, BUT NOT ON COLOR. DISPLAY ALL TAGS IF TAG=ALL, OTHERWISE DISPLAY COLORS FILTERED BY THE TAG
else if ( ($tag != "") && ($color == "") )
{

	$sql = "";

	if ($tag == "all")
	{
		$sql = "SELECT count(*) AS quantity,tag FROM tags,wallpapers WHERE tags.id=wallpapers.id GROUP BY tag ORDER BY tag ASC";
		//EXECUTE SQL QUERY
		$dbresult = mysql_query($sql,$connection);
		while($row = mysql_fetch_assoc($dbresult)) {
			echo '<li><a href="#homelist" onclick="nextPage(\''.$row['color'].'\',\''.$row['tag'].'\')">'.$row['tag'].'</a></li>';
		}
	}
	else
	{
		$sql = "SELECT count(*) AS quantity,tag,color FROM tags,wallpapers,colors WHERE tags.id=wallpapers.id AND colors.id=wallpapers.id AND tag='".$tag."' GROUP BY color ORDER BY color ASC";
		
		$dbresult = mysql_query($sql,$connection);
		while($row = mysql_fetch_assoc($dbresult)) {
			echo '<li><a href="#homelist" onclick="nextPage(\''.$row['color'].'\',\''.$row['tag'].'\')">'.$row['color'].'</a></li>';
		}
	}

}

//FILTERED ON COLOR, BUT NOT ON TAG: DISPLAY TAGS
else if ( ($color != "") && ($tag == "") )
{

	$sql = "";

	if ($color == "all")
	{
		$sql = "SELECT count(*) AS quantity,color FROM colors,wallpapers WHERE colors.id=wallpapers.id GROUP BY color ORDER BY color ASC";
		//EXECUTE SQL QUERY
		$dbresult = mysql_query($sql,$connection);
		while($row = mysql_fetch_assoc($dbresult)) {
			echo '<li><a href="#homelist" onclick="nextPage(\''.$row['color'].'\',\''.$row['tag'].'\')">'.$row['color'].'</a></li>';
		}
	}
	else
	{
		$sql = "SELECT count(*) AS quantity,tag,color FROM tags,wallpapers,colors WHERE tags.id=wallpapers.id AND colors.id=wallpapers.id AND color='".$color."' GROUP BY tag ORDER BY tag ASC";
		
		$dbresult = mysql_query($sql,$connection);
		while($row = mysql_fetch_assoc($dbresult)) {
			echo '<li><a href="#homelist" onclick="nextPage(\''.$row['color'].'\',\''.$row['tag'].'\')">'.$row['tag'].'</a></li>';
		}
	}
}


//FILTERED ON COLOR AND TAG: DISPLAY WALLPAPER RESULTS
else if ( ($color != "") && ($tag != "") )
{

	$sql = "";
	
	if ( ($color == "all") && ($tag == "all") )
	{
		$sql = "SELECT DISTINCT wallpapers.name as name FROM wallpapers ORDER BY wallpapers.name ASC";
	}
	else if ( $tag == "all" )
	{
		$sql = "SELECT wallpapers.name as name,color FROM wallpapers,colors WHERE colors.id=wallpapers.id AND colors.color='".$color."' ORDER BY wallpapers.name ASC";
	}
	else if ( $color == "all" )
	{
		$sql = "SELECT wallpapers.name as name,tag FROM wallpapers,tags WHERE tags.id=wallpapers.id AND tags.tag='".$tag."' ORDER BY wallpapers.name ASC";
	}
	else
	{
	
		$sql = "SELECT wallpapers.name AS name1,tag,color FROM wallpapers,tags,colors WHERE tags.id=wallpapers.id AND colors.id=wallpapers.id AND tags.tag='".$tag."' AND colors.color='".$color."' ORDER BY wallpapers.name ASC";
	}

	

	//EXECUTE SQL QUERY

	$dbresult = mysql_query($sql,$connection);
	
	while($row = mysql_fetch_assoc($dbresult)) {
	
		echo "<li>".$row['name1']."</li>";

	}
	
}

else if ($export_type == "staff")
{
	echo "staff";
}

else

{
	echo "no conditions met";

}



//CLOSE CONNECTION

mysql_close($connection);


?> 