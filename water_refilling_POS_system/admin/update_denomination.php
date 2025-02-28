<?php
    include('admin_connect.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $unit_1 = $_POST['unit_1'];
        $unit_5 = $_POST['unit_5'];
        $unit_10 = $_POST['unit_10'];
        $unit_20 = $_POST['unit_20'];
        $unit_50 = $_POST['unit_50'];
        $unit_100 = $_POST['unit_100'];
        $unit_200 = $_POST['unit_200'];
        $unit_500 = $_POST['unit_500'];
        $unit_1000 = $_POST['unit_1000'];

        $sql = "UPDATE tbl_cash_denomination 
                SET 
                    unit_1 = ?, 
                    unit_5 = ?, 
                    unit_10 = ?, 
                    unit_20 = ?, 
                    unit_50 = ?, 
                    unit_100 = ?, 
                    unit_200 = ?, 
                    unit_500 = ?, 
                    unit_1000 = ? 
                WHERE id = 1";
        
        // Prepare and bind
        if ($stmt = $con->prepare($sql)) {
            $stmt->bind_param("iiiiiiiii", $unit_1, $unit_5, $unit_10, $unit_20, $unit_50, $unit_100, $unit_200, $unit_500, $unit_1000);
            
            // Execute query
            if ($stmt->execute()) {
                echo "<script>alert('Denominations updated successfully!');</script>";
                echo "<script>window.location.href = 'cash_management.php';</script>";
            } else {
                echo "<script>alert('Error updating denominations. Please try again.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Error preparing the update query.');</script>";
        }
        
        $con->close();
    }
?>
