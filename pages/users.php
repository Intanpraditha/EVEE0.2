<?php
require '../config/koneksi.php';
require '../classes/Crud.php';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';


$obj = new Crud($koneksi);

// CREATE
if (isset($_POST['create'])) {
    $data = [
        'id_user' => $_POST['id_user'],
        'nama' => $_POST['nama'],
        'email' => $_POST['email'],
        'tanggal_lahir' => $_POST['tanggal_lahir'],
        'rerata_siklus' => $_POST['rerata_siklus'],
        'last_login' => date('Y-m-d H:i:s')
    ];
    $obj->create('user', $data);
}

$users = $obj->readAll('user');
?>

<div class="container-fluid">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <main>
                    <h5 class="card-title fw-semibold mb-4">Data User</h5>
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" class="d-flex gap-3 mb-4">
                                <input class="form-control" type="text" name="id_user" placeholder="ID" required>
                                <input class="form-control" type="text" name="nama" placeholder="Nama" required>
                                <input class="form-control" type="email" name="email" placeholder="Email" required>
                                <input class="form-control" type="date" name="tanggal_lahir" required>
                                <input class="form-control" type="number" name="rerata_siklus" placeholder="Rata-rata Siklus">
                                <button class="btn btn-primary" type="submit" name="create">Tambah</button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr><th>ID</th><th>Nama</th><th>Email</th><th>Tgl Lahir</th><th>Siklus</th></tr>
                                <?php foreach ($users as $u): ?>
                                <tr>
                                <td><?= $u['id_user'] ?></td>
                                <td><?= $u['nama'] ?></td>
                                <td><?= $u['email'] ?></td>
                                <td><?= $u['tanggal_lahir'] ?></td>
                                <td><?= $u['rerata_siklus'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                    
                    </main>

                    
                    
                </main>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
