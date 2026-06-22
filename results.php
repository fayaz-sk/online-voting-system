<?php
require_once 'auth.php';
requireAdmin();
$title  = getSetting($conn, 'election_title');
$positions = $conn->query("SELECT * FROM positions ORDER BY id");
$total_voters = $conn->query("SELECT COUNT(*) as c FROM voters")->fetch_assoc()['c'];
$total_voted  = $conn->query("SELECT COUNT(*) as c FROM voters WHERE has_voted=1")->fetch_assoc()['c'];
$turnout = $total_voters > 0 ? round(($total_voted / $total_voters) * 100) : 0;

// Reset all votes
$msg = '';
if (isset($_GET['reset_all']) && $_GET['reset_all']==='yes') {
    $conn->query("UPDATE candidates SET votes=0");
    $conn->query("DELETE FROM votes");
    $conn->query("UPDATE voters SET has_voted=0");
    $msg = "All votes have been reset.";
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Results - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head><body>
<div class="navbar"><h1>⚙️ Admin Panel</h1><div class="nav-links"><a href="logout.php" class="btn-nav">Logout</a></div></div>
<div class="container-wide">
  <div class="admin-wrapper">
    <div class="sidebar card card-sm"><?= adminNav('results') ?></div>
    <div class="main-content">
      <div class="page-title">Election Results</div>
      <div class="page-sub"><?= htmlspecialchars($title) ?></div>
      <?php if ($msg): ?><div class="alert alert-warning">⚠️ <?= $msg ?></div><?php endif; ?>

      <div class="stats-grid">
        <div class="stat-card"><div class="stat-num"><?= $total_voters ?></div><div class="stat-label">Registered</div></div>
        <div class="stat-card"><div class="stat-num"><?= $total_voted ?></div><div class="stat-label">Voted</div></div>
        <div class="stat-card"><div class="stat-num"><?= $turnout ?>%</div><div class="stat-label">Turnout</div></div>
      </div>

      <?php while ($pos = $positions->fetch_assoc()):
        $cands = $conn->query("SELECT * FROM candidates WHERE position_id={$pos['id']} ORDER BY votes DESC");
        $total_pos = $conn->query("SELECT SUM(votes) as t FROM candidates WHERE position_id={$pos['id']}")->fetch_assoc()['t'] ?: 1;
      ?>
      <div class="card">
        <h2><?= htmlspecialchars($pos['position_name']) ?></h2>
        <table style="margin-bottom:16px">
          <thead><tr><th>Rank</th><th>Candidate</th><th>Votes</th><th>Percentage</th></tr></thead>
          <tbody>
          <?php $rank=1; while ($c = $cands->fetch_assoc()):
            $pct = round(($c['votes'] / $total_pos) * 100);
          ?>
          <tr <?= $rank===1 ? 'style="background:#f1f8e9"' : '' ?>>
            <td><?= $rank===1 ? '🥇' : ($rank===2 ? '🥈' : ($rank===3 ? '🥉' : $rank)) ?></td>
            <td><b><?= htmlspecialchars($c['name']) ?></b> <?= $rank===1 ? '<span class="badge badge-green">Winner</span>' : '' ?></td>
            <td><?= $c['votes'] ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <div class="result-bar-bg" style="flex:1;height:14px">
                  <div class="result-bar" style="width:<?= $pct ?>%;height:14px"></div>
                </div>
                <span style="font-size:13px;width:36px"><?= $pct ?>%</span>
              </div>
            </td>
          </tr>
          <?php $rank++; endwhile; ?>
          </tbody>
        </table>
      </div>
      <?php endwhile; ?>

      <div class="card card-sm" style="border:2px solid #fce4ec">
        <h2 style="color:#c62828">⚠️ Danger Zone</h2>
        <p style="color:#777;font-size:14px;margin-bottom:14px">This will delete ALL votes and reset every voter's has_voted status. This cannot be undone.</p>
        <a href="?reset_all=yes" class="btn btn-danger" onclick="return confirm('Are you SURE you want to reset all votes? This cannot be undone!')">Reset All Votes</a>
      </div>
    </div>
  </div>
</div>
</body></html>
