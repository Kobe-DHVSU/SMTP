<?php
/**
 * Importing 'dbconnection.php', 'SMTPConfig.php',
 * and 'settings-configuration.php' files.
 */
require_once "../../../database/dbconnection.php";
require_once "../../../config/SMTPConfig.php";
include_once "../../../config/settings-configuration.php";

// Creating "Admin" class.
class Admin {
  private $database; // PDO holder.
  private $smtp; // SMTP Holder.

  /**
   * Assigning "$database" the PDO 
   * from "Database" class in 'dbconnection.php' file.
   * 
   * Assigning "$smtp" with "SMTPConfig" from "SMTPConfig.php" file.
   */
  public function __construct() {
    $this->database = (new Database())->connect();
    $this->smtp = new SMTPConfig();
  }

  // Function for sign up.
  public function add($csrf_token, $user_name, $email, $password) {
    /**
     * CSRF Token Verification.
     * If CSRF Token did not match, 
     * error alert will pop up.
     * Terminating this script.
     */
    if(!isset($csrf_token) || !hash_equals($_SESSION["csrf_token"], $csrf_token)) {
      echo "<script>alert(\"Invalid CSRF Token.\"); window.location.href = \"../../../\";</script>";
      exit();
    }
    
    // Unsetting/Deleting "$_SESSION['csrf_token']".
    unset($_SESSION["csrf_token"]); 

    // Verifying Password Length.
    $this->verifyPasswordLength($password);

    /**
     * Preparing SQL Codes and to be executed.
     * Getting user/s matching with the given email.
     */
    $stmt = $this->database->prepare("SELECT * FROM user WHERE email = :email");
    $stmt->execute(array(":email" => $email));

    /**
     * If $stmt->rowCount() is greater than to 0,
     * then user with the given email has an account already.
     * Terminating this script.
     */
    if($stmt->rowCount() > 0) {
      echo "<script>alert(\"Account Already Existing.\"); window.location.href = \"../../../\";</script>";
      exit();
    }

    // Hashing the "$password" from the sign up to encrypt and secure password.
    $hash_pw = md5($password);
  
    /**
     * Preparing SQL Codes and to be executed.
     * Inserting new account with the given credentials
     * from the sign up page to the database.
     */
    $stmt = $this->database->prepare("INSERT INTO user(username, email, password) VALUES(:username, :email, :password)");

    /**
     * Storing the value (true or false)
     * from "$stmt->execute()" function
     * to check if it is successful or fail.
     */
    $exec = $stmt->execute(array(
      ":username" => $user_name,
      ":email" => $email,
      ":password" => $hash_pw
    ));

    /**
     * If the execution is failed,
     * account will not be added.
     * Otherwise, will be added.
     */
    if(!$exec) {
      echo "<script>alert(\"Accound Adding Failed.\"); window.location.href = \"../../../\";</script>";
      exit();
    }

    echo "
      <script>
        alert(\"Account Added Successfully.\"); 
      </script>
    ";

    /**
     * Saving the "email" and "username" credentials 
     * of unverified account in "$_SESSION['verifying_admin']".
     * 
     * Generated OTP will be stored in "$_SESSION['otp']".
     * 
     * These $_SESSION keys will be deleted to avoid bugs and errors.
     */
    $_SESSION['verifying_admin']['email'] = $email;
    $_SESSION['verifying_admin']['username'] = $user_name;
    $_SESSION['otp'] = rand(100000, 999999);
    
    // Sending OTP.
    $this->sendOTP();

    echo "
      <script>
        alert(\"We have sent you an OTP to {$_SESSION['verifying_admin']['email']}.\"); 
        window.location.href = \"./verify-otp.php\";
      </script>
    ";
  }

  // Signing in an admin account.
  public function signIn($csrf_token, $email, $password) {
    // Using "try-catch" if PDO will fail.
    try {
      /**
       * CSRF Token Verification.
       * If CSRF Token did not match, 
       * error alert will pop up.
       * Terminating this script.
       */
      if(!isset($csrf_token) || !hash_equals($_SESSION["csrf_token"], $csrf_token)) {
        echo "<script>alert(\"Invalid CSRF Token.\"); window.location.href = \"../../../\";</script>";
        exit();
      }

      // Unsetting/Deleting "$_SESSION['csrf_token']".
      unset($_SESSION["csrf_token"]);

      /**
       * Preparing SQL Codes and to be executed.
       * Getting user/s matching with the given email.
       */
      $stmt = $this->database->prepare("SELECT * FROM user WHERE email = :email");
      $stmt->execute(array(":email" => $email));

      // Storing in "$admin" the admin credentials from database by fetching it in associative array type.
      $admin = $stmt->fetch(PDO::FETCH_ASSOC);

      /**
       * If $stmt->rowCount() is less than or equal to 0,
       * then user with the given email has no account yet.
       * Terminating this script.
       */
      if($stmt->rowCount() <= 0) {
        echo "<script>alert(\"Account Does Not Existing.\"); window.location.href = \"../../../\";</script>";
        exit();
      }

      // Verifying Password Length.
      $this->verifyPasswordLength($password);

      /**
       * If an account is existing but not verified,
       * we will send OTP to give chance the admin
       * to verify his/her account.
       */
      if($stmt->rowCount() == 1 && $admin["status"] == "not_verified") {
        echo "
          <script>
            alert(\"Account Is Not Yet Verified.\"); 
          </script>
        ";

        /**
         * Saving the "email" and "username" credentials 
         * of unverified account in "$_SESSION['verifying_admin']".
         * 
         * Generated OTP will be stored in "$_SESSION['otp']".
         * 
         * These $_SESSION keys will be deleted to avoid bugs and errors.
         */
        $_SESSION['verifying_admin']['email'] = $email;
        $_SESSION['verifying_admin']['username'] = $admin['username'];
        $_SESSION['otp'] = rand(100000, 999999);
        $this->sendOTP();

        echo "
          <script>
            alert(\"We have sent you an OTP to {$_SESSION['verifying_admin']['email']}.\"); 
            window.location.href = \"./verify-otp.php\";
          </script>
        ";

        exit();
      }

      /**
       * If $stmt->rowCount() is equal to 1 AND
       * admin password from "$admin["password"]" 
       * is equal to hashed given password "md5($password)",
       * then the admin will be signed in.
       * Otherwise, throwing the alert to show the password
       * from the database and given password is not matched.
       */
      if($stmt->rowCount() == 1 && $admin["password"] == md5($password)) {
        // Calling "logs()" function to log admin activity and his/her id number.
        $this->logs("{$admin['username']} signed in.", $admin["id"]);

        /**
         * Creating associative array named "acc_signed_in"
         * and assigning the associative array "$admin" to "acc_signed_in"
         * in "$_SESSION" so the credential of the signed in admin is accessible 
         * to wherever php file has session started.
         */ 
        $_SESSION["acc_signed_in"] = $admin;

        // Heading to dashboard page "index.php".
        header("Location: ../");
        exit();
      } else {
        echo "<script>alert(\"Wrong Password.\"); window.location.href = \"../../../\";</script>";
        exit();
      }
    } catch(PDOException $pdo_err) {
      echo "<h1>{$pdo_err->getMessage()}</h1>";
    }
  }

  // Signin out the signed in admin.
  public function signOut() {
    /**
     * If "$_SESSION["acc_signed_in"]" is not yet set,
     * then the script will be terminated immediately.
     * Because, it is meaningless to destroy the session.
     * Redirecting back to sign up and sign in ("index.php").
     */
    if(!isset($_SESSION["acc_signed_in"])) exit();

    // Logging the admin's activity, which is signing out.
    $this->logs("{$_SESSION['acc_signed_in']['username']} signed out.", $_SESSION["acc_signed_in"]["id"]);

    // Clearing the "$_SESSION" superglobal.
    session_unset();

    // Destroying session and the "$_SESSION" superglobal.
    session_destroy();

    // Redirecting back to sign up and sign in ("index.php").
    echo "<script>alert(\"Signed Out Successfully.\"); window.location.href = \"../../../\";</script>";
    exit();
  }

  // Function to store admin's activities in database.
  public function logs($activity, $user_id) {
    // Preparing and Executing SQL Codes to store an activity.
    ($this->database->prepare("INSERT INTO logs(activity, user_id) VALUES(:activity, :user_id)"))->execute(array(
      ":activity" => $activity,
      ":user_id" => $user_id
    ));
  }

  // Function for Sending OTP.
  public function sendOTP() {
    /**
     * Storing "email" and "name" of unverified account
     * to use it as receiver credential.
     */
    $receiver_email = $_SESSION['verifying_admin']['email'];
    $receiver_name = $_SESSION['verifying_admin']['username'];

    // Setting Subject for E-Mail.
    $subject = "OTP VERIFICATION";

    /**
     * This "$message" will hold the content of "otp-verification-email.html"
     * to replace "{receiver_name}" and "{otp}" with correct value.
     */
    $message = file_get_contents("./otp-verification-email.html");
    $message = str_replace("{receiver_name}", $receiver_name, $message);
    $message = str_replace("{otp}", $_SESSION['otp'], $message);

    // Send E-Mail with the Credentials of Unverified Account.
    $this->smtp->sendEmail($receiver_email, $receiver_name, $subject, $message);
  }

  // Function for Verifying Admin.
  public function verifyAdmin($otp) {
    /**
     * If "$_SESSION['otp']" is not the same as
     * otp sent from the form, we will throw message 
     * and exit.
     */
    if($_SESSION['otp'] != $otp) {
      echo "
        <script>
          alert(\"{$otp} is not the OTP.\");
          window.location.href = \"./verify-otp.php\";
        </script>
      ";
      exit();
    }

    /**
     * Storing "email" and "name" of unverified account
     * to use it as receiver credential.
     */
    $receiver_email = $_SESSION['verifying_admin']['email'];
    $receiver_name = $_SESSION['verifying_admin']['username'];

    // Generating Hashed Tokencode.
    $tokencode = md5(uniqid(rand()));

    // Updating the "status" and "tokencode" of the unverified account.
    $stmt = $this->database->prepare("UPDATE user SET status = :status, tokencode = :tokencode WHERE email = :email");
    $stmt->execute([
      ":status" => "verified",
      ":tokencode" => $tokencode,
      ":email" => $receiver_email
    ]);

    // Setting Subject for E-Mail.
    $subject = "VERIFICATION SUCCESS";

    /**
     * This "$message" will hold the content of "verification-success-email.html"
     * to replace "{receiver_name}" with correct value.
     */
    $message = file_get_contents("./verification-success-email.html");
    $message = str_replace("{receiver_name}", $receiver_name, $message);

    // Sending E-Mail.
    $this->smtp->sendEmail($receiver_email, $receiver_name, $subject, $message);

    // Clearing the $_SESSION, also to remove the "verifying_admin" and "otp".
    session_unset();
    session_destroy();
    
    echo "
      <script>
        alert(\"Admin {$receiver_name} Verified Successfully.\");
        window.location.href = \"../../../\";
      </script>
    ";
  }

  /**
   * This function is to check if the password length
   * is equal or greater than 8.
   * 
   * If not, then will throw error message and exit.
   */
  private function verifyPasswordLength($password) {
    if(strlen($password) < 8) {
      echo "
        <script>
          alert(\"Password Must Have At Least 8 Characters.\");
          window.location.href = \"../../../\";
        </script>
      ";
      exit();
    }
  }

  /**
   * Function for checking if admin is still in.
   * If "$_SESSION["acc_signed_in"]" is set or created,
   * then an admin account is signed in.
   * Cause to return a value "true".
   * 
   * But, we did not use this function since the
   * credentials of an admin is stored in "$_SESSION["acc_signed_in"]"
   * for convenient, easy, and fast accessibility.
   */
  public function isSignedIn() {
    if(isset($_SESSION["acc_signed_in"])) return true;
  }

  /**
   * Function for redirecting to sign up and sign in page ("index.php").
   * But, we did not use this because the logged in checker is in
   * "./index.php" and "./dashboard/admin/index.php".
   */
  public function redirect() {
    echo "<script>alert(\"Admin must login first.\"); window.location.href = \"../../../\";</script>";
    exit();
  }

  /**
   * Function for returning prepared SQL Codes to be executed.
   * 
   * But, we did not use this also. We sticked to manually
   * use the "prepare()", and for easy debugging incase a bug occured.
   */
  public function runQuery($sql) {
    return $this->database->prepare($sql);
  }

}

/**
 * If "$_POST["signup"]" is existing from "./index.php"
 * with "POST" method, the "add()" from "Admin" class 
 * will be called for adding a new admin account.
 */
if(isset($_POST["signup"])) {
  (new Admin())->add(
    trim($_POST["csrf_token"]),
    trim($_POST["user_name"]),
    trim($_POST["email"]),
    trim($_POST["password"])
  );
}

/**
 * If "$_POST["signin"]" is existing from "./index.php"
 * with "POST" method, the "signIn()" from "Admin" class 
 * will be called for signing in an admin account.
 */
if(isset($_POST["signin"])) {
  (new Admin())->signIn(
    trim($_POST["csrf_token"]),
    trim($_POST["email"]),
    trim($_POST["password"])
  );
}

/**
 * If "$_GET["logout"]" is existing from "./dashboard/admin/index.php"
 * with "GET" method with value of "true", the "signOut()" 
 * from "Admin" class will be called for signing out an admin account.
 */
if(isset($_GET["logout"]) && $_GET["logout"] == true) {
  (new Admin())->signOut();
}

/**
 * If "$_POST['otp_sent']" is existing from "./dashboard/admin/authentication/verify-otp.php"
 * with "POST" method, "verifyAdmin()" from "Admin" class will be called for verifying admin.
 */
if(isset($_POST['otp_sent'])) {
  (new Admin())->verifyAdmin($_POST['otp']);
}