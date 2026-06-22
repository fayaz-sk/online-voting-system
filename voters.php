<?php
require_once 'auth.php';
requireAdmin();
$msg = '';

// Block/Unblock voter
if (isset($_GET['block'])) {
    $id = (int)$_GET['block'];
    $conn->query("UPDATE voters SET status='blocked' WHERE id=$id");
    $msg = "Voter blocked.";
}
if (isset($_GET['unblock'])) {
    $id = (int)$_GET['unblock'];
    $conn->query("UPDATE voters SET status='active' WHERE id=$id");
    $msg = "Voter unblocked.";
}
// Delete voter
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM votes WHERE voter_id=$id");
    $conn->query("DELETE FROM voters WHERE id=$id");
    $msg = "Voter deleted.";
}
// Reset vote
if (isset($_GET['reset'])) {
    $id = (int)$_GET['reset'];
    // Get their votes and decrement candidate counts
    $vts = $conn->query("SELECT candidate_id FROM votes WHERE voter_id=$id");
    while ($vt = $vts->fetch_assoc()) {
        $conn->query("UPDATE candidates SET votes=votes-1 WHERE id={$vt['candidate_id']} AND votes>0");
    }
    $conn->query("DELETE FROM votes WHERE voter_id=$id");
    $conn->query("UPDATE voters SET has_voted=0 WHERE id=$id");
    $msg = "Voter's vote has been reset.";
}

// Add voter manually
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = $conn->real_escape_string(trim($_POST['full_name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $pass  = MD5($_POST['password']);
    $vid   = 'VOT' . strtoupper(substr(md5(uniqid()),0,6));
    $chk   = $conn->query("SELECT id FROM voters WHERE email='$email'");
    if ($chk->num_rows > 0) { $msg = "Email already exists."; }
    else {
        $conn->query("INSERT INTO voters (full_name,email,voter_id,password) VALUES ('$name','$email','$vid','$pass')");
        $msg = "Voter added. Voter ID: $vid";
    }
}

$voters = $conn->query("SELECT * FROM voters ORDER BY registered_at DESC");
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Voters - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head><body>
<div class="navbar">
  <h1>⚙️ Admin Panel</h1>
  <div class="nav-links">
    <a href="logout.php" class="btn-nav">Logout</a>
  </div>
</div>
<div class="container-wide">
  <div class="admin-wrapper">
    <div class="sidebar card card-sm"><?= adminNav('voters') ?></div>
    <div class="main-content">
      <div class="page-title">Manage Voters</div>
      <?php if ($msg): ?><div class="alert alert-info">ℹ️ <?= $msg ?></div><?php endif; ?>

      <!-- Add Voter -->
      <div class="card card-sm">
        <h2>Add Voter Manually</h2>
        <form method="POST" style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:12px;align-items:end">
          <div><label>Full Name</label><input type="text" name="full_name" required></div>
          <div><label>Email</label><input type="email" name="email" required></div>
          <div><label>Password</label><input type="password" name="password" value="voter123" required></div>
          <div><button type="submit" class="btn btn-primary">Add</button></div>
        </form>
      </div>

      <div class="card">
        <h2>All Voters</h2>
        <table>
          <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Voter ID</th><th>Status</th><th>Voted</th><th>Actions</th></tr></thead>
          <tbody>
          <?php $i=1; while ($v = $voters->fetch_assoc()): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($v['full_name']) ?></td>
            <td><?= htmlspecialchars($v['email']) ?></td>
            <td><span class="badge badge-blue"><?= $v['voter_id'] ?></span></td>
            <td><?= $v['status']==='active' ? '<span class="badge badge-green">Active</span>' : '<span class="badge badge-red">Blocked</span>' ?></td>
            <td><?= $v['has_voted'] ? '<span class="badge badge-green">Yes</span>' : '<span class="badge">No</span>' ?></td>
            <td style="white-space:nowrap">
              <?php if ($v['status']==='active'): ?>
                <a href="?block=<?= $v['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Block this voter?')">Block</a>
              <?php else: ?>
                <a href="?unblock=<?= $v['id'] ?>" class="btn btn-sm btn-success">Unblock</a>
              <?php endif; ?>
              <?php if ($v['has_voted']): ?>
                <a href="?reset=<?= $v['id'] ?>" class="btn btn-sm" style="background:#f57f17;color:#fff" onclick="return confirm('Reset their vote?')">Reset</a>
              <?php endif; ?>
              <a href="?delete=<?= $v['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete permanently?')">Delete</a>
            </td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body></html>
