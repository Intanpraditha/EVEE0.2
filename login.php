<?php
session_start();

// jika sudah login → lempar ke dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: pages/dashboard.php");
    exit;
}
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Evee</title>
  <link rel="shortcut icon" type="image/png" href="assets/images/logos/logo-icon.png" />
  <link rel="stylesheet" href="assets/css/styles.min.css" />
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div
      class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
      <div class="d-flex align-items-center justify-content-center w-100">
        <div class="row justify-content-center w-100">
          <div class="col-md-8 col-lg-6 col-xxl-3">
            <div class="card mb-0">
              <div class="card-body">
                <a href="pages/dashboard.php" class="text-nowrap logo-img text-center d-block py-3 w-100">
                  <img src="assets/images/logos/logo- text-default.png" width="180" alt="">
                </a>
                
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger py-2 small"><?= htmlspecialchars($_GET['error']) ?></div>
                <?php endif; ?>

                <form method="POST" action="auth.php">
                    <div class="mb-3">
                        <label class="form-label small">Email</label>
                        <input type="email" name="email" class="form-control" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button class="btn btn-primary w-100">Login</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>