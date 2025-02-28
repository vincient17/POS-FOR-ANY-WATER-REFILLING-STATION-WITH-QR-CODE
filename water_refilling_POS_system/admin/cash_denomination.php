<?php 
session_start();
include "admin_connect.php";
include "function.php";

$user_data = check_login($con);
$id = $_SESSION['id'];

// Get the total daily sales
$timezone = new DateTimeZone('Asia/Manila');
$dateTime = new DateTime(null, $timezone);
$curDay = $dateTime->format("Y-m-d");
$daily_sql = "SELECT * FROM `tbl_sales` WHERE date(date_sold) = '$curDay'";
$daily_result = mysqli_query($con, $daily_sql);

$daily_sales = 0;

$pass_sql = "SELECT password FROM admin WHERE id = $id";
$pass_result = mysqli_query($con, $pass_sql);
$password = '';

if ($pass_result) {
    $row = mysqli_fetch_assoc($pass_result);
    $password = $row['password'];
}

if ($daily_result) {
    while ($row = mysqli_fetch_assoc($daily_result)) {
        $total_price = $row['total_price'];
        $daily_sales += $total_price;
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $unit_1 = $_POST['unit_1'];
    $unit_5 = $_POST['unit_5'];
    $unit_10 = $_POST['unit_10'];
    $unit_20 = $_POST['unit_20'];
    $unit_50 = $_POST['unit_50'];
    $unit_100 = $_POST['unit_100'];
    $unit_200 = $_POST['unit_200'];
    $unit_500 = $_POST['unit_500'];
    $unit_1000 = $_POST['unit_1000'];

    $add_sql = "INSERT INTO `tbl_remit`(`p1`, `p5`, `p10`, `p20`, `p50`, `p100`, `p200`, `p500`, `p1000`, `date_remitted`) 
                VALUES ('$unit_1', '$unit_5', '$unit_10', '$unit_20', '$unit_50', '$unit_100', '$unit_200', '$unit_500', '$unit_1000', NOW())";
    $add_result = mysqli_query($con, $add_sql);

    if ($add_result) {
        header("Location: admin_logout.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cash Denomination</title>

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f7fc;
            color: #333;
            margin: 10px 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        form {
            max-width: 500px;
            width: 100%;
            background-color: #fff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        h3 {
            text-align: center;
            margin: 0px;
            color: #007bff;
            font-size: 1.8em;
        }

        .denomination-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            padding: 5px 10px;
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .denomination-group:hover {
            transform: translateY(-3px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .denomination-group label {
            font-weight: 500;
        }

        .denomination-group input {
            width: 70px;
            padding: 8px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 6px;
            text-align: right;
        }

        .denomination-group input:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.2);
        }

        .total-wrapper {
            text-align: center;
            font-size: 1.5em;
            font-weight: 700;
            margin-top: 25px;
            color: #28a745;
        }

        .btn-wrapper {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            font-size: 1em;
            font-weight: 600;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn-primary {
            background-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .cancel {
            background-color: #dc3545;
        }

        .cancel:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .daily-sales-wrapper{
            text-align: center;
            margin-top: 8px;
            font-size: 1.2rem;
            color: black;
        }
        /* Modal Overlay */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            animation: fadeIn 0.3s ease-in-out;
        }

        /* Modal Container */
        .modal-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: slideIn 0.3s ease-in-out;
        }

        /* Admin Input */
        .admin-input {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            margin-bottom: 20px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 8px;
            text-align: center;
            outline: none;
            transition: border-color 0.3s;
        }

        .admin-input:focus {
            border-color: #007bff;
        }

        /* Button Wrapper */
        .modal-btn-wrapper {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        /* Modal Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

    </style>

</head>
<body>
    <form method="post" id="cashDenominationForm">
        <h3>Cash Denomination</h3>
        <hr>
        <br>

        <div class="total-wrapper">
            Total Daily Sales: ₱<span id="dailySales"><?php echo number_format($daily_sales, 2); ?></span>
        </div>

        <br>

        <!-- Denomination Inputs -->
        <?php 
        $denominations = [
            '1000' => '₱1,000 Bills', '500' => '₱500 Bills', '200' => '₱200 Bills',
            '100' => '₱100 Bills', '50' => '₱50 Bills', '20' => '₱20 Bills',
            '10' => '₱10 Coins', '5' => '₱5 Coins', '1' => '₱1 Coins'
        ];

        foreach ($denominations as $value => $label): ?>
            <div class="denomination-group">
                <label for="bill<?php echo $value; ?>"><?php echo $label; ?>:</label>
                <input type="number" name="unit_<?php echo $value; ?>" id="bill<?php echo $value; ?>" min="0" value="0" data-value="<?php echo $value; ?>">
            </div>
        <?php endforeach; ?>

        <div class="total-wrapper">
            Total Amount: ₱<span id="totalAmount">0.00</span>
        </div>

        <div class="btn-wrapper">
            <button type="button" onclick="window.location='dashboard.php'" class="btn cancel">Cancel</button>
            <button type="submit" name="formSubmit" class="btn btn-primary">Submit</button>
        </div>
    </form>

    <div id="adminModal" class="modal-overlay" style="display: none;">
        <div class="modal-container">
            <h3>Admin Approval Required</h3>
            <p>Please enter the admin password to approve this submission.</p>
            <div class="denomination-group">
                <input type="password" id="adminPassword" placeholder="Enter admin password" style="width: 100%; text-align: left;">
            </div>
            
            <div class="modal-btn-wrapper">
                <button type="button" onclick="submitWithAdminApproval()" class="btn btn-primary">Approve</button>
                <button type="button" onclick="closeModal()" class="btn cancel">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        let dailySales = parseFloat("<?php echo $daily_sales; ?>");

        // Event listener for input changes
        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', calculateTotal);
        });

        // Calculate total function
        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('input[type="number"]').forEach(input => {
                const value = parseFloat(input.value) || 0;
                const denomination = parseFloat(input.dataset.value);
                total += value * denomination;
            });
            document.getElementById('totalAmount').textContent = total.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // Form submit listener
        document.getElementById('cashDenominationForm').addEventListener('submit', function (e) {
            let totalAmount = parseFloat(document.getElementById('totalAmount').textContent.replace(/,/g, ''));

            if (totalAmount < dailySales) {
                e.preventDefault();
                alert('The total cash denomination is less than the daily sales. Please correct it before submitting.');
            } else {
                e.preventDefault(); // Prevent default until admin approval
                openModal();
            }
        });

        // Open modal function
        function openModal() {
            document.getElementById('adminModal').style.display = 'flex';
        }

        // Close modal function
        function closeModal() {
            document.getElementById('adminModal').style.display = 'none';
        }

        function submitWithAdminApproval() {
            let adminPassword = document.getElementById('adminPassword').value;
            let correctPassword = '<?php echo htmlspecialchars($password); ?>';

            console.log('Entered Password:', adminPassword);
            console.log('Correct Password:', correctPassword);

            if (adminPassword === correctPassword) {
                console.log('Password is correct. Submitting form...');
                let form = document.getElementById('cashDenominationForm');
                console.log(form); // Confirm the form is logged correctly

                if (form && typeof form.submit === 'function') {
                    form.submit.call(form); // Use the native submit method
                } else {
                    console.error('Form not found or submit is not a function');
                }
            } else if (adminPassword === '') {
                alert('Please enter the admin password.');
            } else {
                alert('Incorrect password.');
            }
        }
    </script>
</body>
</html>
