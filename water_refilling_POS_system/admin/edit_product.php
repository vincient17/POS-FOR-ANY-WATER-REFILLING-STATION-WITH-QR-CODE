<?php
session_start();
include "admin_connect.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $product_id = mysqli_real_escape_string($con, $_POST['product_id']);
    $p_name = mysqli_real_escape_string($con, $_POST['p_name']);
    $p_price = mysqli_real_escape_string($con, $_POST['p_price']);
    $quantity = mysqli_real_escape_string($con, $_POST['quantity']);

    // Update the customer data in the database

    $sql = "UPDATE tbl_products SET p_name='$p_name', p_price='$p_price', quantity = '$quantity' WHERE product_id='$product_id'";
    $result = mysqli_query($con, $sql);

    if ($result) {
        // Set a session success message
        $_SESSION['success_message'] = "Product data has been successfully updated!";
        // Redirect back to the customers page
        header("Location: products.php");
        exit(); // Ensure no further code is executed
    } else {
        echo "Error: " . mysqli_error($con);
    }
} else {
    // Redirect if accessed without posting
    header("Location: products.php");
}
?>
