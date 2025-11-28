<?php
// api/user_articles.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['user_id'] ?? null;
    if (!$user_id) jsonResponse(400, ['error'=>'user_id wajib']);

    $sql = "SELECT ua.id, ua.article_id, ua.read_at, ua.saved,
                   a.title, a.image, a.phase
            FROM user_articles ua
            JOIN articles a ON a.id = ua.article_id
            WHERE ua.user_id = ?
            ORDER BY ua.read_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    jsonResponse(200, $data);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getJsonInput();
    $user_id    = $input['user_id']    ?? null;
    $article_id = $input['article_id'] ?? null;
    $saved      = (int)($input['saved'] ?? 0);

    if (!$user_id || !$article_id) jsonResponse(400, ['error'=>'user_id & article_id wajib']);

    $id   = uniqid('UA');
    $now  = date('Y-m-d H:i:s');

    $sql = "INSERT INTO user_articles
            (id, user_id, article_id, read_at, saved, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssi', $id, $user_id, $article_id, $now, $saved);
    $ok = $stmt->execute();

    if (!$ok) jsonResponse(500, ['error'=>'Gagal simpan user_article']);

    jsonResponse(201, ['success'=>true, 'id'=>$id]);
}

jsonResponse(405, ['error'=>'Method not allowed']);
