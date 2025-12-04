<?php
session_start();
require 'classes/Database.php';

$db   = new Database();
$conn = $db->getConnection();

$email    = trim($_POST['email']);
$password = trim($_POST['password']);

$sql = "SELECT * FROM users WHERE email = ? AND role = 'admin' LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: login.php?error=Admin tidak ditemukan");
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    header("Location: login.php?error=Password salah");
    exit;
}

// login sukses
$_SESSION['admin_id']   = $user['id'];
$_SESSION['admin_name'] = $user['name'];
$_SESSION['admin_email']= $user['email'];

// --- FIX BAGIAN INI ---
$upd = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
$upd->bind_param("s", $user['id']);
$upd->execute();

header("Location: pages/dashboard.php");
exit;
?>
