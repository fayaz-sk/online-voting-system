<?php
require_once 'auth.php';
requireAdmin();
$msg = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM votes WHERE position_id=$id");
    $conn->query("DELETE FROM candidates WHERE position_id=$id");
    $conn->query("DELETE FROM positions WHERE id=$id");
    $msg = "Position and its candidates deleted.";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string(trim($_POST['position_name']));
    $desc = $conn->real_escape_string(trim($_POST['description']));
    $eid  = isset($_POST['edit_id']) ? (int)$_POST['edit_id'] : 0;
    if ($eid) {
        $conn->query("UPDATE positions SET position_name='$name', description='$desc' WHERE id=$eid");
        $msg = "Position updated.";
    } else {
        $conn->query("INSERT INTO positions (position_name, description) VALUES ('$name','$desc')");
        $msg = "Position added.";
    }
}
$positions = $conn->query("SELECT * FROM positions ORDER BY id");
$edit_p = null;
if (isset($_GET['edit'])) {
    $r = $conn->query("SELECT * FROM positions WHERE id=".(int)$_GET['edit']);
    $edit_p = $r->fetch_assoc();
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Positions - Admin</title>
<link rel="stylesheet" href="../css/style.css">
</head><body>
<div class="navbar"><h1>⚙️ Admin Panel</h1><div class="nav-links"><a href="logout.php" class="btn-nav">Logout</a></div></div>
<div class="container-wide">
  <div class="admin-wrapper">
    <div class="sidebar card card-sm"><?= adminNav('positions') ?></div>
    <div class="main-content">
      <div class="page-title">Manage Positions</div>
      <?php if ($msg): ?><div class="alert alert-info">ℹ️ <?= $msg ?></div><?php endif; ?>

      <div class="card card-sm">
        <h2><?= $edit_p ? '✏️ Edit Position' : '➕ Add Position' ?></h2>
        <form method="POST">
          <?php if ($edit_p): ?><input type="hidden" name="edit_id" value="<?= $edit_p['id'] ?>"><?php endif; ?>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
            <div class="form-group">
              <label>Position Name</label>
              <input type="text" name="position_name" value="<?= $edit_p ? htmlspecialchars($edit_p['position_name']) : '' ?>" required>
            </div>
            <div class="form-group">
              <label>Description (optional)</label>
              <input type="text" name="description" value="<?= $edit_p ? htmlspecialchars($edit_p['description']) : '' ?>">
            </div>
          </div>
          <button type="submit" class="btn btn-primary"><?= $edit_p ? 'Update' : 'Add Position' ?></button>
          <?php if ($edit_p): ?> <a href="positions.php" class="btn" style="background:#eee;color:#333">Cancel</a><?php endif; ?>
        </form>
      </div>

      <div class="card">
        <h2>All Positions</h2>
        <table>
          <thead><tr><th>#</th><th>Position Name</th><th>Description</th><th>Candidates</th><th>Actions</th></tr></thead>
          <tbody>
          <?php $i=1; $positions->data_seek(0); while ($p = $positions->fetch_assoc()):
            $cnt = $conn->query("SELECT COUNT(*) as c FROM candidates WHERE position_id={$p['id']}")->fetch_assoc()['c'];
          ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><b><?= htmlspecialchars($p['position_name']) ?></b></td>
            <td style="color:#777;font-size:13px"><?= htmlspecialchars($p['description']) ?></td>
            <td><span class="badge badge-blue"><?= $cnt ?></span></td>
            <td>
              <a href="?edit=<?= $p['id'] ?>" class="btn btn-sm" style="background:#1565c0;color:#fff">Edit</a>
              <a href="?delete=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete position and all its candidates?')">Delete</a>
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
