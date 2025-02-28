<?php 

	include "admin_connect.php";
	session_start();

	function random_num($length){
		$text = "";
		if($length < 5){
			$length = 5;
		}

		$len = rand(10, $length);

		for ($i=0; $i < $len; $i++) { 
			// code...

			$text .= rand(0, 9);
		}
		return $text;
	}

	if(isset($_POST['submit'])){
		$name = $_POST['name'];
		$address = $_POST['address'];
		$cp_num = $_POST['cp_num'];

		$unique_code = random_num(10);

		$existed = 0;

		$exist_sql = "SELECT * FROM `tbl_customers` where name = '$name' AND address = '$address' and cp_num = '$cp_num'";
		$exist_result = mysqli_query($con, $exist_sql);
		if($exist_result && mysqli_num_rows($exist_result) > 0){
			$existed = 1;
		}


		if($existed == 1){

			$_SESSION['exist_message'] = "Customer Already Existed!";
			header("location: customers.php");

		}else{
			$sql = "INSERT INTO tbl_customers (name, address, cp_num, cus_unique_code) VALUES ('$name','$address','$cp_num', '$unique_code')";
			$result = mysqli_query($con, $sql);

			if($result){
				$_SESSION['success_message'] = "Customer had been added successfully!";
				header("location: customers.php");
				exit();
			}
		}

		



	}

?>