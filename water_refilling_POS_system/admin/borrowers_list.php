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

    $timezone = new DateTimeZone('Asia/Manila');
    $dateTime = new DateTime(null, $timezone);

    // $dateTime->modify('+3 years');
    // $dateTime->modify('+0 months');
     // $dateTime->modify('-1 days');

    $curDay = $dateTime->format("Y-m-d");
    // echo "$curDay";

    $current_year = $dateTime->format("Y");
    $current_month = $dateTime->format("m");
    $current_Month = $dateTime->format("M");
    $current_day = $dateTime->format("d");



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrowers List with QR Code</title>
    <style>
        *{
            font-family: sans-serif;
        }
        .customer-card {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 10px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 265px;
            position: relative;
            cursor: pointer;
        }

        .qr-wrapper.qr-code-box {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 150px;
            height: 150px;
            background: #f8f8f8;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .customer-card p {
            margin: 5px 0;
        }

        .print-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .print-button:hover {
            background-color: #45a049;
        }

        .customer-list {
            display: flex;
            justify-content: space-around !important;
            flex-wrap: wrap;
        }

        .select-checkbox {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        /* Styling for the print layout */
        @media print {
            body * {
                visibility: hidden;
            }
            .print-area, .print-area * {
                visibility: visible;
            }
            .print-area {
                position: absolute;
                left: 0;
                top: 0;
            }

            /* Ensure QR code is 1x1 inch and horizontally aligned with spacing */
            .qr-print {
                width: 150px;
                height: 150px;
                display: inline-block;
                page-break-inside: avoid;
            }

            /* Ensure that multiple QR codes align horizontally and wrap to the next line */
            .print-area {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }
            .qr-code-box {
            	width: 150px !important;
        		height: 150px !important;
	            background: #f8f8f8;
	            border: 2px solid #ddd;
	            border-radius: 10px;
	            padding: 10px;
        	}
        	.qr-code-box img{
        		width: 150px;
        		height: 150px;
        	}

        }
        .header-wrapper{
            position: relative;
            padding: 2rem;
            background: blue;
        }
        .header{
            text-align: center;
            color: white;
            margin: 0;
        }
        .header-wrapper .btn{
            position: absolute;
            top: 0px;
            right: 0px;
            background: red;
            color: white;
            padding: 6px 10px;
            border: 1px solid darkblue;
            cursor: pointer;
            border-radius: 0px;
        }
        .input--group{
            margin-right: 1rem;
        }
        form .btn{
            padding: 6px 15px;
            background: #0064ff;
            color: white;
            border-color: blue;
        }
        .btn-wrapper{
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        input{
            padding: 5px;
            border-radius: 3px;
            border: solid 1px black;
        }
        hr{
            margin: 1rem 0px;
        }
    </style>
</head>
<body>
    <div class="header-wrapper">
        <a href="gallon_inventory.php"><button class="btn">X</button></a>
        <h1 class="header">Borrowers List with QR Codes</h1>
    </div>
    

    <!-- Print Selected Button -->
    <div class="btn-wrapper">
        <button class="print-button" onclick="printSelected()">Print Selected</button>
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

                echo '<div class="customer-list">';

                    $query = "SELECT tbl_borrowedgallon.borrowed_id, tbl_gallon.unique_code, tbl_customers.name, tbl_customers.address, tbl_customers.cp_num, tbl_borrowedgallon.date_borrowed
                              FROM tbl_borrowedgallon
                              JOIN tbl_customers ON tbl_borrowedgallon.cus_id = tbl_customers.cus_id
                              JOIN tbl_gallon ON tbl_borrowedgallon.gallon_id = tbl_gallon.gallon_id
                              WHERE date(date_borrowed) >= '$from_date' AND date(date_borrowed) <= '$to_date'";
                    $result = mysqli_query($con, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $qrData = "Borrowed ID: " . $row['borrowed_id'] . "\n" .
                                  "Borrowed Gallon: " . $row['unique_code'] . "\n" .
                                  "Borrower Name: " . $row['name'] . "\n" .
                                  "Address: " . $row['address'] . "\n" .
                                  "Cp number: " . $row['cp_num'] . "\n" .
                                  "Date Borrowed: " . $row['date_borrowed'];

                        echo '<div class="customer-card" onclick="toggleSelection(this, \'' . $row['unique_code'] . '\')">';
                        echo '<input type="checkbox" class="select-checkbox" data-unique-code="' . $row['unique_code'] . '">';
                        echo "<center>";
                        echo '<div class="qr-wrapper qr-code-box" id="qrcode-' . $row['unique_code'] . '"></div>';
                        echo "</center>";
                        echo "<hr>";
                        echo '<p><strong>Borrowed ID:</strong> ' . $row['borrowed_id'] . '</p>';
                        echo '<p><strong>Borrowed Gallon:</strong> ' . $row['unique_code'] . '</p>';
                        echo '<p><strong>Borrower Name:</strong> ' . $row['name'] . '</p>';
                        echo '<p><strong>Address:</strong> ' . $row['address'] . '</p>';
                        echo '<p><strong>Cp number:</strong> ' . $row['cp_num'] . '</p>';
                        echo '<p><strong>Date Borrowed:</strong> ' . $row['date_borrowed'] . '</p>';
                        echo '</div>';
                    }
                echo "</div>";
                ?>
                <!-- Include QRCode.js library -->
                <script src="../js/qrcode.min.js"></script>

                <script>
                const borrowers = <?php
                    $borrowers_js = [];
                    mysqli_data_seek($result, 0);
                    while ($row = mysqli_fetch_assoc($result)) {
                        $borrowers_js[] = [
                            'gallon_unique_code' => $row['unique_code'],
                            'qrData' => "Borrower ID: " . $row['borrowed_id'] . "\n" .
                                        "Borrowed Gallon: " . $row['unique_code'] . "\n" .
                                        "Borrower Name: " . $row['name'] . "\n" .
                                        "Address: " . $row['address'] . "\n" .
                                        "Cp number: " . $row['cp_num'] . "\n" .
                                        "Date Borrowed: " . $row['date_borrowed']
                        ];
                    }
                    echo json_encode($borrowers_js, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
                ?>;

                borrowers.forEach(borrower => {
                    let qrCodeElementId = 'qrcode-' + borrower.gallon_unique_code;
                    let qrCodeElement = document.getElementById(qrCodeElementId);
                    new QRCode(qrCodeElement, {
                        text: borrower.qrData,
                        width: 150,
                        height: 150
                    });
                });

                const selectedQRs = new Set();

                function toggleSelection(card, uniqueCode) {
                    const checkbox = card.querySelector('.select-checkbox');
                    if (checkbox.checked) {
                        checkbox.checked = false;
                        selectedQRs.delete(uniqueCode);
                    } else {
                        checkbox.checked = true;
                        selectedQRs.add(uniqueCode);
                    }
                }

                function printSelected() {
                    let printArea = document.createElement('div');
                    printArea.className = 'print-area';
                    selectedQRs.forEach(uniqueCode => {
                        let qrCodeElement = document.getElementById('qrcode-' + uniqueCode);
                        let qrPrintElement = qrCodeElement.cloneNode(true);
                        qrPrintElement.classList.add('qr-print');
                        printArea.appendChild(qrPrintElement);
                    });
                    document.body.appendChild(printArea);
                    window.print();
                    document.body.removeChild(printArea);
                }
                </script>
            <?php

            }else{
                echo '<div class="customer-list">';
                    $query = "SELECT tbl_borrowedgallon.borrowed_id, tbl_gallon.unique_code, tbl_customers.name, tbl_customers.address, tbl_customers.cp_num, tbl_borrowedgallon.date_borrowed
                              FROM tbl_borrowedgallon
                              JOIN tbl_customers ON tbl_borrowedgallon.cus_id = tbl_customers.cus_id
                              JOIN tbl_gallon ON tbl_borrowedgallon.gallon_id = tbl_gallon.gallon_id
                              WHERE date(date_borrowed) = '$curDay'";
                    $result = mysqli_query($con, $query);

                    while ($row = mysqli_fetch_assoc($result)) {
                        $qrData = "Borrowed ID: " . $row['borrowed_id'] . "\n" .
                        		  "Borrowed Gallon: " . $row['unique_code'] . "\n" .
                                  "Borrower Name: " . $row['name'] . "\n" .
                                  "Address: " . $row['address'] . "\n" .
                                  "Cp number: " . $row['cp_num'] . "\n" .
                                  "Date Borrowed: " . $row['date_borrowed'];

                        echo '<div class="customer-card" onclick="toggleSelection(this, \'' . $row['unique_code'] . '\')">';
                        echo '<input type="checkbox" class="select-checkbox" data-unique-code="' . $row['unique_code'] . '">';
                        echo "<center>";
                        echo '<div class="qr-wrapper qr-code-box" id="qrcode-' . $row['unique_code'] . '"></div>';
                        echo "</center>";
                        echo "<hr>";
                        echo '<p><strong>Borrowed ID:</strong> ' . $row['borrowed_id'] . '</p>';
                        echo '<p><strong>Borrowed Gallon:</strong> ' . $row['unique_code'] . '</p>';
                        echo '<p><strong>Borrower Name:</strong> ' . $row['name'] . '</p>';
                        echo '<p><strong>Address:</strong> ' . $row['address'] . '</p>';
                        echo '<p><strong>Cp number:</strong> ' . $row['cp_num'] . '</p>';
                        echo '<p><strong>Date Borrowed:</strong> ' . $row['date_borrowed'] . '</p>';
                        echo '</div>';
                    }
                echo "</div>";
            ?>
                <!-- Include QRCode.js library -->
                <script src="../js/qrcode.min.js"></script>

                <script>
                const borrowers = <?php
                    $borrowers_js = [];
                    mysqli_data_seek($result, 0);
                    while ($row = mysqli_fetch_assoc($result)) {
                        $borrowers_js[] = [
                            'gallon_unique_code' => $row['unique_code'],
                            'qrData' => "Borrower ID: " . $row['borrowed_id'] . "\n" .
                                        "Borrowed Gallon: " . $row['unique_code'] . "\n" .
                                        "Borrower Name: " . $row['name'] . "\n" .
                                        "Address: " . $row['address'] . "\n" .
                                        "Cp number: " . $row['cp_num'] . "\n" .
                                        "Date Borrowed: " . $row['date_borrowed']
                        ];
                    }
                    echo json_encode($borrowers_js, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
                ?>;

                borrowers.forEach(borrower => {
                    let qrCodeElementId = 'qrcode-' + borrower.gallon_unique_code;
                    let qrCodeElement = document.getElementById(qrCodeElementId);
                    new QRCode(qrCodeElement, {
                        text: borrower.qrData,
                        width: 150,
                        height: 150
                    });
                });

                const selectedQRs = new Set();

                function toggleSelection(card, uniqueCode) {
                    const checkbox = card.querySelector('.select-checkbox');
                    if (checkbox.checked) {
                        checkbox.checked = false;
                        selectedQRs.delete(uniqueCode);
                    } else {
                        checkbox.checked = true;
                        selectedQRs.add(uniqueCode);
                    }
                }

                function printSelected() {
                    let printArea = document.createElement('div');
                    printArea.className = 'print-area';
                    selectedQRs.forEach(uniqueCode => {
                        let qrCodeElement = document.getElementById('qrcode-' + uniqueCode);
                        let qrPrintElement = qrCodeElement.cloneNode(true);
                        qrPrintElement.classList.add('qr-print');
                        printArea.appendChild(qrPrintElement);
                    });
                    document.body.appendChild(printArea);
                    window.print();
                    document.body.removeChild(printArea);
                }
                </script>
            <?php
            }

        ?>

</body>
</html>
