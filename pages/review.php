<?php
require '../includes/check_login.php';
require '../classes/Database.php';
require '../classes/Crud.php';

$db   = new Database();
$conn = $db->getConnection();
$obj  = new Crud($conn);

/* ========================
   UPDATE STATUS INLINE
   ======================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {

    $id     = $_POST['id']     ?? null;
    $status = $_POST['status'] ?? null;

    $allowed = ['pending','approved','rejected'];

    if ($id && in_array($status, $allowed, true)) {
        $data = [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $obj->update('reviews', $data, "id = ?", [$id]);
    }

    // redirect balik ke halaman review
    header("Location: review.php");
    exit;
}


/* ========================
   DELETE
   ======================== */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $obj->delete('reviews', "id = ?", [$id]);
    echo "<script>window.location='reviews.php';</script>";
    exit;
}

/* ========================
   BACA DATA (JOIN USERS)
   ======================== */
$sql = "
    SELECT 
        r.id,
        r.user_id,
        COALESCE(r.display_name, u.name) AS display_name,
        u.name  AS user_name,
        u.email AS user_email,
        r.rating,
        r.review_text,
        r.status,
        r.created_at
    FROM reviews r
    JOIN users u ON u.id = r.user_id
    ORDER BY r.created_at DESC
";
$res = $conn->query($sql);
$reviews = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/topbar.php';
?>

<style>
    .badge-status { border-radius:999px; padding:2px 10px; font-size:.75rem; }
    .badge-pending  { background:#fff4ce; color:#8a6d00; }
    .badge-approved { background:#d1f7c4; color:#1c6b26; }
    .badge-rejected { background:#ffd6d6; color:#a21313; }
    .rating-stars   { color:#ffc107; font-size:.85rem; }
    .review-text-short {
        max-width:350px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-size:.85rem;
    }
    table td.status-col,
    table th.status-col {
        width: 150px !important;
        min-width: 150px !important;
    }
</style>

<div class="container-fluid">
<div class="card">
    <div class="card-body">
        <h4 class="fw-semibold mb-4">Data Ulasan Pengguna</h4>

        <div class="card">
            <div class="card-body table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pengguna</th>
                            <th>Nama tampil</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th class="status-col">Status</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if (empty($reviews)): ?>
                        <tr><td colspan="8" class="text-center text-muted">Belum ada ulasan.</td></tr>
                    <?php else: ?>

                        <?php foreach ($reviews as $r): ?>
                            <?php 
                                $badgeClass = $r['status']=='approved' ? 'badge-approved' :
                                              ($r['status']=='rejected' ? 'badge-rejected':'badge-pending');

                                $created = date('d M Y H:i', strtotime($r['created_at']));
                            ?>
                            
                            <tr>
                                <td><?= $r['id'] ?></td>

                                <td>
                                    <strong><?= htmlspecialchars($r['user_name']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($r['user_email']) ?></small>
                                </td>

                                <td><?= htmlspecialchars($r['display_name']) ?></td>

                                <td>
                                    <div class="rating-stars">
                                        <?php for ($i=0; $i<$r['rating']; $i++) echo "★"; ?>
                                    </div>
                                    <small class="text-muted"><?= $r['rating'] ?>/5</small>
                                </td>

                                <td>
                                    <div class="review-text-short" title="<?= htmlspecialchars($r['review_text']) ?>">
                                        <?= htmlspecialchars($r['review_text']) ?>
                                    </div>
                                </td>

                                <td class="status-col">
                                    <form method="POST" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($r['id']) ?>">
                                        
                                        <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="pending"  <?= $r['status']=='pending'  ? 'selected' : '' ?>>Pending</option>
                                            <option value="approved" <?= $r['status']=='approved'? 'selected' : '' ?>>Approved</option>
                                            <option value="rejected" <?= $r['status']=='rejected'? 'selected' : '' ?>>Rejected</option>
                                        </select>

                                        <input type="hidden" name="change_status" value="1">
                                    </form>
                                </td>


                                <td><?= $created ?></td>

                                <td>
                                    <a href="reviews.php?delete=<?= $r['id'] ?>"
                                       onclick="return confirm('Hapus review ini?')"
                                       class="btn btn-danger btn-sm">
                                       Hapus
                                    </a>
                                </td>
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
