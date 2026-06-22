<?php
require_once 'config.php';
$error = ''; $success = '';
$title = getSetting($conn, 'election_title');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = $conn->real_escape_string(trim($_POST['full_name']));
    $email    = $conn->real_escape_string(trim($_POST['email']));
    $password = MD5($_POST['password']);
    $voter_id = 'VOT' . strtoupper(substr(md5(uniqid()), 0, 6));

    // Check duplicate email
    $check = $conn->query("SELECT id FROM voters WHERE email='$email'");
    if ($check->num_rows > 0) {
        $error = "Email already registered. Please login.";
    } elseif (strlen($_POST['password']) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $conn->query("INSERT INTO voters (full_name, email, voter_id, password) VALUES ('$name','$email','$voter_id','$password')");
        $success = "Registered successfully! Your Voter ID: <b>$voter_id</b>. <a href='index.php'>Login now</a>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Register - <?= htmlspecialchars($title) ?></title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="navbar">
  <h1>🗳️ <?= htmlspecialchars($title) ?></h1>
  <div class="nav-links"><a href="index.php">Login</a></div>
</div>

<div class="container" style="max-width:440px">
  <div class="card" style="margin-top:50px">
    <h2>📝 Voter Registration</h2>
    <?php if ($error): ?><div class="alert alert-error">❌ <?= $error ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success">✅ <?= $success ?></div><?php endif; ?>
    <?php if (!$success): ?>
    <form method="POST">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="full_name" placeholder="Enter your full name" required>
      </div>
      <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" placeholder="your@email.com" required>
      </div>
      <div class="form-group">
        <label>Password (min 6 chars)</label>
        <input type="password" name="password" placeholder="Create a password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Register</button>
    </form>
    <div class="links">Already registered? <a href="index.php">Login here</a></div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
