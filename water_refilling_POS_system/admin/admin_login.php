<?php 
include "admin_connect.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="../css/bootstrap.css">

  <style>
      body {
          height: 100vh;
          display: flex;
          justify-content: center;
          align-items: center;
          background: radial-gradient(circle, #e0f7fa, #00bcd4);
          margin: 0;
          overflow: hidden;
      }

      .container {
          position: relative;
          width: 400px;
          padding: 40px;
          background-color: rgba(255, 255, 255, 0.9);
          box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
          border-radius: 15px;
          animation: floating 6s ease-in-out infinite;
      }

      @keyframes floating {
          0%, 100% { transform: translateY(0); }
          50% { transform: translateY(-10px); }
      }

      .login-header {
          text-align: center;
          margin-bottom: 20px;
          color: #00796b;
      }

      .txt-field {
          position: relative;
          margin-bottom: 15px;
      }

      .txt-field input {
          width: 100%;
          padding: 10px;
          background: transparent;
          border: 2px solid silver;
          border-radius: 10px;
          outline: none;
          font-size: 1em;
          color: #004d40;
          transition: 0.3s;
      }

      .txt-field label {
          position: absolute;
          left: 10px;
          top: 12px;
          color: #00796b;
          transition: 0.3s;
          z-index: -1;
      }

      .txt-field input:focus ~ label,
      .txt-field input:valid ~ label {
          top: -10px;
          left: 10px;
          font-size: 0.8em;
          color: black;
          background: #00ffd4;
          padding: 0px 6px;
          z-index: 1;
      }

      .submit-wrapper input {
          width: 100%;
          border: none;
          background: linear-gradient(45deg, #00acc1, #009688);
          border-radius: 25px;
          padding: 10px;
          color: white;
          cursor: pointer;
          transition: background 0.3s;
      }

      .submit-wrapper input:hover {
          background: linear-gradient(45deg, #00796b, #004d40);
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
  </style>
</head>
<body>
  <div class="water-bg"></div>
  <div class="container">
    <div class="login-header">
      <h1 class="login-text">Admin Login</h1>
    </div>
    <form action="login.php" method="post">
      <div class="txt-field input-1">
        <input type="text" id="username" required="required" name="username" autocomplete="off" aria-label="Username">
        <label>Username</label>
      </div>
      <div class="txt-field input-2">
        <input type="password" id="password" required="required" name="password" aria-label="Password">
        <label>Password</label>
      </div>
      <div class="submit-wrapper">
        <input class="btn submit-btn" type="submit" value="Login" name="submit">
      </div>
    </form>
  </div>
</body>
</html>
