<?php
include("conn.php");
header('Content-Type: application/json');

$search = $_GET['search'] ?? '';
$stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ?");
$searchTerm = "%$search%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

echo json_encode($result->fetch_all(MYSQLI_ASSOC));
?>