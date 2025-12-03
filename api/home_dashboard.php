<?php
require 'config.php';   // sudah ada fungsi jsonResponse()

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse(405, ['error' => 'Method not allowed']);
}

$user_id = $_GET['user_id'] ?? null;
if (!$user_id) {
    jsonResponse(400, ['error' => 'user_id wajib']);
}

/* 1. CEK USER */
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param('s', $user_id);
$stmt->execute();
$resUser = $stmt->get_result();
if ($resUser->num_rows === 0) {
    jsonResponse(404, ['error' => 'User tidak ditemukan']);
}
$user = $resUser->fetch_assoc();

/* 2. PROFIL SIKLUS dari period_cycles (log terakhir) */
$stmt = $conn->prepare("
    SELECT start_date AS last_period_start,
           period_length,
           cycle_length AS cycle_length_range
    FROM period_cycles
    WHERE user_id = ?
    ORDER BY start_date DESC
    LIMIT 1
");
$stmt->bind_param('s', $user_id);
$stmt->execute();
$resProfile = $stmt->get_result();
$profile = $resProfile->fetch_assoc();

/* Jika belum ada data siklus */
if (!$profile || empty($profile['last_period_start'])) {
    jsonResponse(200, [
        'success'            => true,
        'needs_screening'    => true,
        'message'            => 'Belum ada data siklus',
        'user'               => $user,
        'cycle'              => null,
        'today_mood'         => null,
        'upcoming_activities'=> [],
        'recommended_articles'=> []
    ]);
}

/* 3. HITUNG SIKLUS + HARI MENUJU MENSTRUASI */
$last_start    = $profile['last_period_start'];
$period_length = (int)$profile['period_length'];          // lama haid
$cycle_range   = (int)$profile['cycle_length_range'];     // panjang siklus (hari)

// fallback kalau kosong / 0
if ($cycle_range <= 0) {
    $cycle_range = 28;
}

$lastStart = new DateTime($last_start);
$today     = new DateTime();

// jumlah hari sejak haid terakhir (1 = hari pertama haid)
$totalDiffDays = $lastStart->diff($today)->days + 1;

// normalisasi ke dalam satu siklus
// misal diff=45, cycle_range=28 → cycle_day = 18
$cycleDay = (($totalDiffDays - 1) % $cycle_range) + 1;

// lagi haid atau tidak
$isMenstruating = ($cycleDay >= 1 && $cycleDay <= $period_length);

// cari prediksi tanggal haid berikutnya (>= hari ini)
$nextStart = clone $lastStart;
while ($nextStart <= $today) {
    $nextStart->modify("+{$cycle_range} days");
}
$nextPeriodStr = $nextStart->format("Y-m-d");

// kalau lagi haid → 0 hari, kalau tidak → selisih ke nextStart
if ($isMenstruating) {
    $daysToNext = 0;
} else {
    $daysToNext = $today->diff($nextStart)->days;
}

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

/* 5. MOOD TERBARU HARI INI */
$stmt = $conn->prepare("
    SELECT ml.id, ml.mood_id, m.name, m.icon, m.mood_tag
    FROM mood_logs ml
    JOIN moods m ON m.id = ml.mood_id
    WHERE ml.user_id = ? AND ml.date = CURDATE()
    ORDER BY ml.time DESC, ml.created_at DESC
    LIMIT 1
");
$stmt->bind_param('s', $user_id);
$stmt->execute();
$resMood   = $stmt->get_result();
$todayMood = $resMood ? $resMood->fetch_assoc() : null;

/* 6. AKTIVITAS */
$stmt = $conn->prepare("
    SELECT id, title, date, start_time
    FROM activities
    WHERE user_id = ? 
      AND date >= CURDATE()
    ORDER BY date ASC, start_time ASC
    LIMIT 3
");
$stmt->bind_param('s', $user_id);
$stmt->execute();
$resAct      = $stmt->get_result();
$activities  = $resAct ? $resAct->fetch_all(MYSQLI_ASSOC) : [];

/* 7. ARTIKEL */
$stmt = $conn->prepare("
    SELECT id, title, link, image, phase
    FROM articles
    WHERE phase IS NULL OR phase = ?
    ORDER BY created_at DESC
    LIMIT 3
");
$stmt->bind_param('s', $phase);
$stmt->execute();
$resArt    = $stmt->get_result();
$articles  = $resArt ? $resArt->fetch_all(MYSQLI_ASSOC) : [];

/* 8. RESPONSE */
jsonResponse(200, [
    'success' => true,
    'user'    => $user,
    'cycle'   => [
        'last_start'          => $last_start,
        'period_length'       => $period_length,
        'cycle_length_range'  => $cycle_range,
        'cycle_day'           => $cycleDay,
        'today_phase'         => $phase,
        'next_period_start'   => $nextPeriodStr,
        'is_menstruating'     => $isMenstruating,
        'days_to_next_period' => $daysToNext
    ],
    'today_mood'           => $todayMood,
    'upcoming_activities'  => $activities,
    'recommended_articles' => $articles
]);
