<?php
// Importing "settings-configuration.php".
include_once "../../config/settings-configuration.php";

if(isset($_SESSION['otp']) && isset($_SESSION['verifying_admin'])) {
  unset($_SESSION['otp']);
  unset($_SESSION['verifying_admin']);
}

/**
 * If "$_SESSION["acc_signed_in"]" not yet created or set
 * OR "$_SESSION["acc_signed_in"]" is empty, in short,
 * if admin is not signed in, then user will be prevented 
 * to access this file, and redirecting to "./index.php".
 */
if(!isset($_SESSION["acc_signed_in"]) || empty($_SESSION["acc_signed_in"])) {
  echo "<script>alert(\"Admin must login first.\"); window.location.href = \"../../\";</script>";
  exit();
}
?>

<!-- === HTML Codes === -->
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Metadata -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Tab Icon -->
  <link rel="shortcut icon" href="../../src/res/images/logo.jpg" type="image/x-icon" />

  <!-- Tab Title -->
  <title>Dashboard | Admin</title>

  <!-- CSS Files -->
  <link rel="stylesheet" href="../../src/css/general_style.css">
  <link rel="stylesheet" href="../../src/css/admin-dashboard.css"/>
</head>
<body>
  <!-- Wrapper for the whole content -->
  <div id="wrapper">
    <!-- Header holding the upper part of the page -->
    <header>
      <!-- This div contains the side-bar button and logo -->
      <div>
        <button id="side_bar_button" title="Menu"><img src="../../src/res/images/menu_dark.png"></button>
        <h1><a href="./index.php"><span class="logo-bg1">Black&</span><span class="logo-bg2">White</span></a></h1>
      </div>

      <!-- This div contains the search bar and button -->
      <div>
        <input type="text" name="search" id="search_bar" placeholder="Search..." />
        <button id="search_button" title="Search"><img src="../../src/res/images/search_dark.png"></button>
      </div>

      <!-- This div contains the color theme and account buttons -->
      <div>
        <button id="mode_button" title="Theme"><img src="../../src/res/images/mode_dark.png"></button>
        <button id="account_button" title="<?= $_SESSION["acc_signed_in"]["username"] ?>"><img src="../../src/res/images/account_dark.png"></button>
      </div>
    </header>
    <!-- End of Header -->

    <!-- Main Container -->
    <main>
      <!-- This div contains the side-bar menu -->
      <div id="side_bar">
        <hr style="border: transparent;" />
        <button id="dashboard_button"><img src="../../src/res/images/dashboard_light.png"> Dashboard</button>
        <hr class="divider" />
        <button id="manage_user_button"><img src="../../src/res/images/manage_user_light.png"> Manage Users</button>
        <hr class="divider" />
        <button id="friends_button"><img src="../../src/res/images/friends_light.png"> Friends</button>
        <hr class="divider" />
        <button id="settings_button"><img src="../../src/res/images/settings_light.png"> Settings</button>
        <hr class="divider" />
        <a id="logout_button" href="./authentication/admin-class.php?logout=true"><img src="../../src/res/images/logout_light.png"> Log Out</a>
        <hr class="divider" />
      </div>
      <!-- End of Side-Bar -->

      <!-- This div contains main contents -->
      <div id="main_content">
        <!-- This div contains greeting contents -->
        <div class="greeting_container">
          <h1>Welcome, <?= $_SESSION["acc_signed_in"]["username"] ?>!</h1>
        </div>
        <!-- End of Greeting Container -->

        <!-- This div contains the content containers -->
        <div class="contents_container">
          <div class="content_container">
            <h3>Title Content</h3>
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Pariatur suscipit velit eos repudiandae? Modi velit minima explicabo animi repellat rerum autem in, alias suscipit repellendus, optio doloribus hic magni aspernatur.</p>
          </div>
          <div class="content_container">
            <h3>Title Content</h3>
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Pariatur suscipit velit eos repudiandae? Modi velit minima explicabo animi repellat rerum autem in, alias suscipit repellendus, optio doloribus hic magni aspernatur.</p>
          </div>
          <div class="content_container">
            <h3>Title Content</h3>
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Pariatur suscipit velit eos repudiandae? Modi velit minima explicabo animi repellat rerum autem in, alias suscipit repellendus, optio doloribus hic magni aspernatur.</p>
          </div>
          <div class="content_container">
            <h3>Title Content</h3>
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Pariatur suscipit velit eos repudiandae? Modi velit minima explicabo animi repellat rerum autem in, alias suscipit repellendus, optio doloribus hic magni aspernatur.</p>
          </div>
          <div class="content_container">
            <h3>Title Content</h3>
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Pariatur suscipit velit eos repudiandae? Modi velit minima explicabo animi repellat rerum autem in, alias suscipit repellendus, optio doloribus hic magni aspernatur.</p>
          </div>
          <div class="content_container">
            <h3>Title Content</h3>
            <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Pariatur suscipit velit eos repudiandae? Modi velit minima explicabo animi repellat rerum autem in, alias suscipit repellendus, optio doloribus hic magni aspernatur.</p>
          </div>
        </div>
        <!-- End of Contents Container -->
      </div>
      <!-- End of Main Content -->
    </main>
    <!-- End of Main Container -->
  </div>
  <!-- End of Wrapper Container -->

  <!-- JavaScript Files -->
  <script src="../../src/js/jquery/jquery-3.6.0.min.js"></script>
  <script src="../../src/js/side_bar.js"></script>
  <script src="../../src/js/admin-dashboard-theme.js"></script>
</body>
</html>