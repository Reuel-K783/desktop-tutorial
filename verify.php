<?php
session_start();
include("conn.php");
// Email Verification Handler
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the database
    $stmt = $conn->prepare("SELECT * FROM users WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Token found, update the user's verified status
        $stmt = $conn->prepare("UPDATE users SET verified = 1 WHERE token = ?");
        $stmt->bind_param("s", $token);
        if ($stmt->execute()) {
            echo "Email verified successfully! You can now <a href='signin.php'>login</a>.";
        } else {
            echo "Error verifying email. Please try again.";
        }
    } else {
        echo "Invalid verification link or token.";
    }
    $stmt->close();
} else {
    echo "No token provided.";
}

$conn->close();
?>
