<?php 
session_start();
include "admin_connect.php";

if (isset($_POST['add_category'])) {
    // Sanitize and validate the input
    $category_description = trim($_POST['category_description']);
    if (empty($category_description)) {
        $_SESSION['error_message'] = "Category description cannot be empty.";
        header("location: products.php");
        exit();
    }

    // Check if the category already exists using a prepared statement
    $exist_sql = "SELECT * FROM `tbl_category` WHERE category_description = ?";
    $stmt = $con->prepare($exist_sql);
    $stmt->bind_param("s", $category_description);
    $stmt->execute();
    $exist_result = $stmt->get_result();

    if ($exist_result && $exist_result->num_rows > 0) {
        $_SESSION['exist_message'] = "Category Already Existed!";
        header("location: products.php");
        exit();
    }

    // Insert the new category using a prepared statement
    $sql = "INSERT INTO `tbl_category` (`category_description`) VALUES (?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $category_description);
    $result = $stmt->execute();

    if ($result) {
        $_SESSION['success_message'] = "Category added successfully!";
        header("location: products.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Failed to add category. Please try again.";
        header("location: products.php");
        exit();
    }
}
?>
