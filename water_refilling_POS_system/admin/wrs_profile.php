<?php
// Include the database connection file
include "admin_connect.php"; // Ensure this file has the correct connection setup

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $wrs_name = $_POST['wrs_name'];
    $wrs_acronym = $_POST['wrs_acronym'];

    // Handle file upload for logo if present
    if (isset($_FILES['wrs_logo']) && $_FILES['wrs_logo']['error'] == 0) {
        $logo_name = $_FILES['wrs_logo']['name'];
        $logo_tmp = $_FILES['wrs_logo']['tmp_name'];
        $logo_destination = '../uploads/' . $logo_name; // Correct path to the uploads folder

        // Check if uploads directory exists and create if not
        if (!is_dir('../uploads')) {
            mkdir('../uploads', 0755, true); // Set appropriate permissions
        }

        // Move uploaded file to destination
        if (move_uploaded_file($logo_tmp, $logo_destination)) {
            $logo_path = $logo_name; // Save the path to use in the database and display
        } else {
            echo "<script>alert('Failed to upload logo.');</script>";
        }
    }

    // Update WRS data in the database
    $update_query = "UPDATE tbl_system_setup 
                     SET WRS_name = '$wrs_name', 
                         WRS_acronym = '$wrs_acronym'";

    if (isset($logo_path)) {
        $update_query .= ", WRS_logo = '$logo_path'";
    }

    $update_query .= " WHERE setup_id = 1"; // Assuming there's only one record

    if (mysqli_query($con, $update_query)) {
        echo "<script>alert('Profile updated successfully!');</script>";
    } else {
        echo "Error updating profile: " . mysqli_error($con);
    }
}

// Fetch the current WRS data to display in the form
$wrs_query = "SELECT * FROM tbl_system_setup WHERE setup_id = 1";
$wrs_result = mysqli_query($con, $wrs_query);
if ($wrs_row = mysqli_fetch_assoc($wrs_result)) {
    $wrs_name = $wrs_row['WRS_name'];
    $wrs_acronym = $wrs_row['WRS_acronym'];
    $wrs_logo = $wrs_row['WRS_logo'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WRS Profile</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .profile-container {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        .profile-container h2 {
            font-size: 24px;
            margin-bottom: 30px;
            color: #333;
            letter-spacing: 1px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
            color: #555;
            text-align: left;
        }

        .input-group input[type="text"],
        .input-group input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 16px;
            color: #333;
            outline: none;
            transition: 0.3s ease;
        }

        .input-group input[type="text"]:focus,
        .input-group input[type="file"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .profile-logo img {
            width: 120px;
            height: 120px;
            display: block;
            margin: 15px auto 30px;
            border-radius: 50%;
            border: 2px solid #ccc;
            padding: 5px;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .submit-btn:hover {
            background-color: #0056b3;
        }

        .profile-logo {
            text-align: center;
            margin-bottom: 20px;
        }

        /* Additional styling */
        .profile-container {
            position: relative;
            background: linear-gradient(to bottom right, #e0f7fa, #ffffff);
        }
        .return-btn{
            position: absolute;
            top: 2px;
            right: 1px;
            padding: 2px 8px;
            color: white;
            background-color: red;
            text-decoration: none;
            border-radius: 2px;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <a href="dashboard.php" class="return-btn">x</a>
    <h2>WRS Profile</h2>
    
    <!-- Display current WRS information -->
    <form action="wrs_profile.php" method="post" enctype="multipart/form-data">
        <div class="input-group profile-logo">
            <label for="wrs_logo">Current Logo:</label>
            <?php if ($wrs_logo) : ?>
                <img src="../uploads/<?php echo htmlspecialchars($wrs_logo); ?>" alt="WRS Logo">
            <?php else : ?>
                <p>No logo uploaded.</p>
            <?php endif; ?>
            <input type="file" id="wrs_logo" name="wrs_logo">
        </div>
        <div class="input-group">
            <label for="wrs_acronym">WRS Acronym:</label>
            <input type="text" id="wrs_acronym" name="wrs_acronym" value="<?php echo htmlspecialchars($wrs_acronym); ?>" disabled>
        </div>
        
        <div class="input-group">
            <label for="wrs_name">WRS Name:</label>
            <input type="text" id="wrs_name" name="wrs_name" value="<?php echo htmlspecialchars($wrs_name); ?>" required>
        </div>
        

        <input type="submit" class="submit-btn" value="Update Profile">
    </form>
</div>

</body>
</html>
