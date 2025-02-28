<?php  

	include "admin_connect.php";

	$delete_id = $_GET['deleteid'];

	$sql = "DELETE FROM tbl_products where product_id = $delete_id";
	$result = mysqli_query($con, $sql);

	if($result){
		header('location: products.php');
	}



?>