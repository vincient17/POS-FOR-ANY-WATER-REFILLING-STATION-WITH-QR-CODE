<?php

	$dbhost = "localhost";
	$dbuser = "root";
	$dbpass = "";
	$dbname = "water_refilling";

	$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

	if(!$con){
		die("Failed to connect!");
	}
?>