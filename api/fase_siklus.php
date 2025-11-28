<?php
// api/fase_siklus.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $res = $conn->query("SELECT id_fase, nama_fase, rentang_hari FROM fase_siklus ORDER BY id_fase ASC");
    $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    jsonResponse(200, $data);
}

jsonResponse(405, ['error'=>'Method not allowed']);
