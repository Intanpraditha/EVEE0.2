<?php
require '../config/koneksi.php';
require '../classes/Crud.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';

$obj = new Crud($koneksi);

if (isset($_POST['create'])) {
    $data = [
        'id_period' => $_POST['id_period'],
        'id_user' => $_POST['id_user'],
        'tanggal_mulai' => $_POST['tanggal_mulai'],
        'tanggal_selesai' => $_POST['tanggal_selesai'],
        'panjang_periode' => $_POST['panjang_periode'],
        'panjang_siklus' => $_POST['panjang_siklus'],
        'catatan' => $_POST['catatan'],
        'id_fase' => $_POST['id_fase']
    ];
    $obj->create('period_record', $data);
    header("Location: period_record.php");
}

if (isset($_GET['delete'])) {
    $obj->delete('period_record', 'id_period', $_GET['delete']);
    header("Location: period_record.php");
}

$rows = $obj->readAll('period_record');
?>

<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-body">
            <h4>Data Period Record</h4>
            <div class="card mb-3">
                <div class="card-body">
                    <form method="POST" class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">ID Period</label>
                            <input class="form-control" name="id_period" required placeholder="ex: P0001">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">ID User</label>
                            <input class="form-control" name="id_user" required placeholder="User ID">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="tanggal_mulai" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="tanggal_selesai" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Periode (hari)</label>
                            <input class="form-control" name="panjang_periode" placeholder="mis: 5">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Siklus (hari)</label>
                            <input class="form-control" name="panjang_siklus" placeholder="mis: 28">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Catatan</label>
                            <input class="form-control" name="catatan" placeholder="Tambahkan catatan">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">ID Fase</label>
                            <input class="form-control" name="id_fase" placeholder="ex: F0001">
                        </div>

                        <div class="col-12 mt-2">
                            <button class="btn btn-primary" type="submit" name="create">Tambah Periode</button>
                        </div>
                    </form>

                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>ID</th><th>User</th><th>Mulai</th><th>Selesai</th>
                            <th>Periode</th><th>Siklus</th><th>Catatan</th><th>Fase</th><th>Aksi</th>
                        </tr>
                        <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?= $r['id_period'] ?></td>
                            <td><?= $r['id_user'] ?></td>
                            <td><?= $r['tanggal_mulai'] ?></td>
                            <td><?= $r['tanggal_selesai'] ?></td>
                            <td><?= $r['panjang_periode'] ?></td>
                            <td><?= $r['panjang_siklus'] ?></td>
                            <td><?= $r['catatan'] ?></td>
                            <td><?= $r['id_fase'] ?></td>
                            <td><a href="?delete=<?= $r['id_period'] ?>" class="btn btn-danger btn-sm">Hapus</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
