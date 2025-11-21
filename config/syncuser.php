<?php
header('Content-Type: application/json');
require_once 'koneksi.php';

$uid   = $_POST['uid'] ?? '';
$email = $_POST['email'] ?? '';
$name  = $_POST['name'] ?? 'User';

if (empty($uid) || empty($email)) {
    echo json_encode(['status'=>'error','message'=>'Data tidak lengkap']);
    exit;
}

// Cek user di DB
$query = $koneksi->prepare("SELECT * FROM user WHERE uid = ?");
$query->bind_param("s", $uid);
$query->execute();
$result = $query->get_result();

if ($result->num_rows == 0) {
    // Insert user baru
    $insert = $koneksi->prepare("INSERT INTO user (uid, email, name, role, last_login) VALUES (?, ?, ?, 'user', NOW())");
    $insert->bind_param("sss", $uid, $email, $name);
    $insert->execute();
} else {
    // Update last login
    $update = $koneksi->prepare("UPDATE user SET last_login=NOW() WHERE uid=?");
    $update->bind_param("s", $uid);
    $update->execute();
}

echo json_encode(['status'=>'success']);
?>
