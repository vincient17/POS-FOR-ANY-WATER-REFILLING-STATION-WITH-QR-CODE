<?php 
session_start();
include "admin_connect.php";

// Fetch the category details for editing
if (isset($_GET['editid']) && is_numeric($_GET['editid'])) {
    $category_id = intval($_GET['editid']);

    $sql = "SELECT * FROM `tbl_category` WHERE category_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $category = $result->fetch_assoc();
    } else {
        $_SESSION['exist_message'] = "Category not found.";
        header("location: products.php");
        exit();
    }
} else {
    $_SESSION['exist_message'] = "Invalid request.";
    header("location: products.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_description = trim($_POST['category_description']);

    if (empty($category_description)) {
        $_SESSION['exist_message'] = "Category description cannot be empty.";
    } else {
        // Check for duplicates
        $exist_sql = "SELECT * FROM `tbl_category` WHERE category_description = ? AND category_id != ?";
        $stmt = $con->prepare($exist_sql);
        $stmt->bind_param("si", $category_description, $category_id);
        $stmt->execute();
        $exist_result = $stmt->get_result();

        if ($exist_result && $exist_result->num_rows > 0) {
            $_SESSION['exist_message'] = "Category already exists.";
                header("location: products.php");
        } else {
            // Update the category
            $update_sql = "UPDATE `tbl_category` SET category_description = ? WHERE category_id = ?";
            $stmt = $con->prepare($update_sql);
            $stmt->bind_param("si", $category_description, $category_id);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Category updated successfully!";
                header("location: products.php");
                exit();
            } else {
                $_SESSION['exist_message'] = "Failed to update category. Please try again.";
                header("location: products.php");
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <style>
        /* General reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom right, #0090a7, #77c5ff);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            background: rgba(255, 255, 255, 0.15);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            animation: fadeIn 1s ease-in-out;
            text-align: center;
            width: 90%;
            max-width: 400px;
        }

        h1 {
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        form {
            width: 100%;
        }

        .form-group {
            margin-bottom: 1rem;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            outline: none;
            background: rgba(255, 255, 255, 0.8);
            color: #333;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        button {
            width: 100%;
            padding: 0.75rem;
            background: #ff5722;
            color: #fff;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s, transform 0.3s;
        }

        button:hover {
            background: #e64a19;
            transform: translateY(-2px);
        }

        button:active {
            transform: scale(0.95);
        }

        a {
            display: inline-block;
            margin-top: 1rem;
            color: #fff;
            text-decoration: none;
            font-size: 0.9rem;
            opacity: 0.8;
            transition: opacity 0.3s;
        }

        a:hover {
            opacity: 1;
        }

        /* Keyframes */
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

        /* Responsive Design */
        @media (max-width: 768px) {
            h1 {
                font-size: 1.5rem;
            }

            input[type="text"], button {
                font-size: 0.9rem;
                padding: 0.65rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Category</h1>
        <form action="" method="POST">
            <div class="form-group">
                <label for="category_description">Category Description:</label>
                <input type="text" name="category_description" id="category_description" 
                       value="<?php echo htmlspecialchars($category['category_description'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>
            <div class="form-group">
                <button type="submit">Update Category</button>
                <a href="products.php">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>

