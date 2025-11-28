<?php
// api/period_cycles.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['user_id'] ?? null;
    if (!$user_id) jsonResponse(400, ['error'=>'user_id wajib']);

    $sql = "SELECT id, user_id, start_date, end_date, cycle_length, period_length, note, created_at
            FROM period_cycles
            WHERE user_id = ?
            ORDER BY start_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    jsonResponse(200, $data);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getJsonInput();
    $user_id       = $input['user_id']       ?? null;
    $start_date    = $input['start_date']    ?? null;
    $end_date      = $input['end_date']      ?? null;
    $cycle_length  = $input['cycle_length']  ?? null;
    $period_length = $input['period_length'] ?? null;
    $note          = $input['note']          ?? null;

    if (!$user_id || !$start_date || !$end_date) {
        jsonResponse(400, ['error'=>'user_id, start_date, end_date wajib']);
    }

    $id = uniqid('C');

    $sql = "INSERT INTO period_cycles
            (id, user_id, start_date, end_date, cycle_length, period_length, note, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ssssiss',
        $id, $user_id, $start_date, $end_date,
        $cycle_length, $period_length, $note
    );
    $ok = $stmt->execute();

    if (!$ok) {
        jsonResponse(500, ['error'=>'Gagal simpan siklus']);
    }

    jsonResponse(201, ['success'=>true, 'id'=>$id]);
}

jsonResponse(405, ['error' => 'Method not allowed']);
