<?php 
	session_start();
	include "admin_connect.php";
	$delete_id = $_GET['deleteid'];

	$sql = "DELETE FROM tbl_category WHERE category_id = $delete_id";
	$result = mysqli_query($con, $sql);

	if($result){
		$_SESSION['success_message'] = "Category Successfully Deleted!";
			header("location: products.php");
	}


 ?>