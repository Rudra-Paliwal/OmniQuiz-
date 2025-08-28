<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$conn = new mysqli("sql102.infinityfree.com", "if0_39580483", "RuDr2004", "if0_39580483_omniquiz");

$email = $conn->real_escape_string($data['email']);
$password = $data['password'];

$res = $conn->query("SELECT * FROM users WHERE email='$email'");

if ($res && $res->num_rows > 0) {
  $row = $res->fetch_assoc();

  $stored = $row['password_hash'];

  $isValid = ($password === $stored || password_verify($password, $stored));

  if ($isValid) {
    echo json_encode([
      "success" => true,
      "name" => $row['name'],
      "role" => $row['role'],
      "session_id" => $row['id']
    ]);
  } else {
    echo json_encode(["success" => false, "message" => "Incorrect password"]);
  }
} else {
  echo json_encode(["success" => false, "message" => "User not found"]);
}
