<?php
// api/reviews.php
require 'config.php';

// ==========================
// GET: ambil review (landing page & app)
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;

    $sql = "SELECT 
                r.id,
                r.user_id,
                COALESCE(r.display_name, u.name) AS name,
                r.rating,
                r.review_text,
                r.created_at
            FROM reviews r
            JOIN users u ON u.id = r.user_id
            WHERE r.status = 'approved'
            ORDER BY r.created_at DESC";

    if ($limit > 0) {
        $sql .= " LIMIT ".$limit;
    }

    $res  = $conn->query($sql);
    $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];

    jsonResponse(200, $data);
}

// ==========================
// POST: kirim / update review dari mobile
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = getJsonInput();

    $user_id      = $input['user_id']      ?? null;
    $rating       = $input['rating']       ?? null;
    $review_text  = trim($input['review_text'] ?? '');
    $display_name = trim($input['display_name'] ?? '');

    if (!$user_id || !$rating || $review_text === '') {
        jsonResponse(400, ['error' => 'user_id, rating, dan review_text wajib diisi']);
    }

    $rating = (int)$rating;
    if ($rating < 1 || $rating > 5) {
        jsonResponse(400, ['error' => 'rating harus 1-5']);
    }

    // cek user ada atau tidak
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $resUser = $stmt->get_result();
    if ($resUser->num_rows === 0) {
        jsonResponse(404, ['error' => 'User tidak ditemukan']);
    }

    // cek sudah pernah review?
    $stmt = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? LIMIT 1");
    $stmt->bind_param('s', $user_id);
    $stmt->execute();
    $resRev = $stmt->get_result();

    if ($resRev->num_rows > 0) {
        // UPDATE review lama
        $row = $resRev->fetch_assoc();
        $id  = $row['id'];

        $sql = "UPDATE reviews
                SET display_name = ?, rating = ?, review_text = ?, 
                    status = 'pending', updated_at = NOW()
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $dn = $display_name ?: null;
        // s (display_name), i (rating), s (review_text), s (id)
        $stmt->bind_param('siss', $dn, $rating, $review_text, $id);
        $ok = $stmt->execute();

        if (!$ok) {
            jsonResponse(500, ['error' => 'Gagal memperbarui review']);
        }

        jsonResponse(200, [
            'success' => true,
            'id'      => $id,
            'message' => 'Review diperbarui, menunggu persetujuan admin.'
        ]);
    } else {
        // INSERT baru
        $id = uniqid('R');

        $sql = "INSERT INTO reviews
                (id, user_id, display_name, rating, review_text, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, 'pending', NOW(), NOW())";
        $stmt = $conn->prepare($sql);
        $dn = $display_name ?: null;
        // PARAMETER: id (s), user_id (s), display_name (s), rating (i), review_text (s)
        $stmt->bind_param('sssis', $id, $user_id, $dn, $rating, $review_text);
        //              ^^ PERBAIKAN DISINI (5 tipe, 5 param)
        $ok = $stmt->execute();

        if (!$ok) {
            jsonResponse(500, ['error' => 'Gagal menyimpan review']);
        }

        jsonResponse(201, [
            'success' => true,
            'id'      => $id,
            'message' => 'Review terkirim, menunggu persetujuan admin.'
        ]);
    }
}

// fallback kalau method bukan GET/POST
jsonResponse(405, ['error' => 'Method not allowed']);
