<?php 
    session_start();
    include "admin_connect.php";

    if (isset($_SESSION['success_message'])) {
        echo '<div id="success-message" class="alert alert-success">'.$_SESSION['success_message'].'</div>';
        unset($_SESSION['success_message']);
    }

    if (isset($_SESSION['error_message'])) {
        echo '<div id="error_message" class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Scanner</title>
    <link rel="stylesheet" href="../css/bootstrap.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: radial-gradient(circle, #e0f7fa, #00b6ff);
            margin: 0;
            overflow: hidden;
        }
        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            width: 550px;
            padding: 40px;
            background-color: rgba(255, 234, 255, 0.23);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            backdrop-filter: blur(3px);
            animation: floating 6s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .water-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: url('../img/water_splash.jpg') repeat;
            opacity: 0.1;
            animation: waterFlow 10s linear infinite alternate;
        }

        @keyframes waterFlow {
            from { background-position: 0 0; }
            to { background-position: 100% 100%; }
        }
        #reader {
            width: 300px;
            margin-top: 20px;
            display: none;
            border: 1px solid #ccc;
        }
        #instructions {
            margin-top: 10px;
            font-size: 14px;
            color: black;
        }
        .header {
            font-size: 2.5rem;
        }
        form {
            width: 500px;
            background-image: linear-gradient(128deg, #007bff99, #00658185);
        }
        form h1{
            text-align: center;
        }
        .return-btn {
            position: absolute;
            bottom: 0;
            right: 0;
        }
        .scan-damaged-btn {
            position: absolute;
            bottom: 0;
            left: 0;
        }
        @media (max-width: 700px) {
            body{
                color: white;
                overflow: hidden;
            }
            .container{
                width: 90%;
                padding: 2rem 1rem;
            }
            form h1{
                font-size: 1.8rem !important;
            }
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
            animation: go_down 4s;
        }
        .alert-danger{
            background-color: orange;
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
    </style>
</head>
<body>

    <div class="water-bg"></div>
    <div class="container">
        <form method="post" id="qr-form" action="process_returned_gallon.php">
            <h1 class="header">Gallon Scanner</h1>
            <hr>
            <center>
                <div id="reader"></div>
            </center>
            <div class="input-group">
                <label for="result">QR Code:</label>
                <input type="text" id="result" name="qr_code" placeholder="Tap to scan QR code" readonly required>
            </div>
            <div id="instructions">Tap on the QR Code field to scan a QR code. Please allow camera access if prompted.</div>
        </form>
        <a href="scan_damage_gallon.php" class="btn scan-damaged-btn">Scan Damaged Gallon</a>
        <a href="scan_returned.php" class="btn return-btn">Return</a>
    </div>

    <!-- Include html5-qrcode library -->
    <script src="../js/html5-qrcode.min.js"></script>
    <script>
        let html5QrCode;
        const qrCodeSuccessCallback = (decodedText, decodedResult) => {
            document.getElementById('result').value = decodedText;
            // Automatically submit the form once the QR code is scanned
            document.getElementById('qr-form').submit();
            html5QrCode.stop().then(() => {
                document.getElementById('reader').style.display = 'none'; // Hide the camera
            }).catch((err) => {
                console.error("Failed to stop QR code scanning: ", err);
            });
        };

        const qrCodeErrorCallback = (errorMessage) => {
            console.error("QR Code Scan Error: ", errorMessage);
        };

        async function checkCameraPermissions() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                stream.getTracks().forEach(track => track.stop()); // Stop the stream
                return true; // Permissions granted
            } catch (error) {
                console.error("Camera access error: ", error);
                return false; // Permissions not granted
            }
        }

        document.getElementById('result').addEventListener('click', async function() {
            const hasPermission = await checkCameraPermissions();
            if (hasPermission) {
                if (!html5QrCode) {
                    html5QrCode = new Html5Qrcode("reader");
                }
                document.getElementById('reader').style.display = 'block'; // Show the camera
                html5QrCode.start(
                    { facingMode: "environment" }, // Use the back camera
                    { fps: 10, qrbox: { width: 180, height: 180 } },
                    qrCodeSuccessCallback,
                    qrCodeErrorCallback
                ).catch(err => {
                    console.error("Unable to start QR code scanning: ", err);
                });
            } else {
                alert("Please allow camera access in your browser settings.");
            }
        });
        setTimeout(function() {
            var successMessage = document.getElementById("success-message");
            if (successMessage) {
                successMessage.style.display = "none";
            }
        }, 4000);
        setTimeout(function() {
            var errorMessage = document.getElementById("error_message");
            if (errorMessage) {
                errorMessage.style.display = "none";
            }
        }, 4000);
    </script>
</body>
</html>
