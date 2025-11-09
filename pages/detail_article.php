<?php
// pages/detail_article.php
require '../config/koneksi.php';
require '../classes/Crud.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';

$obj = new Crud($koneksi);

// CREATE
if (isset($_POST['create'])) {
    $data = [
        'id_detail'    => $_POST['id_detail'],
        'id_user'      => $_POST['id_user'],
        'id_article'   => $_POST['id_article'],
        'tanggal_baca' => $_POST['tanggal_baca'],
        'disimpan'     => $_POST['disimpan']
    ];
    $obj->create('detail_article', $data);
    header("Location: detail_article.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id_detail'];
    $data = [
        'id_user'      => $_POST['id_user'],
        'id_article'   => $_POST['id_article'],
        'tanggal_baca' => $_POST['tanggal_baca'],
        'disimpan'     => $_POST['disimpan']
    ];
    $obj->update('detail_article', $data, 'id_detail', $id);
    header("Location: detail_article.php");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $obj->delete('detail_article', 'id_detail', $_GET['delete']);
    header("Location: detail_article.php");
    exit;
}

// READ
$rows = $obj->readAll('detail_article');
$editMode = false;
$editData = null;

if (isset($_GET['edit'])) {
    $editMode = true;
    $editId = $_GET['edit'];
    $editData = $obj->readById('detail_article', 'id_detail', $editId);
}
?>

<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Detail Artikel Dibaca</h5>

            <!-- FORM -->
            <div class="card mb-3">
                <div class="card-body">
                    <?php if ($editMode && $editData): ?>
                        <form method="POST" class="row g-2">
                            <input type="hidden" name="id_detail" value="<?= htmlspecialchars($editData['id_detail']) ?>">
                            <div class="col-md-3">
                                <label class="form-label">ID User</label>
                                <input class="form-control" name="id_user" required value="<?= htmlspecialchars($editData['id_user']) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ID Artikel</label>
                                <input class="form-control" name="id_article" required value="<?= htmlspecialchars($editData['id_article']) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Baca</label>
                                <input type="date" class="form-control" name="tanggal_baca" required value="<?= htmlspecialchars($editData['tanggal_baca']) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Disimpan</label>
                                <select class="form-control" name="disimpan">
                                    <option value="Ya" <?= $editData['disimpan'] == 'Ya' ? 'selected' : '' ?>>Ya</option>
                                    <option value="Tidak" <?= $editData['disimpan'] == 'Tidak' ? 'selected' : '' ?>>Tidak</option>
                                </select>
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-success" type="submit" name="update">Simpan Perubahan</button>
                                <a href="detail_article.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <form method="POST" class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">ID Detail</label>
                                <input class="form-control" name="id_detail" required placeholder="ex: DA0001">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ID User</label>
                                <input class="form-control" name="id_user" required placeholder="User ID">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ID Artikel</label>
                                <input class="form-control" name="id_article" required placeholder="Article ID">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal Baca</label>
                                <input type="date" class="form-control" name="tanggal_baca" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Disimpan</label>
                                <select class="form-control" name="disimpan">
                                    <option value="Ya">Ya</option>
                                    <option value="Tidak">Tidak</option>
                                </select>
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-primary" type="submit" name="create">Tambah Detail</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TABEL -->
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID Detail</th>
                                <th>ID User</th>
                                <th>ID Artikel</th>
                                <th>Tanggal Baca</th>
                                <th>Disimpan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['id_detail']) ?></td>
                                        <td><?= htmlspecialchars($r['id_user']) ?></td>
                                        <td><?= htmlspecialchars($r['id_article']) ?></td>
                                        <td><?= htmlspecialchars($r['tanggal_baca']) ?></td>
                                        <td><?= htmlspecialchars($r['disimpan']) ?></td>
                                        <td>
                                            <a href="?edit=<?= urlencode($r['id_detail']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="?delete=<?= urlencode($r['id_detail']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
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
