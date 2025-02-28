<?php 
	include "admin_connect.php";

	$timezone = new DateTimeZone('Asia/Manila');
	$dateTime = new DateTime(null, $timezone);

	// $dateTime->modify('+3 years');
	// $dateTime->modify('+0 months');
	// $dateTime->modify('+1 days');

	$curDay = $dateTime->format("Y-m-d");


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Gallon</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <style>
    	body {
		    font-family: Arial, sans-serif;
		    background-color: #f4f4f4;
		}

		h1, h2 {
		    color: #333;
		}

		.qr-code-scanner {
		    margin-bottom: 30px;
		}

		.qr-code-scanner input {
		    padding: 10px;
		    width: 200px;
		    margin-right: 10px;
		}

		button {
		    padding: 10px;
		    background-color: #4CAF50;
		    color: white;
		    border: none;
		    cursor: pointer;
		}

		button:hover {
		    background-color: #45a049;
		}

		table {
		    width: 100%;
		    border-collapse: collapse;
		}

		table, th, td {
		    border: 1px solid black;
		}

		th, td {
		    padding: 10px;
		    text-align: left;
		}

		thead {
		    background-color: #333;
		    color: white;
		}
		.returned-gallons h2{
			text-align: center;
			padding: 1rem;
			color: white;
			background-color: blue;
			margin: 0;
		}
		.btn-wrapper{
			display: flex;
			gap: 10px;
			align-items: center;
		}
		.section{
			padding: 0.3rem 0px;
		}
		.top-wrapper{
			display: flex;
			align-items: center;
			justify-content: space-between;
		}
		.input--group{
            margin-right: 0.5rem;
        }
        form .btn{
            padding: 1px 15px;
            background: #0064ff;
            color: white;
            border-color: blue;
        }
        .btn-wrapper{
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-wrapper .btn{
        	padding: 4px 15px;
        }
        input{
            padding: 1px 5px;
            border-radius: 3px;
            border: solid 1px black;
        }
    </style>	
</head>
<body>
	<div class="section">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div class="top-wrapper">
						<form action="" method="post" style="display:flex; align-items: center;">
				            <div class="input--group">
				                <label for="searchInput">Search: </label>
				                <input type="text" id="searchInput" name="searchInput" autocomplete="off">
				            </div>
				            <input type="submit" name="search" class="btn btn-primary" value="Search">
				        </form>
						<form action="" method="post" style="display:flex; align-items: center;">
				            <div class="input--group">
				                <label for="from_date">From: </label>
				                <input type="date" id="from_date" name="from_date">
				            </div>
				            <div class="input--group">
				                <label for="to_date">To: </label>
				                <input type="date" id="to_date" name="to_date">
				            </div>
				            
				            <input type="submit" name="filter" class="btn btn-primary" value="Filter">
				        </form>
				        <div class="btn-wrapper">
							<a href="scan_qr.php"><button class="btn btn-primary">Scan QR Code</button></a>
							<a href="gallon_inventory.php"><button class="btn btn-danger">Go Back</button></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php  

		if(isset($_POST['filter'])){
			$from_date = $_POST['from_date'];
            $to_date = $_POST['to_date'];

            if(empty($from_date)){
                $from_date = $curDay;
            }
            if(empty($to_date)){
                $to_date = $curDay;
            }

            ?>
            	<div class="section">
					<div class="container">
						<div class="row">
							<div class="col-md-12">
								<!-- List of Returned Gallons -->
							    <div class="returned-gallons">
							        <h2>List of Customers Who Returned Gallons</h2>
							        <table border="1">
							            <thead>
							                <tr>
							                    <th>No.</th>
							                    <th>Customer Name</th>
							                    <th>Borrowed Gallon</th>
							                    <th>Date Borrowed</th>
							                    <th>Date Returned</th>
							                </tr>
							            </thead>
							            <tbody id="returnedList">
							                <?php
							                $query = "SELECT * FROM tbl_returnedgallon JOIN tbl_customers ON tbl_returnedgallon.cus_id = tbl_customers.cus_id JOIN tbl_gallon ON tbl_returnedgallon.gallon_id = tbl_gallon.gallon_id WHERE date(date_returned) >= '$from_date' AND date(date_returned) <= '$to_date' ORDER BY date_returned DESC";

							                $result = mysqli_query($con, $query);



							                if (mysqli_num_rows($result) > 0) {
							                	$number = 0;
							                    while ($row = mysqli_fetch_assoc($result)) {
							                    	$number++;

							                    	$name = $row['name'];
							                    	$unique_code = $row['unique_code'];
							                    	$date_borrowed = $row['date_borrowed'];
							                    	$date_returned = $row['date_returned'];

							                    	$date = new DateTime($date_borrowed);
							                        $db_formattedDate = $date->format('F j, Y - g:i A');

							                        $date2 = new DateTime($date_returned);
							                        $dr_formattedDate = $date2->format('F j, Y - g:i A');

							                        echo '<tr>';
							                        echo '<td>' . $number . '</td>';
							                        echo '<td>' . $name . '</td>';
							                        echo '<td>' . $unique_code . '</td>';
							                        echo '<td>' . $db_formattedDate . '</td>';
							                        echo '<td>' . $dr_formattedDate . '</td>';
							                        echo '</tr>';
							                    }
							                } else {
							                    echo '<tr><td colspan="5">No returned gallons yet</td></tr>';
							                }

							                ?>
							            </tbody>
							        </table>
							    </div>
							</div>
						</div>
					</div>
				</div>
            <?php

		}else if (isset($_POST['search'])) {
			$searchInput = $_POST['searchInput'];

			?>
				<div class="section">
					<div class="container">
						<div class="row">
							<div class="col-md-12">
								<!-- List of Returned Gallons -->
							    <div class="returned-gallons">
							        <h2>List of Customers Who Returned Gallons</h2>
							        <table border="1">
							            <thead>
							                <tr>
							                    <th>No.</th>
							                    <th>Customer Name</th>
							                    <th>Borrowed Gallon</th>
							                    <th>Date Borrowed</th>
							                    <th>Date Returned</th>
							                </tr>
							            </thead>
							            <tbody id="returnedList">
							                <?php
							                $query = "SELECT * FROM tbl_returnedgallon JOIN tbl_customers ON tbl_returnedgallon.cus_id = tbl_customers.cus_id JOIN tbl_gallon ON tbl_returnedgallon.gallon_id = tbl_gallon.gallon_id WHERE tbl_customers.name like '%$searchInput%' ORDER BY date_returned DESC";

							                $result = mysqli_query($con, $query);



							                if (mysqli_num_rows($result) > 0) {
							                	$number = 0;
							                    while ($row = mysqli_fetch_assoc($result)) {
							                    	$number++;

							                    	$name = $row['name'];
							                    	$unique_code = $row['unique_code'];
							                    	$date_borrowed = $row['date_borrowed'];
							                    	$date_returned = $row['date_returned'];

							                    	$date = new DateTime($date_borrowed);
							                        $db_formattedDate = $date->format('F j, Y - g:i A');

							                        $date2 = new DateTime($date_returned);
							                        $dr_formattedDate = $date2->format('F j, Y - g:i A');

							                        echo '<tr>';
							                        echo '<td>' . $number . '</td>';
							                        echo '<td>' . $name . '</td>';
							                        echo '<td>' . $unique_code . '</td>';
							                        echo '<td>' . $db_formattedDate . '</td>';
							                        echo '<td>' . $dr_formattedDate . '</td>';
							                        echo '</tr>';
							                    }
							                } else {
							                    echo '<tr><td colspan="5">No returned gallons yet</td></tr>';
							                }

							                ?>
							            </tbody>
							        </table>
							    </div>
							</div>
						</div>
					</div>
				</div>
			<?php


		} else {
				
			?>
				<div class="section">
					<div class="container">
						<div class="row">
							<div class="col-md-12">
								<!-- List of Returned Gallons -->
							    <div class="returned-gallons">
							        <h2>List of Customers Who Returned Gallons</h2>
							        <table border="1">
							            <thead>
							                <tr>
							                    <th>No.</th>
							                    <th>Customer Name</th>
							                    <th>Borrowed Gallon</th>
							                    <th>Date Borrowed</th>
							                    <th>Date Returned</th>
							                </tr>
							            </thead>
							            <tbody id="returnedList">
							                <?php
							                $query = "SELECT * FROM tbl_returnedgallon JOIN tbl_customers ON tbl_returnedgallon.cus_id = tbl_customers.cus_id JOIN tbl_gallon ON tbl_returnedgallon.gallon_id = tbl_gallon.gallon_id WHERE date(date_returned) = '$curDay' ORDER BY date_returned DESC";

							                $result = mysqli_query($con, $query);



							                if (mysqli_num_rows($result) > 0) {
							                	$number = 0;
							                    while ($row = mysqli_fetch_assoc($result)) {
							                    	$number++;

							                    	$name = $row['name'];
							                    	$unique_code = $row['unique_code'];
							                    	$date_borrowed = $row['date_borrowed'];
							                    	$date_returned = $row['date_returned'];

							                    	$date = new DateTime($date_borrowed);
							                        $db_formattedDate = $date->format('F j, Y - g:i A');

							                        $date2 = new DateTime($date_returned);
							                        $dr_formattedDate = $date2->format('F j, Y - g:i A');

							                        echo '<tr>';
							                        echo '<td>' . $number . '</td>';
							                        echo '<td>' . $name . '</td>';
							                        echo '<td>' . $unique_code . '</td>';
							                        echo '<td>' . $db_formattedDate . '</td>';
							                        echo '<td>' . $dr_formattedDate . '</td>';
							                        echo '</tr>';
							                    }
							                } else {
							                    echo '<tr><td colspan="5">No returned gallons yet</td></tr>';
							                }

							                ?>
							            </tbody>
							        </table>
							    </div>
							</div>
						</div>
					</div>
				</div>
			<?php

		}
		

	?>

</body>
</html>
