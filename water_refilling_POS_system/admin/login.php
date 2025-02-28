<?php  
session_start();
include("admin_connect.php");
include("function.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!empty($username)) {

        // Read from the admin database
        $query = "SELECT * FROM admin WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result) {
            if ($result && mysqli_num_rows($result) > 0) {

                $row = mysqli_fetch_assoc($result);
                $db_password = $row['password'];

                // Check the password
                if ($db_password === $password) {

                    // Check if the system is set up
                    $setup_query = "SELECT COUNT(*) as setup_count FROM tbl_system_setup";
                    $setup_result = mysqli_query($con, $setup_query);
                    $setup_row = mysqli_fetch_assoc($setup_result);

                    // If there is no setup data, redirect to Quick Setup
                    if ($setup_row['setup_count'] == 0) {
                        header("Location: quick_setup.php");
                        die;
                    } else {
                        // If the system is set up, proceed to dashboard
                        $_SESSION['id'] = $row['id'];
                        header("Location: dashboard.php");
                        die;
                    }
                } else {
                    echo "<script>alert('Incorrect username or password!')</script>";
                }
            } else {
                echo "<script>alert('Incorrect username or password!')</script>";
            }
        }
        header("Location: admin_login.php");		
    } else {
        header("Location: admin_login.php");
    }
}
?>
