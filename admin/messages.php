<?php
include '../includes/db_connect.php';

// robust session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// auth
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// check admin privilege
$stmt = $conn->prepare("SELECT is_admin, first_name, last_name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userRow = $stmt->get_result()->fetch_assoc();
if (!$userRow || (int)$userRow['is_admin'] !== 1) {
    http_response_code(403);
    echo "Access denied. Admins only.";
    exit;
}

// --- AJAX: return a single message as JSON (safe; admin-only) ---
if (isset($_GET['view_id'])) {
    $viewId = (int)$_GET['view_id'];
    $stmt = $conn->prepare("SELECT message_id, name, email, subject, message, submitted_at FROM contact_messages WHERE message_id = ?");
    $stmt->bind_param("i", $viewId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) {
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Message not found']);
        exit;
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'message_id'   => (int)$row['message_id'],
        'name'         => $row['name'],
        'email'        => $row['email'],
        'subject'      => $row['subject'],
        'message'      => $row['message'],
        'submitted_at' => date('M d, Y H:i', strtotime($row['submitted_at']))
    ]);
    exit;
}

// --- Fetch messages list (bounded for safety) ---
$limit = 1000; // safety cap; replace with pagination if needed
$stmt = $conn->prepare("SELECT message_id, name, email, subject, message, submitted_at FROM contact_messages ORDER BY submitted_at DESC LIMIT ?");
$stmt->bind_param("i", $limit);
$stmt->execute();
$result = $stmt->get_result();

// total count for UI
$totalRow = $conn->query("SELECT COUNT(*) AS cnt FROM contact_messages")->fetch_assoc();
$totalMessages = (int)($totalRow['cnt'] ?? 0);

// helper: safe truncation (multibyte)
function short_text($text, $len = 60) {
    if (mb_strlen($text) <= $len) return $text;
    return mb_substr($text, 0, $len - 1) . '…';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Admin — Contact Messages | FoodFusion</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="../assets/css/admin_common.css">
  <link rel="stylesheet" href="../assets/css/admin_messages.css">
</head>

<body class="admin-page">

  <?php include './admin_sidebar.php'; ?>

  <div class="admin-main">
    <?php include './admin_top_bar.php'; ?>

    <main class="admin-content" id="content">
      <h1 class="admin-heading">Contact Messages</h1>
      <p class="meta">Total messages: <?= htmlspecialchars($totalMessages, ENT_QUOTES, 'UTF-8') ?></p>

      <div class="table-wrap">
        <table class="table messages-table" aria-describedby="messages-desc">
          <caption id="messages-desc" class="sr-only">List of contact messages submitted by users</caption>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Subject</th>
              <th>Message</th>
              <th>Submitted</th>
              <th class="col-actions">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($m = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= (int)$m['message_id'] ?></td>
                  <td><?= htmlspecialchars($m['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($m['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars($m['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars(short_text($m['message'], 60), ENT_QUOTES, 'UTF-8') ?></td>
                  <td><?= htmlspecialchars(date('M d, Y H:i', strtotime($m['submitted_at'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                  <td class="col-actions">
                    <!-- only the id is embedded; full message fetched via AJAX when viewing -->
                    <button
                      class="btn small view-btn"
                      data-id="<?= (int)$m['message_id'] ?>"
                      aria-haspopup="dialog"
                    >View</button>

                    <a
                      href="message_delete.php?id=<?= (int)$m['message_id'] ?>"
                      class="btn small outline"
                      onclick="return confirm('Delete message #<?= (int)$m['message_id'] ?>? This cannot be undone.');"
                    >Delete</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="empty">No messages found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>

    <?php include './admin_footer.php'; ?>
  </div>

  <!-- Message Modal -->
  <div id="messageModal" class="modal hidden" role="dialog" aria-modal="true" aria-labelledby="modal-subject" tabindex="-1">
    <div class="modal-content" role="document">
      <button class="modal-close" aria-label="Close dialog">&times;</button>
      <h2 id="modal-subject"></h2>
      <p><strong>From:</strong> <span id="modal-name"></span> (<a href="#" id="modal-email-link"><span id="modal-email"></span></a>)</p>
      <p><strong>Date:</strong> <span id="modal-date"></span></p>
      <hr>
      <p id="modal-message" style="white-space:pre-wrap;"></p>
    </div>
  </div>

  <script>
    (function() {
      const modal = document.getElementById('messageModal');
      const subject = document.getElementById('modal-subject');
      const nameEl = document.getElementById('modal-name');
      const emailEl = document.getElementById('modal-email');
      const emailLink = document.getElementById('modal-email-link');
      const dateEl = document.getElementById('modal-date');
      const messageEl = document.getElementById('modal-message');
      const closeBtn = modal.querySelector('.modal-close');

      // open modal and fetch message via AJAX
      async function openMessage(id, triggerBtn) {
        try {
          const resp = await fetch(`<?= basename(__FILE__) ?>?view_id=${encodeURIComponent(id)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          });
          if (!resp.ok) throw new Error('Failed to load message');
          const data = await resp.json();
          if (data.error) throw new Error(data.error);

          subject.textContent = data.subject || '(No subject)';
          nameEl.textContent = data.name || '(No name)';
          emailEl.textContent = data.email || '';
          emailLink.setAttribute('href', 'mailto:' + (data.email || ''));
          dateEl.textContent = data.submitted_at || '';
          messageEl.textContent = data.message || '';

          // show modal
          modal.classList.remove('hidden');
          modal.focus();

          // trap focus briefly by focusing close button
          closeBtn.focus();
          // remember trigger to return focus later
          modal._trigger = triggerBtn;
        } catch (err) {
          alert('Could not load message: ' + err.message);
        }
      }

      // close modal and restore focus
      function closeModal() {
        modal.classList.add('hidden');
        // return focus to trigger if available
        if (modal._trigger) modal._trigger.focus();
      }

      // attach handlers to view buttons
      document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
          const id = btn.dataset.id;
          openMessage(id, btn);
        });
      });

      // close handlers
      closeBtn.addEventListener('click', closeModal);
      window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
      });
      window.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
      });
    })();
  </script>

  <script src="../assets/js/admin_side_bar.js"></script>
</body>
</html>
