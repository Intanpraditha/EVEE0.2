<?php
// api/activities.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['user_id'] ?? null;
    if (!$user_id) jsonResponse(400, ['error'=>'user_id wajib']);

    $sql = "SELECT id, title, description, date, start_time, end_time, category, priority, status, created_at
            FROM activities
            WHERE user_id = ?
            ORDER BY date DESC, start_time ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    jsonResponse(200, $data);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getJsonInput();
    $user_id     = $input['user_id']     ?? null;
    $title       = $input['title']       ?? null;
    $description = $input['description'] ?? null;
    $date        = $input['date']        ?? null;
    $start_time  = $input['start_time']  ?? null;
    $end_time    = $input['end_time']    ?? null;
    $category    = $input['category']    ?? null;
    $priority    = $input['priority']    ?? 'Sedang';
    $status      = $input['status']      ?? 'Belum';

    if (!$user_id || !$title || !$date || !$start_time) {
        jsonResponse(400, ['error'=>'user_id, title, date, start_time wajib']);
    }

    $id = uniqid('AC');

    $sql = "INSERT INTO activities
            (id, user_id, title, description, date, start_time, end_time, category, priority, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ssssssssss',
        $id, $user_id, $title, $description, $date,
        $start_time, $end_time, $category, $priority, $status
    );
    $ok = $stmt->execute();

    if (!$ok) jsonResponse(500, ['error'=>'Gagal simpan aktivitas']);

    jsonResponse(201, ['success'=>true, 'id'=>$id]);
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = getJsonInput();
    $id     = $input['id']     ?? null;
    $status = $input['status'] ?? null;

    if (!$id || !$status) jsonResponse(400, ['error'=>'id dan status wajib']);

    $sql = "UPDATE activities SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $status, $id);
    $ok = $stmt->execute();

    if (!$ok) jsonResponse(500, ['error'=>'Gagal update status']);

    jsonResponse(200, ['success'=>true]);
}

jsonResponse(405, ['error'=>'Method not allowed']);
