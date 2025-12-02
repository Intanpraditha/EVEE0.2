<?php
require 'config.php'; // di sini ada $conn, getJsonInput(), jsonResponse()

$input = getJsonInput();

$name     = trim($input['name'] ?? '');
$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if ($name === '' || $email === '' || $password === '') {
    jsonResponse(400, ['success' => false, 'error' => 'name, email, password wajib diisi']);
}

// cek email sudah dipakai belum
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    jsonResponse(409, ['success' => false, 'error' => 'Email sudah terdaftar']);
}

// generate id user (sesuaikan sama cara kamu)
$id = uniqid('U'); // atau pakai Helper::generateId(...)

$hash = password_hash($password, PASSWORD_BCRYPT);

$sql = "INSERT INTO users (id, name, email, password, role, created_at)
        VALUES (?, ?, ?, ?, 'user', NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssss', $id, $name, $email, $hash);

if (!$stmt->execute()) {
    jsonResponse(500, ['success' => false, 'error' => 'Gagal registrasi']);
}

jsonResponse(201, [
    'success' => true,
    'user' => [
        'id'    => $id,
        'name'  => $name,
        'email' => $email,
        'role'  => 'user'
    ]
]);
