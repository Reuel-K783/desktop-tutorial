<?php
session_start();
include("conn.php");

$data = json_decode(file_get_contents('php://input'), true);
$message = $data['message'];
$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
$stmt->bind_param("is", $userId, $message);
$stmt->execute();
?>