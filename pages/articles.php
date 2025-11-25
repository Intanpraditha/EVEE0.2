<?php
require '../classes/Database.php';
require '../classes/Crud.php';
require '../classes/Helper.php';

$db   = new Database();
$conn = $db->getConnection();
$obj  = new Crud($conn);

// ========= DELETE =========
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // hapus gambar juga
    $res = $conn->query("SELECT image FROM articles WHERE id = '".$conn->real_escape_string($id)."'");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['image'])) {
            $filePath = '../assets/img/articles/' . $row['image'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }

    $obj->delete('articles', "id = ?", [$id]);
    echo "<script>window.location='articles.php';</script>";
    exit;
}

// ========= CREATE =========
if (isset($_POST['create'])) {
    $id = Helper::generateId($conn, 'articles', 'A', 'id');

    $imageFileName = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir  = '../assets/img/articles/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $safeName = strtolower($id);
        $imageFileName = $safeName . '.' . $ext;

        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageFileName);
    }

    $data = [
        'id'    => $id,
        'title' => $_POST['title'],
        'link'  => $_POST['link'],
        'phase' => !empty($_POST['phase']) ? $_POST['phase'] : null,
        'image' => $imageFileName
    ];

    $obj->create('articles', $data);
}

// ========= UPDATE =========
if (isset($_POST['update'])) {

    $id = $_POST['edit_id'];
    $imageFileName = $_POST['current_image'];

    if (!empty($_FILES['edit_image']['name'])) {
        $uploadDir  = '../assets/img/articles/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        // hapus gambar lama
        if (!empty($imageFileName)) {
            $old = $uploadDir . $imageFileName;
            if (file_exists($old)) @unlink($old);
        }

        $ext = pathinfo($_FILES['edit_image']['name'], PATHINFO_EXTENSION);
        $safeName = strtolower($id);
        $imageFileName = $safeName . '.' . $ext;

        move_uploaded_file($_FILES['edit_image']['tmp_name'], $uploadDir . $imageFileName);
    }

    $data = [
        'title' => $_POST['edit_title'],
        'link'  => $_POST['edit_link'],
        'phase' => !empty($_POST['edit_phase']) ? $_POST['edit_phase'] : null,
        'image' => $imageFileName
    ];

    $obj->update('articles', $data, "id = ?", [$id]);
}

$articles = $obj->readAll('articles');

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h4 class="fw-semibold mb-4">Data Artikel</h4>

            <!-- Form Tambah -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" class="row g-3" enctype="multipart/form-data">
                        <div class="col-md-3">
                            <input type="text" name="title" class="form-control" placeholder="Judul artikel" required>
                        </div>

                        <div class="col-md-4">
                            <input type="text" name="link" class="form-control" placeholder="Link artikel" required>
                        </div>

                        <div class="col-md-2">
                            <select name="phase" class="form-control">
                                <option value="">Fase (opsional)</option>
                                <option value="menstruasi">Menstruasi</option>
                                <option value="folikular">Folikular</option>
                                <option value="ovulasi">Ovulasi</option>
                                <option value="luteal">Luteal</option>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <input type="file" name="image" class="form-control" accept="image/png,image/jpeg">
                        </div>

                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" name="create">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Artikel -->
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Gambar</th>
                                <th>Judul</th>
                                <th>Fase</th>
                                <th>Link</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($articles as $a): ?>
                            <tr>
                                <td><?= $a['id'] ?></td>

                                <td>
                                    <?php if ($a['image']): ?>
                                        <img src="../assets/img/articles/<?= $a['image'] ?>" style="height:60px;">
                                    <?php else: ?>
                                        <em>-</em>
                                    <?php endif; ?>
                                </td>

                                <td><?= htmlspecialchars($a['title']) ?></td>
                                <td><?= htmlspecialchars($a['phase']) ?></td>
                                <td>
                                    <a href="<?= htmlspecialchars($a['link']) ?>" target="_blank">
                                        <?= htmlspecialchars($a['link']) ?>
                                    </a>
                                </td>

                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editModal<?= $a['id'] ?>">Edit</button>

                                    <a href="articles.php?delete=<?= $a['id'] ?>"
                                    onclick="return confirm('Hapus artikel ini?')"
                                    class="btn btn-danger btn-sm">Hapus</a>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="editModal<?= $a['id'] ?>">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" enctype="multipart/form-data" class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Artikel</h5>
                                            <button class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <input type="hidden" name="edit_id" value="<?= $a['id'] ?>">
                                            <input type="hidden" name="current_image" value="<?= $a['image'] ?>">

                                            <label>Judul</label>
                                            <input type="text" class="form-control mb-2"
                                                name="edit_title" value="<?= htmlspecialchars($a['title']) ?>" required>

                                            <label>Link</label>
                                            <input type="text" class="form-control mb-2"
                                                name="edit_link" value="<?= htmlspecialchars($a['link']) ?>" required>

                                            <label>Fase</label>
                                            <select name="edit_phase" class="form-control mb-2">
                                                <option value="" <?= $a['phase']==''?'selected':'' ?>>Tidak ada</option>
                                                <option value="menstruasi" <?= $a['phase']=='menstruasi'?'selected':'' ?>>Menstruasi</option>
                                                <option value="folikular"  <?= $a['phase']=='folikular'?'selected':'' ?>>Folikular</option>
                                                <option value="ovulasi"    <?= $a['phase']=='ovulasi'?'selected':'' ?>>Ovulasi</option>
                                                <option value="luteal"     <?= $a['phase']=='luteal'?'selected':'' ?>>Luteal</option>
                                            </select>

                                            <label>Gambar saat ini</label><br>
                                            <?php if ($a['image']): ?>
                                                <img src="../assets/img/articles/<?= $a['image'] ?>" style="height:80px;">
                                            <?php else: ?>
                                                <em>Belum ada gambar</em>
                                            <?php endif; ?>
                                            <br><br>

                                            <label>Ganti Gambar (opsional)</label>
                                            <input type="file" name="edit_image" class="form-control"
                                                accept="image/png,image/jpeg">
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" name="update" class="btn btn-primary">Simpan</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        </div>

                                        </form>
                                </div>
                            </div>
                            </div>

                        <?php endforeach; ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
