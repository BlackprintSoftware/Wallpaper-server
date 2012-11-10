<?php

$allowedExts = array("jpg", "jpeg", "gif", "png");
$extension = end(explode(".", $_FILES["file"]["name"]));
$size = $_FILES["file"]["size"];
$comments = $_POST['comments'];
$filename = $_FILES['file']['name'];
$name = $_POST['name'];
$url = 'http://blackprint.ca/Wallpaper/upload/' . $filename;
$thumburl = 'http://blackprint.ca/Wallpaper/thumbs/' . $filename;
$_768x1280url = 'http://blackprint.ca/Wallpaper/768x1280/' . $filename;
$_720x720url = 'http://blackprint.ca/Wallpaper/720x720/' . $filename;
$_1024x1024url = 'http://blackprint.ca/Wallpaper/1024x1024/' . $filename;

$color = $_POST['color1'];
$color2 = $_POST['color2'];
$tag1 = $_POST['tag1'];
$tag2 = $_POST['tag2'];
$tag3 = $_POST['tag3'];
$tag4 = $_POST['tag4'];
$tag5 = $_POST['tag5'];


//CHECK FOR VALID FILE TYPES AND FILE SIZE

if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/png")
|| ($_FILES["file"]["type"] == "image/pjpeg"))
&& ($_FILES["file"]["size"] < 5000000)
&& in_array($extension, $allowedExts))
{
	if ($_FILES["file"]["error"] > 0)
    {
    	echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
    else
    {
	    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
	    echo "Type: " . $_FILES["file"]["type"] . "<br />";
	    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
	    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";

	    if (file_exists("upload/" . $_FILES["file"]["name"]))
	    {
		    echo $_FILES["file"]["name"] . " already exists. ";
		}
		else
		{
      
		    //STORE THE UPLOADED FILE
		      
		    move_uploaded_file($_FILES["file"]["tmp_name"], "upload/" . $_FILES["file"]["name"]);
		    echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
		    
		    //RESIZE AND STORE THE RESIZED FILE
		    
		    $img192x192 = new Imagick('upload/' . $filename);
		    $img768x1280 = new Imagick('upload/' . $filename);
		    $img720x720 = new Imagick('upload/' . $filename);
		    $img1024x1024 = new Imagick('upload/' . $filename);
		    
		    $img192x192->thumbnailImage(192,192,false);
		    $img768x1280->thumbnailImage(768,1280,false);
		    $img720x720->thumbnailImage(720,720,false);
		    $img1024x1024->thumbnailImage(1024,1024,false);
		    
		    $img192x192->writeImage('thumbs/' . $filename);
		    $img768x1280->writeImage('768x1280/' . $filename);
		    $img720x720->writeImage('720x720/' . $filename);
		    $img1024x1024->writeImage('1024x1024/' . $filename);
		    
		    $img192x192->destroy();
		    $img768x1280->destroy();
		    $img720x720->destroy();
		    $img1024x1024->destroy();
	      	//OUTPUT FILE DETAILS
	      
	      	echo "<br />" . "<h1>Name: ".$name . "</h1>";
			echo "<h2>Size: ".$size . " bytes</h2>";
			echo "<h2>url: ". $url . "</h2>";
			echo "<h2>thumburl: ". $thumburl . "</h2>";
			echo "<h2>768x1280url: ". $_768x1280url . "</h2>";
			echo "<h2>720x720url: ". $_720x720url . "</h2>";
			echo "<h2>1024x1024url: ". $_1024x1024url . "</h2>";

			echo "<h2>color primary: ".$color . "</h2>";
			echo "<h2>color other: ".$color2 . "</h2>";
			echo "<h2>tag1: ". $tag1 . "</h2>";
			echo "<h2>tag2: ". $tag2 . "</h2>";
			echo "<h2>tag3: ". $tag3 . "</h2>";
			echo "<h2>tag4: ". $tag4 . "</h2>";
			echo "<h2>tag5: ". $tag5 . "</h2>";
			echo "<h3>comments: ".$comments . "</h3><br />";

			  
			//CONNECT TO THE DATABASE
			$connection = mysql_connect("localhost", "blackpri_david", "sw0rdfi5h");
			if (!$connection)
			{
				die('Could not connect: ' . mysql_error());
			}
			
			mysql_select_db("blackpri_wallpaper1", $connection);
			
			//COMPOSE SQL INSERT QUERY
			
			$sql = "INSERT INTO wallpapers (name,size,url,comments,thumbnail,768x1280,720x720,1024x1024) VALUES ('$name','$size','$url','$comments','$thumburl','$_768x1280url','$_720x720url','$_1024x1024url')";
			
			//EXECUTE SQL INSERT QUERY
			
			if (!mysql_query($sql,$connection))
			{
				die('Error: ' . mysql_error());
			}
			
			echo "1 record added to wallpapers";  
			
			//DETERMINE LATEST WALLPAPER ID
			
			$sql = "SELECT MAX(id) FROM wallpapers";
			
			$result_most_recent_record = mysql_query($sql,$connection);
			
			$row = mysql_fetch_row($result_most_recent_record);
			
			$id = $row[0];
			
			echo " with ID of ".$id."  <BR />";
			
			//INSERT PRIMARY COLOR
			
			$sql = "INSERT INTO colors (id,color) VALUES ('$id','$color')";
			
			if (!mysql_query($sql,$connection))
			{
				die('Error: ' . mysql_error());
			}
			
			//IF SECONDARY COLOR EXISTS, INSERT INTO DATABASE
			
			if ($color2 != "-None-")
			{
				$sql = "INSERT INTO colors (id,color) VALUES ('$id','$color2')";
			
				if (!mysql_query($sql,$connection))
				{
					die('Error: ' . mysql_error());
				}
		
				echo "2 records added to colors <BR />";
			}
			else
				echo "1 record added to colors <BR />";
			
			//INSERT TAGS INTO DATABSE
			$numtags = 0;
			if ($tag1 != "")
			{
				$sql = "INSERT INTO tags (id,tag) VALUES ('$id','$tag1')";
				if (!mysql_query($sql,$connection))
				{
					die('Error: ' . mysql_error());
				}
				$numtags++;
			}
			if ($tag2 != "")
			{
				$sql = "INSERT INTO tags (id,tag) VALUES ('$id','$tag2')";
				if (!mysql_query($sql,$connection))
				{
					die('Error: ' . mysql_error());
				}
				$numtags++;
			}
			if ($tag3 != "")
			{
				$sql = "INSERT INTO tags (id,tag) VALUES ('$id','$tag3')";
				if (!mysql_query($sql,$connection))
				{
					die('Error: ' . mysql_error());
				}
				$numtags++;
			}
			if ($tag4 != "")
			{
				$sql = "INSERT INTO tags (id,tag) VALUES ('$id','$tag4')";
				if (!mysql_query($sql,$connection))
				{
					die('Error: ' . mysql_error());
				}
				$numtags++;
			}
			if ($tag5 != "")
			{
				$sql = "INSERT INTO tags (id,tag) VALUES ('$id','$tag5')";
				if (!mysql_query($sql,$connection))
				{
					die('Error: ' . mysql_error());
				}
				$numtags++;
			}
			echo $numtags . " records added to tags <BR />";
			echo "<img src='$thumburl'></img><BR />";
			echo "<img src='$_768x1280url'></img><BR />";
			echo "<img src='$_720x720url'></img><BR />";
			echo "<img src='$_1024x1024url'></img><BR />";
			
			//CLOSE CONNECTION
			
			mysql_close($connection);
			
			
			
		}
	}
}
else
{
	echo "Invalid file";
}
  



?>