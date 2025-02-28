<?php
include "admin_connect.php";
session_start();
date_default_timezone_set('Asia/Manila');

if (isset($_POST['submit'])) {
    $cus_id = $_POST['cus_id'];
    $customer_action = $_POST['customer_action'];
    $cart_items = json_decode($_POST['cart_items'], true);
    $total_price = $_POST['cart_total'];

    $numericValue = floatval(preg_replace('/[₱,]/', '', $total_price));
    $integerValueTotalPrice = (int) $numericValue;

    // echo "Customer ID: " . $cus_id . "<br>";
    // echo "Customer Action: " . $customer_action . "<br>";
    // echo "Cart Items: <pre>" . print_r($cart_items, true) . "</pre>";
    // echo "Total Price (raw): " . $total_price . "<br>";
    // echo "Total Price (numeric value): " . $numericValue . "<br>";
    // echo "Total Price (integer value): " . $integerValueTotalPrice . "<br>";

    $errors = [];
    $success = false;
    
    foreach ($cart_items as $item) {
        $water_product = $item['productId'];
        $quantity = $item['quantity'];

        // Fetch product stock and category using prepared statements
        $product_sql = "
            SELECT 
                tbl_products.quantity AS stock, 
                tbl_category.category_description 
            FROM 
                tbl_products 
            JOIN 
                tbl_category 
            ON 
                tbl_products.category_id = tbl_category.category_id 
            WHERE 
                tbl_products.product_id = ?";
        $product_stmt = mysqli_prepare($con, $product_sql);
        mysqli_stmt_bind_param($product_stmt, 'i', $water_product);
        mysqli_stmt_execute($product_stmt);
        $product_result = mysqli_stmt_get_result($product_stmt);
        $row = mysqli_fetch_assoc($product_result);

        if ($row) {
            $stock = $row['stock'];
            $category_description = $row['category_description'];

            // Check stock availability for non-water products
            if (strcasecmp($category_description, "Water") !== 0 && $quantity > $stock) {
                $errors[] = "Insufficient stock for product ID $water_product!";
                continue;
            }

            // Deduct stock for non-water products
            if (strcasecmp($category_description, "Water") !== 0) {
                $update_sql = "UPDATE `tbl_products` SET `quantity` = quantity - ? WHERE `product_id` = ?";
                $update_stmt = mysqli_prepare($con, $update_sql);
                mysqli_stmt_bind_param($update_stmt, 'ii', $quantity, $water_product);
                $update_result = mysqli_stmt_execute($update_stmt);

                if (!$update_result) {
                    $errors[] = "Failed to update stock for product ID $water_product!";
                    continue;
                }
            }

            // Insert sale record using prepared statements
            $insert_sql = "INSERT INTO `tbl_sales`(`cus_id`, `product_id`, `customer_service`, `total_order`, `total_price`) 
                           VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = mysqli_prepare($con, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, 'iisii', $cus_id, $water_product, $customer_action, $quantity, $integerValueTotalPrice);
            $insert_result = mysqli_stmt_execute($insert_stmt);

            if (!$insert_result) {
                $errors[] = "Failed to record sale for product ID $water_product!";
                continue;
            }

            $success = true;
	        } else {
	            $errors[] = "Product ID $water_product not found!";
        }
    }

    if (!empty($errors)) {
        $_SESSION['error_message'] = implode("<br>", $errors);
    }

    // header("Location: payment_form.php?totalBill=" . $integerValueTotalPrice);
    $_SESSION['success_message'] = "Purchase completed successfully!";
    header("Location: customers.php");

    exit;
}
?>
