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

	if (isset($_SESSION['success_message'])) {
	    echo '<div id="success-message" class="alert alert-success">'.$_SESSION['success_message'].'</div>';
	    unset($_SESSION['success_message']);
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
	    $query = "SELECT * FROM tbl_products JOIN tbl_category ON tbl_products.category_id = tbl_category.category_id WHERE product_id = '$edit_id' LIMIT 1";
	    $result = mysqli_query($con, $query);

	    if ($result && mysqli_num_rows($result) > 0) {
	        $customer = mysqli_fetch_assoc($result);

	        // Store customer data in variables
	        $p_name_in_database = $customer['p_name'];
	        $p_price_in_database = $customer['p_price'];
	        $quantity_in_database = $customer['quantity'];
	        $category_id_in_database = $customer['category_id'];
	        $category_description_in_database = $customer['category_description'];
		    } else {
	        echo "Product not found.";
	    }
	}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo "Products"; ?></title>

	<link rel="stylesheet" href="../css/bootstrap.css">
	<link rel="stylesheet" href="../css/style.css">
	<link rel="stylesheet" href="../css/all.min.css">

	<style>
		.wrs_logo{
        	background-image: url(../uploads/<?php echo "$WRS_logo"; ?>) !important;
        }
		td{
			/*background: #9d9d9dba;
			color: white;*/
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
            animation: go_down 6s;
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
        .close, .close2, .edit_close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            width: fit-content;
        }

        .close:hover,
        .close:focus,
        .close2:hover,
        .close2:focus,
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
        .view-category{
        	cursor: pointer;
			text-decoration: none;
		}
		#category_content {
            display: none;
            position: fixed;
            width: 700px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-height: 80vh; /* Set a maximum height */
            overflow: auto; /* Enable vertical scrolling if needed */
            padding: 20px;
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            text-align: justify;
            border-radius: 10px;
            z-index: 9999;
        }
        .btn-each{
        	transform: scale(0.8);
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
		        <li class="active">
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
		    					<div class="btn-content dp-flex" style="justify-content: flex-end; gap: 10px;">
		    						<button id="openModalBtn" class="btn btn-primary">Add Product</button>
		    						<button id="openModalBtn2" class="btn btn-primary">Add Category</button>
		    					</div>
		    					<form class="dp-flex" method="post" style="background: lightgray;">
							        <input type="text" name="searchInput" id="searchInput" placeholder="Search" autocomplete="off">
							        <button type="submit" name="submit"><i class="fa-solid fa-search"></i></button>
							    </form>
		    				</div>
		    				<div id="addProductModal" class="modal">
		    					<div class="modal-wrapper">
		    						<div class="modal-content">
									    <span class="close">&times;</span>
									    <center><h2>Add Product</h2></center>
									    <form action="add_product.php" method="POST" style="width: 100%;">
									        <div class="input-group">
									            <label class="label" for="p_name">Product Name:</label>
									            <input type="text" id="p_name" name="p_name" autocomplete="off" required>
									        </div>
									        <div class="input-group">
									            <label class="label" for="p_price">Price:</label>
									            <input type="number" id="p_price" name="p_price" autocomplete="off" required>
									        </div>
									        <div class="input-group">
									            <label class="label" for="product_category">Category:</label>
									            <select name="product_category" id="product_category" required>
									                <option value="" disabled selected>Select Product Category</option>
									                <?php 
									                    $option_sql = "SELECT * FROM `tbl_category`";
									                    $option_result = mysqli_query($con, $option_sql);

									                    if ($option_result) {
									                        while ($row = mysqli_fetch_assoc($option_result)) {
									                            $category_id = $row['category_id'];
									                            $category_description = $row['category_description'];
									                            echo '<option value="'.$category_id.'" data-description="'.$category_description.'">'.$category_description.'</option>';
									                        }
									                    }
									                ?>
									            </select>
									        </div>
									        <div class="input-group" id="quantity-input" style="display: none;">
									            <label class="label" for="quantity">Quantity:</label>
									            <input type="number" id="quantity" name="quantity" min="0" autocomplete="off">
									        </div>
									        <input class="btn btn-primary submit-btn" type="submit" value="Add Product" name="submit">
									    </form>

									    <script>
									        // JavaScript to show/hide the quantity input
									        const productCategorySelect = document.getElementById('product_category');
									        const quantityInputGroup = document.getElementById('quantity-input');

									        productCategorySelect.addEventListener('change', function () {
									            // Get the selected option's data-description attribute
									            const selectedOption = productCategorySelect.options[productCategorySelect.selectedIndex];
									            const categoryDescription = selectedOption.getAttribute('data-description');

									            // Show or hide the quantity input based on the selected category
									            if (categoryDescription !== "Water") {
									                quantityInputGroup.style.display = 'block';
									            } else {
									                quantityInputGroup.style.display = 'none';
									            }
									        });
									    </script>
									</div>
		    					</div>
							</div>
							<div id="addCategoryModal" class="modal">
		    					<div class="modal-wrapper">
		    						<div class="modal-content">
									    <span class="close2">&times;</span>
									    <center><h2>Add Category</h2></center>
									    <form action="add_category.php" method="POST" style="width: 100%;">
									    	<div class="input-group">
									    		<label class="label" for="category_description">Category Description:</label>
									        	<input type="text" id="category_description" name="category_description" autocomplete="off" required>
									    	</div>
									    	<a onclick="displayCategoryContent()" class="view-category" style="text-decoration: none;">View Category</a>
									        <input class="btn btn-primary submit-btn" type="submit" value="Add Category" name="add_category">
									    </form>
									</div>
		    					</div>
							</div>
							<div id="category_content">
								<div>
									<h2>Recorded Category</h2>
								</div>

								<?php 

									$category_sql = "SELECT * FROM `tbl_category`";
									$category_result = mysqli_query($con, $category_sql);
									$cat_number = 0;
									if($category_result){
										while($row = mysqli_fetch_assoc($category_result)){

											$category_id = $row['category_id'];
											$category_description = $row['category_description'];
											$existed = 0;

												
								?>

												<div class="district-wrapper" style="display: flex; justify-content:space-between; align-items: center;">
													<div class="district-name">
														<?php echo ++$cat_number.". $category_description";?>
													</div>
													<div class="operation-btn" style="display:flex;">
														<div class="btn-each">
															<?php echo '<a href="edit_category.php?editid='.$category_id.' " class="text-light"><button class="btn btn-primary edit-btn" style="background: blue;"><i class="fas fa-edit"></i> Edit</button></a>'; ?>
														</div>


														<?php  

															$btn_sql = "SELECT tbl_category.category_id FROM tbl_category WHERE category_id in (SELECT category_id FROM tbl_products)";
															$btn_result = mysqli_query($con, $btn_sql);
															if($btn_result){
																while ($row = mysqli_fetch_assoc($btn_result)) {
																	$db_category_id = $row['category_id'];
																	if($db_category_id == $category_id){
																		$existed = 1;
																		break;
																	}
																}
																if($existed != 0){
																	?>

																		<div class="btn-each">
																			<?php echo '<button id="respondButton" class="btn btn-danger respond-btn" style="background: red;" onclick="confirmDeleteCategory('.$category_id.')" disabled><i class="fas fa-trash"></i> Delete</button>'; ?>
																		</div>

																	<?php
																}else{
																	?>

																		<div class="btn-each">
																			<?php echo '<button id="respondButton" class="btn btn-danger respond-btn" style="background: red;" onclick="confirmDeleteCategory('.$category_id.')"><i class="fas fa-trash"></i> Delete</button>'; ?>
																		</div>

																	<?php
																}
															}

														?>

														
														
													</div>
													
												</div>

								<?php

										}
									}

								 ?>
								 <hr>
								<center>
				        			<button class="close-btn btn btn-danger" onclick="hideContent()" style="background: red;">Close</button>
				        		</center>
							</div>
							<script>
								function confirmDeleteCategory(itemId) {
							        var result = confirm('Are you sure you want to delete this item?');

							        if (result) {
							            // If the user clicks OK, redirect to the delete endpoint or perform deletion
							            window.location.href = 'delete_category.php?deleteid=' + itemId;
							        } else {
							            // User clicked Cancel, do nothing or provide feedback
							            alert('Delete canceled.');
							        }
							    }
								function displayCategoryContent() {
							        // Get the element with the id "displayedContentContainer"
							        var containerElement = document.getElementById('category_content');

							        // Toggle the display property (show/hide)
							        if (containerElement.style.display === 'none' || containerElement.style.display === '') {
							            containerElement.style.display = 'block';
							        } else {
							            containerElement.style.display = 'none';
							        }
							    }
							    function hideContent(){
							    	var containerElement = document.getElementById('category_content');
							    	containerElement.style.display = 'none';
							    }
							</script>

							<!-- Edit Customer Modal -->
							<div id="editProductModal" class="modal" <?php if(isset($_GET['editid'])) { echo 'style="display:block;"'; } ?>>
							    <div class="modal-wrapper">
							        <div class="modal-content">
							            <a href="products.php" class="edit_close">&times;</a>
							            <center><h2>Edit Product</h2></center>
							            <form action="edit_product.php" method="POST" style="width: 100%;">
							                <input type="hidden" id="edit_product_id" name="product_id" value="<?php echo $edit_id; ?>">
							                <div class="input-group">
							                    <label class="label" for="p_name">Product Name:</label>
							                    <input type="text" id="p_name" name="p_name" value="<?php echo $p_name_in_database; ?>" autocomplete="off" required>
							                </div>
							                <div class="input-group">
							                    <label class="label" for="p_price">Price:</label>
							                    <input type="number" id="p_price" name="p_price" value="<?php echo $p_price_in_database; ?>" autocomplete="off" required>
							                </div>
							                <?php 
							                	if($category_description_in_database == "Water"){
							                		?>
							                		<div class="input-group">
									                    <label class="label" for="quantity">Quantity:</label>
									                    <input type="text" id="quantity" name="quantity" value="<?php echo "Uncountable"; ?>" autocomplete="off" disabled required>
									                    <input type="hidden" id="quantity" name="quantity" value="<?php echo $quantity_in_database; ?>">
									                </div>
							                		<?php
							                	}else{
							                		?>
							                		<div class="input-group">
									                    <label class="label" for="quantity">Quantity:</label>
									                    <input type="number" id="quantity" name="quantity" value="<?php echo $quantity_in_database; ?>" autocomplete="off" required>
									                </div>
							                		<?php
							                	}
							                ?>
							                

							                <input class="btn btn-primary submit-btn" type="submit" value="Update Product">
							            </form>
							        </div>
							    </div>
							</div>

		    				<div class="table-wrapper">
		    					<div class="table-content">
		    						<table class="table table-bordered">
		    							<thead class="thead-dark">
		    								<tr>
		    									<th scope="col" style="width: 4rem;">No.</th>
		    									<th scope="col">Product Name</th>
		    									<th scope="col">Stocks</th>
		    									<th scope="col">Category</th>
		    									<th scope="col">Price</th>
		    									<th scope="col" style="width: 14rem;">Operation</th>
		    								</tr>
		    							</thead>
				<tbody>

					<?php 

						if(isset($_POST['submit'])){
							$searchInput = $_POST['searchInput'];

							$sql = "SELECT * FROM `tbl_products` JOIN tbl_category ON tbl_products.category_id = tbl_category.category_id where p_name like '%$searchInput%' order by p_name ASC";
							$result = mysqli_query($con, $sql);
							if($result && mysqli_num_rows($result)>0){
								$number = 0;
								while ($row = mysqli_fetch_assoc($result)) {
									++$number;
									$product_id = $row['product_id'];
									$p_name = $row['p_name'];
									$p_price = $row['p_price'];
									$category_id = $row['category_id'];
									$category_description = $row['category_description'];
									$quantity = $row['quantity'];

									if($quantity >= 99999999){
										$quantity = "<i class='fas fa-infinity'></i>";
									}
									
						?>
								<tr>
									<td><?php echo "$number"; ?></td>
									<td><?php echo "$p_name"; ?></td>
									<td><?php echo "$quantity"; ?></td>
									<td><?php echo "$category_description"; ?></td>
									<td><?php echo "$p_price"; ?></td>
									<td>
										<div class="operation-wrapper dp-flex space-around" style="font-size: 0.5rem;">
											<?php echo '<a href="products.php?editid='.$product_id.'">
												        <button class="btn btn-primary">
												            <i class="fas fa-edit"></i> Edit
												        </button>
												      </a>';

												echo '<button class="btn btn-danger" onclick="confirmDeleteProduct	('.$product_id.')"><i class="fas fa-trash"></i> Delete</button>';

											 ?>
										</div>
									</td>
								</tr>
						<?php
								}


								}else{

									?>
										<tr>
											<td colspan='6'><?php echo "Product not found!"; ?></td>
										</tr>	
									<?php

								}
						}else{
					?>


					<?php  

						$sql = "SELECT * FROM `tbl_products` JOIN tbl_category ON tbl_products.category_id = tbl_category.category_id order by p_name ASC";
						$result = mysqli_query($con, $sql);
						if($result && mysqli_num_rows($result)>0){
							$number = 0;
							while ($row = mysqli_fetch_assoc($result)) {
								++$number;
								$product_id = $row['product_id'];
									$p_name = $row['p_name'];
									$p_price = $row['p_price'];
									$category_id = $row['category_id'];
									$category_description = $row['category_description'];
									$quantity = $row['quantity'];

									if($quantity >= 99999999){
										$quantity = "<i class='fas fa-infinity'></i>";
									}
					?>
								<tr>
									<td><?php echo "$number"; ?></td>
									<td><?php echo "$p_name"; ?></td>
									<td><?php echo "$quantity"; ?></td>
									<td><?php echo "$category_description"; ?></td>
									<td><?php echo "$p_price"; ?></td>
									<td>
										<div class="operation-wrapper dp-flex space-around" style="font-size: 0.5rem;">
											<?php echo '<a href="products.php?editid='.$product_id.'">
													<button class="btn btn-primary">
													    <i class="fas fa-edit"></i> Edit
													</button>
													</a>';
												echo '<button class="btn btn-danger" onclick="confirmDeleteProduct	('.$product_id.')"><i class="fas fa-trash"></i> Delete</button>';

											 ?>
										</div>
									</td>
								</tr>
					<?php
							}
						}else{
							?>
								<tr>
									<td colspan='6'><?php echo "No Product Recorded!"; ?></td>
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
	    var modal = document.getElementById("addProductModal");

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

	    // Add Category Modal
	    var modal2 = document.getElementById("addCategoryModal");

	    // Get button that opens the modal
	    var btn2 = document.getElementById("openModalBtn2");

	    // Get the <span> element that closes the modal
	    var span2 = document.getElementsByClassName("close2")[0];

	    // When the user clicks the button, open the modal 
	    btn2.onclick = function() {
	        modal2.style.display = "block";
	    }

	    // When the user clicks on <span> (x), close the modal
	    span2.onclick = function() {
	        modal2.style.display = "none";
	    }

	    // When the user clicks anywhere outside of the modal, close it
	    window.onclick = function(event) {
	        if (event.target == modal2) {
	            modal2.style.display = "none";
	        }
	    }

	    // Edit Modal
	    // Get the modal element
		var editModal = document.getElementById("editProductModal");

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

	    function confirmDeleteProduct	(itemId) {
	    	var result = confirm('Are you sure you want to remove this product?');

	    	if(result){
	    		window.location.href = 'delete_product.php?deleteid=' + itemId;
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