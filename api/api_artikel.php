<?php
header('Content-Type: application/json');

require '../config/koneksi.php';
require '../classes/Crud.php';

$crud = new Crud($koneksi);

// ambil semua data artikel
$data = $crud->readAll("articles");

// karena kamu hanya butuh "judul", kita map datanya
$hasil = [];
foreach ($data as $row) {
    $hasil[] = [
        "judul" => $row['judul']
    ];
}

// kembalikan JSON
echo json_encode($hasil);
?>
