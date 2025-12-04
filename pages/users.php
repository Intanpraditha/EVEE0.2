<?php
require '../includes/check_login.php';
require '../classes/Database.php';
require '../classes/Crud.php';
require '../classes/Helper.php';

$db   = new Database();
$conn = $db->getConnection();
$obj  = new Crud($conn);

// ========= DELETE =========
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $obj->delete('users', "id = ?", [$id]);
    echo "<script>window.location='users.php';</script>";
    exit;
}

// ========= CREATE =========
if (isset($_POST['create'])) {

    $id = Helper::generateId($conn, 'users', 'U', 'id');

    $password = !empty($_POST['password'])
        ? password_hash($_POST['password'], PASSWORD_BCRYPT)
        : null;

    $data = [
        'id'         => $id,
        'name'       => $_POST['name'],
        'email'      => $_POST['email'],
        'password'   => $password,
        'role'       => $_POST['role'],
        'birth_date' => !empty($_POST['birth_date']) ? $_POST['birth_date'] : null,
    ];

    $obj->create('users', $data);
}

// ========= UPDATE =========
if (isset($_POST['update'])) {
    $id = $_POST['edit_id'];

    $data = [
        'name'       => $_POST['edit_name'],
        'email'      => $_POST['edit_email'],
        'role'       => $_POST['edit_role'],
        'birth_date' => !empty($_POST['edit_birth_date']) ? $_POST['edit_birth_date'] : null,
    ];

    if (!empty($_POST['edit_password'])) {
        $data['password'] = password_hash($_POST['edit_password'], PASSWORD_BCRYPT);
    }

    $obj->update('users', $data, "id = ?", [$id]);
}

// $users = $obj->readAll('users');

$filterRole = $_GET['filter_role'] ?? '';

if ($filterRole === 'user' || $filterRole === 'admin') {
    $users = $obj->rawQuery("SELECT * FROM users WHERE role = ?", [$filterRole]);
} else {
    $users = $obj->readAll('users'); // default semua
}


include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';
// include '../includes/topbar.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h4 class="fw-semibold mb-4">Data User</h4>

            <!-- Form Tambah -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="POST" class="row g-3">
                        <div class="col-md-3">
                            <input class="form-control" type="text" name="name" placeholder="Nama" required>
                        </div>
                        <div class="col-md-3">
                            <input class="form-control" type="email" name="email" placeholder="Email" required>
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" type="date" name="birth_date" placeholder="Tgl Lahir">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" name="role">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input class="form-control" type="password" name="password" placeholder="Password (opsional)">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" type="submit" name="create">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Users -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-semibold mb-0">Data User</h4>

                        <a href="export_users.php?role=<?= $_GET['filter_role'] ?? '' ?>" class="btn btn-sm btn-primary">
                            Export CSV
                        </a>

                    </div>
                    <!-- Filter Role -->
                    <form method="GET" class="row g-3 mb-3">
                        <div class="col-md-3">
                            <select name="filter_role" class="form-control">
                                <option value="">Semua Role</option>
                                <option value="user"  <?= (isset($_GET['filter_role']) && $_GET['filter_role'] == 'user') ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= (isset($_GET['filter_role']) && $_GET['filter_role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" type="submit">Filter</button>
                        </div>
                    </form>

                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Tgl Lahir</th>
                                <th>Dibuat</th>
                                <th>Terakhir login</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                
                                <td><?= htmlspecialchars($u['name']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><?= htmlspecialchars($u['role']) ?></td>
                                <td><?= htmlspecialchars($u['birth_date']) ?></td>
                                <td><?= htmlspecialchars($u['created_at']) ?></td>
                                <td>
                                    <?php if (!empty($u['last_login'])): ?>
                                        <?= htmlspecialchars($u['last_login']) ?>
                                    <?php else: ?>
                                        <span class="text-muted small">Belum pernah login</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-warning btn-sm"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editModal<?= $u['id'] ?>">
                                        Edit
                                    </button>
                                    <a href="users.php?delete=<?= $u['id'] ?>"
                                    onclick="return confirm('Hapus user ini?')"
                                    class="btn btn-danger btn-sm">
                                        Hapus
                                    </a>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="editModal<?= $u['id'] ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                <form method="POST">
                                    <div class="modal-header">
                                    <h5 class="modal-title">Edit User <?= htmlspecialchars($u['name']) ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="edit_id" value="<?= $u['id'] ?>">

                                        <div class="mb-3">
                                            <label>Nama</label>
                                            <input type="text" name="edit_name" class="form-control"
                                                value="<?= htmlspecialchars($u['name']) ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label>Email</label>
                                            <input type="email" name="edit_email" class="form-control"
                                                value="<?= htmlspecialchars($u['email']) ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label>Tanggal Lahir</label>
                                            <input type="date" name="edit_birth_date" class="form-control"
                                                value="<?= htmlspecialchars($u['birth_date']) ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label>Role</label>
                                            <select name="edit_role" class="form-control">
                                                <option value="user"  <?= $u['role']=='user' ? 'selected' : '' ?>>User</option>
                                                <option value="admin" <?= $u['role']=='admin'? 'selected' : '' ?>>Admin</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label>Password (kosongkan jika tidak diganti)</label>
                                            <input type="password" name="edit_password" class="form-control">
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
