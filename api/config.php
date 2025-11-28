<?php
// api/config.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // nanti bisa dibatasi domain tertentu
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require '../classes/Database.php';

$db   = new Database();
$conn = $db->getConnection();

function jsonResponse(int $code, $data) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function getJsonInput(): array {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}
