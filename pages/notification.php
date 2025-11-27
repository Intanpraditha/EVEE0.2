<?php
require '../includes/check_login.php';
require '../classes/Database.php';
require '../classes/Crud.php';

$db   = new Database();
$conn = $db->getConnection();

// join ke users
$sql = "
    SELECT n.*, u.name AS user_name
    FROM notifications n
    JOIN users u ON u.id = n.user_id
    ORDER BY n.created_at DESC
";
$result = $conn->query($sql);
$notifications = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h4 class="fw-semibold mb-4">Notifikasi</h4>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Tipe</th>
                                <th>Pesan</th>
                                <th>Status</th>
                                <th>Period ID</th>
                                <th>Activity ID</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($notifications)): ?>
                            <tr><td colspan="7" class="text-center">Belum ada data</td></tr>
                        <?php else: ?>
                            <?php foreach ($notifications as $n): ?>
                                <tr>
                                    <td><?= htmlspecialchars($n['created_at']) ?></td>
                                    <td><?= htmlspecialchars($n['user_name']) ?> (<?= htmlspecialchars($n['user_id']) ?>)</td>
                                    <td><?= htmlspecialchars($n['type']) ?></td>
                                    <td style="max-width: 300px; white-space:pre-wrap;">
                                        <?= htmlspecialchars($n['message']) ?>
                                    </td>
                                    <td><?= htmlspecialchars($n['status']) ?></td>
                                    <td><?= htmlspecialchars($n['related_period_id']) ?></td>
                                    <td><?= htmlspecialchars($n['related_activity_id']) ?></td>
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
