<?php
// To start web session and use the $_SESSION superglobal.
session_start();

// Setting Error Reports.
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

// Generating CSRF Token.
if(empty($_SESSION["csrf_token"])) {
  $_SESSION["csrf_token"] = $csrf_token = bin2hex(random_bytes(32));
} else {
  $csrf_token = $_SESSION["csrf_token"];
}