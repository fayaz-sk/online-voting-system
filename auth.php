<?php
require_once '../config.php';

function requireAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php");
        exit;
    }
}

function adminNav($active = '') {
    $items = [
        'dashboard'  => ['Dashboard',   '📊', 'dashboard.php'],
        'voters'     => ['Voters',       '👥', 'voters.php'],
        'candidates' => ['Candidates',   '🧑‍💼', 'candidates.php'],
        'positions'  => ['Positions',    '🏷️', 'positions.php'],
        'results'    => ['Results',      '📈', 'results.php'],
        'settings'   => ['Settings',     '⚙️', 'settings.php'],
    ];
    $html = '';
    foreach ($items as $key => $item) {
        $cls = ($active === $key) ? 'menu-item active' : 'menu-item';
        $html .= "<a href='{$item[2]}' class='$cls'>{$item[0]} {$item[1]}</a>";
    }
    return $html;
}
?>
