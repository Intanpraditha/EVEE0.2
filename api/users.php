<?php
// api/users.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        jsonResponse(400, ['error' => 'Parameter id wajib']);
    }

    // ambil user
    $sql = "SELECT id, name, email, role, birth_date, created_at
            FROM users
            WHERE id = ?
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $resUser = $stmt->get_result();
    if ($resUser->num_rows === 0) {
        jsonResponse(404, ['error' => 'User tidak ditemukan']);
    }
    $user = $resUser->fetch_assoc();

    // ambil profile (optional)
    $sql = "SELECT user_id, last_period_start, period_length,
                   cycle_length_range, regularity, pain_level
            FROM user_profile
            WHERE user_id = ?
            LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $resProf = $stmt->get_result();
    $profile = $resProf->num_rows ? $resProf->fetch_assoc() : null;

    jsonResponse(200, [
        'user'    => $user,
        'profile' => $profile,
    ]);
}

jsonResponse(405, ['error' => 'Method not allowed']);
