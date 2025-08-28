<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli("sql102.infinityfree.com", "if0_39580483", "RuDr2004", "if0_39580483_omniquiz");

$labels = [];
$scores = [];

$result = $conn->query("SELECT session_id, SUM(score) as total FROM responses GROUP BY session_id");

while ($row = $result->fetch_assoc()) {
    $labels[] = "User " . $row['session_id'];
    $scores[] = (int) $row['total'];
}

echo json_encode([
    "labels" => $labels,
    "scores" => $scores
]);
?>
