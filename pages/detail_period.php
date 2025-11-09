<?php
// pages/detail_period.php
require '../config/koneksi.php';
require '../classes/Crud.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';

$obj = new Crud($koneksi);

// -- CREATE
if (isset($_POST['create'])) {
    $data = [
        'id_detail'   => $_POST['id_detail'],
        'id_period'   => $_POST['id_period'],
        'tanggal'     => $_POST['tanggal'],
        'gejala'      => $_POST['gejala'],
        'volume_darah'=> $_POST['volume_darah']
    ];
    $obj->create('detail_period', $data);
    header("Location: detail_period.php");
    exit;
}

// -- UPDATE (submit)
if (isset($_POST['update'])) {
    $id = $_POST['id_detail'];
    $data = [
        'id_period'    => $_POST['id_period'],
        'tanggal'      => $_POST['tanggal'],
        'gejala'       => $_POST['gejala'],
        'volume_darah' => $_POST['volume_darah']
    ];
    $obj->update('detail_period', $data, 'id_detail', $id);
    header("Location: detail_period.php");
    exit;
}

// -- DELETE
if (isset($_GET['delete'])) {
    $obj->delete('detail_period', 'id_detail', $_GET['delete']);
    header("Location: detail_period.php");
    exit;
}

// -- READ (for listing)
$rows = $obj->readAll('detail_period');

// -- READ single (for edit form)
$editMode = false;
$editData = null;
if (isset($_GET['edit'])) {
    $editMode = true;
    $editId = $_GET['edit'];
    $editData = $obj->readById('detail_period', 'id_detail', $editId);
}
?>

<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Detail Period - Catatan Harian</h5>

            <!-- FORM: Create / Edit -->
            <div class="card mb-3">
                <div class="card-body">
                    <?php if ($editMode && $editData): ?>
                        <form method="POST" class="row g-2">
                            <input type="hidden" name="id_detail" value="<?= htmlspecialchars($editData['id_detail']) ?>">
                            <div class="col-md-3">
                                <label class="form-label">ID Period</label>
                                <input class="form-control" name="id_period" required value="<?= htmlspecialchars($editData['id_period']) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" required value="<?= htmlspecialchars($editData['tanggal']) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Volume Darah</label>
                                <select class="form-control" name="volume_darah" required>
                                    <option value="ringan" <?= $editData['volume_darah']==='ringan' ? 'selected' : '' ?>>ringan</option>
                                    <option value="sedang" <?= $editData['volume_darah']==='sedang' ? 'selected' : '' ?>>sedang</option>
                                    <option value="berat"  <?= $editData['volume_darah']==='berat'  ? 'selected' : '' ?>>berat</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Gejala</label>
                                <input class="form-control" name="gejala" value="<?= htmlspecialchars($editData['gejala']) ?>">
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-success" type="submit" name="update">Simpan Perubahan</button>
                                <a href="detail_period.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <form method="POST" class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">ID Detail</label>
                                <input class="form-control" name="id_detail" required placeholder="ex: D0001">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ID Period</label>
                                <input class="form-control" name="id_period" required placeholder="ID period terkait">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Volume Darah</label>
                                <select class="form-control" name="volume_darah" required>
                                    <option value="ringan">ringan</option>
                                    <option value="sedang">sedang</option>
                                    <option value="berat">berat</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Gejala</label>
                                <input class="form-control" name="gejala" placeholder="mis: kram, pusing, lelah">
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-primary" type="submit" name="create">Tambah Catatan</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TABLE: List -->
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID Detail</th>
                                <th>ID Period</th>
                                <th>Tanggal</th>
                                <th>Gejala</th>
                                <th>Volume Darah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['id_detail']) ?></td>
                                        <td><?= htmlspecialchars($r['id_period']) ?></td>
                                        <td><?= htmlspecialchars($r['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($r['gejala']) ?></td>
                                        <td><?= htmlspecialchars($r['volume_darah']) ?></td>
                                        <td>
                                            <a href="?edit=<?= urlencode($r['id_detail']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="?delete=<?= urlencode($r['id_detail']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus catatan ini?')">Hapus</a>
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
