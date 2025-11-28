<?php
// api/user_profile.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = getJsonInput();
    $user_id           = $input['user_id']           ?? null;
    $last_period_start = $input['last_period_start'] ?? null;
    $period_length     = $input['period_length']     ?? null;
    $cycle_range       = $input['cycle_length_range']?? null;
    $regularity        = $input['regularity']        ?? null;
    $pain_level        = $input['pain_level']        ?? null;

    if (!$user_id) {
        jsonResponse(400, ['error' => 'user_id wajib']);
    }

    // cek apakah profile sudah ada
    $stmt = $conn->prepare("SELECT user_id FROM user_profile WHERE user_id = ? LIMIT 1");
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows) {
        // update
        $sql = "UPDATE user_profile
                SET last_period_start = ?, period_length = ?, 
                    cycle_length_range = ?, regularity = ?, pain_level = ?, 
                    updated_at = NOW()
                WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'sissss',
            $last_period_start, $period_length, $cycle_range,
            $regularity, $pain_level, $user_id
        );
        $ok = $stmt->execute();
    } else {
        // insert
        $sql = "INSERT INTO user_profile
                (user_id, last_period_start, period_length, cycle_length_range, regularity, pain_level, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'ssisss',
            $user_id, $last_period_start, $period_length,
            $cycle_range, $regularity, $pain_level
        );
        $ok = $stmt->execute();
    }

    if (!$ok) {
        jsonResponse(500, ['error' => 'Gagal simpan profile']);
    }

    jsonResponse(200, ['success' => true]);
}

jsonResponse(405, ['error' => 'Method not allowed']);
