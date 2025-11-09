<?php
// pages/mood.php
require '../config/koneksi.php';
require '../classes/Crud.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';

$obj = new Crud($koneksi);

// CREATE
if (isset($_POST['create'])) {
    $data = [
        'id_mood'   => $_POST['id_mood'],
        'id_user'   => $_POST['id_user'],
        'nama_mood' => $_POST['nama_mood']
    ];
    $obj->create('mood', $data);
    header("Location: mood.php");
    exit;
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id_mood'];
    $data = [
        'id_user'   => $_POST['id_user'],
        'nama_mood' => $_POST['nama_mood']
    ];
    $obj->update('mood', $data, 'id_mood', $id);
    header("Location: mood.php");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $obj->delete('mood', 'id_mood', $_GET['delete']);
    header("Location: mood.php");
    exit;
}

// READ
$rows = $obj->readAll('mood');
$editMode = false;
$editData = null;

if (isset($_GET['edit'])) {
    $editMode = true;
    $editId = $_GET['edit'];
    $editData = $obj->readById('mood', 'id_mood', $editId);
}
?>

<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Mood Harian</h5>

            <!-- FORM -->
            <div class="card mb-3">
                <div class="card-body">
                    <?php if ($editMode && $editData): ?>
                        <form method="POST" class="row g-2">
                            <input type="hidden" name="id_mood" value="<?= htmlspecialchars($editData['id_mood']) ?>">
                            <div class="col-md-4">
                                <label class="form-label">ID User</label>
                                <input class="form-control" name="id_user" required value="<?= htmlspecialchars($editData['id_user']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nama Mood</label>
                                <input class="form-control" name="nama_mood" required value="<?= htmlspecialchars($editData['nama_mood']) ?>">
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-success" type="submit" name="update">Simpan Perubahan</button>
                                <a href="mood.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <form method="POST" class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">ID Mood</label>
                                <input class="form-control" name="id_mood" required placeholder="ex: M0001">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ID User</label>
                                <input class="form-control" name="id_user" required placeholder="User ID">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nama Mood</label>
                                <input class="form-control" name="nama_mood" required placeholder="Misal: Bahagia, Sedih, Stres">
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-primary" type="submit" name="create">Tambah Mood</button>
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
                                <th>ID Mood</th>
                                <th>ID User</th>
                                <th>Nama Mood</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['id_mood']) ?></td>
                                        <td><?= htmlspecialchars($r['id_user']) ?></td>
                                        <td><?= htmlspecialchars($r['nama_mood']) ?></td>
                                        <td>
                                            <a href="?edit=<?= urlencode($r['id_mood']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="?delete=<?= urlencode($r['id_mood']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
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
