<?php
// api/selfcare_rules.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $res = $conn->query("
        SELECT id, phase, busy_level, pain_level, mood_tag, day_to_period, text, priority
        FROM selfcare_rules
        ORDER BY priority ASC
    ");
    $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    jsonResponse(200, $data);
}

jsonResponse(405, ['error'=>'Method not allowed']);
