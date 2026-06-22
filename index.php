<?php
require_once '../config.php';
$error = '';

if (isset($_SESSION['admin_id'])) { header("Location: dashboard.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $conn->real_escape_string(trim($_POST['username']));
    $pass = MD5($_POST['password']);
    $r = $conn->query("SELECT * FROM admin WHERE username='$user' AND password='$pass'");
    if ($r->num_rows === 1) {
        $a = $r->fetch_assoc();
        $_SESSION['admin_id']   = $a['id'];
        $_SESSION['admin_name'] = $a['full_name'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
$title = getSetting($conn, 'election_title');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="navbar"><h1>⚙️ Admin Panel — <?= htmlspecialchars($title) ?></h1></div>
<div class="container" style="max-width:400px">
  <div class="card" style="margin-top:70px">
    <h2>🔐 Admin Login</h2>
    <?php if ($error): ?><div class="alert alert-error">❌ <?= $error ?></div><?php endif; ?>
    <form method="POST">
      <div class="form-group"><label>Username</label>
        <input type="text" name="username" placeholder="admin" required></div>
      <div class="form-group"><label>Password</label>
        <input type="password" name="password" placeholder="Password" required></div>
      <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
    <div class="links"><a href="../index.php">← Back to Voter Portal</a></div>
  </div>
  <p style="text-align:center;color:#aaa;font-size:12px;margin-top:10px">Default: admin / admin123</p>
</div>
</body></html>
