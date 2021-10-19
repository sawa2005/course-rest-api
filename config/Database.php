<?php
class Database {
    // Database settings
    private $host = 'localhost';
    private $db_name = 'rest_kurser';
    private $username = 'root';
    private $password = '';
    private $conn;

    // Connect to the database
    public function connect() {
        $this->conn = null;

        try {
            $this->conn = mysqli_connect($this->host, $this->username, $this->password, $this->db_name) or die('Fel vid anslutning');
        }
        // In case of error
        catch(PDOException $e) {
            echo "Connection Error " . $e->getMessage();
        }
        
        return $this->conn;
    }

    // Close database connection
    public function close() {
        $this->conn = null;
    }
}