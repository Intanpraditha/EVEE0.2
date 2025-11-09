<?php
// pages/fase_siklus.php
require '../config/koneksi.php';
require '../classes/Crud.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';

$obj = new Crud($koneksi);

// CREATE
if (isset($_POST['create'])) {
    $data = [
        'id_fase' => $_POST['id_fase'],
        'nama_fase' => $_POST['nama_fase'],
        'jangka_waktu' => $_POST['jangka_waktu']
    ];
    $obj->create('fase_siklus', $data);
    header("Location: fase_siklus.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id_fase'];
    $data = [
        'nama_fase' => $_POST['nama_fase'],
        'jangka_waktu' => $_POST['jangka_waktu']
    ];
    $obj->update('fase_siklus', $data, 'id_fase', $id);
    header("Location: fase_siklus.php");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $obj->delete('fase_siklus', 'id_fase', $_GET['delete']);
    header("Location: fase_siklus.php");
    exit;
}

// READ
$rows = $obj->readAll('fase_siklus');
$editMode = false;
$editData = null;
if (isset($_GET['edit'])) {
    $editMode = true;
    $editData = $obj->readById('fase_siklus', 'id_fase', $_GET['edit']);
}
?>

<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Data Fase Siklus</h5>

            <!-- FORM -->
            <div class="card mb-3">
                <div class="card-body">
                    <?php if ($editMode && $editData): ?>
                        <form method="POST" class="row g-2">
                            <input type="hidden" name="id_fase" value="<?= htmlspecialchars($editData['id_fase']) ?>">
                            <div class="col-md-4">
                                <label class="form-label">Nama Fase</label>
                                <input class="form-control" name="nama_fase" value="<?= htmlspecialchars($editData['nama_fase']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jangka Waktu (hari)</label>
                                <input type="number" class="form-control" name="jangka_waktu" value="<?= htmlspecialchars($editData['jangka_waktu']) ?>" required>
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-success" type="submit" name="update">Simpan</button>
                                <a href="fase_siklus.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <form method="POST" class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">ID Fase</label>
                                <input class="form-control" name="id_fase" required placeholder="ex: F0001">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nama Fase</label>
                                <input class="form-control" name="nama_fase" required placeholder="Menstruasi / Folikular / Luteal">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jangka Waktu (hari)</label>
                                <input type="number" class="form-control" name="jangka_waktu" required placeholder="mis: 5">
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-primary" type="submit" name="create">Tambah</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TABLE -->
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID Fase</th>
                                <th>Nama Fase</th>
                                <th>Jangka Waktu (hari)</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['id_fase']) ?></td>
                                        <td><?= htmlspecialchars($r['nama_fase']) ?></td>
                                        <td><?= htmlspecialchars($r['jangka_waktu']) ?></td>
                                        <td>
                                            <a href="?edit=<?= urlencode($r['id_fase']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="?delete=<?= urlencode($r['id_fase']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center">Belum ada data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
