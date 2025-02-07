<?php
session_start();
include("conn.php");
header('Content-Type: application/json');

$userId = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM messages WHERE user_id = ? ORDER BY timestamp");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

echo json_encode($result->fetch_all(MYSQLI_ASSOC));
?>