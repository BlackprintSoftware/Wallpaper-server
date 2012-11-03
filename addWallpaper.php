<?php

$allowedExts = array("jpg", "jpeg", "gif", "png");
$extension = end(explode(".", $_FILES["file"]["name"]));
$size = $_FILES["file"]["size"];
$comments = $_POST['comments'];
$filename = $_FILES['file']['name'];
$name = $_POST['name'];
$url = 'http://blackprint.ca/upload/Wallpaper/' . $filename;
$color = $_POST['color1'];
$color2 = $_POST['color2'];

//CHECK FOR VALID FILE TYPES AND FILE SIZE

if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/png")
|| ($_FILES["file"]["type"] == "image/pjpeg"))
&& ($_FILES["file"]["size"] < 2000000)
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
	      
	      	//OUTPUT FILE DETAILS
	      
	      	echo "<br />" . "name:".$name . "<br />";
			echo "size:".$size . "<br />";
			echo "url:". $url . "<br />";
			echo "comments:".$comments . "<br />";
			echo "color primary:".$color . "<br />";
			echo "color other:".$color2 . "<br />";
			  
			//CONNECT TO THE DATABASE
			$connection = mysql_connect("localhost", "blackpri_david", "sw0rdfi5h");
			if (!$connection)
			{
				die('Could not connect: ' . mysql_error());
			}
			
			mysql_select_db("blackpri_wallpaper1", $connection);
			
			//COMPOSE SQL INSERT QUERY
			
			$sql = "INSERT INTO wallpapers (name,size,url,comments) VALUES ('$name','$size','$url','$comments')";
			
			//EXECUTE SQL INSERT QUERY
			
			if (!mysql_query($sql,$connection))
			{
				die('Error: ' . mysql_error());
			}
			
			echo "1 record added to wallpapers <BR />";  
			
			//DETERMINE LATEST WALLPAPER ID
			
			$sql = "SELECT MAX(id) FROM wallpapers";
			
			$result_most_recent_record = mysql_query($sql,$connection);
			
			$row = mysql_fetch_row($result_most_recent_record);
			
			$id = $row[0];
			
			echo "most recent ID is ".$id."  <BR />";
			
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