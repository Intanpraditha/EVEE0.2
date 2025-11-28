<?php
// api/mood_logs.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['user_id'] ?? null;
    if (!$user_id) {
        jsonResponse(400, ['error' => 'user_id wajib']);
    }

    $sql = "SELECT ml.id, ml.date, ml.time, ml.note, ml.created_at,
                   m.name AS mood_name, m.mood_tag, m.icon
            FROM mood_logs ml
            JOIN moods m ON m.id = ml.mood_id
            WHERE ml.user_id = ?
            ORDER BY ml.date DESC, ml.time DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    jsonResponse(200, $data);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input  = getJsonInput();
    $user_id = $input['user_id'] ?? null;
    $mood_id = $input['mood_id'] ?? null;
    $note    = $input['note']    ?? null;

    if (!$user_id || !$mood_id) {
        jsonResponse(400, ['error' => 'user_id dan mood_id wajib']);
    }

    $date = date('Y-m-d');
    $time = date('H:i:s');

    $sql = "INSERT INTO mood_logs (id, user_id, mood_id, date, time, note, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    // ID bisa auto, tapi kalau kamu pakai VARCHAR manual, ganti di sini
    $id  = uniqid('ML');

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssss', $id, $user_id, $mood_id, $date, $time, $note);
    $ok = $stmt->execute();

    if (!$ok) {
        jsonResponse(500, ['error' => 'Gagal simpan mood log']);
    }

    jsonResponse(201, ['success' => true, 'id' => $id]);
}

jsonResponse(405, ['error' => 'Method not allowed']);
