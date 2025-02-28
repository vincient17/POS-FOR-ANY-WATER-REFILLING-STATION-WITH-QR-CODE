<?php
    session_start();
    include "admin_connect.php";
    include "function.php"; 

    if (isset($_POST['submit'])) {
        
        $status = $_POST['status'];
        $quantity = $_POST['quantity'];

        // Get the user-defined acronym for the unique code
        $acronym_query = "SELECT WRS_acronym FROM tbl_system_setup";
        $acronym_result = mysqli_query($con, $acronym_query);
        $acronym_row = mysqli_fetch_assoc($acronym_result);
        $acronym = $acronym_row['WRS_acronym'];

        for ($i=1; $i <= $quantity; $i++) { 
            // Fetch the latest gallon ID to determine the next unique code
            $latest_id_query = "SELECT MAX(gallon_id) AS max_id FROM tbl_gallon";
            $latest_id_result = mysqli_query($con, $latest_id_query);
            $latest_id_row = mysqli_fetch_assoc($latest_id_result);
            $next_id = $latest_id_row['max_id'] + 1; // Incrementing the ID for the new gallon

            // Generate the unique code
            $unique_code = $acronym . sprintf('%05d', $next_id); // Format to three digits

            // Insert the new gallon record into the database
            $insert_query = "INSERT INTO tbl_gallon (unique_code, status) VALUES ('$unique_code', '$status')";
            $insert_result = mysqli_query($con, $insert_query);   
        }

        if ($insert_result) {
            $gallonWord = ($quantity > 1) ? 'gallons' : 'gallon';
            $_SESSION['success_message'] = "$quantity $gallonWord added successfully!";
            // Redirect back to the gallon inventory page
            header("Location: gallon_inventory.php");
            exit();
        }
        
    }

    // If not submitted, redirect to the gallon inventory page
    header("Location: gallon_inventory.php");
    exit();
?>
