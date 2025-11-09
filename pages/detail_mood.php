<?php
// pages/detail_mood.php
require '../config/koneksi.php';
require '../classes/Crud.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';

$obj = new Crud($koneksi);

// CREATE
if (isset($_POST['create'])) {
    $data = [
        'id_dtl_mood' => $_POST['id_dtl_mood'],
        'id_user'     => $_POST['id_user'],
        'id_mood'     => $_POST['id_mood'],
        'tanggal'     => $_POST['tanggal'],
        'waktu'       => $_POST['waktu']
    ];
    $obj->create('detail_mood', $data);
    header("Location: detail_mood.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id_dtl_mood'];
    $data = [
        'id_user' => $_POST['id_user'],
        'id_mood' => $_POST['id_mood'],
        'tanggal' => $_POST['tanggal'],
        'waktu'   => $_POST['waktu']
    ];
    $obj->update('detail_mood', $data, 'id_dtl_mood', $id);
    header("Location: detail_mood.php");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $obj->delete('detail_mood', 'id_dtl_mood', $_GET['delete']);
    header("Location: detail_mood.php");
    exit;
}

// READ
$rows = $obj->readAll('detail_mood');
$editMode = false;
$editData = null;

if (isset($_GET['edit'])) {
    $editMode = true;
    $editId = $_GET['edit'];
    $editData = $obj->readById('detail_mood', 'id_dtl_mood', $editId);
}
?>

<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Detail Mood Harian</h5>

            <!-- FORM -->
            <div class="card mb-3">
                <div class="card-body">
                    <?php if ($editMode && $editData): ?>
                        <form method="POST" class="row g-2">
                            <input type="hidden" name="id_dtl_mood" value="<?= htmlspecialchars($editData['id_dtl_mood']) ?>">
                            <div class="col-md-3">
                                <label class="form-label">ID User</label>
                                <input class="form-control" name="id_user" required value="<?= htmlspecialchars($editData['id_user']) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ID Mood</label>
                                <input class="form-control" name="id_mood" required value="<?= htmlspecialchars($editData['id_mood']) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" required value="<?= htmlspecialchars($editData['tanggal']) ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Waktu</label>
                                <input type="time" class="form-control" name="waktu" required value="<?= htmlspecialchars($editData['waktu']) ?>">
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-success" type="submit" name="update">Simpan Perubahan</button>
                                <a href="detail_mood.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <form method="POST" class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">ID Detail Mood</label>
                                <input class="form-control" name="id_dtl_mood" required placeholder="ex: DM0001">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ID User</label>
                                <input class="form-control" name="id_user" required placeholder="User ID">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ID Mood</label>
                                <input class="form-control" name="id_mood" required placeholder="Mood ID">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Waktu</label>
                                <input type="time" class="form-control" name="waktu" required>
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-primary" type="submit" name="create">Tambah Catatan</button>
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
                                <th>ID Detail Mood</th>
                                <th>ID User</th>
                                <th>ID Mood</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['id_dtl_mood']) ?></td>
                                        <td><?= htmlspecialchars($r['id_user']) ?></td>
                                        <td><?= htmlspecialchars($r['id_mood']) ?></td>
                                        <td><?= htmlspecialchars($r['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($r['waktu']) ?></td>
                                        <td>
                                            <a href="?edit=<?= urlencode($r['id_dtl_mood']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="?delete=<?= urlencode($r['id_dtl_mood']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
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
