<?php
// Cek posisi file yang lagi diakses (apakah di folder /pages atau root)
$basePath = (strpos($_SERVER['SCRIPT_NAME'], '/pages/') !== false) ? '../' : './';
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>EVEE! your period tracker!</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/logo-icon.png" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <link rel="stylesheet" href="../assets/css/custom.css">
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">