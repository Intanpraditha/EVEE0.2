<?php
session_start();

require 'classes/Database.php';

$db   = new Database();
$conn = $db->getConnection();

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($name === '' || $email === '' || $password === '') {
    header("Location: register.php?error=Semua field wajib diisi");
    exit;
}

// cek email sudah ada atau belum
$sql = "SELECT id FROM users WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    header("Location: register.php?error=Email sudah digunakan");
    exit;
}

// generate ID admin otomatis
// misal: "ADM001"
$prefix = "ADM";
$query = $conn->query("SELECT id FROM users WHERE id LIKE '$prefix%' ORDER BY id DESC LIMIT 1");

if ($query && $row = $query->fetch_assoc()) {
    $last = intval(substr($row['id'], strlen($prefix))) + 1;
} else {
    $last = 1;
}
$newId = $prefix . str_pad($last, 3, '0', STR_PAD_LEFT);

// hash password
$hashed = password_hash($password, PASSWORD_DEFAULT);

// insert admin
$sql = "INSERT INTO users (id, name, email, password, role, created_at)
        VALUES (?, ?, ?, ?, 'admin', NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $newId, $name, $email, $hashed);
$ok = $stmt->execute();

if (!$ok) {
    header("Location: register.php?error=Gagal menyimpan admin");
    exit;
}

header("Location: register.php?success=Berhasil membuat akun admin!");
exit;
?>
