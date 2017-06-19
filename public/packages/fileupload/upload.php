<?php
$date = date('Y-m-d');
$time = date('H:i:s');
$propertyid = $_REQUEST["propertyid"];
$mediatype = $_REQUEST["mediatype"];
$output_dir = "../properties/$propertyid/";

if (!file_exists($output_dir)) {
    mkdir($output_dir, 0777, true);
}

$fileName = "";

if(isset($_FILES["propertyFile"]))
{
	$ret = array();

	$error =$_FILES["propertyFile"]["error"];
	//You need to handle  both cases
	//If Any browser does not support serializing of multiple files using FormData() 
	if(!is_array($_FILES["propertyFile"]["name"])) //single file
	{
 	 	$fileName = $_FILES["propertyFile"]["name"];
 		move_uploaded_file($_FILES["propertyFile"]["tmp_name"],$output_dir.$fileName);
    	$ret[]= $fileName;
	}
	else  //Multiple files, file[]
	{
	  $fileCount = count($_FILES["propertyFile"]["name"]);
	  for($i=0; $i < $fileCount; $i++)
	  {
	  	$fileName = $_FILES["propertyFile"]["name"][$i];
		move_uploaded_file($_FILES["propertyFile"]["tmp_name"][$i],$output_dir.$fileName);
	  	$ret[]= $fileName;
	  }
	
	}
    echo $fileName;
 }
 ?>