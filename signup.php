<?php
session_start();

include("conn.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Function to send verification email
function sendVerificationEmail($email, $token) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ckavwenje139nkk@gmail.com';
        $mail->Password = 'dfqb bpju uqzf fzxt';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        
        $mail->setFrom('ckavwenje139nkk@gmail.com', 'Pencil Verification');
        $mail->addAddress($email);
        
        $mail->isHTML(true);
        $mail->Subject = 'WELCOME TO PENCIL';
        $mail->Body = "Click the link to verify your email: <a href='http://localhost/KAVWENJE/PENCIL/signup.php?token=$token'>Verify Email</a>";
        
        $mail->send();
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
    }
}

// Signup Handler
if (isset($_POST['signup'])) {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $token = bin2hex(random_bytes(50));
    
    $stmt = $conn->prepare("INSERT INTO users (email, phone, token, verified) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("sss", $email, $phone, $token);
    
    if ($stmt->execute()) {
        sendVerificationEmail($email, $token);
        echo "Verification email sent. Please check your inbox.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Signin Handler
if (isset($_POST['signin'])) {
    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && $user['verified']) {
        $_SESSION['user'] = $user;
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Please verify your email first.";
    }
    $stmt->close();
}

// Email Verification Handler
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $conn->prepare("UPDATE users SET verified = 1 WHERE token = ?");
    $stmt->bind_param("s", $token);
    if ($stmt->execute()) {
        echo "Email verified successfully! You can now login.";
    }
    $stmt->close();
}

// Dashboard Access Restriction
if (basename($_SERVER['PHP_SELF']) == 'dashboard.php') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['verified'] == 0) {
        header("Location: signin.php");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Pencil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .signup-container {
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
    <div class="signup-container">
        <h2>Signup for Pencil</h2>
        <p>Unlock business management tools with your Pencil account.</p>
        <form method="POST" action="">
            <label for="email">Email:</label>
            <input type="email" name="email" placeholder="Enter your email" required>
            <label for="phone">Phone:</label>
            <input type="text" name="phone" placeholder="Enter your phone number" required>
            <button type="submit" name="signup">Signup</button>
        </form>
    </div>
</body>
</html>
