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
            ORDER BY ml.date DESC, ml.time DESC, ml.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    jsonResponse(200, $data);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input   = getJsonInput();
    $user_id = $input['user_id'] ?? null;
    $mood_id = $input['mood_id'] ?? null;
    $note    = $input['note']    ?? null;

    if (!$user_id || !$mood_id) {
        jsonResponse(400, ['error' => 'user_id dan mood_id wajib']);
    }

    // generate id manual karena kolom id bukan AUTO_INCREMENT
    $id = uniqid('ML');

    $sql = "INSERT INTO mood_logs (id, user_id, mood_id, date, time, note, created_at)
            VALUES (?, ?, ?, CURDATE(), CURTIME(), ?, NOW())";

    $stmt = $conn->prepare($sql);
    // ada 4 placeholder: id, user_id, mood_id, note
    $stmt->bind_param('ssss', $id, $user_id, $mood_id, $note);
    $ok = $stmt->execute();

    if (!$ok) {
        jsonResponse(500, ['error' => 'Gagal simpan mood log', 'detail' => $stmt->error]);
    }

    // Ambil mood terakhir hari ini untuk dikembalikan
    $sqlLast = "SELECT ml.id, ml.mood_id, m.name, m.icon, m.mood_tag
                FROM mood_logs ml
                JOIN moods m ON ml.mood_id = m.id
                WHERE ml.user_id = ? AND ml.date = CURDATE()
                ORDER BY ml.time DESC, ml.created_at DESC
                LIMIT 1";
    $stmt2 = $conn->prepare($sqlLast);
    $stmt2->bind_param('s', $user_id);
    $stmt2->execute();
    $res2 = $stmt2->get_result();
    $todayMood = $res2 ? $res2->fetch_assoc() : null;

    jsonResponse(201, ['success' => true, 'today_mood' => $todayMood]);
}

jsonResponse(405, ['error' => 'Method not allowed']);
