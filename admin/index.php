<?php
include '../includes/db_connect.php';

// robust session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// Check admin privilege (prepared)
$stmt = $conn->prepare("SELECT is_admin, first_name, last_name, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userRow = $stmt->get_result()->fetch_assoc();
if (!$userRow || (int)$userRow['is_admin'] !== 1) {
    http_response_code(403);
    echo "Access denied. Admins only.";
    exit;
}
$stmt->close();

// Fetch totals (simple, safe)
function count_table($conn, $table) {
    // whitelist table names to prevent injection
    $allowed = ['users','recipes','contact_messages','events'];
    if (!in_array($table, $allowed, true)) return 0;
    $res = $conn->query("SELECT COUNT(*) AS cnt FROM {$table}");
    return $res ? (int)$res->fetch_assoc()['cnt'] : 0;
}
$totalUsers = count_table($conn, 'users');
$totalRecipes = count_table($conn, 'recipes');
$totalMessages = count_table($conn, 'contact_messages');
$totalEvents = count_table($conn, 'events');

// Fetch recent items (LIMIT small number)
$limit = 6;

$recentUsers = [];
if ($ru = $conn->prepare("SELECT user_id, first_name, last_name, email, created_at, is_admin FROM users ORDER BY created_at DESC LIMIT ?")) {
    $ru->bind_param("i", $limit);
    $ru->execute();
    $res = $ru->get_result();
    while ($r = $res->fetch_assoc()) $recentUsers[] = $r;
    $ru->close();
}

$recentRecipes = [];
if ($rr = $conn->prepare("SELECT recipe_id, title, cuisine_type, difficulty, created_at FROM recipes ORDER BY created_at DESC LIMIT ?")) {
    $rr->bind_param("i", $limit);
    $rr->execute();
    $res = $rr->get_result();
    while ($r = $res->fetch_assoc()) $recentRecipes[] = $r;
    $rr->close();
}

$recentMessages = [];
if ($rm = $conn->prepare("SELECT message_id, name, subject, submitted_at FROM contact_messages ORDER BY submitted_at DESC LIMIT ?")) {
    $rm->bind_param("i", $limit);
    $rm->execute();
    $res = $rm->get_result();
    while ($r = $res->fetch_assoc()) $recentMessages[] = $r;
    $rm->close();
}

$conn->close();

// safe helper to format datetime (return empty if invalid)
function fmt_dt($ts) {
    if (empty($ts)) return '';
    $t = strtotime($ts);
    if ($t === false) return htmlspecialchars($ts, ENT_QUOTES, 'UTF-8');
    return date('M d, Y H:i', $t);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Admin Dashboard — FoodFusion</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link rel="stylesheet" href="../assets/css/admin_index.css">
  <link rel="stylesheet" href="../assets/css/admin_common.css">
</head>

<body class="admin-page">

  <?php include './admin_sidebar.php'; ?>

  <div class="admin-main">
    <?php include './admin_top_bar.php'; ?>

    <main class="admin-content" role="main">
      <h1 class="admin-heading">Dashboard</h1>

      <!-- Stats -->
      <section class="stats-grid" aria-label="Key metrics">
        <div class="stat" role="article" aria-labelledby="stat-users">
          <div id="stat-users" class="stat-number"><?= htmlspecialchars($totalUsers, ENT_QUOTES, 'UTF-8') ?></div>
          <div class="stat-label">Users</div>
        </div>

        <div class="stat" role="article" aria-labelledby="stat-recipes">
          <div id="stat-recipes" class="stat-number"><?= htmlspecialchars($totalRecipes, ENT_QUOTES, 'UTF-8') ?></div>
          <div class="stat-label">Recipes</div>
        </div>

        <div class="stat" role="article" aria-labelledby="stat-messages">
          <div id="stat-messages" class="stat-number"><?= htmlspecialchars($totalMessages, ENT_QUOTES, 'UTF-8') ?></div>
          <div class="stat-label">Contact Messages</div>
        </div>

        <div class="stat" role="article" aria-labelledby="stat-events">
          <div id="stat-events" class="stat-number"><?= htmlspecialchars($totalEvents, ENT_QUOTES, 'UTF-8') ?></div>
          <div class="stat-label">Events</div>
        </div>
      </section>

      <!-- Recent Lists -->
      <section class="recent-grid" aria-label="Recent items">
        <div class="card" aria-labelledby="recent-users">
          <h2 id="recent-users">Recent Users</h2>
          <table class="table" aria-describedby="recent-users-desc">
            <thead>
              <tr><th>ID</th><th>Name</th><th>Email</th><th>Admin</th><th>Joined</th></tr>
            </thead>
            <tbody>
              <?php if (count($recentUsers) === 0): ?>
                <tr><td colspan="5" class="empty">No recent users.</td></tr>
              <?php else: ?>
                <?php foreach ($recentUsers as $u): ?>
                  <tr>
                    <td><?= (int)$u['user_id'] ?></td>
                    <td><?= htmlspecialchars(trim($u['first_name'].' '.$u['last_name']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= (int)$u['is_admin'] ? 'Yes' : 'No' ?></td>
                    <td><?= htmlspecialchars(fmt_dt($u['created_at']), ENT_QUOTES, 'UTF-8') ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
          <a class="btn-link" href="users.php">View all users →</a>
        </div>

        <div class="card" aria-labelledby="recent-recipes">
          <h2 id="recent-recipes">Recent Recipes</h2>
          <table class="table">
            <thead>
              <tr><th>ID</th><th>Title</th><th>Cuisine</th><th>Difficulty</th><th>Created</th></tr>
            </thead>
            <tbody>
              <?php if (count($recentRecipes) === 0): ?>
                <tr><td colspan="5" class="empty">No recent recipes.</td></tr>
              <?php else: ?>
                <?php foreach ($recentRecipes as $r): ?>
                  <tr>
                    <td><?= (int)$r['recipe_id'] ?></td>
                    <td><?= htmlspecialchars($r['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($r['cuisine_type'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($r['difficulty'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(fmt_dt($r['created_at']), ENT_QUOTES, 'UTF-8') ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
          <a class="btn-link" href="recipes.php">Manage recipes →</a>
        </div>

        <div class="card" aria-labelledby="recent-messages">
          <h2 id="recent-messages">Recent Messages</h2>
          <table class="table">
            <thead>
              <tr><th>ID</th><th>Name</th><th>Subject</th><th>Date</th></tr>
            </thead>
            <tbody>
              <?php if (count($recentMessages) === 0): ?>
                <tr><td colspan="4" class="empty">No recent messages.</td></tr>
              <?php else: ?>
                <?php foreach ($recentMessages as $m): ?>
                  <tr>
                    <td><?= (int)$m['message_id'] ?></td>
                    <td><?= htmlspecialchars($m['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($m['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(fmt_dt($m['submitted_at']), ENT_QUOTES, 'UTF-8') ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
          <a class="btn-link" href="messages.php">View all messages →</a>
        </div>
      </section>
    </main>

    <?php include './admin_footer.php'; ?>
  </div>

  <script src="../assets/js/admin_side_bar.js"></script>
</body>
</html>
