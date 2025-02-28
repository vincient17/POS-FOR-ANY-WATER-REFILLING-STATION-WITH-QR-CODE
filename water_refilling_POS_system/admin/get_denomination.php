<?php
    session_start();
    include "admin_connect.php";

    // Fetch available denominations from the database
    $sql = "SELECT * FROM `tbl_cash_denomination` WHERE id = 1";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);

    $denominations = [
        1000 => $row['unit_1000'],
        500  => $row['unit_500'],
        200  => $row['unit_200'],
        100  => $row['unit_100'],
        50   => $row['unit_50'],
        20   => $row['unit_20'],
        10   => $row['unit_10'],
        5    => $row['unit_5'],
        1    => $row['unit_1']
    ];

    if (isset($_POST['submit'])) {
        $bill_amount = $_POST['bill_amount'];
        $received_units = [
            1000 => $_POST['unit_1000'],
            500  => $_POST['unit_500'],
            200  => $_POST['unit_200'],
            100  => $_POST['unit_100'],
            50   => $_POST['unit_50'],
            20   => $_POST['unit_20'],
            10   => $_POST['unit_10'],
            5    => $_POST['unit_5'],
            1    => $_POST['unit_1']
        ];

        $total_payment = 0;
        foreach ($received_units as $denomination => $count) {
            $total_payment += $denomination * $count;
        }

        $change = $total_payment - $bill_amount;

        function calculateChange($change, $denominations) {
            $changeGiven = [];
            foreach ($denominations as $denomination => $availableCount) {
                $numOfNotes = min(intval($change / $denomination), $availableCount);
                if ($numOfNotes > 0) {
                    $changeGiven[$denomination] = $numOfNotes;
                    $change -= $numOfNotes * $denomination;
                }
            }

            if ($change > 0) {
                return ['error' => 'Insufficient change in available denominations.'];
            }
            return $changeGiven;
        }

        if ($change >= 0) {
            $changeResult = calculateChange($change, $denominations);
            if (isset($changeResult['error'])) {
                $message = $changeResult['error'];
                $showDenominations = true;
            } else {
                foreach ($received_units as $denomination => $count) {
                    $denominations[$denomination] += $count;
                }
                foreach ($changeResult as $denomination => $count) {
                    $denominations[$denomination] -= $count;
                }
                $updateQuery = "UPDATE `tbl_cash_denomination` SET 
                    unit_1000 = {$denominations[1000]},
                    unit_500  = {$denominations[500]},
                    unit_200  = {$denominations[200]},
                    unit_100  = {$denominations[100]},
                    unit_50   = {$denominations[50]},
                    unit_20   = {$denominations[20]},
                    unit_10   = {$denominations[10]},
                    unit_5    = {$denominations[5]},
                    unit_1    = {$denominations[1]}
                    WHERE id = 1";
                mysqli_query($con, $updateQuery);
                $message = "Change given successfully.";
            }
        } else {
            $message = "Payment is insufficient. Please provide more cash.";
            $showDenominations = true;
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Change</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to right, #6dd5ed, #2193b0);
            color: #333;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 5px 20px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 100%;
            animation: fadeIn 1s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .message {
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            animation: slideDown 0.5s ease;
        }
        .message-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .list-group-item {
            background-color: #6dd5ed;
            color: #fff;
            border: none;
            border-radius: 10px;
            margin-bottom: 5px;
            transition: background 0.3s;
        }
        .list-group-item:hover {
            background: #2193b0;
        }
        .btn-custom {
            background-color: #2193b0;
            color: #fff;
        }
        .btn-custom:hover {
            background-color: #6dd5ed;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mb-4">Payment Summary</h2>
        <p class="text-center fw-bold">Total Bill: ₱<?php echo isset($bill_amount) ? $bill_amount : 0; ?></p>
        <p class="text-center fw-bold">Total Payment: ₱<?php echo isset($total_payment) ? $total_payment : 0; ?></p>
        <p class="text-center fw-bold">Change: ₱<?php echo isset($change) && $change >= 0 ? $change : 'N/A'; ?></p>

        <?php if (isset($message)) : ?>
            <div class="message <?php echo isset($changeResult['error']) || (isset($showDenominations) && $showDenominations) ? 'message-error' : 'message-success'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($showDenominations) && $showDenominations) : ?>
            <h4 class="mt-4">Current Available Denominations:</h4>
            <ul class="list-group">
                <?php foreach ($denominations as $denomination => $count) : ?>
                    <li class="list-group-item">₱<?php echo $denomination; ?>: <?php echo $count; ?> available</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($message == "Change given successfully.") : ?>
            <h5 class="mt-4 text-center">Breakdown of Change:</h5>
            <ul class="list-group">
                <?php foreach ($changeResult as $denomination => $count) : ?>
                    <li class="list-group-item">
                        ₱<?php echo $denomination; ?> x <?php echo $count; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="text-center mt-4">
            <?php
            if (isset($changeResult['error']) && $changeResult['error'] == "Insufficient change in available denominations.") {
                echo '<a href="payment_form.php?totalBill=' . $bill_amount . '" class="btn btn-custom">Go Back</a>';
            } elseif ($message == "Payment is insufficient. Please provide more cash.") {
                echo '<a href="payment_form.php?totalBill=' . $bill_amount . '" class="btn btn-custom">Go Back</a>';
            } else {
                $_SESSION['success_message'] = "Purchase completed successfully!";
                echo '<a href="customers.php" class="btn btn-custom">Go Back</a>';
            }
            ?>
        </div>
    </div>
</body>
</html>
