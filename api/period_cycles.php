<?php
// api/period_cycles.php
require 'config.php';
require_once __DIR__ . '/../classes/Helper.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log_period_cycles.txt');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

/* =========================================
   GET  → ambil semua siklus user
   ========================================= */
if ($method === 'GET') {
    $user_id = $_GET['user_id'] ?? null;
    if (!$user_id) {
        jsonResponse(400, ['error' => 'user_id wajib']);
    }

    $sql = "
        SELECT id, user_id, start_date, end_date, 
               cycle_length, period_length, note, created_at
        FROM period_cycles
        WHERE user_id = ?
        ORDER BY start_date DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $res  = $stmt->get_result();
    $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

    jsonResponse(200, $rows);
}

/* =========================================
   POST → simpan siklus baru
   ========================================= */
if ($method === 'POST') {
    $input = getJsonInput();

    $user_id       = $input['user_id']       ?? null;
    $start_date    = $input['start_date']    ?? null;   // yyyy-mm-dd
    $end_date      = $input['end_date']      ?? null;   // yyyy-mm-dd
    $cycle_length  = $input['cycle_length']  ?? null;   // int
    $period_length = $input['period_length'] ?? null;   // int
    $note          = $input['note']          ?? null;

    if (!$user_id || !$start_date || !$end_date || !$cycle_length || !$period_length) {
        jsonResponse(400, ['error' => 'user_id, start_date, end_date, cycle_length, period_length wajib']);
    }

    // pastikan tipe integer
    $cycle_length  = (int)$cycle_length;
    $period_length = (int)$period_length;

    // generate id seperti di panel admin
    $id = Helper::generateId($conn, 'period_cycles', 'PC', 'id');

    $sql = "
        INSERT INTO period_cycles
        (id, user_id, start_date, end_date, cycle_length, period_length, note, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        jsonResponse(500, ['error' => 'Prepare gagal', 'detail' => $conn->error]);
    }

    $stmt->bind_param(
        'ssssiis',
        $id,
        $user_id,
        $start_date,
        $end_date,
        $cycle_length,
        $period_length,
        $note
    );

    $ok = $stmt->execute();
    if (!$ok) {
        jsonResponse(500, ['error' => 'Gagal simpan siklus', 'detail' => $stmt->error]);
    }

    jsonResponse(201, [
        'success' => true,
        'id'      => $id,
        'message' => 'Siklus tersimpan'
    ]);
}

jsonResponse(405, ['error' => 'Method not allowed']);
