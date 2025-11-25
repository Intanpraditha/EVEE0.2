<?php
// class Crud {
//     private $conn;

//     public function __construct($db) {
//         $this->conn = $db;
//     }

//     public function create($table, $data) {
//         $fields = implode(",", array_keys($data));
//         $values = "'" . implode("','", array_values($data)) . "'";
//         $query = "INSERT INTO $table ($fields) VALUES ($values)";
//         return mysqli_query($this->conn, $query);
//     }

//     public function readAll($table) {
//         $result = mysqli_query($this->conn, "SELECT * FROM $table");
//         return mysqli_fetch_all($result, MYSQLI_ASSOC);
//     }

//     public function readById($table, $idField, $idValue) {
//         $result = mysqli_query($this->conn, "SELECT * FROM $table WHERE $idField = '$idValue'");
//         return mysqli_fetch_assoc($result);
//     }

//     public function update($table, $data, $idField, $idValue) {
//         $updateData = [];
//         foreach ($data as $key => $value) {
//             $updateData[] = "$key = '$value'";
//         }
//         $query = "UPDATE $table SET " . implode(",", $updateData) . " WHERE $idField = '$idValue'";
//         return mysqli_query($this->conn, $query);
//     }

//     public function delete($table, $idField, $idValue) {
//         return mysqli_query($this->conn, "DELETE FROM $table WHERE $idField = '$idValue'");
//     }
// }


class Crud
{
    /** @var mysqli */
    private $conn;

    public function __construct(mysqli $connection)
    {
        $this->conn = $connection;
    }

    public function create(string $table, array $data): bool
    {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $values = array_values($data);
        // semua string → 's', kalau ada INT bisa disesuaikan
        $types  = str_repeat("s", count($values));

        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    public function readAll(string $table): array
    {
        $sql = "SELECT * FROM {$table}";
        $result = $this->conn->query($sql);

        if (!$result) {
            die("Query failed: " . $this->conn->error);
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function update(string $table, array $data, string $where, array $whereParams): bool
    {
        $set = [];
        foreach ($data as $col => $val) {
            $set[] = "{$col} = ?";
        }
        $setClause = implode(", ", $set);

        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $values = array_values($data);
        $values = array_merge($values, array_values($whereParams));
        $types  = str_repeat("s", count($values));

        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    public function delete(string $table, string $where, array $whereParams): bool
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $values = array_values($whereParams);
        $types  = str_repeat("s", count($values));

        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }
}
?>
