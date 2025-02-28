<?php

    include "admin_connect.php";

    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve form inputs
        $wrs_name = $_POST['wrs_name'];
        $wrs_acronym = $_POST['wrs_acronym'];
        $wrs_logo = '';

        // Handle file upload
        if (isset($_FILES['wrs_logo']) && $_FILES['wrs_logo']['error'] === 0) {
            $target_dir = "../uploads/";
            $file_name = basename($_FILES["wrs_logo"]["name"]);
            $target_file = $target_dir . $file_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Ensure the file is an image
            $check = getimagesize($_FILES["wrs_logo"]["tmp_name"]);
            if ($check !== false) {
                // Check file type
                $allowed_file_types = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array($imageFileType, $allowed_file_types)) {
                    // Move the uploaded file
                    if (move_uploaded_file($_FILES["wrs_logo"]["tmp_name"], $target_file)) {
                        $wrs_logo = $file_name;
                    } else {
                        $message = "Sorry, there was an error uploading your file.";
                    }
                } else {
                    $message = "Only JPG, JPEG, PNG, and GIF files are allowed.";
                }
            } else {
                $message = "File is not an image.";
            }
        }

        // Insert data into the database if no error
        if (empty($message)) {
            $stmt = $con->prepare("INSERT INTO tbl_system_setup (WRS_name, WRS_acronym, WRS_logo) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $wrs_name, $wrs_acronym, $wrs_logo);

            $guest_sql = "INSERT INTO `tbl_customers`(`name`, `cus_unique_code`) VALUES ('Guest','0000000000')";
            $guest_result = mysqli_query($con, $guest_sql);

            $cash_sql = "INSERT INTO `tbl_cash_denomination`(`unit_1`, `unit_5`, `unit_10`, `unit_20`, `unit_50`, `unit_100`, `unit_200`, `unit_500`, `unit_1000`) VALUES ('0','0','0','0','0','0','0','0','0')";
            $cash_result = mysqli_query($con, $cash_sql);

            if ($stmt->execute() && $guest_result && $cash_result) {
                header("location: dashboard.php");
            } else {
                $message = "Error: " . $stmt->error;
            }

            $stmt->close();
        }
    }

$con->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick System Setup - Water Refilling Station</title>
    <style>
        @keyframes floating {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
            100% {
                transform: translateY(0px);
            }
        }

        @keyframes waterFlow {
            from {
                background-position: 10% 10;%
            }
            to {
                background-position: 100% 100%;
            }
        }

        body {
            font-family: 'Arial', sans-serif;
            background: radial-gradient(circle, #e0f7fa, #00bcd4);
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            width: 400px;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            position: relative;
            text-align: center;
            animation: floating 6s ease-in-out infinite;
        }

        h2 {
            color: #00796b;
            margin-bottom: 20px;
            font-size: 28px;
        }

        label {
            display: block;
            margin: 10px 0;
            font-weight: bold;
            color: #004d40;
        }

        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button {
            width: 100%;
            padding: 10px;
            background: linear-gradient(45deg, #00acc1, #009688);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button:hover {
            background: linear-gradient(45deg, #00796b, #004d40);
        }

        .water-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: url('../img/water_ripple.jpg') repeat;
            opacity: 0.1;
            animation: waterFlow 10s linear infinite alternate;
        }

        .aura {
            position: absolute;
            top: -20px;
            left: -20px;
            width: 460px;
            height: 460px;
            background: radial-gradient(circle, rgba(0, 150, 136, 0.2), rgba(0, 188, 212, 0.3));
            filter: blur(60px);
            z-index: -1;
        }

        .message {
            color: red;
            margin-top: 20px;
            font-weight: bold;
        }

        .success {
            color: green;
        }
    </style>
</head>
<body>
    <div class="aura"></div>
    <div class="water-bg"></div>
    <div class="container">
        <h2>Quick System Setup</h2>

        <!-- Display message -->
        <?php if (!empty($message)): ?>
            <p class="message <?php echo (strpos($message, 'success') !== false) ? 'success' : ''; ?>">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>

        <!-- Setup Form -->
        <form action="quick_setup.php" method="post" enctype="multipart/form-data">
            <!-- WRS Name -->
            <label for="wrs_name">Water Refilling Station Name:</label>
            <input type="text" id="wrs_name" name="wrs_name" placeholder="Enter your WRS name" required>

            <!-- WRS Acronym -->
            <label for="wrs_acronym">WRS Acronym:</label>
            <input type="text" id="wrs_acronym" name="wrs_acronym" placeholder="Enter your WRS acronym (e.g., ABC)" required>

            <!-- WRS Logo -->
            <label for="wrs_logo">Upload WRS Logo:</label>
            <input type="file" id="wrs_logo" name="wrs_logo" accept="image/*" required>

            <!-- Submit Button -->
            <button type="submit">Save and Continue</button>
        </form>
    </div>
</body>
</html>
