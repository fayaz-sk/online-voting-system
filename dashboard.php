<?php
require_once 'auth.php';
requireAdmin();
$title         = getSetting($conn, 'election_title');
$status        = getSetting($conn, 'election_status');
$total_voters  = $conn->query("SELECT COUNT(*) as c FROM voters")->fetch_assoc()['c'];
$total_voted   = $conn->query("SELECT COUNT(*) as c FROM voters WHERE has_voted=1")->fetch_assoc()['c'];
$total_cands   = $conn->query("SELECT COUNT(*) as c FROM candidates")->fetch_assoc()['c'];
$total_pos     = $conn->query("SELECT COUNT(*) as c FROM positions")->fetch_assoc()['c'];
$turnout       = $total_voters > 0 ? round(($total_voted / $total_voters) * 100) : 0;
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head><body>
<div class="navbar">
  <h1>⚙️ Admin Panel</h1>
  <div class="nav-links">
    <span style="color:#c5caf9;font-size:13px">👤 <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
    <a href="../results.php" target="_blank">View Site</a>
    <a href="logout.php" class="btn-nav">Logout</a>
  </div>
</div>
<div class="container-wide">
  <div class="admin-wrapper">
    <div class="sidebar card card-sm">
      <?= adminNav('dashboard') ?>
    </div>
    <div class="main-content">
      <div class="page-title">Dashboard</div>
      <div class="page-sub"><?= htmlspecialchars($title) ?> &mdash; Status: <span class="badge <?= $status==='open'?'badge-green':'badge-red' ?>"><?= strtoupper($status) ?></span></div>

      <div class="stats-grid">
        <div class="stat-card"><div class="stat-num"><?= $total_voters ?></div><div class="stat-label">Total Voters</div></div>
        <div class="stat-card"><div class="stat-num"><?= $total_voted ?></div><div class="stat-label">Voted</div></div>
        <div class="stat-card"><div class="stat-num"><?= $turnout ?>%</div><div class="stat-label">Turnout</div></div>
        <div class="stat-card"><div class="stat-num"><?= $total_cands ?></div><div class="stat-label">Candidates</div></div>
        <div class="stat-card"><div class="stat-num"><?= $total_pos ?></div><div class="stat-label">Positions</div></div>
      </div>

      <!-- Quick toggle -->
      <div class="card card-sm">
        <h2>Election Control</h2>
        <form method="POST" action="settings.php">
          <input type="hidden" name="election_status" value="<?= $status==='open'?'closed':'open' ?>">
          <input type="hidden" name="quick_toggle" value="1">
          <button type="submit" class="btn <?= $status==='open'?'btn-danger':'btn-success' ?>">
            <?= $status==='open' ? '🔴 Close Voting' : '🟢 Open Voting' ?>
          </button>
          &nbsp; Voting is currently <b><?= strtoupper($status) ?></b>
        </form>
      </div>

      <!-- Recent voters -->
      <div class="card">
        <h2>Recent Registrations</h2>
        <table>
          <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Voter ID</th><th>Voted?</th></tr></thead>
          <tbody>
          <?php
          $recent = $conn->query("SELECT * FROM voters ORDER BY registered_at DESC LIMIT 10");
          $i = 1;
          while ($v = $recent->fetch_assoc()):
          ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($v['full_name']) ?></td>
            <td><?= htmlspecialchars($v['email']) ?></td>
            <td><span class="badge badge-blue"><?= $v['voter_id'] ?></span></td>
            <td><?= $v['has_voted'] ? '<span class="badge badge-green">Yes</span>' : '<span class="badge">No</span>' ?></td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</body></html>
