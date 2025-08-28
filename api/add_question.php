<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$conn = new mysqli("sql102.infinityfree.com", "if0_39580483", "RuDr2004", "if0_39580483_omniquiz");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "❌ DB Connection Failed"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$text = $conn->real_escape_string($data['text']);
$options_json = $conn->real_escape_string($data['options_json']);
$correct_option = $conn->real_escape_string($data['correct_option']);
$topic = intval($data['topic']);
$difficulty = intval($data['difficulty']);
$hint = $conn->real_escape_string($data['hint']);

$sql = "INSERT INTO questions (text, options_json, correct_option, topic_id, difficulty, hint)
        VALUES ('$text','$options_json','$correct_option','$topic','$difficulty','$hint')";

if ($conn->query($sql)) {
    echo json_encode(["success" => true, "message" => "✅ Question Added"]);
} else {
    echo json_encode(["success" => false, "message" => "❌ Error: " . $conn->error]);
}
?>
