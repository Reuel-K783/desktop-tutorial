<?php
session_start();

// Database connection using MySQLi
include("conn.php");

// Signin Handler
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signin'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    // Prepare and execute SQL query using MySQLi
    $stmt = $conn->prepare("SELECT user_id, verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);  // Bind the email parameter
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user) {
        if ($user['verified'] == 1) {
            $_SESSION['user_id'] = $user['user_id'];
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Please verify your email before signing in.');</script>";
        }
    } else {
        echo "<script>alert('No account found with this email. Please sign up.');</script>";
    }
    $stmt->close();  // Close the prepared statement
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signin - Pencil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .signin-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 {
            color: #333;
        }
        label {
            display: block;
            margin-top: 10px;
            text-align: left;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #ffcc00;
            border: none;
            color: #333;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }
        button:hover {
            background: #e6b800;
        }
    </style>
</head>
<body>
    <div class="signin-container">
        <h2>Signin to Pencil</h2>
        <p>Access your business management tools.</p>
        <form method="POST" action="">
            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="Enter your email" required>
            <button type="submit" name="signin">Signin</button>
        </form>
    </div>
</body>
</html>
