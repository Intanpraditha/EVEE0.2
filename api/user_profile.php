<?php
require 'config.php';   // di sini ada $conn, getJsonInput(), jsonResponse()

// ============ GET -> CEK PROFIL USER ============
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['user_id'] ?? null;

    if (!$user_id) {
        jsonResponse(400, ['error' => 'user_id wajib']);
    }

    $stmt = $conn->prepare("
        SELECT user_id, last_period_start, period_length, cycle_length,
               regularity, pain_level, created_at, updated_at
        FROM user_profile
        WHERE user_id = ?
        LIMIT 1
    ");
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if (!$row) {
        // belum pernah screening
        jsonResponse(200, ['has_profile' => false]);
    }

    $row['has_profile'] = true;
    jsonResponse(200, $row);
}

// ============ POST -> SIMPAN / UPDATE SCREENING ============
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getJsonInput();

    $user_id           = $input['user_id']           ?? null;
    $last_period_start = $input['last_period_start'] ?? null;   // Y-m-d
    $period_length     = $input['period_length']     ?? null;   // int
    $cycle_length      = $input['cycle_length']      ?? null;   // int
    $regularity        = $input['regularity']        ?? null;   // string
    $pain_level        = $input['pain_level']        ?? null;   // string

    // ====== VALIDASI SEDERHANA ======
    if (!$user_id || !$last_period_start || !$period_length || !$cycle_length) {
        jsonResponse(400, ['success' => false, 'error' => 'user_id, last_period_start, period_length, cycle_length wajib diisi']);
    }

    $period_length = (int)$period_length;
    $cycle_length  = (int)$cycle_length;

    if ($period_length <= 0 || $period_length > 15) {
        jsonResponse(400, ['success' => false, 'error' => 'period_length tidak valid']);
    }
    if ($cycle_length < 15 || $cycle_length > 60) {
        jsonResponse(400, ['success' => false, 'error' => 'cycle_length tidak valid']);
    }

    // default value kalau kosong
    if (!$regularity) $regularity = 'teratur';
    if (!$pain_level) $pain_level = 'ringan';

    // ====== CEK user ada atau tidak ======
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $resUser = $stmt->get_result();
    if ($resUser->num_rows === 0) {
        jsonResponse(404, ['success' => false, 'error' => 'User tidak ditemukan']);
    }

    // ====== CEK SUDAH PUNYA user_profile BELUM ======
    $stmt = $conn->prepare("SELECT user_id FROM user_profile WHERE user_id = ? LIMIT 1");
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $resProf = $stmt->get_result();
    $exists  = $resProf->num_rows > 0;

    if ($exists) {
        // UPDATE
        $sql = "UPDATE user_profile
                SET last_period_start = ?,
                    period_length     = ?,
                    cycle_length      = ?,
                    regularity        = ?,
                    pain_level        = ?,
                    updated_at        = NOW()
                WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'siisss',
            $last_period_start,
            $period_length,
            $cycle_length,
            $regularity,
            $pain_level,
            $user_id
        );
        $ok = $stmt->execute();
    } else {
        // INSERT
        $sql = "INSERT INTO user_profile
                (user_id, last_period_start, period_length, cycle_length,
                 regularity, pain_level, created_at, updated_at)
                VALUES (?,?,?,?,?,?,NOW(),NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'ssiiss',
            $user_id,
            $last_period_start,
            $period_length,
            $cycle_length,
            $regularity,
            $pain_level
        );
        $ok = $stmt->execute();
    }

    if (!$ok) {
        jsonResponse(500, ['success' => false, 'error' => 'Gagal menyimpan user_profile']);
    }

    // ====== SINKRONKAN DENGAN period_cycles ======
    // cek apakah sudah ada siklus dengan start_date itu
    $stmt = $conn->prepare("
        SELECT id FROM period_cycles
        WHERE user_id = ? AND start_date = ?
        LIMIT 1
    ");
    $stmt->bind_param('ss', $user_id, $last_period_start);
    $stmt->execute();
    $resCycle = $stmt->get_result();

    if ($resCycle->num_rows > 0) {
        // UPDATE siklus lama
        $rowCycle = $resCycle->fetch_assoc();
        $cycleId  = $rowCycle['id'];

        $sql = "UPDATE period_cycles
                SET period_length = ?,
                    cycle_length  = ?,
                    updated_at    = NOW()
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iis', $period_length, $cycle_length, $cycleId);
        $stmt->execute();
    } else {
        // INSERT siklus baru
        $cycleId = uniqid('C');
        $sql = "INSERT INTO period_cycles
                (id, user_id, start_date, end_date, period_length, cycle_length, created_at, updated_at)
                VALUES (?,?,?,NULL,?,?,NOW(),NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'sssii',
            $cycleId,
            $user_id,
            $last_period_start,
            $period_length,
            $cycle_length
        );
        $stmt->execute();
    }

    jsonResponse(200, [
        'success' => true,
        'message' => 'Screening tersimpan',
        'user_id' => $user_id,
        'cycle_id'=> $cycleId
    ]);
}

jsonResponse(405, ['error' => 'Method not allowed']);
