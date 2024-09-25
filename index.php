<?php
// Importing "settings-configuration.php".
include_once "./config/settings-configuration.php";

if(isset($_SESSION['otp']) && isset($_SESSION['verifying_admin'])) {
  unset($_SESSION['otp']);
  unset($_SESSION['verifying_admin']);
}

/**
 * If "$_SESSION["acc_signed_in"]" is created or set
 * OR "$_SESSION["acc_signed_in"]" is not empty, in short,
 * if admin is signed in, then user will be prevented 
 * to access this file, and redirecting to "./dashboard/admin/index.php".
 */
if(isset($_SESSION["acc_signed_in"]) || !empty($_SESSION["acc_signed_in"])) {
  echo "<script>alert(\"Admin {$_SESSION['acc_signed_in']['username']} is currently logged in.\"); window.location.href = \"./dashboard/admin/\";</script>";
  exit();
}
?>

<!-- === HTML Codes === -->
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Metadata -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Tab Icon -->
  <link rel="shortcut icon" href="./src/res/images/logo.jpg" type="image/x-icon" />

  <!-- Tab Title -->
  <title>Black&White</title>

  <!-- CSS Files -->
   <link rel="stylesheet" href="./src/css/general_style.css" />
   <link rel="stylesheet" href="./src/css/signupin.css" />
</head>
<body>
  <!-- Wrapper for the whole content -->
  <div id="wrapper">
    <!-- This div contains web logo and details -->
    <div id="info">
      <h1><span class="logo-bg1">Black&</span><span class="logo-bg2">White</span></h1>
      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Perspiciatis totam dignissimos, amet aliquid numquam ratione distinctio provident molestiae libero velit, pariatur, aliquam est laborum consequuntur! Voluptate molestias repellendus quas architecto!</p>
    </div>
    <!-- End of Info Container -->

    <hr class="divider" />

    <!-- This div contains sign up form -->
    <div class="form" id="signup">
      <h1>Sign Up</h1>
      <form action="./dashboard/admin/authentication/admin-class.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>" />
        <input class="input" type="text" name="user_name" placeholder="Enter username" required />
        <br />
        <input class="input" type="email" name="email" placeholder="Enter email" required />
        <br />
        <input class="input" type="password" name="password" placeholder="Enter password" required />
        <br />
        <input class="invert-bgcolor submit-button" type="submit" name="signup" value="Sign Up">
      </form>
      <button id="sign_in_button">Sign In</button>
    </div>
    <!-- End of Sign Up -->

    <!-- This div contains sign in form -->
    <div class="form" id="signin">
      <h1>Sign In</h1>
      <form action="./dashboard/admin/authentication/admin-class.php" method="post">
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>" />
        <input class="input" type="email" name="email" placeholder="Enter email" required />
        <br />
        <input class="input" type="password" name="password" placeholder="Enter password" required />
        <br />
        <input class="invert-bgcolor submit-button" type="submit" name="signin" value="Sign In">
      </form>
      <button id="sign_up_button">Sign Up</button>
    </div>
    <!-- End of Sign In -->
  </div>
  <!-- End of Wrapper -->

  <!-- JavaScript Files -->
   <script src="./src/js/jquery/jquery-3.6.0.min.js"></script>
   <script src="./src/js/signupin.js"></script>
</body>
</html>