<?php
session_start();
include "admin_connect.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $cus_id = mysqli_real_escape_string($con, $_POST['cus_id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $address = mysqli_real_escape_string($con, $_POST['address']);
    $cp_num = mysqli_real_escape_string($con, $_POST['cp_num']);

    // Update the customer data in the database
    $sql = "UPDATE tbl_customers SET name='$name', address='$address', cp_num='$cp_num' WHERE cus_id='$cus_id'";
    $result = mysqli_query($con, $sql);

    if ($result) {
        // Set a session success message
        $_SESSION['success_message'] = "Customer data has been successfully updated!";
        // Redirect back to the customers page
        header("Location: customers.php");
        exit(); // Ensure no further code is executed
    } else {
        echo "Error: " . mysqli_error($con);
    }
} else {
    // Redirect if accessed without posting
    header("Location: customers.php");
}
?>
