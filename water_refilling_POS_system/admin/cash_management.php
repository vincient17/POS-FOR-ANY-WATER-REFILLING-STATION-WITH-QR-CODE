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

    // Fetch system details
    $wrs_sql = "SELECT * FROM `tbl_system_setup` WHERE setup_id = 1";
    $wrs_result = mysqli_query($con, $wrs_sql);
    if ($wrs_result) {
        $row = mysqli_fetch_assoc($wrs_result);
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

    $timezone = new DateTimeZone('Asia/Manila');
	$dateTime = new DateTime(null, $timezone);
	$curDay = $dateTime->format("Y-m-d");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "Cash Remitted"; ?></title>

    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/all.min.css">

    <style>
        .wrs_logo {
            background-image: url(../uploads/<?php echo "$WRS_logo"; ?>) !important;
        }
        a {
            text-decoration: none;
        }
        .denomination-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }
        .denomination-table th, .denomination-table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .denomination-table th {
            background-color: #f4f4f4;
        }
        .denomination-container {
            padding: 2rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }
        /* Title Styling */
        h2 {
            font-size: 24px;
            color: #0056b3; /* Water blue theme */
            margin-bottom: 20px;
            font-weight: bold;
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
                <li>
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
                <li class="active">
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
            
            <div class="table-wrapper">
                <div class="table-content">
                    <h2>Remittance Data</h2>
                    <table class="table table-bordered denomination-table table-secondary">
					    <thead>
					        <tr>
					            <th>No.</th>
					            <th>₱ 1.00</th>
					            <th>₱ 5.00</th>
					            <th>₱ 10.00</th>
					            <th>₱ 20.00</th>
					            <th>₱ 50.00</th>
					            <th>₱ 100.00</th>
					            <th>₱ 200.00</th>
					            <th>₱ 500.00</th>
					            <th>₱ 1000.00</th>
					            <th>Total</th>
					            <th>Date Remitted</th>
					        </tr>
					    </thead>
					    <tbody>
					        <?php 

							    $remit_sql = "SELECT * FROM tbl_remit ORDER BY date_remitted DESC LIMIT 30";
							    $remit_result = mysqli_query($con, $remit_sql);

							    if($remit_result && mysqli_num_rows($remit_result) > 0){
							    	$number = 0;
							    	while($remit_row = mysqli_fetch_assoc($remit_result)){
									    $unit_1 = $remit_row['p1'] ;
									    $unit_5 = $remit_row['p5'] ;
									    $unit_10 = $remit_row['p10'];
									    $unit_20 = $remit_row['p20'];
									    $unit_50 = $remit_row['p50'];
									    $unit_100 = $remit_row['p100'];
									    $unit_200 = $remit_row['p200'];
									    $unit_500 = $remit_row['p500'];
									    $unit_1000 = $remit_row['p1000'];
							    		$date_remitted = date("F j, Y, g:i A", strtotime($remit_row['date_remitted']));
							    		$overall_total = ($unit_1 * 1) + ($unit_5 * 5) + ($unit_10 * 10) + ($unit_20 * 20) + ($unit_50 * 50) + ($unit_100 * 100) + ($unit_200 * 200) + ($unit_500 * 500) + ($unit_1000 * 1000);

							    		?>

							    		<tr>
							    			<td><?php echo ++$number; ?></td>
							    			<td><?php echo "$unit_1"; ?></td>
							    			<td><?php echo "$unit_5"; ?></td>
							    			<td><?php echo "$unit_10"; ?></td>
							    			<td><?php echo "$unit_20"; ?></td>
							    			<td><?php echo "$unit_50"; ?></td>
							    			<td><?php echo "$unit_100"; ?></td>
							    			<td><?php echo "$unit_200"; ?></td>
							    			<td><?php echo "$unit_500"; ?></td>
							    			<td><?php echo "$unit_1000"; ?></td>
							    			<td><?php echo "$overall_total"; ?></td>
							    			<td><?php echo "$date_remitted"; ?></td>
							    		</tr>		

							    		<?php


							    	}
							    }


					         ?>
					    </tbody>
					</table>

                </div>
            </div>
        </div>
    </main>
</body>
</html>
