<?php 

include "admin_connect.php";
session_start();

if (isset($_POST['submit'])) {
    $p_name = trim($_POST['p_name']);
    $p_price = trim($_POST['p_price']);
    $product_category = trim($_POST['product_category']);
    $quantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : '';

    // Default quantity for Water category or if quantity is empty
    if (empty($quantity) || $product_category == "Water") {
        $quantity = "99999999";
    }

    // Validate and sanitize inputs
    $p_name = htmlspecialchars($p_name, ENT_QUOTES, 'UTF-8');
    $p_price = floatval($p_price);
    $product_category = intval($product_category);
    $quantity = intval($quantity);

    $existed = 0;

    // Use prepared statements for checking existence
    $exist_sql = "SELECT * FROM `tbl_products` 
                  JOIN tbl_category 
                  ON tbl_products.category_id = tbl_category.category_id 
                  WHERE p_name = ? AND tbl_products.category_id = ?";
    $stmt = $con->prepare($exist_sql);
    $stmt->bind_param('si', $p_name, $product_category);
    $stmt->execute();
    $exist_result = $stmt->get_result();

    if ($exist_result && $exist_result->num_rows > 0) {
        $existed = 1;
    }

    $stmt->close();

    if ($existed == 1) {
        $_SESSION['exist_message'] = "Product Already Existed!";
        header("location: products.php");
        exit();
    } else {
        // Use prepared statements for inserting data
        $sql = "INSERT INTO tbl_products (p_name, p_price, category_id, quantity) 
                VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param('sdii', $p_name, $p_price, $product_category, $quantity);
        $result = $stmt->execute();

        if ($result) {
            $_SESSION['success_message'] = "Product added successfully!";
            header("location: products.php");
            exit();
        }

        $stmt->close();
    }
}
?>
