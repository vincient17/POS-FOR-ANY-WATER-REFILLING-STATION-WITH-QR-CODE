<?php  
	session_start();
	include "admin_connect.php";
	include "function.php";

	$timezone = new DateTimeZone('Asia/Manila');
	$dateTime = new DateTime(null, $timezone);

	// $dateTime->modify('+3 years');
	// $dateTime->modify('+0 months');
	// $dateTime->modify('-6 days');

	$curDay = $dateTime->format("Y-m-d");
	// echo "$curDay";
	$currentDay = $dateTime->format("M d, Y");

	$current_year = $dateTime->format("Y");
	$current_month = $dateTime->format("m");
	$current_day = $dateTime->format("d");
	$currentDate = "$current_year-$current_month-$current_day";


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


	if (isset($_GET['orderid'])) {
	    $sales_ID = intval($_GET['orderid']);
	    
	    // Fetch customer data from the database
	    $query = "SELECT * FROM tbl_customers JOIN tbl_sales ON tbl_customers.cus_id = tbl_sales.cus_id JOIN tbl_products ON tbl_products.product_id = tbl_sales.product_id where sales_ID = $sales_ID LIMIT 1";
	    $result = mysqli_query($con, $query);

	    if ($result && mysqli_num_rows($result) > 0) {
	        $row = mysqli_fetch_assoc($result);

	        $db_cus_id = $row['cus_id'];
	        $db_name = $row['name'];
	        $db_address = htmlspecialchars($row['address']);
	        $db_cp_num = htmlspecialchars($row['cp_num']);
	        $db_unique_code = $row['cus_unique_code'];
	        $db_product_id = $row['product_id'];
	        $db_customer_service = $row['customer_service'];
	        $db_total_order = $row['total_order'];
	        $db_total_price = $row['total_price'];
	        $db_date_sold = $row['date_sold'];
	        $db_p_name = $row['p_name'];
	        $db_p_price = $row['p_price'];



	    }
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo "Sales"; ?></title>

	<link rel="stylesheet" href="../css/bootstrap.css">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/sales.css">
	<link rel="stylesheet" href="../css/all.min.css">

	<style>
		.wrs_logo{
        	background-image: url(../uploads/<?php echo "$WRS_logo"; ?>) !important;
        }
        input{
        	width: inherit;
        	padding: 5px 8px !important;
        }
        select{
        	padding: 5px 8px !important;
        }
        p{
        	margin: 0;
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
		        <li class="active">
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
		    			<div class="col-md-12">
		    				<?php 

									if(isset($_POST['filter'])){
										$from_date = $_POST['from_date'];
										$to_date = $_POST['to_date'];
										$name = $_POST['name'];
										$service = $_POST['service'];

										if(empty($from_date) && empty($to_date)){
											$from_date = $currentDate;
											$to_date = $currentDate;
										}elseif (empty($to_date)) {
											$to_date = $currentDate;
										}elseif (empty($from_date)) {
											$from_date = $currentDate;
										}

										if(empty($name)){ $name = ''; }
										if($service == "All"){ $service = ''; }

										$filtered_date1 = new DateTime($from_date);
										$fromSelectedDate = $filtered_date1->format("M d, Y");

										$filtered_date2 = new DateTime($to_date);
										$toSelectedDate = $filtered_date2->format("M d, Y");


									?>

									<div class="content-wrapper dp-flex align-center space-between">
										<button class="btn btn-secondary" onclick="printTable()">Print Report</button>
										<div></div>
										<form action="" method="post">
											<div class="input--group">
												<label for="name">Name: </label>
												<input type="text" id="name" name="name" value="<?php echo "$name"; ?>">
											</div>
											<div class="input--group">
												<label for="service">Service: </label>
												<select name="service" id="service">
													<option value="<?php echo "$service"; ?>" selected><?php if($service == ''){ echo "All";}else{echo "$service";} ?></option>
													<option value="All">All</option>
													<option value="Pick-up">Pick-up</option>
													<option value="Deliver">Deliver</option>
												</select>
											</div>
											<div class="date-input dp-flex">
												<div class="input--group">
													<label for="from_date">From: </label>
													<input type="date" id="from_date" name="from_date" value="<?php echo "$from_date"; ?>">
												</div>
												<div class="input--group">
													<label for="to_date">To: </label>
													<input type="date" id="to_date" name="to_date" value="<?php echo "$to_date"; ?>">
												</div>
											</div>
											<input type="submit" name="filter" class="btn btn-primary" value="Filter">
										</form>
									</div>
								    <div class="table-content">
										<p><?php

												if($fromSelectedDate == $toSelectedDate){
													echo "Data from $fromSelectedDate"; 
												}else{
													echo "Data from $fromSelectedDate - $toSelectedDate"; 
												}

												
											?></p>
										<table id="reportTable" class="table table-bordered">
											<thead style="background: gray; color: white;">
												<tr>
											      <th scope="col">No.</th>
											      <th scope="col">Customer</th>
											      <th scope="col">Product</th>
											      <th scope="col">Customer Service</th>
											      <th scope="col">Amount</th>
											      <th scope="col">Total Order</th>
											      <th scope="col">Total Price</th>
											      <th scope="col">Date Sold</th>
											    </tr>
											</thead>
											<div class="tbody-content"></div>
												<tbody>

									<?php

										$sql = "SELECT * FROM tbl_customers JOIN tbl_sales ON tbl_customers.cus_id = tbl_sales.cus_id JOIN tbl_products ON tbl_products.product_id = tbl_sales.product_id JOIN tbl_category ON tbl_products.category_id = tbl_category.category_id where date(date_sold) >= '$from_date' AND date(date_sold) <= '$to_date' and tbl_customers.name LIKE '%$name%' AND tbl_sales.customer_service LIKE '%$service%' ORDER BY date_sold desc";
										$result = mysqli_query($con, $sql);
										
										if($result){
											$number = 0;
											$total_daily_sales = 0;
											$pick_count = 0;
											$deliver_count = 0;
											while ($row = mysqli_fetch_assoc($result)) {
												++$number;
												$sales_ID = $row['sales_ID'];
												$cus_id = $row['cus_id'];
												$name = $row['name'];
												$address = $row['address'];
												$cp_num = $row['cp_num'];
												$cus_unique_code = $row['cus_unique_code'];
												$product_id = $row['product_id'];
												$customer_service = $row['customer_service'];
												$total_order = $row['total_order'];
												$total_price = $row['total_price'];
												$date_sold = $row['date_sold'];
												$p_name = $row['p_name'];
												$p_price = $row['p_price'];
												$category_description = $row['category_description'];

												$date = new DateTime($date_sold);
                                    			$formattedDate = $date->format('F j, Y - g:i A');

												$total_daily_sales += $total_price;

												if($customer_service == "Pick-up"){
													$pick_count++;
												}
												if($customer_service == "Deliver"){
													$deliver_count++;
												}


									?>
											<tr>
												
													<td style="width: 2rem;"><?php echo "$number"; ?></td>
													<td><?php echo "$name"; ?></td>
													<td><?php echo "$p_name ($category_description)"; ?></td>
													<td><?php echo "$customer_service"; ?></td>
													<td><?php echo "$p_price"; ?></td>
													<td><?php echo "$total_order"; ?></td>
														
													<td>₱ <?php echo number_format($total_price, 2); ?></td>
													<td><?php echo "$formattedDate"; ?></td>
												
											</tr>

									<?php	

											}
										}

									}else{

										$from_date = $currentDate;
										$to_date = $currentDate;

									?>

										<div class="content-wrapper dp-flex align-center space-between">
											<button class="btn btn-secondary" onclick="printTable()">Print Report</button>
											<div></div>
											<form action="" method="post">
												<div class="input--group">
													<label for="name">Name: </label>
													<input type="text" id="name" name="name">
												</div>
												<div class="input--group">
													<label for="service">Service: </label>
													<select name="service" id="service" required>
														<option value="All" selected>All</option>
														<option value="Pick-up">Pick-up</option>
														<option value="Deliver">Deliver</option>
													</select>
												</div>
												<div class="date-input dp-flex">
													<div class="input--group">
														<label for="from_date">From: </label>
														<input type="date" id="from_date" name="from_date" value="<?php echo "$currentDate"; ?>">
													</div>
													<div class="input--group">
														<label for="to_date">To: </label>
														<input type="date" id="to_date" name="to_date" value="<?php echo "$currentDate"; ?>">
													</div>
												</div>
												<input type="submit" name="filter" class="btn btn-primary" value="Filter">
											</form>

										</div>
									    <div class="table-content">
									    	<p><?php echo "Data from $currentDay"; ?></p>
											<table id="reportTable" class="table table-bordered">
												<thead style="background: gray; color: white;">
												<tr>
											      <th scope="col">No.</th>
											      <th scope="col">Customer</th>
											      <th scope="col">Product</th>
											      <th scope="col">Customer Service</th>
											      <th scope="col">Amount</th>
											      <th scope="col">Total Order</th>
											      <th scope="col">Total Price</th>
											      <th scope="col">Date Sold</th>
											    </tr>
												</thead>
												<div class="tbody-content"></div>
												<tbody>

									<?php

										$sql = "SELECT * FROM tbl_customers JOIN tbl_sales ON tbl_customers.cus_id = tbl_sales.cus_id JOIN tbl_products ON tbl_products.product_id = tbl_sales.product_id JOIN tbl_category ON tbl_products.category_id = tbl_category.category_id where date(date_sold) = '$currentDate' ORDER BY date_sold desc";
										$result = mysqli_query($con, $sql);

										if($result){
											$number = 0;
											$total_daily_sales = 0;
											$pick_count = 0;
											$deliver_count = 0;
											while ($row = mysqli_fetch_assoc($result)) {
												++$number;
												$sales_ID = $row['sales_ID'];
												$cus_id = $row['cus_id'];
												$name = $row['name'];
												$address = $row['address'];
												$cp_num = $row['cp_num'];
												$cus_unique_code = $row['cus_unique_code'];
												$product_id = $row['product_id'];
												$customer_service = $row['customer_service'];
												$total_order = $row['total_order'];
												$total_price = $row['total_price'];
												$date_sold = $row['date_sold'];
												$p_name = $row['p_name'];
												$p_price = $row['p_price'];
												$category_description = $row['category_description'];

												if($customer_service == "Pick-up"){
													$pick_count++;
												}
												if($customer_service == "Deliver"){
													$deliver_count++;
												}

												$date = new DateTime($date_sold);
                                    			$formattedDate = $date->format('F j, Y - g:i A');

												$total_daily_sales += $total_price;

									?>
											<tr>
												
													<td style="width: 2rem;"><?php echo "$number"; ?></td>
													<td><?php echo "$name"; ?></td>
													<td><?php echo "$p_name ($category_description)"; ?></td>
													<td><?php echo "$customer_service"; ?></td>
													<td><?php echo "$p_price"; ?></td>
													<td><?php echo "$total_order"; ?></td>
														
													<td>₱ <?php echo number_format($total_price, 2); ?></td>
													<td><?php echo "$formattedDate"; ?></td>
												
											</tr>

									<?php	

											}
										}
									?>

										<div id="orderDetails" class="modal" <?php if(isset($_GET['orderid'])) { echo 'style="display:block;"'; } ?>>
					    					<div class="modal-wrapper">
					    						<div class="modal-content">
												    <span class="close">&times;</span>
												    <div class="container">
												        <h1>QR Code of <br><?php echo $db_name; ?></h1>
												        
												        <center>
												        	<div id="qrcode"></div>
												        </center>
												        <div class="btn-wrapper">
												        	<button id="save-btn" onclick="saveQRCode()">Save QR Code</button>	
												        </div>
												    </div>
												</div>
					    					</div>
										</div>

									<?php

									}

								?>

									</tbody>
								</table>
								<script>
									function printTable() {
								        const table = document.getElementById("reportTable").outerHTML;
								        const newWindow = window.open("", "_blank");
								        newWindow.document.write(`
								            <html>
								                <head>
								                    <title>Print Report</title>
								                    <style>
								                        table {
								                            width: 100%;
								                            border-collapse: collapse;
								                        }
								                        th, td {
								                            border: 1px solid black;
								                            padding: 8px;
								                            text-align: left;
								                        }
								                        th {
								                            background-color: gray;
								                            color: white;
								                        }
								                    </style>
								                </head>
								                <body>
								                    ${table}
								                </body>
								            </html>
								        `);
								        newWindow.document.close();
								        newWindow.print();
								        newWindow.close();
								    }
								</script>
						    </div>
							<hr>
							<div class="section">
								<div class="container">
									<div class="row">
										<div class="col-md-3">
											<div class="sales-wrapper" style="background: blue; color: white; padding: 1rem; border-radius: 5px;">
												<p>Pick-up: <?php echo "$pick_count"; ?></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="sales-wrapper" style="background: blue; color: white; padding: 1rem; border-radius: 5px;">
												<p>Deliver: <?php echo "$deliver_count"; ?></p>
											</div>
										</div>
										<div class="col-md-6">
											<div class="sales-wrapper" style="background: blue; color: white; padding: 1rem; border-radius: 5px;">
												<div class="sales-content dp-flex align-center space-between">
													<div class="label">Total Daily Sales:</div>
													<div class="sale-value">₱ <?php echo number_format($total_daily_sales, 2); ?></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
		    			</div>
		    		</div>
		    	</div>
		    </div>
		</div>	
	</main>

	<script src="../js/qrcode.min.js"></script>
	<script>

		// Get modal element
	    var modal = document.getElementById("orderDetails");

	    // Get the <span> element that closes the modal
	    var span = document.getElementsByClassName("close")[0];

	    // When the user clicks on <span> (x), close the modal
	    span.onclick = function() {
	        modal.style.display = "none";
	    }

	    // When the user clicks anywhere outside of the modal, close it
	    window.onclick = function(event) {
	        if (event.target == modal) {
	            modal.style.display = "none";
	        }
	    }
	    

	    // When the user clicks the button, open the modal 
	    btn.onclick = function() {
	        modal.style.display = "block";
	    }

	    

	    
	</script>

</body>
</html>