<?php
// pages/selfcare_recom.php
require '../config/koneksi.php';
require '../classes/Crud.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';

$obj = new Crud($koneksi);

// CREATE
if (isset($_POST['create'])) {
    $data = [
        'id_recom'    => $_POST['id_recom'],
        'id_user'     => $_POST['id_user'],
        'tanggal'     => $_POST['tanggal'],
        'kondisi'     => $_POST['kondisi'],
        'rekomendasi' => $_POST['rekomendasi']
    ];
    $obj->create('selfcare_recom', $data);
    header("Location: selfcare_recom.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id_recom'];
    $data = [
        'id_user'     => $_POST['id_user'],
        'tanggal'     => $_POST['tanggal'],
        'kondisi'     => $_POST['kondisi'],
        'rekomendasi' => $_POST['rekomendasi']
    ];
    $obj->update('selfcare_recom', $data, 'id_recom', $id);
    header("Location: selfcare_recom.php");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $obj->delete('selfcare_recom', 'id_recom', $_GET['delete']);
    header("Location: selfcare_recom.php");
    exit;
}

// READ
$rows = $obj->readAll('selfcare_recom');
$editMode = false;
$editData = null;
if (isset($_GET['edit'])) {
    $editMode = true;
    $editData = $obj->readById('selfcare_recom', 'id_recom', $_GET['edit']);
}
?>

<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Data Selfcare Recommendation</h5>

            <!-- FORM -->
            <div class="card mb-3">
                <div class="card-body">
                    <?php if ($editMode && $editData): ?>
                        <form method="POST" class="row g-2">
                            <input type="hidden" name="id_recom" value="<?= htmlspecialchars($editData['id_recom']) ?>">
                            <div class="col-md-2">
                                <label class="form-label">ID User</label>
                                <input class="form-control" name="id_user" value="<?= htmlspecialchars($editData['id_user']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" value="<?= htmlspecialchars($editData['tanggal']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kondisi</label>
                                <input class="form-control" name="kondisi" value="<?= htmlspecialchars($editData['kondisi']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Rekomendasi</label>
                                <textarea class="form-control" name="rekomendasi" rows="2" required><?= htmlspecialchars($editData['rekomendasi']) ?></textarea>
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-success" name="update">Simpan</button>
                                <a href="selfcare_recom.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <form method="POST" class="row g-2">
                            <div class="col-md-2">
                                <label class="form-label">ID Recom</label>
                                <input class="form-control" name="id_recom" required placeholder="R0001">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">ID User</label>
                                <input class="form-control" name="id_user" required placeholder="U0001">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kondisi</label>
                                <input class="form-control" name="kondisi" required placeholder="contoh: nyeri perut, stres">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Rekomendasi</label>
                                <textarea class="form-control" name="rekomendasi" rows="2" required placeholder="istirahat cukup, minum air putih"></textarea>
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-primary" name="create">Tambah</button>
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
                                <th>ID Recom</th>
                                <th>ID User</th>
                                <th>Tanggal</th>
                                <th>Kondisi</th>
                                <th>Rekomendasi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['id_recom']) ?></td>
                                        <td><?= htmlspecialchars($r['id_user']) ?></td>
                                        <td><?= htmlspecialchars($r['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($r['kondisi']) ?></td>
                                        <td><?= htmlspecialchars($r['rekomendasi']) ?></td>
                                        <td>
                                            <a href="?edit=<?= urlencode($r['id_recom']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="?delete=<?= urlencode($r['id_recom']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">Belum ada data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
