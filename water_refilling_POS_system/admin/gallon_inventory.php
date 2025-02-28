<?php  
session_start();
include "admin_connect.php";
include "function.php";

$user_data = check_login($con);
$id = $_SESSION['id'];

$sql = "SELECT * FROM admin WHERE id = $id";
$result = mysqli_query($con, $sql);
$row = mysqli_fetch_assoc($result);
$username = $row['username'];

$wrs_sql = "SELECT * FROM `tbl_system_setup` WHERE setup_id = 1";
$wrs_result = mysqli_query($con, $wrs_sql);

if ($wrs_result) {
    $row = mysqli_fetch_assoc($wrs_result); // Corrected this line

    // Make sure to check if $row is not empty
    if ($row) {
        $WRS_name = $row['WRS_name'];
        $WRS_acronym = $row['WRS_acronym'];
        $WRS_logo = $row['WRS_logo'];
    } else {
        echo "No records found.";
    }
} else {
    echo "Error in query: " . mysqli_error($con); // Handle query errors
}

if (isset($_SESSION['success_message'])) {
    echo '<div id="success-message" class="alert alert-success">'.$_SESSION['success_message'].'</div>';
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['borrow_success_message'])) {
    echo '<div id="success-message" class="alert alert-success">' . $_SESSION['borrow_success_message'] . '</div>';
    unset($_SESSION['borrow_success_message']);
}

if (isset($_SESSION['error_message'])) {
    echo '<div id="error_message" class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
    unset($_SESSION['error_message']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "Gallon Inventory"; ?></title>

    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/all.min.css">

    <!-- jQuery for toggling detailed rows -->
    <script src="../js/jquery-3.6.0.min.js"></script>

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
        .borrowmodal {
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
    <main>
        <div class="sidebar">
            <ul class="menu" style="margin-top: 3em;">
                <li>
                    <a href="dashboard.php">
                        <i class="fas fa-chart-bar"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="customers.php">
                        <i class="fas fa-users"></i>
                        <span>Customers</span>
                    </a>
                </li>
                <li class="active">
                    <a href="gallon_inventory.php">
                        <i class="fas fa-jar"></i>
                        <span>Gallon Tracking</span>
                    </a>
                </li>
                <li>
                    <a href="products.php">
                        <i class="fas fa-tags"></i>
                        <span>Products</span>
                    </a>
                </li>
                <li>
                    <a href="sales.php">
                        <i class="fas fa-coins"></i>
                        <span>Sales</span>
                    </a>
                </li>
                <li>
                    <a href="cash_management.php">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Cash Remitted</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="main-content">
            <?php include "header.php"; ?>

            <div class="banner-img-wrapper"></div>

            <div class="section sect1">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12 dp-flex align-center" style="gap:1rem;">
                            <button id="openAddGallonModal" class="btn btn-primary" onclick="document.getElementById('addGallonModal').style.display='block'">Add Gallon</button>
                            <button id="openBorrowGallonModal" class="btn btn-primary" onclick="document.getElementById('borrowGallonModal').style.display='block'">Borrow Gallon</button>
                            <a href="scan_returned.php"><button class="btn btn-primary">Return Gallon</button></a>
                            <a href="borrowers_list.php"><button class="btn btn-primary">Print QR</button></a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Add Gallon Modal -->
            <div id="addGallonModal" class="modal">
                <div class="modal-wrapper">
                    <div class="modal-content">
                        <span class="close" onclick="document.getElementById('addGallonModal').style.display='none'">&times;</span>
                        <center><h2>Add Gallon</h2></center>
                        <form action="add_gallon.php" method="post">
                            <div class="input-group">
                                <label class="label" for="status">Status:</label>
                                <select id="status" name="status" required>
                                    <option value="" disabled selected>Select Status</option>
                                    <option value="New">New</option>
                                    <option value="Old">Old</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <label class="label" for="quantity">Quantity:</label>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" required>
                            </div>
                            <input class="btn btn-primary submit-btn" type="submit" value="Add Gallon" name="submit">
                        </form>
                    </div>
                </div>
            </div>

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



            <!-- Return Gallon Modal -->
            <div id="returnGallonModal" class="modal">
                <div class="modal-wrapper">
                    <div class="modal-content">
                        <span class="close" onclick="document.getElementById('returnGallonModal').style.display='none'">&times;</span>
                        <center><h2>Return Gallon</h2></center>
                        <form action="return_gallon.php" method="post">
                            <div class="input-group">
                                <label class="label" for="gallon_id">Gallon ID:</label>
                                <input type="text" id="gallon_id" name="gallon_id" required>
                            </div>
                            <input class="btn btn-danger submit-btn" type="submit" value="Return Gallon">
                        </form>
                    </div>
                </div>
            </div>


            <div class="table-wrapper">
                <div class="table-content">
                    <div class="table-header">List of Borrowed Gallon</div>
                    <table class="table table-bordered table-primary">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" style="width: 4rem;">No.</th>
                                <th scope="col">Name</th>
                                <th scope="col">Address</th>
                                <th scope="col">Cellphone No.</th>
                                <th scope="col">No. of Gallons</th>
                                <th scope="col">Date Borrowed</th>
                            </tr>
                        </thead>
                        <tbody class="table-info">
                            <?php
                            // Grouping query by customer and date_borrowed
                            $sql = "SELECT tbl_customers.cus_id, tbl_customers.name, tbl_customers.address, tbl_customers.cp_num, 
                                    COUNT(tbl_borrowedgallon.gallon_id) as total_gallons, 
                                    MIN(tbl_borrowedgallon.date_borrowed) as date_borrowed 
                                    FROM tbl_borrowedgallon 
                                    JOIN tbl_customers ON tbl_borrowedgallon.cus_id = tbl_customers.cus_id 
                                    GROUP BY tbl_customers.cus_id, tbl_borrowedgallon.date_borrowed 
                                    ORDER BY tbl_borrowedgallon.borrowed_id DESC";
                            
                            $result = mysqli_query($con, $sql);
                            if($result && mysqli_num_rows($result) > 0){
                                $number = 0;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ++$number;
                                    $cus_id = $row['cus_id'];
                                    $name = $row['name'];
                                    $address = $row['address'];
                                    $cp_num = $row['cp_num'];
                                    $total_gallons = $row['total_gallons'];
                                    $date_borrowed = $row['date_borrowed'];

                                    // Format the date
                                    $date = new DateTime($date_borrowed);
                                    $formattedDate = $date->format('F j, Y - g:i A');

                                    // Summarized customer row
                                    echo "<tr data-toggle='collapse' data-target='#details$number' class='accordion-toggle'>
                                            <td>$number</td>
                                            <td>$name</td>
                                            <td>$address</td>
                                            <td>$cp_num</td>
                                            <td>$total_gallons</td>
                                            <td>$formattedDate</td>
                                          </tr>";
                                    
                                    // Detailed rows for specific gallons (initially hidden)
                                    echo "<tr id='details$number' class='collapse'>
                                            <td colspan='6'>
                                              <table class='table'>
                                                <thead>
                                                  <tr>
                                                    <th scope='col'>Gallon Code</th>
                                                  </tr>
                                                </thead>
                                                <tbody>";
                                                
                                    // Query for each specific gallon borrowed by the customer
                                    $gallon_sql = "SELECT unique_code 
                                                   FROM tbl_borrowedgallon 
                                                   JOIN tbl_gallon ON tbl_borrowedgallon.gallon_id = tbl_gallon.gallon_id 
                                                   WHERE tbl_borrowedgallon.cus_id = '$cus_id' 
                                                   AND tbl_borrowedgallon.date_borrowed = '$date_borrowed'";
                                    
                                    $gallon_result = mysqli_query($con, $gallon_sql);
                                    if($gallon_result && mysqli_num_rows($gallon_result) > 0){
                                        while($gallon_row = mysqli_fetch_assoc($gallon_result)){
                                            $unique_code = $gallon_row['unique_code'];
                                            echo "<tr><td>$unique_code</td></tr>";
                                        }
                                    }

                                    echo "      </tbody>
                                              </table>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>No borrowed gallons found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            
            <script>
                $(document).ready(function(){
                    $('.accordion-toggle').click(function(){
                        $(this).next().toggleClass('collapse');
                    });
                });
            </script>
            <script>
                // Close modal when clicking outside
                window.onclick = function(event) {
                    if (event.target.className === 'modal') {
                        event.target.style.display = 'none';
                    }
                }

                setTimeout(function() {
                    var successMessage = document.getElementById("success-message");
                    if (successMessage) {
                        successMessage.style.display = "none";
                    }
                }, 4000);
                setTimeout(function() {
                    var errorMessage = document.getElementById("error_message");
                    if (errorMessage) {
                        errorMessage.style.display = "none";
                    }
                }, 4000);
            </script>
        </div>    
    </main>

</body>
</html>
