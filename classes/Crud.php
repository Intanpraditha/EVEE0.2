<?php
class Crud {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($table, $data) {
        $fields = implode(",", array_keys($data));
        $values = "'" . implode("','", array_values($data)) . "'";
        $query = "INSERT INTO $table ($fields) VALUES ($values)";
        return mysqli_query($this->conn, $query);
    }

    public function readAll($table) {
        $result = mysqli_query($this->conn, "SELECT * FROM $table");
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function readById($table, $idField, $idValue) {
        $result = mysqli_query($this->conn, "SELECT * FROM $table WHERE $idField = '$idValue'");
        return mysqli_fetch_assoc($result);
    }

    public function update($table, $data, $idField, $idValue) {
        $updateData = [];
        foreach ($data as $key => $value) {
            $updateData[] = "$key = '$value'";
        }
        $query = "UPDATE $table SET " . implode(",", $updateData) . " WHERE $idField = '$idValue'";
        return mysqli_query($this->conn, $query);
    }

    public function delete($table, $idField, $idValue) {
        return mysqli_query($this->conn, "DELETE FROM $table WHERE $idField = '$idValue'");
    }
}
?>
