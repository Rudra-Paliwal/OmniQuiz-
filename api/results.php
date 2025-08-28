<?php
// ===== FILE: api/results.php =====
header('Content-Type: application/json');
$conn = new mysqli("sql102.infinityfree.com", "if0_39580483", "RuDr2004", "if0_39580483_omniquiz");

// Get the latest session_id
$latest = $conn->query("SELECT MAX(session_id) AS max_id FROM responses")->fetch_assoc();
$session_id = $latest['max_id'] ?? 1;

// Get all responses from the latest session
$res = $conn->query("SELECT * FROM responses WHERE session_id = $session_id ORDER BY question_id ASC");

$labels = [];
$scores = [];

while ($row = $res->fetch_assoc()) {
  $labels[] = "Q" . $row['question_id'];
  $scores[] = (int) $row['score'];
}

echo json_encode([
  "labels" => $labels,
  "scores" => $scores,
  "total" => array_sum($scores)
]);
?>
