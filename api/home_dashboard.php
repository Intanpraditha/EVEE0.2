<?php
require 'config.php';   // koneksi, json helper, dsb

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(405, ['error' => 'Method not allowed']);
}

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    jsonResponse(400, ['error' => 'user_id wajib']);
}

/* ============================================================
   1. CEK USER ADA ATAU TIDAK
   ============================================================ */
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param('s', $user_id);
$stmt->execute();
$resUser = $stmt->get_result();

if ($resUser->num_rows === 0) {
    jsonResponse(404, ['error' => 'User tidak ditemukan']);
}

$user = $resUser->fetch_assoc();

/* ============================================================
   2. AMBIL PROFIL SIKLUS USER (SCREENING)
   ============================================================ */
$stmt = $conn->prepare("
    SELECT last_period_start, period_length, cycle_length,
           regularity, pain_level
    FROM user_profile
    WHERE user_id = ?
    LIMIT 1
");
$stmt->bind_param('s', $user_id);
$stmt->execute();
$resProfile = $stmt->get_result();

$profile = $resProfile->fetch_assoc();

if (!$profile) {
    jsonResponse(200, [
        'needs_screening' => true,
        'message' => 'User belum mengisi screening awal'
    ]);
}

/* ============================================================
   3. HITUNG PREDIKSI SIKLUS
   ============================================================ */
$last_start     = $profile['last_period_start'];
$period_length  = (int)$profile['period_length'];
$cycle_length   = (int)$profile['cycle_length'];

$lastStart      = new DateTime($last_start);
$today          = new DateTime();

$cycleDay = $lastStart->diff($today)->days + 1;   // contoh: 1 = hari pertama haid

// Prediksi periode berikutnya
$nextStart = clone $lastStart;
$nextStart->modify("+{$cycle_length} days");

$todayStr = $today->format("Y-m-d");
$nextPeriodStr = $nextStart->format("Y-m-d");

/* ============================================================
   4. TENTUKAN FASE SIKLUS HARI INI
   ============================================================ */
$phase = "folikular"; // default

if ($cycleDay <= $period_length) {
    $phase = "menstruasi";
} else if ($cycleDay == 14) {
    $phase = "ovulasi";
} else if ($cycleDay > $period_length && $cycleDay < 14) {
    $phase = "folikular";
} else if ($cycleDay > 14) {
    $phase = "luteal";
}

/* ============================================================
   5. MOOD HARI INI
   ============================================================ */
$stmt = $conn->prepare("
    SELECT ml.id, ml.mood_id, m.name, m.icon, m.mood_tag
    FROM mood_logs ml
    JOIN moods m ON m.id = ml.mood_id
    WHERE ml.user_id = ? AND ml.date = CURDATE()
    LIMIT 1
");
$stmt->bind_param('s', $user_id);
$stmt->execute();
$resMood = $stmt->get_result();
$todayMood = $resMood->fetch_assoc();

/* ============================================================
   6. AKTIVITAS TERDEKAT (3 TERATAS)
   ============================================================ */
$stmt = $conn->prepare("
    SELECT id, title, event_date, event_time
    FROM activities
    WHERE user_id = ? 
      AND event_date >= CURDATE()
    ORDER BY event_date ASC, event_time ASC
    LIMIT 3
");
$stmt->bind_param('s', $user_id);
$stmt->execute();
$activities = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* ============================================================
   7. REKOMENDASI ARTIKEL BERDASARKAN FASE
   ============================================================ */
$stmt = $conn->prepare("
    SELECT id, title, link, image, phase
    FROM articles
    WHERE phase IS NULL OR phase = ?
    ORDER BY created_at DESC
    LIMIT 3
");
$stmt->bind_param('s', $phase);
$stmt->execute();
$articles = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* ============================================================
   8. RESPONSE LENGKAP UNTUK DASHBOARD
   ============================================================ */
jsonResponse(200, [
    'success' => true,
    'user' => $user,

    'cycle' => [
        'last_start'       => $last_start,
        'period_length'    => $period_length,
        'cycle_length'     => $cycle_length,
        'cycle_day'        => $cycleDay,
        'today_phase'      => $phase,
        'next_period_start'=> $nextPeriodStr
    ],

    'today_mood' => $todayMood ?: null,

    'upcoming_activities' => $activities,

    'recommended_articles' => $articles
]);
