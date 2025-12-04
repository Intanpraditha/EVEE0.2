<?php
require '../includes/check_login.php';
require '../classes/Database.php';

$db   = new Database();
$conn = $db->getConnection();

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=users_export_' . date("Y-m-d") . '.csv');

$output = fopen('php://output', 'w');

// Header CSV
fputcsv($output, ['ID User', 'Nama', 'Email', 'Role', 'Tanggal Buat', 'Terakhir Login']);

$sql = "
    SELECT 
        id,
        name,
        email,
        role,
        created_at,
        last_login
    FROM users
    WHERE LOWER(role) = 'user'
    ORDER BY created_at DESC
";

$res = $conn->query($sql);

// Debug kalau query error
if (!$res) {
    fputcsv($output, ["SQL Error:", $conn->error]);
    fclose($output);
    exit;
}

// Kalau tidak ada data user
if ($res->num_rows === 0) {
    fputcsv($output, ["Tidak ada data user"]);
    fclose($output);
    exit;
}

// Tulis setiap row
while ($row = $res->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['name'],
        $row['email'],
        $row['role'],
        $row['created_at'],
        $row['last_login']
    ]);
}

fclose($output);
exit;
