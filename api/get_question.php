<?php
header('Content-Type: application/json');
$conn = new mysqli("sql102.infinityfree.com", "if0_39580483", "RuDr2004", "if0_39580483_omniquiz");

$data = json_decode(file_get_contents("php://input"), true);
$exclude = isset($data['exclude']) && is_array($data['exclude']) ? $data['exclude'] : [];

$where = '';
if (count($exclude) > 0) {
  $ids = implode(",", array_map('intval', $exclude));
  $where = "WHERE id NOT IN ($ids)";
}

$res = $conn->query("SELECT * FROM questions $where ORDER BY RAND() LIMIT 1");

if ($res && $res->num_rows > 0) {
  $q = $res->fetch_assoc();
  echo json_encode([
    "id" => $q["id"],
    "text" => $q["text"],
    "options" => json_decode($q["options_json"], true),
    "hint" => $q["hint"]
  ]);
} else {
  echo json_encode(["end" => true]); // ✅ No more questions
}
?>
