<?php
require '../classes/Database.php';
require '../classes/Crud.php';

$db   = new Database();
$conn = $db->getConnection();

// Ambil user_articles + user + article
$sql = "
    SELECT ua.*, 
           u.name       AS user_name,
           a.title      AS article_title,
           a.phase      AS article_phase,
           a.link       AS article_link
    FROM user_articles ua
    JOIN users    u ON u.id = ua.user_id
    JOIN articles a ON a.id = ua.article_id
    ORDER BY ua.read_at DESC, ua.created_at DESC
";
$result       = $conn->query($sql);
$userArticles = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="container-fluid">
    <h4 class="fw-semibold mb-4">Artikel yang Dibaca / Disimpan User</h4>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Judul Artikel</th>
                        <th>Fase</th>
                        <th>Link</th>
                        <th>Read At</th>
                        <th>Saved?</th>
                        <th>Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($userArticles)): ?>
                    <tr><td colspan="7" class="text-center">Belum ada data</td></tr>
                <?php else: ?>
                    <?php foreach ($userArticles as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['user_name']) ?> (<?= htmlspecialchars($row['user_id']) ?>)</td>
                            <td><?= htmlspecialchars($row['article_title']) ?></td>
                            <td><?= htmlspecialchars($row['article_phase']) ?></td>
                            <td>
                                <a href="<?= htmlspecialchars($row['article_link']) ?>" target="_blank">
                                    <?= htmlspecialchars($row['article_link']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($row['read_at']) ?></td>
                            <td>
                                <?= $row['saved'] ? '<span class="badge bg-success">Yes</span>'
                                                 : '<span class="badge bg-secondary">No</span>' ?>
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

<?php include '../includes/footer.php'; ?>
