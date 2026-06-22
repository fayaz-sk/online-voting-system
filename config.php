<?php
session_start();

$host = "localhost";
$user = "root";
$pass = "";
$db   = "voting_db";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("
    <div style='font-family:sans-serif;max-width:500px;margin:80px auto;background:#fce4ec;padding:30px;border-radius:12px;border-left:5px solid red;'>
    <h2>❌ Database Connection Failed</h2>
    <p>" . $conn->connect_error . "</p>
    <hr style='margin:16px 0'>
    <b>Steps to fix:</b>
    <ol style='line-height:2'>
      <li>Open XAMPP → Start <b>Apache</b> and <b>MySQL</b></li>
      <li>Go to <a href='http://localhost/phpmyadmin'>phpMyAdmin</a></li>
      <li>Create database named <b>voting_db</b></li>
      <li>Import the file: <b>sql/voting_db.sql</b></li>
    </ol>
    </div>");
}

// Helper: get setting value
function getSetting($conn, $key) {
    $r = $conn->query("SELECT setting_value FROM settings WHERE setting_key='$key'");
    $row = $r->fetch_assoc();
    return $row ? $row['setting_value'] : '';
}
?>
