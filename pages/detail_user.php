<?php
require '../config/koneksi.php';
require '../classes/Crud.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';

$obj = new Crud($koneksi);

if (isset($_POST['create'])) {
    $data = [
        'id_user' => $_POST['id_user'],
        'role' => $_POST['role']
    ];
    $obj->create('detail_user', $data);
    header("Location: detail_user.php");
}

if (isset($_GET['delete'])) {
    $obj->delete('detail_user', 'id_user', $_GET['delete']);
    header("Location: detail_user.php");
}

$rows = $obj->readAll('detail_user');
?>

<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-body">
            <h4>Detail User</h4>
            <div class="card mb-3">
                <div class="card-body">
                    <form method="POST" class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">ID User</label>
                            <input type="text" class="form-control" name="id_user" required placeholder="ex: U0001">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Role</label>
                            <select class="form-control" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>

                        <div class="col-12 mt-2">
                            <button class="btn btn-primary" type="submit" name="create">Tambah User</button>
                        </div>
                    </form>
                </div>
            </div>


            <div class="card mb-3">
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th>ID User</th><th>Role</th><th>Aksi</th></tr>
                        <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?= $r['id_user'] ?></td>
                            <td><?= $r['role'] ?></td>
                            <td><a href="?delete=<?= $r['id_user'] ?>" class="btn btn-danger btn-sm">Hapus</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
