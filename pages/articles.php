<?php
// pages/articles.php
require '../config/koneksi.php';
require '../classes/Crud.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';

$obj = new Crud($koneksi);

// CREATE
if (isset($_POST['create'])) {

    // Upload gambar
    $namaFile = null;
    if (!empty($_FILES['gambar']['name'])) {
        $folder = "../uploads/";
        $namaFile = time() . "-" . $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], $folder . $namaFile);
    }

    $data = [
        'id_article'  => $_POST['id_article'],
        'id_user'     => $_POST['id_user'],
        'judul'       => $_POST['judul'],
        'link'        => $_POST['link'],
        'id_fase'     => $_POST['id_fase'],
        'gambar'      => $namaFile    // simpan nama filenya
    ];

    $obj->create('articles', $data);
    header("Location: articles.php");
    exit;
}


// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id_article'];
    $data = [
        'id_user'     => $_POST['id_user'],
        'judul'       => $_POST['judul'],
        'link'        => $_POST['link'],
        'id_fase'     => $_POST['id_fase']
    ];
    $obj->update('articles', $data, 'id_article', $id);
    header("Location: articles.php");
    exit;
}

// DELETE
if (isset($_GET['delete'])) {
    $obj->delete('articles', 'id_article', $_GET['delete']);
    header("Location: articles.php");
    exit;
}

// READ
$rows = $obj->readAll('articles');
$editMode = false;
$editData = null;

if (isset($_GET['edit'])) {
    $editMode = true;
    $editId = $_GET['edit'];
    $editData = $obj->readById('articles', 'id_article', $editId);
}
?>

<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title fw-semibold mb-4">Manajemen Artikel</h5>

            <!-- FORM -->
            <div class="card mb-3">
                <div class="card-body">
                    <?php if ($editMode && $editData): ?>
                        <form method="POST" class="row g-2">
                            <input type="hidden" name="id_article" value="<?= htmlspecialchars($editData['id_article']) ?>">
                            <div class="col-md-4">
                                <label class="form-label">ID User</label>
                                <input class="form-control" name="id_user" required value="<?= htmlspecialchars($editData['id_user']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Judul</label>
                                <input class="form-control" name="judul" required value="<?= htmlspecialchars($editData['judul']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Link</label>
                                <input class="form-control" name="link" required value="<?= htmlspecialchars($editData['link']) ?>">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">ID Fase</label>
                                <input class="form-control" name="id_fase" required value="<?= htmlspecialchars($editData['id_fase']) ?>">
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-success" type="submit" name="update">Simpan Perubahan</button>
                                <a href="articles.php" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <form method="POST" enctype="multipart/form-data" class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">ID Artikel</label>
                                <input class="form-control" name="id_article" required placeholder="ex: A0001">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ID User</label>
                                <input class="form-control" name="id_user" required placeholder="User ID">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Judul</label>
                                <input class="form-control" name="judul" required placeholder="Judul artikel">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Gambar</label>
                                <input  class="form-control" type="file" name="gambar" accept="image/*">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Link</label>
                                <input class="form-control" name="link" required placeholder="https://...">
                            </div>
                            
                            <div class="col-md-4">
                                <label class="form-label">ID Fase</label>
                                <input class="form-control" name="id_fase" required placeholder="Fase ID">
                            </div>
                            <div class="col-12 mt-2">
                                <button class="btn btn-primary" type="submit" name="create">Tambah Artikel</button>
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
                                <th>ID Artikel</th>
                                <th>ID User</th>
                                <th>Judul</th>
                                <th>Gambar</th>
                                <th>Link</th>
                                <th>Fase Siklus</th>
                                <th>Diunggah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($r['id_article']) ?></td>
                                        <td><?= htmlspecialchars($r['id_user']) ?></td>

                                        <td><?= htmlspecialchars($r['judul']) ?></td>

                                        <!-- Gambar -->
                                        <td>
                                            <?php if (!empty($r['gambar'])): ?>
                                                <img src="/EVEE0.2/uploads/<?= htmlspecialchars($r['gambar']) ?>" width="60" alt="">
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>


                                        <!-- Link -->
                                        <td><a href="<?= htmlspecialchars($r['link']) ?>" target="_blank">Buka</a></td>

                                        <!-- Fase -->
                                        <td><?= htmlspecialchars($r['id_fase']) ?></td>
                                        <td><?= htmlspecialchars($r['created_at']) ?></td>

                                        <td>
                                            <a href="?edit=<?= urlencode($r['id_article']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                            <a href="?delete=<?= urlencode($r['id_article']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
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
