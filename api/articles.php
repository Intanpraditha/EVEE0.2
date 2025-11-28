<?php
// api/articles.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $phase = $_GET['phase'] ?? null;

    if ($phase) {
        $sql = "SELECT id, title, link, phase, image, created_at
                FROM articles
                WHERE phase = ?
                ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $phase);
        $stmt->execute();
        $res = $stmt->get_result();
    } else {
        $res = $conn->query("SELECT id, title, link, phase, image, created_at FROM articles ORDER BY created_at DESC");
    }

    $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    jsonResponse(200, $data);
}

jsonResponse(405, ['error'=>'Method not allowed']);
