<?php  

	include "admin_connect.php";

	$delete_id = $_GET['deleteid'];

	$sql = "DELETE FROM tbl_customers where cus_id = $delete_id";
	$result = mysqli_query($con, $sql);

	if($result){
		header('location: customers.php');
	}



?>