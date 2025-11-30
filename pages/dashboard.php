<?php
require '../includes/check_login.php';
require '../classes/Database.php';
require '../classes/Crud.php';

$db   = new Database();
$conn = $db->getConnection();

/* ========================
   FILTER TAHUN - PER BAGIAN
   ======================== */

// Tahun untuk grafik PERTUMBUHAN USER
$userYear = isset($_GET['user_year']) ? (int)$_GET['user_year'] : (int)date('Y');

// Tahun untuk ARTIKEL POPULER
$articleYear = isset($_GET['article_year']) ? (int)$_GET['article_year'] : 0;


/* ========================
   LIST TAHUN UNTUK DROPDOWN
   ======================== */

// user.created_at
$userYears = [];
$resYears = $conn->query("SELECT DISTINCT YEAR(created_at) AS y FROM users ORDER BY y DESC");
while ($row = $resYears->fetch_assoc()) {
    if ($row['y'] != null) $userYears[] = (int)$row['y'];
}
if (empty($userYears)) $userYears[] = $userYear;

// user_articles.read_at
$articleYears = [];
$resYears = $conn->query("SELECT DISTINCT YEAR(read_at) AS y FROM user_articles ORDER BY y DESC");
while ($row = $resYears->fetch_assoc()) {
    if ($row['y'] != null) $articleYears[] = (int)$row['y'];
}
if (empty($articleYears)) $articleYears[] = (int)date('Y');


/* ========================
   SUMMARY CARDS
   ======================== */

$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role='user'")
                    ->fetch_assoc()['total'];

$totalMoods = $conn->query("SELECT COUNT(*) AS total FROM moods")
                    ->fetch_assoc()['total'];

$totalArticles = $conn->query("SELECT COUNT(*) AS total FROM articles")
                    ->fetch_assoc()['total'];

$totalRules = $conn->query("SELECT COUNT(*) AS total FROM selfcare_rules")
                    ->fetch_assoc()['total'];


/* ========================
   GRAFIK PERTUMBUHAN USER
   ======================== */
$monthNames = [
    1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
    7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'
];

$usersPerMonth = array_fill(1, 12, 0);

$res = $conn->query("
    SELECT MONTH(created_at) AS m, COUNT(*) AS total
    FROM users
    WHERE YEAR(created_at) = {$userYear}
    GROUP BY m
");
while ($row = $res->fetch_assoc()) {
    $usersPerMonth[$row['m']] = (int)$row['total'];
}

$chartUserLabels = array_values($monthNames);
$chartUserData   = array_values($usersPerMonth);


/* ========================
   ARTIKEL POPULER
   ======================== */

$whereArticleYear = $articleYear > 0
    ? "WHERE YEAR(ua.read_at) = {$articleYear}"
    : "";

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

$popularArticles = $conn->query($sqlArticles)->fetch_all(MYSQLI_ASSOC);


include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';
?>

<style>
    .card {
        border-radius: 20px;
        border: none;
        box-shadow: 0 8px 20px rgba(0,0,0,0.03);
    }
    .card-small {
        height: 120px;
        border-radius: 18px;
        box-shadow: 0 6px 16px rgba(0,0,0,0.03);
    }
    .stat-label {
        font-size: .85rem; color:#999;
    }
    .stat-value {
        font-size:1.7rem; font-weight:700; color:#333;
    }
    .stat-sub {
        font-size:.8rem; color:#aaa;
    }
    .article-thumb {
        width:60px; height:60px; object-fit:cover;
        border-radius:14px; background:#f5f5f5;
    }
    
</style>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h4 class="mb-4 fw-semibold">Dashboard</h4>

            <!-- =================== SUMMARY =================== -->
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
                                <div class="stat-sub">Mood tersedia</div>
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
                                <div class="stat-sub">Rekomendasi aktif</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- =================== GRAFIK =================== -->
            <div class="row g-3 mb-4">

                <div class="col-lg-12">
                    <div class="card p-3">

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Pertumbuhan Pengguna</h6>

                            <form method="GET">
                                <input type="hidden" name="article_year" value="<?= $articleYear ?>">
                                <select name="user_year" onchange="this.form.submit()"
                                        class="form-select form-select-sm" style="width:140px;">
                                    <?php foreach ($userYears as $y): ?>
                                        <option value="<?= $y ?>" <?= $userYear == $y ? 'selected':'' ?>>
                                            <?= $y ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>

                        <canvas id="usersGrowthChart"></canvas>

                    </div>
                </div>

            </div>

            <!-- =================== ARTIKEL POPULER =================== -->
            <div class="card p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0">Artikel Populer</h6>

                    <form method="GET">
                        <input type="hidden" name="user_year" value="<?= $userYear ?>">
                        <select name="article_year" onchange="this.form.submit()"
                                class="form-select form-select-sm" style="width:150px;">
                            <option value="0" <?= $articleYear==0?'selected':'' ?>>Semua tahun</option>
                            <?php foreach ($articleYears as $y): ?>
                                <option value="<?= $y ?>" <?= $articleYear==$y?'selected':'' ?>>
                                    <?= $y ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <?php if (empty($popularArticles)): ?>
                    <p class="text-muted small">Belum ada data pembacaan artikel.</p>
                <?php else: ?>
                    <?php foreach ($popularArticles as $pa): ?>
                    <div class="list-group-item px-0 d-flex align-items-center justify-content-between">

                        <div class="d-flex align-items-center gap-3 mb-5">
                            <?php if (!empty($pa['image'])): ?>
                                <img src="../assets/img/articles/<?= htmlspecialchars($pa['image']) ?>"
                                     class="article-thumb">
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
                <?php endif; ?>

            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const userLabels = <?= json_encode($chartUserLabels) ?>;
const userData   = <?= json_encode($chartUserData) ?>;

new Chart(document.getElementById('usersGrowthChart'), {
    type: 'line',
    data: {
        labels: userLabels,
        datasets: [{
            label: "User baru",
            data: userData,
            borderColor: "#850F37",
            backgroundColor: "rgba(133, 15, 55, 0.15)",
            tension: .3,
            pointRadius: 4,
            pointBackgroundColor: "#850F37"
        }]
    },
    options: {
        plugins: { legend: { display:false }},
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,   // naik 1 angka per grid
                    precision: 0,  // hilangkan koma
                    callback: function(value) {
                        return Number.isInteger(value) ? value : ''; 
                    }
                }
            }
        }

    }
});
</script>
