<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli("sql102.infinityfree.com", "if0_39580483", "RuDr2004", "if0_39580483_omniquiz");

$input = json_decode(file_get_contents("php://input"), true);
$session_id = intval($_COOKIE['session_id'] ?? 0);  // or however you’re tracking session
$qid = intval($input['question_id']);
$chosen_option = $input['chosen_option'];
$confidence = intval($input['confidence']);
$hint_used = !empty($input['hint_used']);

// ✅ Fetch question
$q = $conn->query("SELECT * FROM questions WHERE id = $qid")->fetch_assoc();
$correct = $q['correct_option'];
$hintCost = $hint_used ? 10 : 0;

// ✅ Score calculation
if ($chosen_option === $correct) {
    $score = $confidence - $hintCost;
    $message = "✅ Correct!";
} else {
    $score = -$confidence - $hintCost;
    $message = "❌ Wrong! Correct answer was $correct";
}

// ✅ Save response
$conn->query("
    INSERT INTO responses (session_id, question_id, chosen_option, confidence, hint_cost, score)
    VALUES ($session_id, $qid, '$chosen_option', $confidence, $hintCost, $score)
");

// ✅ Count total questions
$totalQ = $conn->query("SELECT COUNT(*) AS cnt FROM questions")->fetch_assoc()['cnt'];

// ✅ Count answered by this session
$answeredQ = $conn->query("SELECT COUNT(*) AS cnt FROM responses WHERE session_id = $session_id")->fetch_assoc()['cnt'];

// ✅ Check if more questions remain
$hasNext = ($answeredQ < $totalQ);

// ✅ Return JSON
echo json_encode([
    "success" => true,
    "message" => $message,
    "next"    => $hasNext
]);
?>
