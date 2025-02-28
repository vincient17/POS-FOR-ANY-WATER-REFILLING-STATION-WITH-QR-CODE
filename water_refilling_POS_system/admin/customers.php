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
	// $dateTime->modify('-5 days');

	$curDay = $dateTime->format("Y-m-d");
	// echo "$curDay";
	$currentDay = $dateTime->format("M d, Y");

	$current_year = $dateTime->format("Y");
	$current_month = $dateTime->format("m");
	$current_day = $dateTime->format("d");
	$currentDate = "$current_year-$current_month-$current_day";

	if (isset($_SESSION['success_message'])) {
	    echo '<div id="success-message" class="alert alert-success">'.$_SESSION['success_message'].'</div>';
	    unset($_SESSION['success_message']);
	}
	if (isset($_SESSION['error_message'])) {
	    echo '<div id="success-message" class="alert alert-danger">'.$_SESSION['error_message'].'</div>';
	    unset($_SESSION['error_message']);
	}
	if (isset($_SESSION['exist_message'])) {
	    echo '<div id="success-message" class="alert alert-success" style = "background: red;">'.$_SESSION['exist_message'].'</div>';
	    unset($_SESSION['exist_message']);
	}

	function random_num($length){
		$text = "";
		if($length < 5){
			$length = 5;
		}

		$len = rand(10, $length);

		for ($i=0; $i < $len; $i++) { 
			// code...

			$text .= rand(0, 9);
		}
		return $text;
	}

	if (isset($_GET['editid'])) {
	    $edit_id = mysqli_real_escape_string($con, $_GET['editid']);
	    
	    // Fetch customer data from the database
	    $query = "SELECT * FROM tbl_customers WHERE cus_id = '$edit_id' LIMIT 1";
	    $result = mysqli_query($con, $query);

	    if ($result && mysqli_num_rows($result) > 0) {
	        $customer = mysqli_fetch_assoc($result);

	        // Store customer data in variables
	        $name_in_database = $customer['name'];
	        $address_in_database = $customer['address'];
	        $cp_num_in_database = $customer['cp_num'];
	    } else {
	        echo "Customer not found.";
	    }
	}
	if (isset($_GET['buyid'])) {
	    $buy_id = mysqli_real_escape_string($con, $_GET['buyid']);
	    
	    // Fetch customer data from the database
	    $query = "SELECT * FROM tbl_customers WHERE cus_id = '$buy_id' LIMIT 1";
	    $result = mysqli_query($con, $query);

	    if ($result && mysqli_num_rows($result) > 0) {
	        $customer = mysqli_fetch_assoc($result);

	        // Store customer data in variables
	        $name_in_database = $customer['name'];
	        $address_in_database = $customer['address'];
	        $cp_num_in_database = $customer['cp_num'];
	    } else {
	        echo "Customer not found.";
	    }
	}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo "Customer"; ?></title>

	<link rel="stylesheet" href="../css/bootstrap.css">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/all.min.css">

	<style>
		.wrs_logo{
        	background-image: url(../uploads/<?php echo "$WRS_logo"; ?>) !important;
        }
		table td{
			/*background: #9d9d9dba;
			color: white;*/
			background: white;
			color: black;
		}
		table tr td{ 
			border-bottom-color: black !important;
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
            animation: go_down 6s;
        }
        .alert-danger{
        	background-color: red;
        }

        @keyframes go_down {
        	0%{
        		top: -85px;
        	}
        	25%{
        		top: 7px;
        	}
        	75%{
        		top: 7px;
        	}
        	100%{
        		top: -85px;
        	}
        }
        a{
        	text-decoration: none;
        }
		.dashboard-wrapper{
			height: 80vh;
		}
		.dashboard-content{
			width: 40rem;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		form{
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
		form button{
		    background: black;
		    padding: 5px 13px;
		    transform: translateX(-2px);
		    border-radius: 3px;
		}
		form button i{
		    font-size: 20px;
		    color: white;
		}
		.sect1{
			margin-top: 2rem;
		}
		/* Modal container */
        .modal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 99; /* On top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            background-color: rgba(0, 0, 0, 0.5); /* Black with opacity */
        }

        /* Modal content box */
        .modal-content {
            background-color: white;
            padding: 20px;
            border: 1px solid #888;
            width: 40%; /* Width adjustment */
        }

        /* Close button */
        .close, .edit_close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            width: fit-content;
        }

        .close:hover,
        .close:focus,
        .edit_close:hover,
        .edit_close:focus {
            color: black;
            cursor: pointer;
        }

        /* Simple form styling */
        .modal form {
            display: flex;
            flex-direction: column;
        }

        .modal-form input[type="text"], .modal-form input[type="email"] {
            padding: 0 10px;
            margin: 10px 0;
            width: 100%;
            box-sizing: border-box;
        }

        .modal-form input[type="submit"] {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .modal-form input[type="submit"]:hover {
            background-color: #45a049;
        }
        .submit-btn{
        	margin-top: 1.3rem;
        }
        .label{
        	margin: 0;
        }
        .input-group{
        	margin: 1rem 0px;
        }
        .modal-wrapper{
        	width: 100%;
        	height: 100%;
        	display: flex;
        	align-items: center;
        	justify-content: center;
        }
        .operation-wrapper a, .delete-wrapper{
        	position: relative;
        }
        .btn-buy:hover::before, .btn-edit:hover::before, .btn-delete:hover::before{
        	display: block;
        }
        .btn-buy{
        	background: limegreen;
        }
        .btn-edit{
        	background: blue;
        }
        .btn-delete{
        	background: red;
        }
        .btn-buy::before{
        	content: 'Buy';
		    position: absolute;
		    top: -20px;
		    left: 0;
		    font-size: 12px;
		    background: white;
		    color: black;
		    width: 100%;
		    border-radius: 2px;
		    display: none;

        }
        .btn-edit::before{
        	content: 'Edit';
        	position: absolute;
        	top: -20px;
		    left: 0;
		    font-size: 12px;
		    background: white;
		    color: black;
		    width: 100%;
		    border-radius: 2px;
		    display: none;
        }
        .btn-delete::before{
        	content: 'Delete';
        	position: absolute;
        	top: -20px;
		    left: 0;
		    font-size: 12px;
		    background: white;
		    color: black;
		    width: 100%;
		    border-radius: 2px;
		    display: none;
        }
		h2, h3 {
		    color: #2c3e50;
		}

		/* Cart Section */
		#cart-container {
		    margin-top: 1rem;
		    padding: 1rem;
		    background-color: #f9f9f9;
		    border-radius: 8px;
		    overflow-y: auto;
		    max-height: 200px;
		    transition: max-height 0.3s ease-out;
		}

		/* Individual Cart Item */
		.cart-row {
		    padding: 0.8rem;
		    background-color: #fff;
		    border-radius: 8px;
		    margin-bottom: 1rem;
		    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
		    display: flex;
		    justify-content: space-between;
		    align-items: center;
		}

		.cart-row p {
		    font-size: 0.9rem;
		    margin: 0;
		}

		.remove-from-cart {
		    background-color: #e74c3c;
		    color: white;
		    border: none;
		    border-radius: 4px;
		    padding: 0.3rem 0.8rem;
		    cursor: pointer;
		    font-size: 0.8rem;
		    transition: background-color 0.3s;
		}

		.remove-from-cart:hover {
		    background-color: #c0392b;
		}

		/* Button Styles */
		.purchase-modal .btn {
		    padding: 0.6rem 1.2rem;
		    background-color: #3498db;
		    color: white;
		    border: none;
		    border-radius: 4px;
		    cursor: pointer;
		    transition: background-color 0.3s;
		}

		.purchase-modal .btn:hover {
		    background-color: #2980b9;
		}

		.submit-btn {
		    background-color: #2ecc71;
		}

		.submit-btn:hover {
		    background-color: #27ae60;
		}

		.return-btn {
		    display: inline-block;
		    text-decoration: none;
		    color: #e74c3c;
		    margin-top: 1rem;
		}

		.return-btn:hover {
		    text-decoration: underline;
		}

		/* Input Fields */
		.input-group {
		    margin-bottom: 1rem;
		}

		.label {
		    font-size: 1rem;
		    font-weight: bold;
		    margin-bottom: 0.5rem;
		}

		input, select {
		    width: 100%;
		    padding: 0.8rem;
		    border-radius: 4px;
		    border: 1px solid #ddd;
		    font-size: 1rem;
		}

		/* Price Group */
		.price-group {
		    margin-top: 1rem;
		}

		.total-text {
		    font-size: 1.2rem;
		    font-weight: bold;
		}

		#cart_total {
		    font-size: 1.2rem;
		    font-weight: bold;
		    text-align: right;
		    background: #ecf0f1;
		}
		a:hover{
			text-decoration: none !important;
		}
		/* Responsive Design */
		@media (max-width: 600px) {
		    .modal-wrapper {
		        padding: 1rem;
		    }

		    .cart-row {
		        flex-direction: column;
		        align-items: flex-start;
		    }
		}
		.wholesale-checkbox {
		    display: flex;
		    align-items: center;
		    gap: 5px;
		    margin-left: 0.5rem !important;
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
		        <li class="active">
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


		    <div class="section sect1">
		    	<div class="container">
		    		<div class="row">
		    			<div class="col-md-12">
		    				<div class="main-btn-wrapper dp-flex space-between" style="margin-bottom: 0.3rem;">
		    					<div class="btn-content dp-flex" style="justify-content: flex-end;">
		    						<button id="openModalBtn" class="btn btn-primary">Add Customer</button>
		    					</div>
		    					<form class="dp-flex" method="post" style="background: lightgray;">
							        <input type="text" name="searchInput" id="searchInput" placeholder="Search" autocomplete="off">
							        <button type="submit" name="submit"><i class="fa-solid fa-search"></i></button>
							    </form>
		    				</div>
		    				<div id="addCustomerModal" class="modal">
		    					<div class="modal-wrapper">
		    						<div class="modal-content">
									    <span class="close">&times;</span>
									    <center><h2>Add New Customer</h2></center>
									    <form action="add_customer.php" method="POST" style="width: 100%;">
									    	<div class="input-group">
									    		<label class="label" for="name">Name:</label>
									        	<input type="text" id="name" name="name" autocomplete="off" required>
									    	</div>
									    	<div class="input-group">
									    		<label class="label" for="address">Address:</label>
									        	<input type="text" id="address" name="address" autocomplete="off">
									    	</div>
									    	<div class="input-group">
								                <label for="cp_num" style = "width: 100%;">Phone Number</label>
								                <div style="display: flex; align-items: center; width: 100%;">
								                    <span style="padding: 5px 10px; background-color: #e9ecef; border: 1px solid #ccc; border-right: none;">63</span>
								                    <input type="text" id="cp_num" name="cp_num" required pattern="[9][0-9]{9}" placeholder="9123456789" title="Phone number must start with 9 and have 10 digits (e.g., 9123456789)" maxlength="10" style="flex: 1; border: 1px solid #ccc; border-left: none; padding: 5px;">
								                </div>
								                <small class="validation-message" style="color: red; display: none;">Invalid phone number format.</small>
								            </div>

								            <script>
								                const phoneNumberInput = document.getElementById('cp_num');
								                const validationMessage = document.querySelector('.validation-message');

								                phoneNumberInput.addEventListener('input', () => {
								                    const isValid = phoneNumberInput.value.match(/^[9][0-9]{9}$/);

								                    // Show/hide validation message
								                    if (!isValid && phoneNumberInput.value.length > 0) {
								                        validationMessage.style.display = 'block';
								                    } else {
								                        validationMessage.style.display = 'none';
								                    }
								                });
								            </script>

									        <input class="btn btn-primary submit-btn" type="submit" value="Add Customer" name="submit">
									    </form>
									</div>
		    					</div>
							</div>
							<!-- Edit Customer Modal -->
							<div id="editCustomerModal" class="modal" <?php if(isset($_GET['editid'])) { echo 'style="display:block;"'; } ?>>
							    <div class="modal-wrapper">
							        <div class="modal-content">
							            <a href="customers.php" class="edit_close">&times;</a>
							            <center><h2>Edit Customer</h2></center>
							            <form action="edit_customer.php" method="POST" style="width: 100%;">
							                <input type="hidden" id="edit_cus_id" name="cus_id" value="<?php echo $edit_id; ?>">

							                <div class="input-group">
							                    <label class="label" for="name">Name:</label>
							                    <input type="text" id="name" name="name" value="<?php echo $name_in_database; ?>" autocomplete="off" required>
							                </div>
							                <div class="input-group">
							                    <label class="label" for="address">Address:</label>
							                    <input type="text" id="address" name="address" value="<?php echo $address_in_database; ?>" autocomplete="off" required>
							                </div>
							                <div class="input-group">
								                <label for="cp_num" style = "width: 100%;">Phone Number</label>
								                <div style="display: flex; align-items: center; width: 100%;">
								                    <span style="padding: 5px 10px; background-color: #e9ecef; border: 1px solid #ccc; border-right: none;">63</span>
								                    <input type="text" id="cp_number" name="cp_num"  value="<?php echo $cp_num_in_database; ?>" required pattern="[9][0-9]{9}" placeholder="9123456789" title="Phone number must start with 9 and have 10 digits (e.g., 9123456789)" maxlength="10" style="flex: 1; border: 1px solid #ccc; border-left: none; padding: 5px;">
								                </div>
								                <small class="edit-validation-message" style="color: red; display: none;">Invalid phone number format.</small>
								            </div>

								            <script>
								                const cpNumberInput = document.getElementById('cp_number');
								                const editValidationMessage = document.querySelector('.edit-validation-message');

								                cpNumberInput.addEventListener('input', () => {
								                    const isValid = cpNumberInput.value.match(/^[9][0-9]{9}$/);

								                    // Show/hide validation message
								                    if (!isValid && cpNumberInput.value.length > 0) {
								                        editValidationMessage.style.display = 'block';
								                    } else {
								                        editValidationMessage.style.display = 'none';
								                    }
								                });
								            </script>

							                <input class="btn btn-primary submit-btn" type="submit" value="Update Customer">
							            </form>
							        </div>
							    </div>
							</div>
							<div id="editCustomerModal" class="modal purchase-modal" <?php if (isset($_GET['buyid'])) { echo 'style="display:block;"'; } ?>>
							    <div class="modal-wrapper" style="margin-top: 14rem;">
							        <div class="modal-content">
							            <a href="customers.php" class="edit_close">&times;</a>
							            <center><h2>Purchase Water</h2></center>
							            <form id="orderForm" action="process_cart.php" method="POST" style="width: 100%; position: relative;">
							                <input type="hidden" id="purchase_cus_id" name="cus_id" value="<?php echo $buy_id; ?>">

							                <!-- Customer Name -->
							                <div class="input-group">
							                    <label class="label" for="name">Name:</label>
							                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name_in_database); ?>" autocomplete="off" required disabled style="background: white; color: black;">
							                </div>

							                <!-- Customer Action -->
							                <div class="input-group">
							                    <label class="label" for="customer_action">Customer Action:</label>
							                    <select name="customer_action" id="customer_action">
							                        <option value="Pick-up">Pick-up</option>
							                        <option value="Deliver">Deliver</option>
							                    </select>
							                </div>

							                <!-- Product Selection -->
							                <div id="product-selection">
							                    <h3>Add Products</h3>
							                    <div class="input-group">
							                        <label class="label" for="water_product">Product:</label>
							                        <select id="water_product">
							                            <option value="" disabled selected>Select Product</option>
							                            <?php 
							                                $product_sql = "
							                                    SELECT *
							                                    FROM 
							                                        tbl_products 
							                                    JOIN 
							                                        tbl_category 
							                                    ON 
							                                        tbl_products.category_id = tbl_category.category_id";
							                                $product_result = mysqli_query($con, $product_sql);

							                                while ($row = mysqli_fetch_assoc($product_result)) {
							                                    $stock = ($row['category_description'] === 'Water') ? 'Uncountable' : $row['quantity'];
							                                    echo "
							                                        <option 
							                                            value='{$row['product_id']}' 
							                                            data-price='{$row['p_price']}' 
							                                            data-stock='{$row['quantity']}' 
							                                            data-category='{$row['category_description']}'>
							                                            {$row['p_name']} ({$row['category_description']}) - Stock: {$stock}
							                                        </option>";
							                                }
							                            ?>
							                        </select>
							                    </div>
							                    <div class="input-group">
												    <label class="label" for="quantity">Quantity:</label>
												    <input type="number" id="quantity" value="1" min="1" required>

												    <div class="wholesale-checkbox">
												        <input type="checkbox" id="wholesaleCheckbox">
												        <label for="wholesaleCheckbox">Wholesale</label>
												    </div>
												</div>

							                    <div class="input-group">
							                        <label class="label" for="discount">Discount (%):</label>
							                        <input type="number" id="discount" value="0" min="0" max="100">
							                    </div>
							                    <button type="button" id="add-to-cart" class="btn btn-success">Add to Cart</button>
							                </div>

							                <!-- Cart Section -->
							                <hr>
							                <h3>Your Cart</h3>
							                <div id="cart-container" style="overflow-y: auto; max-height: 200px;"></div>

							                <!-- Total Price -->
							                <div class="price-group dp-flex space-between align-center">
							                    <p class="total-text" style="margin: 0;">Total: </p>
							                    <input type="text" id="cart_total" name="cart_total" value="₱0.00" readonly style="width: 150px;">
							                </div>
							                <input type="hidden" id="cart_data" name="cart_items">

							                <!-- Submit and Return Buttons -->
							                <input class="btn btn-primary submit-btn" type="submit" value="Purchase" name="submit">
							                <a href="customers.php" class="return-btn">Return</a>
							            </form>
							        </div>
							    </div>
							</div>

							<script>
								document.getElementById('wholesaleCheckbox').addEventListener('change', function () {
								    const quantityInput = document.getElementById('quantity');

								    if (this.checked) {
								        quantityInput.min = 15;
								        quantityInput.value = 15;
								    } else {
								        quantityInput.min = 1;
								        quantityInput.value = 1;
								    }
								});

							    document.addEventListener('DOMContentLoaded', () => {
							        const cartContainer = document.getElementById('cart-container');
							        const addToCartBtn = document.getElementById('add-to-cart');
							        const cartTotal = document.getElementById('cart_total');
							        const orderForm = document.getElementById('orderForm');
							        const productSelect = document.getElementById('water_product');
							        const quantityInput = document.getElementById('quantity');
							        const discountInput = document.getElementById('discount');
							        let cart = [];
							        let adjustedStock = {};

							        productSelect.addEventListener('change', () => {
							            const selectedOption = productSelect.options[productSelect.selectedIndex];
							            const category = selectedOption.getAttribute('data-category');
							            const stock = parseInt(selectedOption.getAttribute('data-stock'));

							            // Set quantity input constraints
							            if (category === 'Water') {
							                quantityInput.removeAttribute('max'); // No max for "Water"
							            } else {
							                quantityInput.max = stock; // Set max for other categories
							            }
							        });

							        function updateCart() {
							            cartContainer.innerHTML = '';
							            let total = 0;

							            cart.forEach((item, index) => {
							                const { productName, price, quantity, discount, subtotal } = item;

							                const cartRow = document.createElement('div');
							                cartRow.classList.add('cart-row');
							                cartRow.innerHTML = `
							                    <p>${productName} - ₱${price} x ${quantity} (Discount: ${discount}%)</p>
							                    <p>Subtotal: ₱${subtotal.toFixed(2)}</p>
							                    <button class="remove-from-cart" data-index="${index}">Remove</button>
							                `;
							                cartContainer.appendChild(cartRow);

							                total += subtotal;
							            });

							            cartTotal.value = `₱${total.toFixed(2)}`;
							        }

							        productSelect.addEventListener('change', () => {
							            const stock = parseInt(productSelect.options[productSelect.selectedIndex].getAttribute('data-stock'));
							            const productId = productSelect.value;

							            adjustedStock[productId] = adjustedStock[productId] ?? stock;
							            quantityInput.max = adjustedStock[productId]; // Set max quantity in input
							        });

							        addToCartBtn.addEventListener('click', () => {
							            const productId = productSelect.value;
							            const productName = productSelect.options[productSelect.selectedIndex].text;
							            const price = parseFloat(productSelect.options[productSelect.selectedIndex].getAttribute('data-price'));
							            const stock = parseInt(productSelect.options[productSelect.selectedIndex].getAttribute('data-stock'));
							            const quantity = parseInt(quantityInput.value);
							            const discount = parseFloat(discountInput.value);

							            if (!productId || isNaN(price) || quantity <= 0) {
							                alert('Please select a valid product and quantity.');
							                return;
							            }

							            if (quantity > adjustedStock[productId]) {
							                alert(`The quantity exceeds available stock (${adjustedStock[productId]}).`);
							                return;
							            }

							            adjustedStock[productId] -= quantity; // Decrement the stock
							            const subtotal = (price * quantity) * ((100 - discount) / 100);
							            cart.push({ productId, productName, price, quantity, discount, subtotal });
							            updateCart();

							            // Reset fields
							            productSelect.value = "";
							            quantityInput.value = "1";
							            discountInput.value = "0";
							            document.getElementById('wholesaleCheckbox').checked = false;
							            quantityInput.min = 1;
							        });

							        cartContainer.addEventListener('click', (e) => {
							            if (e.target.classList.contains('remove-from-cart')) {
							                const index = e.target.getAttribute('data-index');
							                const removedItem = cart.splice(index, 1)[0];
							                adjustedStock[removedItem.productId] += removedItem.quantity; // Restore stock
							                updateCart();
							            }
							        });

							        orderForm.addEventListener('submit', (e) => {
							            if (cart.length === 0) {
							                e.preventDefault();
							                alert('Please add items to the cart before submitting.');
							            } else {
							                // Update the hidden input with the cart items before submitting
							                const cartDataInput = document.getElementById('cart_data');
							                cartDataInput.value = JSON.stringify(cart); // Ensure cart is serialized before form submission
							            }
							        });
							    });
							</script>







		    				<div class="table-wrapper">
		    					<div class="table-content">
		    						<table class="table table-bordered">
		    							<thead class="thead-dark">
		    								<tr>
		    									<th scope="col" style="width: 4rem;">No.</th>
		    									<th scope="col">Name</th>
		    									<th scope="col">Address</th>
		    									<th scope="col">Cellphone no.:</th>
		    									<th scope="col" style="width: 14rem;">Operation</th>
		    								</tr>
		    							</thead>
				<tbody>

					<?php 

						if(isset($_POST['submit'])){
							$searchInput = $_POST['searchInput'];

							$sql = "SELECT * FROM `tbl_customers` where name like '%$searchInput%' or address like '%$searchInput%' or cp_num like '%$searchInput%' order by cus_id ASC";
							$result = mysqli_query($con, $sql);
							if($result && mysqli_num_rows($result)>0){
								$number = 0;
								while ($row = mysqli_fetch_assoc($result)) {
									++$number;
									$cus_id = $row['cus_id'];
									$name = $row['name'];
									$address = $row['address'];
									$cp_num = "0".$row['cp_num'];

									if($name == "Guest"){
										$cp_num = $row['cp_num'];
									}
									
						?>
								<tr>
									<td><?php echo "$number"; ?></td>
									<?php 	
						      			echo "<td>$name</td>";
						      		 ?>
									<td><?php echo "$address"; ?></td>
									<td><?php echo "$cp_num"; ?></td>
									<td>
										<div class="operation-wrapper dp-flex space-around" style="font-size: 0.5rem; gap: 10px;">
											<?php 
												echo '<a href="customers.php?buyid='.$cus_id.'">
													<button class="btn btn-success btn-buy">
													    <i class="fas fa-money-bill-wave"></i>
													</button>
													</a>';

												echo '<a href="customers.php?editid='.$cus_id.'">
												        <button class="btn btn-primary btn-edit">
												            <i class="fas fa-edit"></i>
												        </button>
												      </a>';


												$delete_sql = "SELECT DISTINCT cus_id FROM `tbl_sales` WHERE cus_id IN (SELECT cus_id FROM tbl_customers) AND cus_id = $cus_id";
												$delete_result = mysqli_query($con, $delete_sql);
												if($delete_result && mysqli_num_rows($delete_result) > 0){
													echo '<div class="delete-wrapper"><button disabled class="btn btn-danger btn-delete b_disabled" onclick="confirmDeleteCustomer('.$cus_id.')"><i class="fas fa-trash"></i></button></div>';
												}else{
													echo '<div class="delete-wrapper"><button class="btn btn-danger btn-delete" onclick="confirmDeleteCustomer('.$cus_id.')"><i class="fas fa-trash"></i></button></div>';
												}

											 ?>
										</div>
									</td>
								</tr>
						<?php
								}


								}else{
									?>
										<tr>
											<td colspan='6'><?php echo "Customer not found!"; ?></td>
										</tr>	
									<?php

								}
						}else{
					?>


					<?php  

						$sql = "SELECT * FROM `tbl_customers` order by cus_id ASC";
						$result = mysqli_query($con, $sql);
						if($result && mysqli_num_rows($result)>0){
							$number = 0;
							while ($row = mysqli_fetch_assoc($result)) {
								++$number;
								$cus_id = $row['cus_id'];
								$name = $row['name'];
								$address = $row['address'];
								$cp_num = "0".$row['cp_num'];

								if($name == "Guest"){
									$cp_num = $row['cp_num'];
								}
					?>
								<tr>
									<td><?php echo "$number"; ?></td>
									<?php 	
						      			echo "<td>$name</td>";
						      		 ?>
									<td><?php echo "$address"; ?></td>
									<td><?php echo "$cp_num"; ?></td>
									<td>
										<div class="operation-wrapper dp-flex space-around" style="font-size: 0.5rem; gap: 10px	;">
											<?php 
												echo '<a href="customers.php?buyid='.$cus_id.'">
													<button class="btn btn-success btn-buy">
													    <i class="fas fa-money-bill-wave"></i>
													</button>
													</a>';

												echo '<a href="customers.php?editid='.$cus_id.'">
													<button class="btn btn-primary btn-edit">
													    <i class="fas fa-edit"></i>
													</button>
													</a>';

												$delete_sql = "SELECT DISTINCT cus_id FROM `tbl_sales` WHERE cus_id IN (SELECT cus_id FROM tbl_customers) AND cus_id = $cus_id";
												$delete_result = mysqli_query($con, $delete_sql);
												if($delete_result && mysqli_num_rows($delete_result) > 0){
													echo '<div class="delete-wrapper"><button disabled class="btn btn-danger btn-delete b_disabled" onclick="confirmDeleteCustomer('.$cus_id.')"><i class="fas fa-trash"></i></button></div>';
												}else{
													echo '<div class="delete-wrapper"><button class="btn btn-danger btn-delete" onclick="confirmDeleteCustomer('.$cus_id.')"><i class="fas fa-trash"></i></button></div>';
												}

												

											 ?>
										</div>
									</td>
								</tr>
					<?php
							}
						}else{
							?>
								<tr>
									<td colspan='6'><?php echo "No Customer Recorded!"; ?></td>
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
		    		</div>
		    	</div>
		    </div>
		</div>	
	</main>

	<script>
	    // Get modal element
	    var modal = document.getElementById("addCustomerModal");

	    // Get button that opens the modal
	    var btn = document.getElementById("openModalBtn");

	    // Get the <span> element that closes the modal
	    var span = document.getElementsByClassName("close")[0];

	    // When the user clicks the button, open the modal 
	    btn.onclick = function() {
	        modal.style.display = "block";
	    }

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

	    // Edit Modal
	    // Get the modal element
		var editModal = document.getElementById("editCustomerModal");

		// Get the <span> element that closes the modal
		var edit_span = document.getElementsByClassName("edit_close")[0];

		// Close the modal when the user clicks on the span (x)
		edit_span.onclick = function() {
		    editModal.style.display = "none";
		};

		// Close the modal when the user clicks anywhere outside of it
		window.onclick = function(event) {
		    if (event.target == editModal) {
		        editModal.style.display = "none";
		    }
		};

	    function confirmDeleteCustomer(itemId) {
	    	var result = confirm('Are you sure you want to remove this customer?');

	    	if(result){
	    		window.location.href = 'delete_customer.php?deleteid=' + itemId;
	    	}else{
	    		alert('Delete canceled');
	    	}

	    }

	   	setTimeout(function() {
            var successMessage = document.getElementById("success-message");
            if (successMessage) {
                successMessage.style.display = "none";
            }
        }, 6000);

	</script>

</body>
</html>