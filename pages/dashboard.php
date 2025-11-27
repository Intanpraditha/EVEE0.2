<?php
require '../includes/check_login.php';
require '../classes/Database.php';
require '../classes/Crud.php';

$db   = new Database();
$conn = $db->getConnection();

/* ========================
   FILTER PER-BAGIAN
   ======================== */

// Tahun untuk grafik PERTUMBUHAN USER
$userYear = isset($_GET['user_year']) ? (int)$_GET['user_year'] : (int)date('Y');

// Tahun untuk DISTRIBUSI SIKLUS (0 = semua tahun)
$phaseYear = isset($_GET['phase_year']) ? (int)$_GET['phase_year'] : 0;

// Tahun untuk ARTIKEL POPULER (0 = semua tahun)
$articleYear = isset($_GET['article_year']) ? (int)$_GET['article_year'] : 0;

// ===== Daftar tahun yang tersedia di masing-masing tabel =====

// users.created_at
$userYears = [];
$resYears = $conn->query("SELECT DISTINCT YEAR(created_at) AS y FROM users ORDER BY y DESC");
if ($resYears) {
    while ($row = $resYears->fetch_assoc()) {
        $userYears[] = (int)$row['y'];
    }
}
if (empty($userYears)) {
    $userYears[] = $userYear;
}

// day_logs.date
$phaseYears = [];
$resYears = $conn->query("SELECT DISTINCT YEAR(date) AS y FROM day_logs ORDER BY y DESC");
if ($resYears) {
    while ($row = $resYears->fetch_assoc()) {
        if ($row['y'] != null) {
            $phaseYears[] = (int)$row['y'];
        }
    }
}
$phaseYears = array_unique($phaseYears);
if (empty($phaseYears)) {
    $phaseYears[] = (int)date('Y');
}

// user_articles.read_at
$articleYears = [];
$resYears = $conn->query("SELECT DISTINCT YEAR(read_at) AS y FROM user_articles ORDER BY y DESC");
if ($resYears) {
    while ($row = $resYears->fetch_assoc()) {
        if ($row['y'] != null) {
            $articleYears[] = (int)$row['y'];
        }
    }
}
$articleYears = array_unique($articleYears);
if (empty($articleYears)) {
    $articleYears[] = (int)date('Y');
}

/* ========================
   SUMMARY CARDS
   ======================== */

// total user (role user saja)
$totalUsers = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='user'");
if ($res && $row = $res->fetch_assoc()) {
    $totalUsers = (int)$row['total'];
}

// total mood
$totalMoods = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM moods");
if ($res && $row = $res->fetch_assoc()) {
    $totalMoods = (int)$row['total'];
}

// total artikel
$totalArticles = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM articles");
if ($res && $row = $res->fetch_assoc()) {
    $totalArticles = (int)$row['total'];
}

// total selfcare rules
$totalRules = 0;
$res = $conn->query("SELECT COUNT(*) AS total FROM selfcare_rules");
if ($res && $row = $res->fetch_assoc()) {
    $totalRules = (int)$row['total'];
}

/* ========================
   GRAFIK PERTUMBUHAN PENGGUNA
   (user baru per bulan di tahun yg dipilih)
   ======================== */

$monthNames = [
    1 => 'Jan','Feb','Mar','Apr','Mei','Jun',
    'Jul','Agu','Sep','Okt','Nov','Des'
];
$usersPerMonth = array_fill(1, 12, 0);

$res = $conn->query("
    SELECT MONTH(created_at) AS m, COUNT(*) AS total
    FROM users
    WHERE YEAR(created_at) = {$userYear}
    GROUP BY m
");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $m = (int)$row['m'];
        if ($m >= 1 && $m <= 12) {
            $usersPerMonth[$m] = (int)$row['total'];
        }
    }
}
$chartUserLabels = array_values($monthNames);
$chartUserData   = array_values($usersPerMonth);

/* ========================
   DONUT: DISTRIBUSI FASE SIKLUS
   (bisa filter tahun, 0 = semua tahun)
   ======================== */

$phaseOrder   = ['menstruasi', 'folikular', 'luteal', 'ovulasi'];
$phaseCounts  = array_fill_keys($phaseOrder, 0);

$wherePhaseYear = '';
if ($phaseYear > 0) {
    $wherePhaseYear = "AND YEAR(date) = {$phaseYear}";
}

$res = $conn->query("
    SELECT phase, COUNT(*) AS total
    FROM day_logs
    WHERE phase IS NOT NULL AND phase <> ''
      {$wherePhaseYear}
    GROUP BY phase
");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $p = $row['phase'];
        if (isset($phaseCounts[$p])) {
            $phaseCounts[$p] = (int)$row['total'];
        }
    }
}

$chartPhaseLabels = array_keys($phaseCounts);
$chartPhaseData   = array_values($phaseCounts);

/* ========================
   ARTIKEL POPULER (TOP 3)
   (bisa filter tahun read_at, 0 = semua tahun)
   ======================== */

$popularArticles = [];
$whereArticleYear = '';
if ($articleYear > 0) {
    $whereArticleYear = "WHERE YEAR(ua.read_at) = {$articleYear}";
}

$sqlArticles = "
    SELECT a.id, a.title, a.image, a.phase,
           COUNT(ua.article_id) AS read_count
    FROM user_articles ua
    JOIN articles a ON a.id = ua.article_id
    {$whereArticleYear}
    GROUP BY ua.article_id
    ORDER BY read_count DESC
    LIMIT 3
";
$res = $conn->query($sqlArticles);
if ($res) {
    $popularArticles = $res->fetch_all(MYSQLI_ASSOC);
}

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';
?>

<style>
    /* Style lembut mirip mockup-mu */
    .card {
        border-radius: 20px;
        border: none;
        box-shadow: 0 8px 20px rgba(0,0,0,0.03);
    }
    .card-small {
        border-radius: 18px;
        border: none;
        box-shadow: 0 6px 16px rgba(0,0,0,0.03);
        height: 120px;
    }
    .stat-label {
        font-size: .85rem;
        color: #999;
    }
    .stat-value {
        font-size: 1.7rem;
        font-weight: 700;
        color: #333;
    }
    .stat-sub {
        font-size: .8rem;
        color: #aaa;
    }
    .chart-container {
        position: relative;
        height: 280px;
    }
    .article-thumb {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 14px;
        background: #f5f5f5;
    }
</style>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4 fw-semibold">Dashboard</h4>

            <!-- ===== SUMMARY CARDS ===== -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="card card-small p-3 d-flex flex-column justify-content-between">
                                <div class="stat-label">Total user</div>
                                <div class="stat-value"><?= $totalUsers ?></div>
                                <div class="stat-sub">Pengguna aplikasi</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-small p-3 d-flex flex-column justify-content-between">
                                <div class="stat-label">Total mood</div>
                                <div class="stat-value"><?= $totalMoods ?></div>
                                <div class="stat-sub">Pilihan mood yang tersedia</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-small p-3 d-flex flex-column justify-content-between">
                                <div class="stat-label">Total artikel</div>
                                <div class="stat-value"><?= $totalArticles ?></div>
                                <div class="stat-sub">Konten edukasi</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-small p-3 d-flex flex-column justify-content-between">
                                <div class="stat-label">Self-care rules</div>
                                <div class="stat-value"><?= $totalRules ?></div>
                                <div class="stat-sub">Aturan rekomendasi aktif</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== GRAFIK + DONUT ===== -->
            <div class="row g-3 mb-5">
                <div class="col-lg-8">
                    <div class="card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Pertumbuhan Pengguna</h6>
                            <!-- Filter tahun khusus grafik user -->
                            <form method="GET" class="d-flex align-items-center gap-2">
                                <!-- pertahankan filter lain -->
                                <input type="hidden" name="phase_year" value="<?= htmlspecialchars($phaseYear) ?>">
                                <input type="hidden" name="article_year" value="<?= htmlspecialchars($articleYear) ?>">

                                <select name="user_year" onchange="this.form.submit()"
                                        class="form-select form-select-sm" style="width:130px;">
                                    <?php foreach ($userYears as $y): ?>
                                        <option value="<?= $y ?>" <?= $userYear == $y ? 'selected' : '' ?>>
                                            <?= $y ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <canvas id="usersGrowthChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribusi siklus + filter tahun sendiri -->
                <div class="col-lg-4">
                    <div class="card p-3 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Distribusi siklus</h6>
                            <form method="GET" class="d-flex align-items-center gap-2">
                                <!-- pertahankan filter lain -->
                                <input type="hidden" name="user_year" value="<?= htmlspecialchars($userYear) ?>">
                                <input type="hidden" name="article_year" value="<?= htmlspecialchars($articleYear) ?>">

                                <select name="phase_year" onchange="this.form.submit()"
                                        class="form-select form-select-sm" style="width:140px;">
                                    <option value="0" <?= $phaseYear == 0 ? 'selected' : '' ?>>Semua tahun</option>
                                    <?php foreach ($phaseYears as $y): ?>
                                        <option value="<?= $y ?>" <?= $phaseYear == $y ? 'selected' : '' ?>>
                                            <?= $y ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                        <div class="chart-container mb-3">
                            <canvas id="phaseDonutChart"></canvas>
                        </div>
                        <div class="d-flex flex-wrap gap-2 justify-content-center small mt-3">
                            <span><span class="badge rounded-pill me-1" style="background:#E8445F;">&nbsp;</span>menstruasi</span>
                            <span><span class="badge rounded-pill me-1" style="background:#F49AA3;">&nbsp;</span>folikular</span>
                            <span><span class="badge rounded-pill me-1" style="background:#A8BF89;">&nbsp;</span>luteal</span>
                            <span><span class="badge rounded-pill me-1" style="background:#89B8C2;">&nbsp;</span>ovulasi</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ===== ARTIKEL POPULER (dengan filter tahun sendiri) ===== -->
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <div class="card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Artikel populer</h6>
                            <form method="GET" class="d-flex align-items-center gap-2">
                                <!-- pertahankan filter lain -->
                                <input type="hidden" name="user_year" value="<?= htmlspecialchars($userYear) ?>">
                                <input type="hidden" name="phase_year" value="<?= htmlspecialchars($phaseYear) ?>">

                                <select name="article_year" onchange="this.form.submit()"
                                        class="form-select form-select-sm" style="width:140px;">
                                    <option value="0" <?= $articleYear == 0 ? 'selected' : '' ?>>Semua tahun</option>
                                    <?php foreach ($articleYears as $y): ?>
                                        <option value="<?= $y ?>" <?= $articleYear == $y ? 'selected' : '' ?>>
                                            <?= $y ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>

                        <?php if (empty($popularArticles)): ?>
                            <p class="text-muted small mb-0">Belum ada data pembacaan artikel.</p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($popularArticles as $pa): ?>
                                    <div class="list-group-item px-0 d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <?php if (!empty($pa['image'])): ?>
                                                <img src="../assets/img/articles/<?= htmlspecialchars($pa['image']) ?>"
                                                     class="article-thumb" alt="<?= htmlspecialchars($pa['title']) ?>">
                                            <?php else: ?>
                                                <div class="article-thumb d-flex align-items-center justify-content-center text-muted small">
                                                    No image
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-semibold"><?= htmlspecialchars($pa['title']) ?></div>
                                                <div class="text-muted small">
                                                    Fase: <?= htmlspecialchars($pa['phase'] ?: '-') ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-semibold"><?= (int)$pa['read_count'] ?>x</div>
                                            <div class="text-muted small">dibaca</div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include '../includes/footer.php'; ?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ===== DATA DARI PHP =====
const userLabels = <?= json_encode($chartUserLabels) ?>;
const userData   = <?= json_encode($chartUserData) ?>;

const phaseLabels = <?= json_encode($chartPhaseLabels) ?>;
const phaseData   = <?= json_encode($chartPhaseData) ?>;

// ===== GRAFIK PERTUMBUHAN PENGGUNA =====
const ctxUsers = document.getElementById('usersGrowthChart');
if (ctxUsers) {
    new Chart(ctxUsers, {
        type: 'line',
        data: {
            labels: userLabels,
            datasets: [{
                label: 'User baru',
                data: userData,
                tension: 0.3,
                borderColor: '#850F37',
                backgroundColor: 'rgba(224,86,253,0.12)',
                pointBackgroundColor: '#850F37',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 5
                    }
                }
            }
        }
    });
}

// ===== DONUT DISTRIBUSI FASE =====
const ctxPhase = document.getElementById('phaseDonutChart');
if (ctxPhase) {
    new Chart(ctxPhase, {
        type: 'doughnut',
        data: {
            labels: phaseLabels,
            datasets: [{
                data: phaseData,
                backgroundColor: [
                    '#E8445F', // menstruasi
                    '#F49AA3', // folikular
                    '#A8BF89', // luteal
                    '#89B8C2'  // ovulasi
                ],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '65%',
            plugins: {
                legend: { display: false }
            }
        }
    });
}
</script>
