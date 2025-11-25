<?php
class Database
{
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "evee_new"; // ganti sesuai nama database kamu

    private $conn;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

        if ($this->conn->connect_error) {
            die("Koneksi gagal: " . $this->conn->connect_error);
        }

        // biar aman buat emoji / karakter non-ASCII
        $this->conn->set_charset("utf8mb4");
    }

    /** @return mysqli */
    public function getConnection()
    {
        if (!$this->conn) {
            $this->connect();
        }
        return $this->conn;
    }
}
