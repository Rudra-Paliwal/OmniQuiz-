<?php
// ===== FILE: api/question_count.php =====
header('Content-Type: application/json');
$conn = new mysqli("sql102.infinityfree.com", "if0_39580483", "RuDr2004", "if0_39580483_omniquiz");

$res = $conn->query("SELECT COUNT(*) AS total FROM questions");
$row = $res->fetch_assoc();

echo json_encode(["total" => intval($row['total'])]);
?>
