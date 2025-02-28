<?php  
	session_start();
	include "admin_connect.php";
	include "function.php";

	$user_data = check_login($con);
	$id = $_SESSION['id'];

	$sql = "select * from admin where id = $id";
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


	$timezone = new DateTimeZone('Asia/Manila');
	$dateTime = new DateTime(null, $timezone);

	// $dateTime->modify('+3 years');
	// $dateTime->modify('+0 months');
	// $dateTime->modify('-6 days');

	$curDay = $dateTime->format("Y-m-d");
	// echo "$curDay";

	$current_year = $dateTime->format("Y");
	$current_month = $dateTime->format("m");
	$current_Month = $dateTime->format("M");
	$current_day = $dateTime->format("d");

	$this_day = "$current_Month $current_day, $current_year";
	$this_month = "$current_Month, $current_year";

	$daily_sales = 0;
	$monthly_sales = 0;
	$annual_sales = 0;

	//daily sales

	$daily_sql = "SELECT * FROM `tbl_sales` where date(date_sold) = '$curDay'";
	$daily_result = mysqli_query($con, $daily_sql);
	if($daily_result){
		
		while ($row = mysqli_fetch_assoc($daily_result)) {
			$total_price = $row['total_price'];
			$daily_sales+=$total_price;
			
		}
	}

	//monthly sales

	$monthly_sql = "SELECT * FROM `tbl_sales` where MONTH(date_sold) = '$current_month' AND year(date_sold) = '$current_year'";
	$monthly_result = mysqli_query($con, $monthly_sql);
	if($monthly_result){
		
		while ($row = mysqli_fetch_assoc($monthly_result)) {
			$total_price = $row['total_price'];
			$monthly_sales+=$total_price;
			
		}
	}

	//Annual sales

	$annual_sql = "SELECT * FROM `tbl_sales` where year(date_sold) = '$current_year'";
	$annual_result = mysqli_query($con, $annual_sql);
	if($annual_result){
		
		while ($row = mysqli_fetch_assoc($annual_result)) {
			$total_price = $row['total_price'];
			$annual_sales+=$total_price;
			
		}
	}

	//customers
	$customer_sql = "SELECT COUNT(*) FROM tbl_customers";
	$customer_result = mysqli_query($con, $customer_sql);
	if($customer_result){
		$row = mysqli_fetch_row($customer_result);
		$total_customers = $row[0];
	}
	

	//Borrowed Gallon
	$borrowed_sql = "SELECT COUNT(*) FROM `tbl_borrowedgallon`";
	$borrowed_result = mysqli_query($con, $borrowed_sql);
	if($borrowed_result){
		$row = mysqli_fetch_row($borrowed_result);
		$total_borGal = $row[0];
	}


	//Borrowable Gallons
	$returned_sql = "SELECT COUNT(*) AS borrowable_gallons FROM tbl_gallon WHERE status IN ('New', 'Old', 'Returned');";
	$returned_result = mysqli_query($con, $returned_sql);
	if($returned_result){
		$row = mysqli_fetch_assoc($returned_result);
		$total_borrowables = $row['borrowable_gallons'];
	}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo "Dashboard"; ?></title>

	<link rel="stylesheet" href="../css/bootstrap.css">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/all.min.css">

	<style>
		.dashboard-wrapper{
			height: 80vh;
		}
		.dashboard-content{
			width: 40rem;
			display: flex;
			justify-content: center;
			align-items: center;
		}
		.content-wrapper{
			width: 90% !important;
			position: relative;
        	display: flex;
		    justify-content: center;
		    align-items: center;
		    padding: 0rem;
		    background: #218ae7;
		    backdrop-filter: blur(3px);
		    color: white;
		    border: solid 1px gray;
		    border-radius: 5px;
        }
        .content-inside{
        	width: 100%;
    		text-align: center;
        }
        .sales{
        	width: inherit;
        }
        .label{
        	font-size: 1.5rem;
        	width: 100%;
        	padding: 1.5rem;
        	background-color: blue;
        }
        .value{
        	width: 100%;
        	font-size: 2.2rem;
        	padding: 3rem 1rem;
        }
        .sales-section{
        	padding-top: 2.0rem;
        }
        .content-wrapper::before{
		    position: absolute;
		    top: -28px;
		    color: black;
		    font-size: 1.2rem;
		    display: none;
        }
        .content-wrapper.daily_sales::before{
        	content: '<?php echo "$this_day"; ?>';
        	left: 34%;
        }
        .content-wrapper.monthly_sales::before{
        	content: '<?php echo "$this_month"; ?>';
        	left: 36%;
        }
        .content-wrapper.annual_sales::before{
        	content: '<?php echo "$current_year"; ?>';
        	left: 42%;
        }
        .content-wrapper:hover::before{
        	display: block;
        }
        .wrs_logo{
        	background-image: url(../uploads/<?php echo "$WRS_logo"; ?>) !important;
        }
        a{
        	text-decoration: none;
        }
        .customer-section{
        	padding-top: 1.5rem;
        }
	</style>

</head>
<body>
			
	<main>
		<div class="sidebar">
		    <ul class="menu" style="margin-top: 3em;">
		        <li class="active">
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
		    <div class="section sales-section">
		    	<div class="container">
		    		<div class="row">
		    			<div class="col-md-4">
		    				<center>
			    				<div class="content-wrapper daily_sales">
			    					<div class="content-inside">
			    						<div class="sales daily--sales">
			    							<div class="label"><i class="fas fa-chart-line"></i> Daily Sales</div>
			    							<div class="value">₱ <?php echo number_format($daily_sales,2); ?></div>			
			    						</div>
			    					</div>
			    				</div>
		    				</center>
		    			</div>
		    			<div class="col-md-4">
		    				<center>
		    					<div class="content-wrapper monthly_sales">
			    					<div class="content-inside">
			    						<div class="sales monthly--sales">
			    							<div class="label"><i class="fas fa-chart-bar"></i> Monthly Sales</div>
			    							<div class="value">₱ <?php echo number_format($monthly_sales,2); ?></div>
			    						</div>
			    					</div>
			    				</div>
		    				</center>
		    			</div>
		    			<div class="col-md-4">
		    				<center>
		    					<div class="content-wrapper annual_sales">
			    					<div class="content-inside">
			    						<div class="sales annual--sales">
			    							<div class="label"><i class="fas fa-chart-area"></i> Annual Sales</div>
			    							<div class="value">₱ <?php echo number_format($annual_sales,2); ?></div>
			    						</div>
			    					</div>
			    				</div>
		    				</center>
		    			</div>
		    		</div>
		    	</div>
		    </div>
		    <div class="section customer-section">
		    	<div class="container">
		    		<div class="row">
		    			<div class="col-md-4">
		    				<center>
			    				<div class="content-wrapper">
			    					<div class="content-inside">
			    						<div class="sales daily--sales">
			    							<div class="label"><i class="fas fa-users"></i> Customers</div>
			    							<div class="value"><?php echo "$total_customers"; ?></div>			
			    						</div>
			    					</div>
			    				</div>
		    				</center>
		    			</div>
		    			<div class="col-md-4">
		    				<center>
		    					<div class="content-wrapper">
			    					<div class="content-inside">
			    						<div class="sales monthly--sales">
			    							<div class="label"><i class="fas fa-retweet"></i> Borrowed Gallon</div>
			    							<div class="value"><?php echo "$total_borGal"; ?></div>
			    						</div>
			    					</div>
			    				</div>
		    				</center>
		    			</div>
		    			<div class="col-md-4">
		    				<center>
		    					<div class="content-wrapper">
			    					<div class="content-inside">
			    						<div class="sales annual--sales">
			    							<div class="label"><i class="fas fa-check-circle"></i> Borrowable Gallons</div>
			    							<div class="value"><?php echo "$total_borrowables"; ?></div>
			    						</div>
			    					</div>
			    				</div>
		    				</center>
		    			</div>
		    		</div>
		    	</div>
		    </div>
		</div>	
	</main>
</body>
</html>