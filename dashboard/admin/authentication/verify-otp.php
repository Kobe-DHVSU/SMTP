<?php
// Including "settings-configuration.php" from "config" folder.
include_once __DIR__ . "/../../../config/settings-configuration.php";

/**
 * If "$_SESSION['verifying_admin']" and "$_SESSION['otp']" is not
 * set, we will throw error message and redirect to index.php
 */
if(!isset($_SESSION['verifying_admin']) && !isset($_SESSION['otp'])) {
  echo "
    <script>
      alert(\"No OTP Set!\");
      window.location.href = \"../../../\";
    </script>
  ";
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Verify <?= $_SESSION['verifying_admin']['username'] ?></title>
  <link rel="shortcut icon" href="../../../src/res/images/logo.jpg" type="image/x-icon"/>
  <link rel="stylesheet" href="../../../src/css/general_style.css">
  <link rel="stylesheet" href="../../../src/css/verify-otp-style.css">
</head>
<body>
  <div id="wrapper">
    <header>
      <h1><span class="logo-bg1">Black&</span><span class="logo-bg2">White</span></h1>
    </header>
    <main>
      <h1>Enter OTP</h1>
      <p>Enter the sent OTP to <?= $_SESSION['verifying_admin']['email'] ?></p>
      <form action="./admin-class.php" method="POST">
        <input type="number" name="otp" placeholder="Enter OTP" required />
        <input type="submit" name="otp_sent" value="Verify" />
      </form>
    </main>
  </div>
</body>
</html>