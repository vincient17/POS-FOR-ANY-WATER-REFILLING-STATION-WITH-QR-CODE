<?php 
    session_start();
    include "admin_connect.php";

    if(isset($_POST['qr_code'])){
        $qr_code = $_POST['qr_code'];

        // Initialize variables for each value
        $borrowed_gallon = '';

        // Use regex patterns to extract values
        if (preg_match('/Borrowed Gallon:\s*(.*?)Borrower Name:/', $qr_code, $matches)) {
          $borrowed_gallon = trim($matches[1]);
        }

        $update_sql = "UPDATE `tbl_gallon` SET `status`='Damaged' WHERE unique_code = '$borrowed_gallon'";
        $update_result = mysqli_query($con, $update_sql);

        if($update_result){
            $_SESSION['success_message'] = "Gallon $borrowed_gallon status = Damaged!";
            header("location: scan_damage_gallon.php");
            exit();
        }
                
    }

?>