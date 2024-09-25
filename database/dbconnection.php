<?php
// Creating "Database" class.
class Database {
  private $server_name; // Contains server name.
  private $db_name; // Contains database name.
  private $user_name; // Contains username.
  private $password; // Contains password.
  public $pdo; // Contains PDO object.

  // Function for creating "Database" instance.
  public function __construct() {
    if($_SERVER["SERVER_NAME"] === "localhost" || $_SERVER["SERVER_ADDR"] === "127.0.0.1" || $_SERVER["SERVER_ADDR"] === "192.168.1.72") {
      $this->server_name = "localhost";
      $this->db_name = "itelec2";
      $this->user_name = "root";
      $this->password = "";
    } else {
      $this->server_name = "localhost";
      $this->db_name = "";
      $this->user_name = "";
      $this->password = "";
    }
  }

  // Function for database connection.
  public function connect() {
    // Initializing "$pdo" to null.
    $this->pdo = null;
    
    // Using "try-catch" incase PDO failed.
    try {
      // Creating PDO and storing in "$pdo".
      $this->pdo = new PDO("mysql:host={$this->server_name};dbname={$this->db_name}", $this->user_name, $this->password);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $pdo_err) {
      echo "<script>alert(\"Database Connection Failed.\");</script>";
      echo "<h1>{$pdo_err->getMessage()}</h1>";
    }

    return $this->pdo;
  }
}