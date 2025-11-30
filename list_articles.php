<?php
require 'classes/Database.php';

$db   = new Database();
$conn = $db->getConnection();

/* ===== AMBIL SEMUA ARTIKEL ===== */
$articles = [];
$res = $conn->query("
    SELECT id, title, link, phase, image, created_at
    FROM articles
    ORDER BY created_at DESC
");
if ($res) {
    $articles = $res->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Daftar Artikel - EVEE</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
  <link rel="shortcut icon" type="image/png" href="assets/assets-landing/img/logo-icon.png" />
  <link rel="stylesheet" href="assets/assets-landing/css/bootstrap.css">
  <link rel="stylesheet" href="assets/assets-landing/vendor/animate/animate.css">
  <link rel="stylesheet" href="assets/assets-landing/css/theme.css">
  <style>
    .card-blog-list .post-thumb img {
        height: 180px;
        object-fit: cover;
        width: 100%;
    }
  </style>
</head>
<body>

<header class="py-3 shadow-sm bg-white">
  <div class="container d-flex justify-content-between align-items-center">
    <a href="index.php" class="navbar-brand m-0">
      <img src="assets/assets-landing/img/logo- text-default 1.png" alt="" style="height:40px;">
    </a>
    <a href="index.php#article" class="btn btn-outline-primary btn-sm">Kembali</a>
  </div>
</header>

<div class="page-section" id="article">
  <div class="container">
    <div class="text-center wow fadeInUp">
      <div class="subhead">Artikel</div>
      <h2 class="title-section">Semua artikel EVEE</h2>
      <div class="divider mx-auto"></div>
    </div>

    <div class="row mt-4">
      <?php if (empty($articles)): ?>
        <div class="col-12 text-center text-muted">
          Belum ada artikel.
        </div>
      <?php else: ?>
        <?php foreach ($articles as $art): ?>
          <div class="col-lg-4 col-md-6 py-3 wow fadeInUp">
            <div class="card-blog card-blog-list">
              <div class="header">
                <div class="post-thumb">
                  <?php if (!empty($art['image'])): ?>
                    <img src="assets/img/articles/<?= htmlspecialchars($art['image']) ?>" alt="">
                  <?php else: ?>
                    <img src="assets/assets-landing/img/blog/blog-1.jpg" alt="">
                  <?php endif; ?>
                </div>
              </div>
              <div class="body">
                <h5 class="post-title">
                  <a href="<?= htmlspecialchars($art['link']) ?>" target="_blank">
                    <?= htmlspecialchars($art['title']) ?>
                  </a>
                </h5>
                <div class="post-date">
                  Posted on 
                  <a href="#">
                    <?= date('d M Y', strtotime($art['created_at'])) ?>
                  </a>
                </div>
                <?php if (!empty($art['phase'])): ?>
                  <div class="mt-1 text-muted small">
                    Fase: <?= htmlspecialchars($art['phase']) ?>
                  </div>
                <?php endif; ?>
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
