<?php
session_start();
include "admin_connect.php";

// Retrieve WRS details from system setup
$wrs_sql = "SELECT * FROM `tbl_system_setup` WHERE setup_id = 1";
$wrs_result = mysqli_query($con, $wrs_sql);

if ($wrs_result && $row = mysqli_fetch_assoc($wrs_result)) {
    $WRS_name = $row['WRS_name'];
    $WRS_acronym = $row['WRS_acronym']; // Use for validation
} else {
    $_SESSION['error_message'] = $wrs_result
        ? "Water Refilling Station details not found!"
        : "Error fetching system setup: " . mysqli_error($con);
    header("location: scan_qr.php");
    exit();
}

if (isset($_POST['qr_code'])) {
    $qr_code = $_POST['qr_code'];

    // Extract Borrower ID and Borrowed Gallon using regex
    if (preg_match('/Borrower ID:\s*(.*?)Borrowed Gallon:\s*(.*?)Borrower Name:/s', $qr_code, $matches)) {
        $borrowed_id = trim($matches[1]);
        $borrowed_gallon = trim($matches[2]);
    } else {
        $_SESSION['error_message'] = "Invalid QR Code format! Please scan a valid QR code.";
        header("location: scan_qr.php");
        exit();
    }

    // Validate gallon belongs to the station
    if (strpos($borrowed_gallon, $WRS_acronym) !== 0) {
        $_SESSION['error_message'] = "This gallon is not from your water refilling station!";
        header("location: scan_qr.php");
        exit();
    }

    // Check if the borrowed gallon exists
    $borrowed_sql = "SELECT * FROM tbl_borrowedgallon WHERE borrowed_id = ?";
    $stmt = $con->prepare($borrowed_sql);
    $stmt->bind_param('i', $borrowed_id);
    $stmt->execute();
    $borrowed_result = $stmt->get_result();

    if ($borrowed_result && $borrowed_result->num_rows > 0) {
        $row = $borrowed_result->fetch_assoc();
        $cus_id = $row['cus_id'];
        $gallon_id = $row['gallon_id'];
        $date_borrowed = $row['date_borrowed'];
    } else {
        $_SESSION['error_message'] = "Gallon $borrowed_gallon already returned or not found!";
        header("location: scan_qr.php");
        exit();
    }

    // Begin transaction
    $con->begin_transaction();

    try {
        // Insert returned gallon record
        $returned_sql = "INSERT INTO tbl_returnedgallon (gallon_id, cus_id, date_borrowed) VALUES (?, ?, ?)";
        $stmt = $con->prepare($returned_sql);
        $stmt->bind_param('iis', $gallon_id, $cus_id, $date_borrowed);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting return record.");
        }

        // Update gallon status
        $update_sql = "UPDATE tbl_gallon SET status = 'Returned' WHERE gallon_id = ?";
        $stmt = $con->prepare($update_sql);
        $stmt->bind_param('i', $gallon_id);
        if (!$stmt->execute()) {
            throw new Exception("Error updating gallon status.");
        }

        // Delete borrowed gallon record
        $delete_sql = "DELETE FROM tbl_borrowedgallon WHERE borrowed_id = ?";
        $stmt = $con->prepare($delete_sql);
        $stmt->bind_param('i', $borrowed_id);
        if (!$stmt->execute()) {
            throw new Exception("Error deleting borrowed record.");
        }

        // Commit transaction
        $con->commit();
        $_SESSION['success_message'] = "Gallon $borrowed_gallon returned successfully!";
    } catch (Exception $e) {
        // Rollback transaction on failure
        $con->rollback();
        $_SESSION['error_message'] = "An error occurred: " . $e->getMessage();
    }

    header("location: scan_qr.php");
    exit();
}
?>
