<?php
require 'config.php';   // koneksi, json helper, dsb

header('Content-Type: application/json; charset=utf-8');
error_reporting(E_ALL);
ini_set('display_errors', 0); // jangan tampilkan HTML error ke output

function jsonResponse($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(405, ['error' => 'Method not allowed']);
}

$user_id = $_GET['user_id'] ?? null;
if (!$user_id) {
    jsonResponse(400, ['error' => 'user_id wajib']);
}

/* 1. CEK USER */
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ? LIMIT 1");
if (!$stmt) jsonResponse(500, ['error' => 'Prepare gagal: '.$conn->error]);
$stmt->bind_param('s', $user_id);
if (!$stmt->execute()) jsonResponse(500, ['error' => 'Execute gagal: '.$stmt->error]);
$resUser = $stmt->get_result();
if (!$resUser) jsonResponse(500, ['error' => 'get_result gagal: '.$stmt->error]);
if ($resUser->num_rows === 0) jsonResponse(404, ['error' => 'User tidak ditemukan']);
$user = $resUser->fetch_assoc();

/* 2. PROFIL SIKLUS */
$stmt = $conn->prepare("
    SELECT last_period_start, period_length, cycle_length,
           regularity, pain_level
    FROM user_profile
    WHERE user_id = ?
    LIMIT 1
");
if (!$stmt) jsonResponse(500, ['error' => 'Prepare gagal: '.$conn->error]);
$stmt->bind_param('s', $user_id);
if (!$stmt->execute()) jsonResponse(500, ['error' => 'Execute gagal: '.$stmt->error]);
$resProfile = $stmt->get_result();
if (!$resProfile) jsonResponse(500, ['error' => 'get_result gagal: '.$stmt->error]);
$profile = $resProfile->fetch_assoc();

if (!$profile) {
    jsonResponse(200, [
        'success' => true,
        'needs_screening' => true,
        'message' => 'User belum mengisi screening awal',
        'user' => $user,
        'cycle' => null,
        'today_mood' => null,
        'upcoming_activities' => [],
        'recommended_articles' => []
    ]);
}

/* 3. HITUNG SIKLUS */
$last_start     = $profile['last_period_start'];
$period_length  = (int)$profile['period_length'];
$cycle_length   = (int)$profile['cycle_length'];

$lastStart      = new DateTime($last_start);
$today          = new DateTime();
$cycleDay       = $lastStart->diff($today)->days + 1;

$nextStart      = clone $lastStart;
$nextStart->modify("+{$cycle_length} days");
$nextPeriodStr  = $nextStart->format("Y-m-d");

/* 4. FASE */
$phase = "folikular";
if ($cycleDay <= $period_length) {
    $phase = "menstruasi";
} elseif ($cycleDay == 14) {
    $phase = "ovulasi";
} elseif ($cycleDay > $period_length && $cycleDay < 14) {
    $phase = "folikular";
} elseif ($cycleDay > 14) {
    $phase = "luteal";
}

/* 5. MOOD */
$stmt = $conn->prepare("
    SELECT ml.id, ml.mood_id, m.name, m.icon, m.mood_tag
    FROM mood_logs ml
    JOIN moods m ON m.id = ml.mood_id
    WHERE ml.user_id = ? AND ml.date = CURDATE()
    LIMIT 1
");
if (!$stmt) jsonResponse(500, ['error' => 'Prepare gagal: '.$conn->error]);
$stmt->bind_param('s', $user_id);
if (!$stmt->execute()) jsonResponse(500, ['error' => 'Execute gagal: '.$stmt->error]);
$resMood = $stmt->get_result();
$todayMood = $resMood ? $resMood->fetch_assoc() : null;

/* 6. AKTIVITAS */
$stmt = $conn->prepare("
    SELECT id, title, event_date, event_time
    FROM activities
    WHERE user_id = ? 
      AND event_date >= CURDATE()
    ORDER BY event_date ASC, event_time ASC
    LIMIT 3
");
if (!$stmt) jsonResponse(500, ['error' => 'Prepare gagal: '.$conn->error]);
$stmt->bind_param('s', $user_id);
if (!$stmt->execute()) jsonResponse(500, ['error' => 'Execute gagal: '.$stmt->error]);
$resAct = $stmt->get_result();
$activities = $resAct ? $resAct->fetch_all(MYSQLI_ASSOC) : [];

/* 7. ARTIKEL */
$stmt = $conn->prepare("
    SELECT id, title, link, image, phase
    FROM articles
    WHERE phase IS NULL OR phase = ?
    ORDER BY created_at DESC
    LIMIT 3
");
if (!$stmt) jsonResponse(500, ['error' => 'Prepare gagal: '.$conn->error]);
$stmt->bind_param('s', $phase);
if (!$stmt->execute()) jsonResponse(500, ['error' => 'Execute gagal: '.$stmt->error]);
$resArt = $stmt->get_result();
$articles = $resArt ? $resArt->fetch_all(MYSQLI_ASSOC) : [];

/* 8. RESPONSE */
jsonResponse(200, [
    'success' => true,
    'user' => $user,
    'cycle' => [
        'last_start'        => $last_start,
        'period_length'     => $period_length,
        'cycle_length'      => $cycle_length,
        'cycle_day'         => $cycleDay,
        'today_phase'       => $phase,
        'next_period_start' => $nextPeriodStr
    ],
    'today_mood'          => $todayMood,
    'upcoming_activities' => $activities,
    'recommended_articles'=> $articles
]);
