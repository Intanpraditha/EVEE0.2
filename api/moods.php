<?php
// api/moods.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $res = $conn->query("SELECT id, name, description, icon, mood_tag FROM moods ORDER BY name ASC");
    $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    jsonResponse(200, $data);
}

jsonResponse(405, ['error' => 'Method not allowed']);
