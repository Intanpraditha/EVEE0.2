<?php
require '../includes/check_login.php';
require '../classes/Database.php';
require '../classes/Crud.php';

$db   = new Database();
$conn = $db->getConnection();

// Ambil day_logs + user + period_cycles (opsional)
$sql = "
    SELECT dl.*, 
           u.name AS user_name,
           pc.start_date AS period_start,
           pc.end_date   AS period_end
    FROM day_logs dl
    JOIN users u ON u.id = dl.user_id
    LEFT JOIN period_cycles pc ON pc.id = dl.period_id
    ORDER BY dl.date DESC
";
$result   = $conn->query($sql);
$dayLogs  = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';
?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h4 class="fw-semibold mb-4">Log Harian (Gejala & Flow)</h4>

            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>User</th>
                                <th>Fase</th>
                                <th>Flow</th>
                                <th>Gejala</th>
                                <th>Period</th>
                                <th>Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($dayLogs)): ?>
                            <tr><td colspan="7" class="text-center">Belum ada data</td></tr>
                        <?php else: ?>
                            <?php foreach ($dayLogs as $row): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['date']) ?></td>
                                    <td><?= htmlspecialchars($row['user_name']) ?> (<?= htmlspecialchars($row['user_id']) ?>)</td>
                                    <td><?= htmlspecialchars($row['phase']) ?></td>
                                    <td><?= htmlspecialchars($row['flow']) ?></td>
                                    <td style="max-width:250px; white-space:pre-wrap;">
                                        <?= htmlspecialchars($row['symptoms']) ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['period_id'])): ?>
                                            ID: <?= htmlspecialchars($row['period_id']) ?><br>
                                            <?= htmlspecialchars($row['period_start']) ?> 
                                            s/d <?= htmlspecialchars($row['period_end']) ?>
                                        <?php else: ?>
                                            <em>-</em>
                                        <?php endif; ?>
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
