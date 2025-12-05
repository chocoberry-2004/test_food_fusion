<?php
// admin/users.php - simple read-only "All Users" list
include '../includes/db_connect.php';

// robust session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// require login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// admin check (prepared)
$stmt = $conn->prepare("SELECT is_admin, first_name, last_name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userRow = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$userRow || (int)$userRow['is_admin'] !== 1) {
    http_response_code(403);
    echo "Access denied. Admins only.";
    exit;
}

// Fetch all users (read-only, no actions)
$sql = "SELECT user_id, first_name, last_name, email, is_admin, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);

// safe helpers
function esc($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
function fmt_dt($ts) {
    if (empty($ts)) return '';
    $t = strtotime($ts);
    if ($t === false) return esc($ts);
    return date('M d, Y H:i', $t);
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Admin — All Users | FoodFusion</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link rel="stylesheet" href="../assets/css/admin_common.css">
  <link rel="stylesheet" href="../assets/css/admin_users.css">
</head>

<body class="admin-page">
  <?php include './admin_sidebar.php'; ?>
  <div class="admin-main">
    <?php include './admin_top_bar.php'; ?>

    <main class="admin-content" role="main">
      <h1 class="admin-heading">All Users</h1>
      <p class="meta">Total users: <?= esc($result ? $result->num_rows : 0) ?></p>

      <div class="table-wrap">
        <table class="table users-table" aria-describedby="users-desc">
          <caption id="users-desc" class="sr-only">All registered users</caption>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Admin</th>
              <th>Joined</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($u = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= (int)$u['user_id'] ?></td>
                  <td><?= esc(trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''))) ?></td>
                  <td><?= esc($u['email'] ?? '') ?></td>
                  <td><?= (int)$u['is_admin'] ? 'Yes' : 'No' ?></td>
                  <td><?= esc(fmt_dt($u['created_at'])) ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="empty">No users found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>

    <?php include './admin_footer.php'; ?>
  </div>

  <script src="../assets/js/admin_side_bar.js"></script>
</body>
</html>
