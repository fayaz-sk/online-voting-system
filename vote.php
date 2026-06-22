<?php
require_once 'config.php';
if (!isset($_SESSION['voter_id'])) { header("Location: index.php"); exit; }

$voter_db_id = $_SESSION['voter_id'];
$title       = getSetting($conn, 'election_title');
$status      = getSetting($conn, 'election_status');

// Re-check from DB
$vr = $conn->query("SELECT has_voted FROM voters WHERE id=$voter_db_id");
$vrow = $vr->fetch_assoc();
$_SESSION['has_voted'] = $vrow['has_voted'];

$error = ''; $success = '';

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $status === 'open' && !$_SESSION['has_voted']) {
    $positions = $conn->query("SELECT id FROM positions");
    $all_voted = true;

    while ($pos = $positions->fetch_assoc()) {
        if (!isset($_POST['vote_' . $pos['id']])) {
            $all_voted = false;
            break;
        }
    }

    if (!$all_voted) {
        $error = "Please vote for all positions before submitting.";
    } else {
        $positions->data_seek(0);
        while ($pos = $positions->fetch_assoc()) {
            $cid = (int)$_POST['vote_' . $pos['id']];
            $pid = (int)$pos['id'];
            // Insert vote
            $conn->query("INSERT INTO votes (voter_id, candidate_id, position_id) VALUES ($voter_db_id, $cid, $pid)");
            // Increment candidate votes
            $conn->query("UPDATE candidates SET votes=votes+1 WHERE id=$cid");
        }
        // Mark voter as voted
        $conn->query("UPDATE voters SET has_voted=1 WHERE id=$voter_db_id");
        $_SESSION['has_voted'] = 1;
        $success = "Your vote has been recorded successfully! Thank you for voting.";
    }
}

// Load positions + candidates
$positions = $conn->query("SELECT * FROM positions ORDER BY id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Vote - <?= htmlspecialchars($title) ?></title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="navbar">
  <h1>🗳️ <?= htmlspecialchars($title) ?></h1>
  <div class="nav-links">
    <span style="color:#c5caf9;font-size:13px">👤 <?= htmlspecialchars($_SESSION['voter_name']) ?></span>
    <a href="results.php">📊 Results</a>
    <a href="logout.php" class="btn-nav">Logout</a>
  </div>
</div>

<div class="container">
  <?php if ($success): ?>
    <div class="card" style="text-align:center;padding:50px 30px">
      <div style="font-size:60px">🎉</div>
      <h2 style="border:none;color:#2e7d32;font-size:24px;margin:16px 0 8px">Vote Submitted!</h2>
      <p style="color:#555;font-size:16px"><?= $success ?></p>
      <br>
      <a href="results.php" class="btn btn-primary">View Live Results →</a>
    </div>

  <?php elseif ($_SESSION['has_voted']): ?>
    <div class="card" style="text-align:center;padding:50px 30px">
      <div style="font-size:60px">✅</div>
      <h2 style="border:none;color:#1a237e">You Already Voted!</h2>
      <p style="color:#777;margin-top:10px">You have already cast your vote in this election.</p>
      <br>
      <a href="results.php" class="btn btn-primary">View Live Results →</a>
    </div>

  <?php elseif ($status !== 'open'): ?>
    <div class="alert alert-warning" style="font-size:16px;margin-top:10px">⚠️ Voting is currently <b>closed</b>. Please check back later.</div>

  <?php else: ?>
    <div class="page-title">Cast Your Vote</div>
    <div class="page-sub">Select one candidate for each position. You cannot change your vote after submitting.</div>

    <?php if ($error): ?><div class="alert alert-error">❌ <?= $error ?></div><?php endif; ?>

    <form method="POST">
      <?php while ($pos = $positions->fetch_assoc()):
        $cands = $conn->query("SELECT * FROM candidates WHERE position_id={$pos['id']} ORDER BY name");
      ?>
      <div class="card">
        <div class="position-section" style="margin-bottom:0">
          <h3><?= htmlspecialchars($pos['position_name']) ?></h3>
          <?php if ($pos['description']): ?>
          <p style="font-size:13px;color:#777;margin-bottom:12px"><?= htmlspecialchars($pos['description']) ?></p>
          <?php endif; ?>
          <div class="candidate-list">
            <?php while ($c = $cands->fetch_assoc()): ?>
            <label class="candidate-option">
              <input type="radio" name="vote_<?= $pos['id'] ?>" value="<?= $c['id'] ?>" required>
              <div class="c-info">
                <div class="c-name"><?= htmlspecialchars($c['name']) ?></div>
                <?php if ($c['bio']): ?><div class="c-bio"><?= htmlspecialchars($c['bio']) ?></div><?php endif; ?>
              </div>
            </label>
            <?php endwhile; ?>
          </div>
        </div>
      </div>
      <?php endwhile; ?>

      <button type="submit" class="btn btn-primary btn-block" style="padding:15px;font-size:16px">
        🗳️ Submit My Vote
      </button>
      <p style="text-align:center;font-size:13px;color:#999;margin-top:10px">⚠️ You cannot change your vote after submitting.</p>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
