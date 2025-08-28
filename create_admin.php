<?php
// ===== FILE: create_admin.php =====
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to DB
$conn = new mysqli("sql102.infinityfree.com", "if0_39580483", "RuDr2004", "if0_39580483_omniquiz");
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Admin credentials
$name = "Admin";
$email = "admin@example.com";
$password = "RuDr@2004";
$role = "admin";

// Hash password properly
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check if admin already exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo "⚠️ Admin already exists. No action taken.";
    $check->close();
    $conn->close();
    exit;
}
$check->close();

// Insert new admin
$stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

if ($stmt->execute()) {
    echo "✅ Admin user created successfully!<br>";
    echo "📧 Email: <b>$email</b><br>";
    echo "🔑 Password: <b>$password</b><br>";
} else {
    echo "❌ Failed to insert admin: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
