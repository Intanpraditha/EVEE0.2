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

    // hapus icon kalau ada
    $res = $conn->query("SELECT icon FROM moods WHERE id = '".$conn->real_escape_string($id)."'");
    if ($res && $row = $res->fetch_assoc()) {
        if (!empty($row['icon'])) {
            $filePath = '../assets/img/moods/' . $row['icon'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }

    $obj->delete('moods', "id = ?", [$id]);

    // redirect pake JS biar ga pakai header()
    echo "<script>window.location='mood.php';</script>";
    exit;
}

// ========= CREATE =========
if (isset($_POST['create'])) {

    $id = Helper::generateId($conn, 'moods', 'M', 'id');

    // handle upload icon (opsional)
    $iconFileName = null;
    if (!empty($_FILES['icon']['name'])) {
        $uploadDir  = '../assets/img/moods/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext        = pathinfo($_FILES['icon']['name'], PATHINFO_EXTENSION);
        $safeName   = strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '_', $id));
        $iconFileName = $safeName . '.' . $ext;

        move_uploaded_file($_FILES['icon']['tmp_name'], $uploadDir . $iconFileName);
    }

    $data = [
        'id'          => $id,
        'name'        => $_POST['name'],
        'icon'        => $iconFileName,
        'description' => !empty($_POST['description']) ? $_POST['description'] : null,
        'mood_tag'    => !empty($_POST['mood_tag']) ? $_POST['mood_tag'] : null,
    ];

    $obj->create('moods', $data);
}

// ========= UPDATE =========
if (isset($_POST['update'])) {
    $id = $_POST['edit_id'];

    // default: pakai icon lama
    $iconFileName = $_POST['current_icon'];

    // kalau ada upload baru, override
    if (!empty($_FILES['edit_icon']['name'])) {
        $uploadDir  = '../assets/img/moods/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext        = pathinfo($_FILES['edit_icon']['name'], PATHINFO_EXTENSION);
        $safeName   = strtolower(preg_replace('/[^a-zA-Z0-9_\-]/', '_', $id));
        $iconFileName = $safeName . '.' . $ext;

        move_uploaded_file($_FILES['edit_icon']['tmp_name'], $uploadDir . $iconFileName);
    }

    $data = [
        'name'        => $_POST['edit_name'],
        'icon'        => $iconFileName,
        'description' => !empty($_POST['edit_description']) ? $_POST['edit_description'] : null,
        'mood_tag'    => !empty($_POST['edit_mood_tag']) ? $_POST['edit_mood_tag'] : null,
    ];

    $obj->update('moods', $data, "id = ?", [$id]);
}

$moods = $obj->readAll('moods');

// ==== setelah semua proses PHP, baru include tampilan ====
include '../includes/header.php';
include '../includes/sidebar.php';
// include '../includes/topbar.php'; // kalau perlu
?>

<div class="container-fluid">
    <h4 class="fw-semibold mb-4">Data Mood</h4>

    <!-- Form Tambah -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" class="row g-3" enctype="multipart/form-data">
                <div class="col-md-3">
                    <input type="text" name="name" class="form-control" placeholder="Nama Mood" required>
                </div>

                <div class="col-md-3">
                    <input type="file" name="icon" class="form-control" accept="image/png,image/jpeg">
                </div>

                <div class="col-md-3">
                    <input type="text" name="description" class="form-control" placeholder="Deskripsi (opsional)">
                </div>

                <div class="col-md-2">
                    <select name="mood_tag" class="form-control">
                        <option value="">Tag (opsional)</option>
                        <option value="netral">Netral</option>
                        <option value="capek">Capek</option>
                        <option value="cemas">Cemas</option>
                        <option value="sedih">Sedih</option>
                        <option value="sensitif">Sensitif</option>
                        <option value="senang">Senang</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <button class="btn btn-primary w-100" name="create">Tambah</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Mood -->
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Icon</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Tag</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($moods as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['id']) ?></td>
                        <td>
                            <?php if (!empty($m['icon'])): ?>
                                <img src="../assets/img/moods/<?= htmlspecialchars($m['icon']) ?>"
                                     alt="<?= htmlspecialchars($m['name']) ?>" style="height:50px;">
                            <?php else: ?>
                                <em>-</em>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($m['name']) ?></td>
                        <td><?= htmlspecialchars($m['description']) ?></td>
                        <td><?= htmlspecialchars($m['mood_tag']) ?></td>

                        <td>
                            <button class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal<?= $m['id'] ?>">
                                Edit
                            </button>
                            <a href="mood.php?delete=<?= $m['id'] ?>"
                               onclick="return confirm('Hapus mood ini?')"
                               class="btn btn-danger btn-sm">
                                Hapus
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal<?= $m['id'] ?>" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <form method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                              <h5 class="modal-title">Edit Mood <?= htmlspecialchars($m['name']) ?></h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="edit_id" value="<?= $m['id'] ?>">
                                <input type="hidden" name="current_icon" value="<?= htmlspecialchars($m['icon']) ?>">

                                <div class="mb-3">
                                    <label>Nama</label>
                                    <input type="text" name="edit_name" class="form-control"
                                           value="<?= htmlspecialchars($m['name']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label>Icon saat ini</label><br>
                                    <?php if (!empty($m['icon'])): ?>
                                        <img src="../assets/img/moods/<?= htmlspecialchars($m['icon']) ?>"
                                             alt="" style="height:50px;">
                                    <?php else: ?>
                                        <em>Belum ada icon</em>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label>Ganti Icon (opsional)</label>
                                    <input type="file" name="edit_icon" class="form-control"
                                           accept="image/png,image/jpeg">
                                </div>

                                <div class="mb-3">
                                    <label>Deskripsi (opsional)</label>
                                    <input type="text" name="edit_description" class="form-control"
                                           value="<?= htmlspecialchars($m['description']) ?>">
                                </div>

                                <div class="mb-3">
                                    <label>Tag (untuk self-care rules)</label>
                                    <select name="edit_mood_tag" class="form-control">
                                        <option value="" <?= $m['mood_tag'] == '' ? 'selected' : '' ?>>(Tidak ada)</option>
                                        <option value="netral"   <?= $m['mood_tag']=='netral'   ? 'selected' : '' ?>>Netral</option>
                                        <option value="capek"    <?= $m['mood_tag']=='capek'    ? 'selected' : '' ?>>Capek</option>
                                        <option value="cemas"    <?= $m['mood_tag']=='cemas'    ? 'selected' : '' ?>>Cemas</option>
                                        <option value="sedih"    <?= $m['mood_tag']=='sedih'    ? 'selected' : '' ?>>Sedih</option>
                                        <option value="sensitif" <?= $m['mood_tag']=='sensitif' ? 'selected' : '' ?>>Sensitif</option>
                                        <option value="senang"   <?= $m['mood_tag']=='senang'   ? 'selected' : '' ?>>Senang</option>
                                    </select>
                                </div>

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

<?php include '../includes/footer.php'; ?>
