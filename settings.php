<?php
require_once 'auth.php';
requireAdmin();
$msg = '';

// Quick toggle from dashboard
if (isset($_POST['quick_toggle'])) {
    $status = $conn->real_escape_string($_POST['election_status']);
    $conn->query("UPDATE settings SET setting_value='$status' WHERE setting_key='election_status'");
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $et     = $conn->real_escape_string(trim($_POST['election_title']));
    $es     = $conn->real_escape_string($_POST['election_status']);
    $cn     = $conn->real_escape_string(trim($_POST['college_name']));
    $conn->query("UPDATE settings SET setting_value='$et' WHERE setting_key='election_title'");
    $conn->query("UPDATE settings SET setting_value='$es' WHERE setting_key='election_status'");
    $conn->query("UPDATE settings SET setting_value='$cn' WHERE setting_key='college_name'");

    // Change admin password
    if (!empty($_POST['new_password'])) {
        if ($_POST['new_password'] === $_POST['confirm_password']) {
            $newp = MD5($_POST['new_password']);
            $conn->query("UPDATE admin SET password='$newp' WHERE id={$_SESSION['admin_id']}");
            $msg = "Settings and password updated.";
        } else {
            $msg = "Passwords do not match!";
        }
    } else {
        $msg = "Settings saved.";
    }
}

$et = getSetting($conn, 'election_title');
$es = getSetting($conn, 'election_status');
$cn = getSetting($conn, 'college_name');
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Settings - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head><body>
<div class="navbar"><h1>⚙️ Admin Panel</h1><div class="nav-links"><a href="logout.php" class="btn-nav">Logout</a></div></div>
<div class="container-wide">
  <div class="admin-wrapper">
    <div class="sidebar card card-sm"><?= adminNav('settings') ?></div>
    <div class="main-content">
      <div class="page-title">Settings</div>
      <?php if ($msg): ?><div class="alert alert-info">✅ <?= $msg ?></div><?php endif; ?>

      <div class="card">
        <h2>Election Settings</h2>
        <form method="POST">
          <div class="form-group">
            <label>Election Title</label>
            <input type="text" name="election_title" value="<?= htmlspecialchars($et) ?>" required>
          </div>
          <div class="form-group">
            <label>College / Organization Name</label>
            <input type="text" name="college_name" value="<?= htmlspecialchars($cn) ?>">
          </div>
          <div class="form-group">
            <label>Voting Status</label>
            <select name="election_status">
              <option value="open"   <?= $es==='open'  ?'selected':''?>>🟢 Open (Voting Allowed)</option>
              <option value="closed" <?= $es==='closed'?'selected':''?>>🔴 Closed (No Voting)</option>
            </select>
          </div>
          <hr style="margin:20px 0;border-color:#e8eaf6">
          <h3 style="margin-bottom:14px;font-size:15px;color:#555">Change Admin Password (leave blank to keep current)</h3>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
            <div class="form-group">
              <label>New Password</label>
              <input type="password" name="new_password" placeholder="Leave blank to keep">
            </div>
            <div class="form-group">
              <label>Confirm Password</label>
              <input type="password" name="confirm_password" placeholder="Repeat new password">
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
      </div>
    </div>
  </div>
</div>
</body></html>
