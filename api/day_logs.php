<?php
// api/day_logs.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['user_id'] ?? null;
    if (!$user_id) jsonResponse(400, ['error'=>'user_id wajib']);

    $sql = "SELECT dl.*, pc.start_date AS period_start, pc.end_date AS period_end
            FROM day_logs dl
            LEFT JOIN period_cycles pc ON pc.id = dl.period_id
            WHERE dl.user_id = ?
            ORDER BY dl.date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    jsonResponse(200, $data);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getJsonInput();
    $user_id   = $input['user_id']   ?? null;
    $period_id = $input['period_id'] ?? null;
    $date      = $input['date']      ?? date('Y-m-d');
    $phase     = $input['phase']     ?? null;
    $symptoms  = $input['symptoms']  ?? null;
    $flow      = $input['flow']      ?? null;

    if (!$user_id) jsonResponse(400, ['error'=>'user_id wajib']);

    $id = uniqid('D');

    $sql = "INSERT INTO day_logs (id, user_id, period_id, date, phase, symptoms, flow, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssss', $id, $user_id, $period_id, $date, $phase, $symptoms, $flow);
    $ok = $stmt->execute();

    if (!$ok) jsonResponse(500, ['error'=>'Gagal simpan day log']);

    jsonResponse(201, ['success'=>true, 'id'=>$id]);
}

jsonResponse(405, ['error'=>'Method not allowed']);
