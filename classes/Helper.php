<?php
class Helper
{
    public static function generateId(mysqli $conn, string $table, string $prefix, string $column = 'id')
    {
        // ambil ID terakhir
        $sql = "SELECT $column FROM $table ORDER BY $column DESC LIMIT 1";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $last = $row[$column];

            // ambil angka di belakang prefix (misal U001 → 001)
            $number = intval(substr($last, strlen($prefix))) + 1;
            return $prefix . str_pad($number, 3, "0", STR_PAD_LEFT);
        } else {
            // kalau belum ada data
            return $prefix . "001";
        }
    }
}
