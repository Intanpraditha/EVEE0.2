<?php
// $host = "eveeapp.mysql.database.azure.com";
// $user = "eveeapp";
// $pass = "SHEVACvk25";
// $db   = "evee_database";

// $koneksi = mysqli_connect($host, $user, $pass, $db);

// if (!$koneksi) {
//     die("Koneksi database gagal: " . mysqli_connect_error());
// }


$host = "localhost";
$user = "root";
$pass = "";
$db   = "evee_database";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}


?>
