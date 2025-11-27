<?php
require '../includes/check_login.php';
require '../classes/Database.php';
require '../classes/Crud.php';

$db   = new Database();
$conn = $db->getConnection();

// join ke users biar kelihatan nama user
$sql = "
    SELECT pc.*, u.name AS user_name
    FROM period_cycles pc
    JOIN users u ON u.id = pc.user_id
    ORDER BY pc.start_date DESC
";
$result = $conn->query($sql);
$periods = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h4 class="fw-semibold mb-4">Riwayat Siklus Haid</h4>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>ID Siklus</th>
                                <th>Tgl Mulai</th>
                                <th>Tgl Selesai</th>
                                <th>Panjang Siklus</th>
                                <th>Lama Haid</th>
                                <th>Catatan</th>
                                <th>Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($periods)): ?>
                            <tr><td colspan="8" class="text-center">Belum ada data</td></tr>
                        <?php else: ?>
                            <?php foreach ($periods as $p): ?>
                                <tr>
                                    <td><?= htmlspecialchars($p['user_name']) ?> (<?= htmlspecialchars($p['user_id']) ?>)</td>
                                    <td><?= htmlspecialchars($p['id']) ?></td>
                                    <td><?= htmlspecialchars($p['start_date']) ?></td>
                                    <td><?= htmlspecialchars($p['end_date']) ?></td>
                                    <td><?= htmlspecialchars($p['cycle_length']) ?></td>
                                    <td><?= htmlspecialchars($p['period_length']) ?></td>
                                    <td><?= htmlspecialchars($p['note']) ?></td>
                                    <td><?= htmlspecialchars($p['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
