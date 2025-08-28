<?php
// ===== FILE: api/start_quiz.php =====
$conn = new mysqli("sql102.infinityfree.com", "if0_39580483", "RuDr2004", "if0_39580483_omniquiz");
$conn->query("INSERT INTO quiz_sessions (user_id, start_time) VALUES (1, NOW())"); // Static user ID for testing
?>
