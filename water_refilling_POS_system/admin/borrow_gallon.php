<?php
session_start();
include('admin_connect.php');

if (isset($_POST['submit'])) {
    // Get the customer ID from the POST request
    $cus_id = mysqli_real_escape_string($con, $_POST['cus_id']);
    
    // Get the array of selected gallon IDs from the form
    $gallon_ids = $_POST['gallon_id'];


    // Check if there are selected gallon IDs
    if (!empty($gallon_ids) && count($gallon_ids) > 0) {
        foreach ($gallon_ids as $index => $gallon_id) {
            $gallon_id = mysqli_real_escape_string($con, $gallon_id);

            // Insert into tbl_borrowedGallon with QR code data
            $sql = "INSERT INTO `tbl_borrowedGallon`(`cus_id`, `gallon_id`) VALUES ('$cus_id', '$gallon_id')";
            $result = mysqli_query($con, $sql);

            if ($result) {
                // Update the gallon's status to 'Borrowed'
                $update_query = "UPDATE tbl_gallon SET status = 'Borrowed' WHERE gallon_id = '$gallon_id'";
                mysqli_query($con, $update_query);
            }
        }

        // Set success message and redirect
        $_SESSION['borrow_success_message'] = count($gallon_ids) . " gallon(s) borrowed successfully!";
        header('Location: gallon_inventory.php');
        exit();
    } else {
        // Set an error message if no gallon was selected
        $_SESSION['error_message'] = 'Please select at least one gallon to borrow.';
        header('Location: gallon_inventory.php');
        exit();
    }
} else {
    header('Location: gallon_inventory.php');
    exit();
}
?>
