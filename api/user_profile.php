<?php
// ===============================================
// DEBUG (hapus jika sudah produksi)
// ===============================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ===============================================
require 'config.php';   // WAJIB berisi $conn + getJsonInput() + jsonResponse()
// ===============================================


// ===============================================
// GET — Ambil data profil user
// ===============================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $user_id = $_GET['user_id'] ?? null;
    if (!$user_id) jsonResponse(400, ['error' => 'user_id wajib']);

    $stmt = $conn->prepare("
        SELECT user_id, last_period_start, period_length, cycle_length_range,
               regularity, pain_level, created_at, updated_at
        FROM user_profile
        WHERE user_id = ?
        LIMIT 1
    ");
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    // belum pernah screening
    if (!$row) jsonResponse(200, ['has_profile' => false]);

    $row['has_profile'] = true;

    jsonResponse(200, $row);
}


// ===============================================
// POST — Simpan screening (based on answers[])
// ===============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = getJsonInput();

    if (!isset($input['user_id']) || !isset($input['answers'])) {
        jsonResponse(400, ['success'=>false, 'error'=>'user_id dan answers wajib']);
    }

    $user_id = $input['user_id'];
    $answers = $input['answers'];

    if (!is_array($answers) || count($answers) < 6) {
        jsonResponse(400, ['success'=>false, 'error'=>'answers harus berisi 6 item']);
    }

    // ===============================================
    // KONVERSI ANSWERS → FIELD DATABASE
    // ===============================================

    // 1 — last_period_start
    switch ($answers[1]) {
        case 0:  $last_period_start = date('Y-m-d', strtotime('-3 days')); break;
        case 1:  $last_period_start = date('Y-m-d', strtotime('-10 days')); break;
        case 2:  $last_period_start = date('Y-m-d', strtotime('-25 days')); break;
        default: $last_period_start = date('Y-m-d', strtotime('-30 days')); break;
    }

    // 2 — period_length
    $period_length_map = [2, 4, 6, 8];
    $period_length = $period_length_map[$answers[2]] ?? 5;

    // 3 — cycle_length_range (enum)
    $cycle_range_map = ['<25', '25-30', '>30', 'tidak_pasti'];
    $cycle_length_range = $cycle_range_map[$answers[3]] ?? '25-30';

    // 4 — regularitas
    $regularity_map = ['teratur', 'kadang_telat', 'sangat_tidak_teratur'];
    $regularity = $regularity_map[$answers[4]] ?? 'teratur';

    // 5 — tingkat nyeri
    $pain_map = ['ringan', 'sedang', 'berat'];
    $pain_level = $pain_map[$answers[5]] ?? 'ringan';

    // ===============================================
    // CEK USER ADA
    // ===============================================
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $resUser = $stmt->get_result();

    if ($resUser->num_rows === 0) {
        jsonResponse(404, ['success'=>false, 'error'=>'User tidak ditemukan']);
    }

    // ===============================================
    // INSERT / UPDATE user_profile
    // ===============================================
    $stmt = $conn->prepare("SELECT user_id FROM user_profile WHERE user_id = ? LIMIT 1");
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $exists = $stmt->get_result()->num_rows > 0;

    if ($exists) {
        $sql = "UPDATE user_profile
                SET last_period_start = ?,
                    period_length     = ?,
                    cycle_length_range= ?,
                    regularity        = ?,
                    pain_level        = ?,
                    updated_at        = NOW()
                WHERE user_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'sissss',
            $last_period_start,
            $period_length,
            $cycle_length_range,
            $regularity,
            $pain_level,
            $user_id
        );
        $stmt->execute();

    } else {
        $sql = "INSERT INTO user_profile
                (user_id, last_period_start, period_length, cycle_length_range,
                regularity, pain_level, created_at, updated_at)
                VALUES (?,?,?,?,?,?,NOW(),NOW())";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            'ssisss',
            $user_id,
            $last_period_start,
            $period_length,
            $cycle_length_range,
            $regularity,
            $pain_level
        );
        $stmt->execute();
    }

    // ===============================================
    // INSERT period_cycles (riwayat siklus)
    // ===============================================
    $cycleId = uniqid('C');
    $end_date = null; // bisa diisi otomatis jika mau
    $note = null;

    $sql = "INSERT INTO period_cycles
            (id, user_id, start_date, end_date, cycle_length, period_length, note, created_at)
            VALUES (?,?,?,?,?,?,?,NOW())";

    $stmt = $conn->prepare($sql);
    // untuk cycle_length, ambil angka default dari mapping (misalnya 28 hari)
    $cycle_length_map = [24, 28, 30, 32];
    $cycle_length = $cycle_length_map[$answers[3]] ?? 28;

    $stmt->bind_param(
        'sssiiis',
        $cycleId,
        $user_id,
        $last_period_start,
        $end_date,
        $cycle_length,
        $period_length,
        $note
    );
    $stmt->execute();

    // ===============================================
    // RESPONSE
    // ===============================================
    jsonResponse(200, [
        'success' => true,
        'message' => 'Screening tersimpan',
        'user_id' => $user_id,
        'cycle_id'=> $cycleId
    ]);
}


// ===============================================
jsonResponse(405, ['error' => 'Method not allowed']);
// ===============================================
