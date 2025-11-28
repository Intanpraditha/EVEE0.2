<?php
// api/notifications.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['user_id'] ?? null;
    if (!$user_id) jsonResponse(400, ['error'=>'user_id wajib']);

    $sql = "SELECT id, user_id, type, message, status, created_at,
                   related_period_id, related_activity_id
            FROM notifications
            WHERE user_id = ?
            ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    jsonResponse(200, $data);
}

jsonResponse(405, ['error'=>'Method not allowed']);
