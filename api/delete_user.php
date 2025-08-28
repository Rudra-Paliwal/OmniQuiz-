<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli("sql102.infinityfree.com", "if0_39580483", "RuDr2004", "if0_39580483_omniquiz");

$data = json_decode(file_get_contents("php://input"), true);
$user_id = intval($data["id"]);

if ($conn->query("DELETE FROM users WHERE id=$user_id")) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["success" => false, "error" => $conn->error]);
}
?>
