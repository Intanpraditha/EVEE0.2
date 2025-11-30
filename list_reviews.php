<?php
require 'classes/Database.php';

$db   = new Database();
$conn = $db->getConnection();

/* ===== AMBIL SEMUA ULASAN APPROVED ===== */
$reviews = [];
$res = $conn->query("
    SELECT 
        COALESCE(r.display_name, u.name) AS name,
        r.rating,
        r.review_text,
        r.created_at
    FROM reviews r
    JOIN users u ON u.id = r.user_id
    WHERE r.status = 'approved'
    ORDER BY r.created_at DESC
");
if ($res) {
    $reviews = $res->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Daftar Ulasan - EVEE</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />

  <link rel="shortcut icon" type="image/png" href="assets/assets-landing/img/logo-icon.png" />
  <link rel="stylesheet" href="assets/assets-landing/css/bootstrap.css">
  <link rel="stylesheet" href="assets/assets-landing/vendor/animate/animate.css">
  <link rel="stylesheet" href="assets/assets-landing/css/theme.css">
</head>
<body>

<header class="py-3 shadow-sm bg-white">
  <div class="container d-flex justify-content-between align-items-center">
    <a href="index.php" class="navbar-brand m-0">
      <img src="assets/assets-landing/img/logo- text-default 1.png" alt="" style="height:40px;">
    </a>
    <a href="index.php#reviews" class="btn btn-outline-primary btn-sm">Kembali</a>
  </div>
</header>

<div class="page-section">
  <div class="container">
    <div class="text-center wow fadeInUp">
      <div class="subhead">Ulasan Pengguna</div>
      <h2 class="title-section">Apa kata mereka tentang EVEE</h2>
      <div class="divider mx-auto"></div>
    </div>

    <div class="row mt-4">
      <?php if (empty($reviews)): ?>
        <div class="col-12 text-center text-muted">
          Belum ada ulasan.
        </div>
      <?php else: ?>
        <?php foreach ($reviews as $rev): ?>
          <div class="col-md-6 col-lg-4 py-3 wow fadeInUp">
            <div class="review-bubble">
              <div class="review-text">
                “<?= htmlspecialchars($rev['review_text']) ?>”
              </div>
              <div class="review-user">
                <!-- Kalau mau avatar default tinggal aktifin lagi img di sini -->
                <!-- <img src="assets/assets-landing/img/review/default.png" alt=""> -->
                <div>
                  <h6><?= htmlspecialchars($rev['name']) ?></h6>
                  <span class="stars">
                    <?php for ($i = 0; $i < (int)$rev['rating']; $i++) echo "★"; ?>
                  </span>
                  <div class="text-muted small mt-1">
                    <?= date('d M Y', strtotime($rev['created_at'])) ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<footer id="footer" class="page-footer bg-image" style="background-image: url(assets/assets-landing/img/car-pattern.png);">
    <div class="container">
      <div class="row mb-5">
        <div class="col-lg-8 py-3">
          <h3>EVEE</h3>
          <p>Dibuat untuk para perempuan, membantu melacak siklusmu, <br> sinkron dengan aktivitas harian, dan memprioritaskan kesejahteraanmu.</p>

          <div class="social-media-button">
            <!-- <a href="#"><span class="mai-logo-facebook-f"></span></a>
            <a href="#"><span class="mai-logo-twitter"></span></a>
            <a href="#"><span class="mai-logo-google-plus-g"></span></a> -->
            <a href="https://www.instagram.com/evee.app/"><i class="ti ti-brand-instagram"></i></a>
            <!-- <a href="#"><span class="mai-logo-youtube"></span></a> -->
          </div>
        </div>
        
        <div class="col-lg-3 py-3">
          <h5>Kontak kami</h5>
          <p>Jember, Jawa timur, Indonesia</p>
          <a href="#" class="footer-link">+62 85706298928</a>
          <a href="#" class="footer-link">eveapp@gmail.com</a>
        </div>
        
      </div>

      <p class="text-center" id="copyright">Copyright &copy; 2025. dikembangkan oleh <a href="https://www.youtube.com/watch?v=xvFZjo5PgG0" target="_blank">Grub kecil kecilan</a></p>
    </div>
  </footer>

<script src="assets/assets-landing/js/jquery-3.5.1.min.js"></script>
<script src="assets/assets-landing/js/bootstrap.bundle.min.js"></script>
<script src="assets/assets-landing/vendor/wow/wow.min.js"></script>
<script src="assets/assets-landing/js/theme.js"></script>
</body>
</html>
