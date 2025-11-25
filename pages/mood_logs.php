<?php
require '../classes/Database.php';
require '../classes/Crud.php';

$db   = new Database();
$conn = $db->getConnection();

// Ambil mood_logs + user + mood
$sql = "
    SELECT ml.*, 
           u.name  AS user_name,
           m.name  AS mood_name,
           m.mood_tag
    FROM mood_logs ml
    JOIN users u ON u.id = ml.user_id
    JOIN moods m ON m.id = ml.mood_id
    ORDER BY ml.date DESC, ml.time DESC
";
$result    = $conn->query($sql);
$moodLogs  = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h4 class="fw-semibold mb-4">Riwayat Mood User</h4>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>User</th>
                                <th>Mood</th>
                                <th>Tag</th>
                                <th>Catatan</th>
                                <th>Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($moodLogs)): ?>
                            <tr><td colspan="7" class="text-center">Belum ada data</td></tr>
                        <?php else: ?>
                            <?php foreach ($moodLogs as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['date']) ?></td>
                                    <td><?= htmlspecialchars($row['time']) ?></td>
                                    <td><?= htmlspecialchars($row['user_name']) ?> (<?= htmlspecialchars($row['user_id']) ?>)</td>
                                    <td><?= htmlspecialchars($row['mood_name']) ?></td>
                                    <td><?= htmlspecialchars($row['mood_tag']) ?></td>
                                    <td style="max-width:250px; white-space:pre-wrap;">
                                        <?= htmlspecialchars($row['note']) ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['created_at']) ?></td>
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
