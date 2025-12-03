<?php
require 'config.php'; // pastikan ada fungsi jsonResponse() dan getJsonInput()

// Matikan error ke output, log saja ke file
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

/* =========================
   GET: Ambil semua aktivitas user
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['user_id'] ?? null;
    if (!$user_id) {
        jsonResponse(400, ['error' => 'user_id wajib']);
    }

    $sql = "SELECT id, title, description, date, start_time, end_time,
                   category, priority, status, created_at
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

/* =========================
   POST: Simpan aktivitas baru
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getJsonInput();

    $user_id     = $input['user_id']     ?? null;
    $title       = $input['title']       ?? null;
    $description = $input['description'] ?? '';
    $date        = $input['date']        ?? null;
    $start_time  = $input['start_time']  ?? null;
    $end_time    = $input['end_time']    ?? null;
    $category    = $input['category']    ?? 'Lainnya';
    $priority    = $input['priority']    ?? 'Sedang';
    $status      = $input['status']      ?? 'Belum';

    // Validasi field wajib
    if (!$user_id || !$title || !$date || !$start_time) {
        jsonResponse(400, ['error' => 'user_id, title, date, start_time wajib']);
    }

    $id = uniqid('AC');

    $sql = "INSERT INTO activities
            (id, user_id, title, description, date, start_time, end_time,
             category, priority, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ssssssssss',
        $id, $user_id, $title, $description, $date,
        $start_time, $end_time, $category, $priority, $status
    );
    $ok = $stmt->execute();

    if (!$ok) {
    jsonResponse(500, [
        'error' => 'Gagal simpan aktivitas',
        'detail' => $stmt->error
    ]);
}

    jsonResponse(201, ['success' => true, 'id' => $id]);
}

/* =========================
   PUT: Update status aktivitas
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = getJsonInput();

    $id     = $input['id']     ?? null;
    $status = $input['status'] ?? null;

    if (!$id || !$status) {
        jsonResponse(400, ['error' => 'id dan status wajib']);
    }

    $sql = "UPDATE activities SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $status, $id);
    $ok = $stmt->execute();

    if (!$ok) {
        jsonResponse(500, ['error' => 'Gagal update status', 'detail' => $stmt->error]);
    }

    jsonResponse(200, ['success' => true]);
}

/* =========================
   METHOD tidak diizinkan
   ========================= */
jsonResponse(405, ['error' => 'Method not allowed']);
