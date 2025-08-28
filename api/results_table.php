<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli("sql102.infinityfree.com", "if0_39580483", "RuDr2004", "if0_39580483_omniquiz");

// ✅ JOIN questions to get question text + correct option
$res = $conn->query("
  SELECT r.*, q.text AS question_text, q.correct_option
  FROM responses r
  JOIN questions q ON r.question_id = q.id
  ORDER BY r.session_id DESC
");

$data = [];

while ($row = $res->fetch_assoc()) {
  $data[] = [
    "user" => "User " . $row['session_id'],
    "question" => $row['question_text'],
    "opt" => $row['chosen_option'],
    "correct" => $row['correct_option'],
    "conf" => $row['confidence'],
    "hint" => $row['hint_cost'] > 0 ? "Yes" : "No",
    "time" => $row['time_taken'],
    "score" => $row['score']
  ];
}

echo json_encode($data);
?>
