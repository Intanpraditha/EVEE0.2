<?php
require '../classes/Database.php';
require '../classes/Crud.php';

$db   = new Database();
$conn = $db->getConnection();

// join ke users
$sql = "
    SELECT a.*, u.name AS user_name
    FROM activities a
    JOIN users u ON u.id = a.user_id
    ORDER BY a.date DESC, a.start_time ASC
";
$result = $conn->query($sql);
$activities = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="container-fluid">
    <h4 class="fw-semibold mb-4">Kegiatan User</h4>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Kategori</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($activities)): ?>
                    <tr><td colspan="8" class="text-center">Belum ada data</td></tr>
                <?php else: ?>
                    <?php foreach ($activities as $a): ?>
                        <tr>
                            <td><?= htmlspecialchars($a['user_name']) ?> (<?= htmlspecialchars($a['user_id']) ?>)</td>
                            <td><?= htmlspecialchars($a['title']) ?></td>
                            <td><?= htmlspecialchars($a['date']) ?></td>
                            <td>
                                <?= htmlspecialchars($a['start_time']) ?>
                                <?php if (!empty($a['end_time'])): ?>
                                    - <?= htmlspecialchars($a['end_time']) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($a['category']) ?></td>
                            <td><?= htmlspecialchars($a['priority']) ?></td>
                            <td><?= htmlspecialchars($a['status']) ?></td>
                            <td><?= htmlspecialchars($a['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
