<?php
// ===== FILE: api/export_csv.php =====
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="results.csv"');

$conn = new mysqli("sql102.infinityfree.com", "if0_39580483", "RuDr2004", "if0_39580483_omniquiz");

$output = fopen("php://output", "w");

// Write header row
fputcsv($output, ['Session ID', 'Question ID', 'Chosen Option', 'Confidence', 'Time Taken', 'Hint Cost', 'Score']);

// Fetch responses from latest session
$res = $conn->query("SELECT * FROM responses ORDER BY session_id DESC");

while ($row = $res->fetch_assoc()) {
    fputcsv($output, [
        $row['session_id'],
        $row['question_id'],
        $row['chosen_option'],
        $row['confidence'],
        $row['time_taken'],
        $row['hint_cost'],
        $row['score']
    ]);
}

fclose($output);
$conn->close();
exit;
?>
