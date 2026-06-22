<?php
require_once 'auth.php';
requireAdmin();
$msg = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM votes WHERE candidate_id=$id");
    $conn->query("DELETE FROM candidates WHERE id=$id");
    $msg = "Candidate deleted.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = $conn->real_escape_string(trim($_POST['name']));
    $pos_id  = (int)$_POST['position_id'];
    $bio     = $conn->real_escape_string(trim($_POST['bio']));
    $edit_id = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;

    if ($edit_id) {
        $conn->query("UPDATE candidates SET name='$name', position_id=$pos_id, bio='$bio' WHERE id=$edit_id");
        $msg = "Candidate updated.";
    } else {
        $conn->query("INSERT INTO candidates (name, position_id, bio) VALUES ('$name',$pos_id,'$bio')");
        $msg = "Candidate added.";
    }
}

$candidates = $conn->query("SELECT c.*, p.position_name FROM candidates c JOIN positions p ON c.position_id=p.id ORDER BY p.id, c.name");
$positions  = $conn->query("SELECT * FROM positions ORDER BY position_name");
$edit_c = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $er  = $conn->query("SELECT * FROM candidates WHERE id=$eid");
    $edit_c = $er->fetch_assoc();
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Candidates - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head><body>
<div class="navbar"><h1>⚙️ Admin Panel</h1><div class="nav-links"><a href="logout.php" class="btn-nav">Logout</a></div></div>
<div class="container-wide">
  <div class="admin-wrapper">
    <div class="sidebar card card-sm"><?= adminNav('candidates') ?></div>
    <div class="main-content">
      <div class="page-title">Manage Candidates</div>
      <?php if ($msg): ?><div class="alert alert-info">ℹ️ <?= $msg ?></div><?php endif; ?>

      <div class="card card-sm">
        <h2><?= $edit_c ? '✏️ Edit Candidate' : '➕ Add Candidate' ?></h2>
        <form method="POST">
          <?php if ($edit_c): ?><input type="hidden" name="edit_id" value="<?= $edit_c['id'] ?>"><?php endif; ?>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
            <div class="form-group">
              <label>Candidate Name</label>
              <input type="text" name="name" value="<?= $edit_c ? htmlspecialchars($edit_c['name']) : '' ?>" required>
            </div>
            <div class="form-group">
              <label>Position</label>
              <select name="position_id" required>
                <?php
                $positions->data_seek(0);
                while ($p = $positions->fetch_assoc()):
                  $sel = ($edit_c && $edit_c['position_id']==$p['id']) ? 'selected' : '';
                ?>
                <option value="<?= $p['id'] ?>" <?= $sel ?>><?= htmlspecialchars($p['position_name']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Bio / Description</label>
            <textarea name="bio"><?= $edit_c ? htmlspecialchars($edit_c['bio']) : '' ?></textarea>
          </div>
          <button type="submit" class="btn btn-primary"><?= $edit_c ? 'Update Candidate' : 'Add Candidate' ?></button>
          <?php if ($edit_c): ?> <a href="candidates.php" class="btn" style="background:#eee;color:#333">Cancel</a><?php endif; ?>
        </form>
      </div>

      <div class="card">
        <h2>All Candidates</h2>
        <table>
          <thead><tr><th>#</th><th>Name</th><th>Position</th><th>Bio</th><th>Votes</th><th>Actions</th></tr></thead>
          <tbody>
          <?php $i=1; $candidates->data_seek(0); while ($c = $candidates->fetch_assoc()): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><b><?= htmlspecialchars($c['name']) ?></b></td>
            <td><span class="badge badge-blue"><?= htmlspecialchars($c['position_name']) ?></span></td>
            <td style="font-size:13px;color:#777"><?= htmlspecialchars($c['bio']) ?></td>
            <td><span class="badge badge-green"><?= $c['votes'] ?></span></td>
            <td>
              <a href="?edit=<?= $c['id'] ?>" class="btn btn-sm" style="background:#1565c0;color:#fff">Edit</a>
              <a href="?delete=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</a>
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
