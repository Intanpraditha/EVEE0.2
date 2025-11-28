<?php
// api/login.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(405, ['error' => 'Method not allowed']);
}

$input = getJsonInput();
$email    = trim($input['email']    ?? '');
$password = trim($input['password'] ?? '');

if ($email === '' || $password === '') {
    jsonResponse(400, ['error' => 'Email dan password wajib diisi']);
}

$sql  = "SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$res  = $stmt->get_result();

if ($res->num_rows === 0) {
    jsonResponse(401, ['error' => 'User tidak ditemukan']);
}

$user = $res->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    jsonResponse(401, ['error' => 'Password salah']);
}

// nanti kalau mau pakai JWT/token, bisa generate di sini
jsonResponse(200, [
    'id'    => $user['id'],
    'name'  => $user['name'],
    'email' => $user['email'],
    'role'  => $user['role'],
]);
