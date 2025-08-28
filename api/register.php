<?php
// ===== FILE: api/register.php =====
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$conn = new mysqli("sql102.infinityfree.com", "if0_39580483", "RuDr2004", "if0_39580483_omniquiz");

$name = $conn->real_escape_string($data['name']);
$email = $conn->real_escape_string($data['email']);
$password = password_hash($data['password'], PASSWORD_DEFAULT);

$conn->query("INSERT INTO users (name, email, password_hash, role) VALUES ('$name', '$email', '$password', 'user')");
echo json_encode(["success" => true, "message" => "Registered successfully"]);
?>
