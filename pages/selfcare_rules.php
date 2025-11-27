<?php
require '../includes/check_login.php';
require '../classes/Database.php';
require '../classes/Crud.php';

$db   = new Database();
$conn = $db->getConnection();
$obj  = new Crud($conn);

// ========= DELETE =========
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $obj->delete('selfcare_rules', "id = ?", [$id]);
    echo "<script>window.location='selfcare_rules.php';</script>";
    exit;
}

// ========= CREATE =========
if (isset($_POST['create'])) {
    $data = [
        'phase'         => !empty($_POST['phase']) ? $_POST['phase'] : null,
        'busy_level'    => !empty($_POST['busy_level']) ? $_POST['busy_level'] : null,
        'pain_level'    => !empty($_POST['pain_level']) ? $_POST['pain_level'] : null,
        'mood_tag'      => !empty($_POST['mood_tag']) ? $_POST['mood_tag'] : null,
        'day_to_period' => ($_POST['day_to_period'] !== '') ? $_POST['day_to_period'] : null,
        'text'          => $_POST['text'],
        'priority'      => !empty($_POST['priority']) ? $_POST['priority'] : 100,
    ];

    $obj->create('selfcare_rules', $data);
}

// ========= UPDATE =========
if (isset($_POST['update'])) {
    $id = $_POST['edit_id'];

    $data = [
        'phase'         => !empty($_POST['edit_phase']) ? $_POST['edit_phase'] : null,
        'busy_level'    => !empty($_POST['edit_busy_level']) ? $_POST['edit_busy_level'] : null,
        'pain_level'    => !empty($_POST['edit_pain_level']) ? $_POST['edit_pain_level'] : null,
        'mood_tag'      => !empty($_POST['edit_mood_tag']) ? $_POST['edit_mood_tag'] : null,
        'day_to_period' => ($_POST['edit_day_to_period'] !== '') ? $_POST['edit_day_to_period'] : null,
        'text'          => $_POST['edit_text'],
        'priority'      => !empty($_POST['edit_priority']) ? $_POST['edit_priority'] : 100,
    ];

    $obj->update('selfcare_rules', $data, "id = ?", [$id]);
}

$rules = $conn->query("SELECT * FROM selfcare_rules ORDER BY priority ASC, id ASC")
         ->fetch_all(MYSQLI_ASSOC);

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h4 class="fw-semibold mb-4">Self-care Rules</h4>

            <!-- Form Tambah -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-2">
                            <label>Phase</label>
                            <select name="phase" class="form-control">
                                <option value="">(Semua)</option>
                                <option value="menstruasi">Menstruasi</option>
                                <option value="folikular">Folikular</option>
                                <option value="ovulasi">Ovulasi</option>
                                <option value="luteal">Luteal</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Busy Level</label>
                            <select name="busy_level" class="form-control">
                                <option value="">(Semua)</option>
                                <option value="rendah">Rendah</option>
                                <option value="sedang">Sedang</option>
                                <option value="tinggi">Tinggi</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Pain Level</label>
                            <select name="pain_level" class="form-control">
                                <option value="">(Semua)</option>
                                <option value="ringan">Ringan</option>
                                <option value="sedang">Sedang</option>
                                <option value="berat">Berat</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Mood Tag</label>
                            <select name="mood_tag" class="form-control">
                                <option value="">(Semua)</option>
                                <option value="netral">Netral</option>
                                <option value="capek">Capek</option>
                                <option value="cemas">Cemas</option>
                                <option value="sedih">Sedih</option>
                                <option value="sensitif">Sensitif</option>
                                <option value="senang">Senang</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Day to Period</label>
                            <input type="number" name="day_to_period" class="form-control"
                                placeholder="mis. -3, -1, 0, 1">
                        </div>
                        <div class="col-md-2">
                            <label>Priority</label>
                            <input type="number" name="priority" class="form-control" value="100">
                        </div>
                        <div class="col-12">
                            <label>Text Rekomendasi</label>
                            <textarea name="text" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" name="create">Tambah Rule</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Rules -->
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered align-middle">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Phase</th>
                            <th>Busy</th>
                            <th>Pain</th>
                            <th>Mood</th>
                            <th>Day</th>
                            <th>Priority</th>
                            <th>Text</th>
                            <th>Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($rules as $r): ?>
                            <tr>
                                <td><?= $r['id'] ?></td>
                                <td><?= $r['phase'] ?></td>
                                <td><?= $r['busy_level'] ?></td>
                                <td><?= $r['pain_level'] ?></td>
                                <td><?= $r['mood_tag'] ?></td>
                                <td><?= $r['day_to_period'] ?></td>
                                <td><?= $r['priority'] ?></td>
                                <td style="max-width: 300px; white-space:pre-wrap;"><?= htmlspecialchars($r['text']) ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal<?= $r['id'] ?>">
                                        Edit
                                    </button>
                                    <a href="selfcare_rules.php?delete=<?= $r['id'] ?>"
                                    onclick="return confirm('Hapus rule ini?')"
                                    class="btn btn-danger btn-sm">
                                        Hapus
                                    </a>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="editModal<?= $r['id'] ?>" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header">
                                    <h5 class="modal-title">Edit Rule #<?= $r['id'] ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="edit_id" value="<?= $r['id'] ?>">

                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label>Phase</label>
                                                <select name="edit_phase" class="form-control">
                                                    <option value="" <?= $r['phase']=='' ? 'selected':''?>>(Semua)</option>
                                                    <option value="menstruasi" <?= $r['phase']=='menstruasi'?'selected':'' ?>>Menstruasi</option>
                                                    <option value="folikular"  <?= $r['phase']=='folikular'?'selected':'' ?>>Folikular</option>
                                                    <option value="ovulasi"    <?= $r['phase']=='ovulasi'?'selected':'' ?>>Ovulasi</option>
                                                    <option value="luteal"     <?= $r['phase']=='luteal'?'selected':'' ?>>Luteal</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Busy Level</label>
                                                <select name="edit_busy_level" class="form-control">
                                                    <option value="" <?= $r['busy_level']=='' ? 'selected':''?>>(Semua)</option>
                                                    <option value="rendah" <?= $r['busy_level']=='rendah'?'selected':'' ?>>Rendah</option>
                                                    <option value="sedang" <?= $r['busy_level']=='sedang'?'selected':'' ?>>Sedang</option>
                                                    <option value="tinggi" <?= $r['busy_level']=='tinggi'?'selected':'' ?>>Tinggi</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Pain Level</label>
                                                <select name="edit_pain_level" class="form-control">
                                                    <option value="" <?= $r['pain_level']=='' ? 'selected':''?>>(Semua)</option>
                                                    <option value="ringan" <?= $r['pain_level']=='ringan'?'selected':'' ?>>Ringan</option>
                                                    <option value="sedang" <?= $r['pain_level']=='sedang'?'selected':'' ?>>Sedang</option>
                                                    <option value="berat"  <?= $r['pain_level']=='berat'?'selected':''  ?>>Berat</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Mood Tag</label>
                                                <select name="edit_mood_tag" class="form-control">
                                                    <option value="" <?= $r['mood_tag']=='' ? 'selected':''?>>(Semua)</option>
                                                    <option value="netral"   <?= $r['mood_tag']=='netral'?'selected':'' ?>>Netral</option>
                                                    <option value="capek"    <?= $r['mood_tag']=='capek'?'selected':'' ?>>Capek</option>
                                                    <option value="cemas"    <?= $r['mood_tag']=='cemas'?'selected':'' ?>>Cemas</option>
                                                    <option value="sedih"    <?= $r['mood_tag']=='sedih'?'selected':'' ?>>Sedih</option>
                                                    <option value="sensitif" <?= $r['mood_tag']=='sensitif'?'selected':'' ?>>Sensitif</option>
                                                    <option value="senang"   <?= $r['mood_tag']=='senang'?'selected':'' ?>>Senang</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label>Day to Period</label>
                                                <input type="number" name="edit_day_to_period" class="form-control"
                                                    value="<?= htmlspecialchars($r['day_to_period']) ?>">
                                            </div>
                                            <div class="col-md-3">
                                                <label>Priority</label>
                                                <input type="number" name="edit_priority" class="form-control"
                                                    value="<?= htmlspecialchars($r['priority']) ?>">
                                            </div>
                                            <div class="col-12">
                                                <label>Text Rekomendasi</label>
                                                <textarea name="edit_text" class="form-control" rows="3" required><?= htmlspecialchars($r['text']) ?></textarea>
                                            </div>
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
    </div>
</div>

<?php include '../includes/footer.php'; ?>
