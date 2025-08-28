<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=\"my_results.csv\"");

$data = json_decode(file_get_contents("php://input"), true);
$sessionId = intval($data['session_id']);

$conn = new mysqli("sql102.infinityfree.com", "if0_39580483", "RuDr2004", "if0_39580483_omniquiz");
$output = fopen("php://output", "w");

// Header
fputcsv($output, ["question_id", "chosen_option", "confidence", "score", "hint_cost", "time_taken"]);

$res = $conn->query("SELECT * FROM responses WHERE session_id = $sessionId");

while ($row = $res->fetch_assoc()) {
    fputcsv($output, [
        $row['question_id'],
        $row['chosen_option'],
        $row['confidence'],
        $row['score'],
        $row['hint_cost'],
        $row['time_taken']
    ]);
}

fclose($output);
$conn->close();
