<?php
/**
 * Importing "dbconnection.php" from "database" folder,
 * and "autoload.php" from "vendor" folder.
 */
require_once __DIR__ . "/../database/dbconnection.php";
require_once __DIR__ . "/../src/vendor/autoload.php";

/**
 * Using or Including "PHPMailer", "Exception", and "SMTP" classes
 * by the help of "composer.json" and "autoload.php".
 * 
 * Those classes are in namspace "PHPMailer\PHPMailer".
 */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class SMTPConfig {
  private $email; // Email for SMTP Server.
  private $name; // Name for SMTP Server.
  private $password; // Password fro SMTP Server.
  private $pdo; // Database Connection Instance.

  public function __construct() {
    // Connecting to Database.
    $this->pdo = (new Database())->connect();

    // Fetching Account for SMTP Server from Database.
    $stmt = $this->pdo->prepare("SELECT * FROM smtp_user");
    $stmt->execute();
    $smtp_user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Assigning "email", "name", and "password" from Database.
    $this->email = $smtp_user['email'];
    $this->name = $smtp_user['name'];
    $this->password = $smtp_user['password'];
  }

  // Function for Getting Email.
  public function getEmail() {
    return $this->email;
  }

  // Function for Getting Name.
  public function getName() {
    return $this->name;
  }

  // Function for Getting Password.
  public function getPassword() {
    return $this->password;
  }

  // Function for Sending E-Mail with PHPMailer.
  public function sendEmail($receiver_email, $receiver_name, $subject, $message) {
    /**
     * This code will use "PHPMailer".
     * 
     * The "true" is optional, but we use it for try-catch purpose.
     * In-case an error occured on sending e-mail, we can identify
     * with try-catch.
     */
    $php_mailer = new PHPMailer(true); // Enable Exception.
    try {
      // Server Settings.
      $php_mailer->isSMTP();
      $php_mailer->SMTPDebug = 0;
      $php_mailer->SMTPAuth = true;
      $php_mailer->SMTPSecure = "tls";
      $php_mailer->Host = "smtp.gmail.com";
      $php_mailer->Port = 587;
      $php_mailer->Username = $this->email;
      $php_mailer->Password = $this->password;

      // Sender Settings.
      $php_mailer->setFrom($this->email, $this->name);

      // Receiver Settings.
      $php_mailer->addAddress($receiver_email, $receiver_name);

      // Message Settings.
      $php_mailer->Subject = $subject;
      $php_mailer->msgHTML($message);

      // Send Email.
      $php_mailer->send();
    } catch(Exception $php_mailer_err) {
      echo "
        <script>
          alert(\"Send Email Failed.\");
          alert(\"{$php_mailer_err->getMessage()}\");
          window.location.href = \"../../../\";
        </script>
      ";
      exit();
    }
  }
}