<?php
require 'config.php';

$input = getJsonInput();

$email    = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if ($email === '' || $password === '') {
    jsonResponse(400, ['success' => false, 'error' => 'email dan password wajib diisi']);
}

$sql = "SELECT id, name, email, password, role 
        FROM users 
        WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user || !password_verify($password, $user['password'])) {
    jsonResponse(401, ['success' => false, 'error' => 'Email atau password salah']);
}

jsonResponse(200, [
    'success' => true,
    'user' => [
        'id'    => $user['id'],
        'name'  => $user['name'],
        'email' => $user['email'],
        'role'  => $user['role'],
    ]
]);
