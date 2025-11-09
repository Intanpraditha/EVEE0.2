<?php
// pages/reminders.php
require '../config/koneksi.php';
require '../classes/Crud.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';

$obj = new Crud($koneksi);

// CREATE
if (isset($_POST['create'])) {
    $data = [
        'id_reminder' => $_POST['id_reminder'],
        'id_user'     => $_POST['id_user'],
        'judul'       => $_POST['judul'],
        'tanggal'     => $_POST['tanggal'],
        'jam'         => $_POST['jam'],
        'kategori'    => $_POST['kategori'],
        'prioritas'   => $_POST['prioritas']
    ];
    $obj->create('reminders', $data);
    header("Location: reminders.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id_reminder'];
    $data = [
        'id_user'   => $_POST['id_user'],
        'judul'     => $_POST['judul'],
        'tanggal'   => $_POST['tanggal'],
        'jam'       => $_POST['jam'],
        'kategori'  => $_POST['kategori'],
        'prioritas' => $_POST['prioritas']
    ];
    $obj->update('reminders', $data, 'id_reminder', $id);
    header("Location: reminders.php");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $obj->delete('reminders', 'id_reminder', $_GET['delete']);
    header("Location: reminders.php");
    exit;
}

// READ
$rows = $obj->readAll('reminders');
$editMode = false;
$editData = null;
if (isset($_GET['edit'])) {
    $editMode = true;
    $editData = $obj->readById('reminders', 'id_reminder', $_GET['edit']);
}
?>

<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Daftar Pengingat (Reminders)</h5>

            <!-- FORM -->
            <div class="card mb-3">
                <div class="card-body">
                    <?php if ($editMode && $editData): ?>
                        <form method="POST" class="row g-2">
                            <input type="hidden" name="id_reminder" value="<?= htmlspecialchars($editData['id_reminder']) ?>">
                            <div class="col-md-3">
                                <label class="form-label">ID User</label>
                                <input class="form-control" name="id_user" value="<?= htmlspecialchars($editData['id_user']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Judul</label>
                                <input class="form-control" name="judul" value="<?= htmlspecialchars($editData['judul']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" value="<?= htmlspecialchars($editData['tanggal']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jam</label>
                                <input type="time" class="form-control" name="jam" value="<?= htmlspecialchars($editData['jam']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kategori</label>
                                <input class="form-control" name="kategori" value="<?= htmlspecialchars($editData['kategori']) ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Prioritas</label>
                                <select class="form-control" name="prioritas" required>
                                    <option value="Rendah" <?= $editData['prioritas'] == 'Rendah' ? 'selected' : '' ?>>Rendah</option>
                                    <option value="Sedang" <?= $editData['prioritas'] == 'Sedang' ? 'selected' : '' ?>>Sedang</option>
                                    <option value="Tinggi" <?= $editData['prioritas'] == 'Tinggi' ? 'selected' : '' ?>>Tinggi</option>
                                </select>
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-success" type="submit" name="update">Simpan Perubahan</button>
                                <a href="reminders.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <form method="POST" class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label">ID Reminder</label>
                                <input class="form-control" name="id_reminder" required placeholder="ex: R0001">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">ID User</label>
                                <input class="form-control" name="id_user" required placeholder="User ID">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Judul</label>
                                <input class="form-control" name="judul" required placeholder="Judul pengingat">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control" name="tanggal" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jam</label>
                                <input type="time" class="form-control" name="jam" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kategori</label>
                                <input class="form-control" name="kategori" required placeholder="mis: kegiatan, kesehatan, kerja">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Prioritas</label>
                                <select class="form-control" name="prioritas" required>
                                    <option value="Rendah">Rendah</option>
                                    <option value="Sedang">Sedang</option>
                                    <option value="Tinggi">Tinggi</option>
                                </select>
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-primary" type="submit" name="create">Tambah Pengingat</button>
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
                                <th>ID Reminder</th>
                                <th>ID User</th>
                                <th>Judul</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Kategori</th>
                                <th>Prioritas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['id_reminder']) ?></td>
                                        <td><?= htmlspecialchars($r['id_user']) ?></td>
                                        <td><?= htmlspecialchars($r['judul']) ?></td>
                                        <td><?= htmlspecialchars($r['tanggal']) ?></td>
                                        <td><?= htmlspecialchars($r['jam']) ?></td>
                                        <td><?= htmlspecialchars($r['kategori']) ?></td>
                                        <td><?= htmlspecialchars($r['prioritas']) ?></td>
                                        <td>
                                            <a href="?edit=<?= urlencode($r['id_reminder']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="?delete=<?= urlencode($r['id_reminder']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pengingat ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center">Belum ada data</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
