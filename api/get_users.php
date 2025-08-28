<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli("sql102.infinityfree.com", "if0_39580483", "RuDr2004", "if0_39580483_omniquiz");

$res = $conn->query("SELECT id, name, email, role FROM users");

$users = [];
while ($row = $res->fetch_assoc()) {
  $users[] = $row;
}

echo json_encode($users);
?>
