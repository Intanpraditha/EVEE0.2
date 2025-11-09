<?php
// pages/notification.php
require '../config/koneksi.php';
require '../classes/Crud.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';

$obj = new Crud($koneksi);

// CREATE
if (isset($_POST['create'])) {
    $data = [
        'id_notif'        => $_POST['id_notif'],
        'id_user'         => $_POST['id_user'],
        'tipe_notifikasi' => $_POST['tipe_notifikasi'],
        'pesan'           => $_POST['pesan'],
        'status'          => $_POST['status'],
        'waktu'           => $_POST['waktu']
    ];
    $obj->create('notification', $data);
    header("Location: notification.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id_notif'];
    $data = [
        'id_user'         => $_POST['id_user'],
        'tipe_notifikasi' => $_POST['tipe_notifikasi'],
        'pesan'           => $_POST['pesan'],
        'status'          => $_POST['status'],
        'waktu'           => $_POST['waktu']
    ];
    $obj->update('notification', $data, 'id_notif', $id);
    header("Location: notification.php");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $obj->delete('notification', 'id_notif', $_GET['delete']);
    header("Location: notification.php");
    exit;
}

// READ
$rows = $obj->readAll('notification');
$editMode = false;
$editData = null;
if (isset($_GET['edit'])) {
    $editMode = true;
    $editData = $obj->readById('notification', 'id_notif', $_GET['edit']);
}
?>

<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Data Notifikasi</h5>

            <!-- FORM -->
            <div class="card mb-3">
                <div class="card-body">
                    <?php if ($editMode && $editData): ?>
                        <form method="POST" class="row g-2">
                            <input type="hidden" name="id_notif" value="<?= htmlspecialchars($editData['id_notif']) ?>">
                            <div class="col-md-2">
                                <label class="form-label">ID User</label>
                                <input class="form-control" name="id_user" value="<?= htmlspecialchars($editData['id_user']) ?>" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tipe Notifikasi</label>
                                <select class="form-control" name="tipe_notifikasi" required>
                                    <option value="Menstruasi" <?= $editData['tipe_notifikasi'] == 'Menstruasi' ? 'selected' : '' ?>>Menstruasi</option>
                                    <option value="Kegiatan" <?= $editData['tipe_notifikasi'] == 'Kegiatan' ? 'selected' : '' ?>>Kegiatan</option>
                                    <option value="Lainnya" <?= $editData['tipe_notifikasi'] == 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pesan</label>
                                <textarea class="form-control" name="pesan" rows="2" required><?= htmlspecialchars($editData['pesan']) ?></textarea>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="Terkirim" <?= $editData['status'] == 'Terkirim' ? 'selected' : '' ?>>Terkirim</option>
                                    <option value="Belum terkirim" <?= $editData['status'] == 'Belum terkirim' ? 'selected' : '' ?>>Belum terkirim</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Waktu</label>
                                <input type="time" class="form-control" name="waktu" value="<?= htmlspecialchars($editData['waktu']) ?>" required>
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-success" name="update">Simpan</button>
                                <a href="notification.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <form method="POST" class="row g-2">
                            <div class="col-md-2">
                                <label class="form-label">ID Notif</label>
                                <input class="form-control" name="id_notif" required placeholder="N0001">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">ID User</label>
                                <input class="form-control" name="id_user" required placeholder="U0001">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tipe</label>
                                <select class="form-control" name="tipe_notifikasi" required>
                                    <option value="Menstruasi">Menstruasi</option>
                                    <option value="Kegiatan">Kegiatan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Pesan</label>
                                <textarea class="form-control" name="pesan" rows="2" required></textarea>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="Terkirim">Terkirim</option>
                                    <option value="Belum terkirim">Belum terkirim</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Waktu</label>
                                <input type="time" class="form-control" name="waktu" required>
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
                                <th>ID Notif</th>
                                <th>ID User</th>
                                <th>Tipe</th>
                                <th>Pesan</th>
                                <th>Status</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['id_notif']) ?></td>
                                        <td><?= htmlspecialchars($r['id_user']) ?></td>
                                        <td><?= htmlspecialchars($r['tipe_notifikasi']) ?></td>
                                        <td><?= htmlspecialchars($r['pesan']) ?></td>
                                        <td><?= htmlspecialchars($r['status']) ?></td>
                                        <td><?= htmlspecialchars($r['waktu']) ?></td>
                                        <td>
                                            <a href="?edit=<?= urlencode($r['id_notif']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="?delete=<?= urlencode($r['id_notif']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="7" class="text-center">Belum ada data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
