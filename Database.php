<?php
class Database {
    private $host = "localhost";       // Database host
    private $username = "root";         // Database username
    private $password = "";             // Database password (usually empty for XAMPP)
    private $database = "form";         // Set your actual database name here
    public $connection;

    public function __construct() {
        $this->connect();
    }

    public function connect() {
        $this->connection = new mysqli($this->host, $this->username, $this->password, $this->database);

        // Check for connection error
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }

        return $this->connection;
    }
}
?>
