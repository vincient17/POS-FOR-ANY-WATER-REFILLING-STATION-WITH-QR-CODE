<?php 

    include "admin_connect.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/all.min.css">

      <style>
        .wrs_logo{
            background-image: url(../uploads/<?php echo "$WRS_logo"; ?>) !important;
        }
        td {
            background: white;
            color: black;
        }
        .alert {
            padding: 15px;
            background-color: #00c908;
            color: white;
            text-align: center;
            position: fixed;
            top: -85px;
            right: 10px;
            width: 300px;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            animation: go_down 4s;
        }
        .alert-danger{
            background-color: red;
        }
        @keyframes go_down {
            0% {
                top: -85px;
            }
            25% {
                top: 7px;
            }
            75% {
                top: 7px;
            }
            100% {
                top: -85px;
            }
        }

        .dashboard-wrapper {
            height: 80vh;
        }
        .dashboard-content {
            width: 40rem;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        form {
            position: relative;
            width: fit-content;
            padding: 0rem;
            border: none;
            border-radius: 5px;
            background: transparent !important;
        }
        #searchInput {
            margin-bottom: 0px;
        }
        form button {
            background: black;
            padding: 5px 13px;
            transform: translateX(-2px);
            border-radius: 3px;
        }
        form button i {
            font-size: 20px;
            color: white;
        }
        .sect1 {
            margin-top: 2rem;
        }
        /* Modal styles */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 99; /* On top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            background-color: rgba(0, 0, 0, 0.5); /* Black with opacity */
            overflow: auto; /* Enable scroll if needed */
        }

        /* Modal wrapper and content */
        .modal-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px; /* Rounded corners */
            width: 40%; /* Width adjustment */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        /* Close button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            cursor: pointer;
        }

        /* Simple form styling */
        .modal form {
            display: flex;
            flex-direction: column;
            width: 100%; /* Full width for form */
        }

        .modal-form input[type="text"], 
        .modal-form input[type="email"], 
        .modal-form input[type="submit"],
        .modal-form input[type="number"] {
            padding: 10px;
            margin: 10px 0;
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px; /* Rounded corners for inputs */
            font-size: 16px; /* Larger text for better readability */
        }

        .modal-form input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .modal-form input[type="submit"]:hover {
            background-color: #45a049;
        }

        .submit-btn {
            margin-top: 1.3rem;
        }
        .label {
            margin: 0;
        }
        .input-group {
            margin: 1rem 0px;
        }
        .select2{
            width: 100% !important;
        }
        .select2-container--default .select2-selection--single{
            padding: 7px 0px !important;
        }
        .select2-container .select2-selection--single{
            height: unset !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow{
            top: 9px !important;
        }
        .qr-code-box {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 170px;
            height: 170px;
            background: #f8f8f8;
            border: 2px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px;
        }
        .table-header{
            text-align: center;
            padding:8px;
            background-color: blue;
            color: white;
            font-size: 2rem;
        }
    </style>

</head>
<body>
    <button id="openBorrowGallonModal" class="btn btn-primary" onclick="document.getElementById('borrowGallonModal').style.display='block'">Borrow Gallon</button>
        
      <!-- Borrow Gallon Modal -->
        <div id="borrowGallonModal" class="modal">
            <div class="modal-wrapper">
                <div class="modal-content">
                    <span class="close" onclick="document.getElementById('borrowGallonModal').style.display='none'">&times;</span>
                    <center><h2>Borrow Gallon</h2></center>
                    <form id="borrowForm" action="borrow_gallon.php" method="post" onsubmit="return handleSubmit()">
                        <div class="input-group">
                            <label class="label" for="cus_id">Customer:</label>
                            <select id="cus_id" name="cus_id" class="searchable-dropdown" required>
                                <option value="" disabled selected>Select Customer</option>
                                <?php
                                // Fetch customers from the database
                                $customer_query = "SELECT cus_id, name FROM tbl_customers";
                                $customer_result = mysqli_query($con, $customer_query);
                                while ($row = mysqli_fetch_assoc($customer_result)) {
                                    echo '<option value="'.$row['cus_id'].'">'.$row['name'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="input-group">
                            <label class="label" for="gallon_id">Available Gallon:</label>
                            <select id="gallon_id" name="gallon_id[]" multiple="multiple" required>
                                <option value="" disabled>Select Available Gallon(s)</option>
                                <?php
                                // Fetch gallons from the database
                                $gallon_query = "
                                    SELECT *, 
                                    CASE 
                                        WHEN status = 'New' THEN 'New'
                                        WHEN status = 'Old' THEN 'Old'
                                        WHEN status = 'Returned' THEN 'Old'
                                    END AS gallon_status
                                    FROM tbl_gallon
                                    WHERE status IN ('New', 'Old', 'Returned')
                                ";
                                $gallon_result = mysqli_query($con, $gallon_query);
                                while ($row = mysqli_fetch_assoc($gallon_result)) {
                                    $gallon_status = $row['gallon_status'];
                                    $unique_code = $row['unique_code'];
                                    $gallon_id = $row['gallon_id'];
                                    echo '<option value="'.$gallon_id.'">'.$unique_code.' - '.$gallon_status.'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <input class="btn btn-warning submit-btn" type="submit" value="Borrow Gallon" name="submit">
                    </form>
                </div>
            </div>
        </div>

        
        <!-- Include jQuery for the search functionality -->
        <script src="../js/jquery.min.js"></script>
        <!-- Include jQuery UI for autocomplete functionality -->
        <script src="../js/jquery-ui.js"></script>

        <script>
            $(document).ready(function() {
                // Convert the select element into a searchable dropdown using jQuery
                $('.searchable-dropdown').select2({
                    placeholder: 'Search and select customer',
                    allowClear: true
                });
            });
        </script>

        <!-- Include Select2 CSS and JS for the searchable dropdown -->
        <link href="../css/select2.min.css" rel="stylesheet" />
        <script src="../js/select2.min.js"></script>

</body>
</html>