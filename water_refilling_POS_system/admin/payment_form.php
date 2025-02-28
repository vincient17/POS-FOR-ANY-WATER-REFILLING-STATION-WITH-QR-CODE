<?php
    include "admin_connect.php";
    
    $bill_amount = floatval($_GET['totalBill']);
    
    $rounded_bill_amount = ceil($bill_amount);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Form</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .form-group label {
            font-weight: 600;
        }

        .form-summary {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-summary.success {
            background-color: #e9f7e1;
            border-left: 5px solid #28a745;
            color: #155724;
        }

        .form-summary.error {
            background-color: #fcebea;
            border-left: 5px solid #dc3545;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container form-container">
        <h2>Payment Form</h2>
        <form method="POST" action="get_denomination.php" id="payment-form">
            <!-- Total Bill -->
            <div class="form-group">
                <label for="bill_amount">Total Bill (₱):</label>
                <input type="number" class="form-control" name="bill_amount" id="bill_amount" value="<?php echo "$rounded_bill_amount"; ?>" readonly>
            </div>

            <h5 class="text-primary mt-4">Payment Received (by Denomination):</h5>

            <!-- Payment by denomination -->
            <div class="row">
                <div class="col-md-4 form-group">
                    <label for="unit_1">₱1:</label>
                    <input type="number" class="form-control" name="unit_1" id="unit_1" value="0" required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="unit_5">₱5:</label>
                    <input type="number" class="form-control" name="unit_5" id="unit_5" value="0" required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="unit_10">₱10:</label>
                    <input type="number" class="form-control" name="unit_10" id="unit_10" value="0" required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="unit_20">₱20:</label>
                    <input type="number" class="form-control" name="unit_20" id="unit_20" value="0" required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="unit_50">₱50:</label>
                    <input type="number" class="form-control" name="unit_50" id="unit_50" value="0" required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="unit_100">₱100:</label>
                    <input type="number" class="form-control" name="unit_100" id="unit_100" value="0" required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="unit_200">₱200:</label>
                    <input type="number" class="form-control" name="unit_200" id="unit_200" value="0" required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="unit_500">₱500:</label>
                    <input type="number" class="form-control" name="unit_500" id="unit_500" value="0" required>
                </div>
                <div class="col-md-4 form-group">
                    <label for="unit_1000">₱1000:</label>
                    <input type="number" class="form-control" name="unit_1000" id="unit_1000" value="0" required>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="submit" class="btn btn-primary btn-block mt-4">Submit Payment</button>
        </form>

        <!-- Change Summary -->
        <div id="changeSummary" class="form-summary"></div>
    </div>

    <!-- Bootstrap JS -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>

