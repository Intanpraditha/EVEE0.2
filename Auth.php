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

// verifikasi password hashed
if (!password_verify($password, $user['password'])) {
    header("Location: login.php?error=Password salah");
    exit;
}

// login sukses → buat session
$_SESSION['admin_id']   = $user['id'];
$_SESSION['admin_name'] = $user['name'];
$_SESSION['admin_email']= $user['email'];

header("Location: pages/dashboard.php");
exit;
?>
