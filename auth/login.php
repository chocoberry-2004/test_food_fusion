<?php
include '../includes/db_connect.php';
if (!isset($_SESSION)) {
  session_start();
}
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST['email']);
  $password = $_POST['password'];

  $sql = "SELECT * FROM users WHERE email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if ($user['lock_until'] && strtotime($user['lock_until']) > time()) {
      // $remaining = ceil((strtotime($user['lock_until']) - time()) / 60);
      $remaining = strtotime($user['lock_until']) - time();
      $message = "Account locked.";
      $lock_until_timestamp = strtotime($user['lock_until']);
    } else {
      if (password_verify($password, $user['password_hash'])) {
        $update = "UPDATE users SET failed_attempts = 0, lock_until = NULL WHERE user_id = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("i", $user['user_id']);
        $stmt->execute();

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['is_admin'] = $user['is_admin'];

        if ($user['is_admin']) {
          header("Location: ../admin/index.php");
        } else {
          header("Location: ../index.php");
        }
        exit;
      } else {
        $failed = $user['failed_attempts'] + 1;
        $lock_until = null;

        if ($failed >= 3) {
          $lock_until = date("Y-m-d H:i:s", strtotime("+3 minutes"));
          $failed = 0;
          $message = "Too many failed attempts. Account locked for 3 minutes.";
        } else {
          $attempts_left = 3 - $failed;
          $message = "Invalid password. You have $attempts_left attempt(s) left before lock.";
        }

        $update = "UPDATE users SET failed_attempts = ?, lock_until = ? WHERE user_id = ?";
        $stmt = $conn->prepare($update);
        $stmt->bind_param("isi", $failed, $lock_until, $user['user_id']);
        $stmt->execute();
      }
    }
  } else {
    $message = "No account found with that email.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>FoodFusion - Login</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/forms.css">
</head>

<body class="login-page">
  <div class="form-container">
    <h2>Login to FoodFusion</h2>

    <?php if ($message): ?>
      <p class="message"><?= htmlspecialchars($message) ?></p>
      <?php if (isset($lock_until_timestamp)): ?>
        <p id="countdown"></p>
        <script>
          let lockUntil = <?= $lock_until_timestamp ?> * 1000; // JS expects ms
          function updateCountdown() {
            let now = Date.now();
            let diff = lockUntil - now;
            if (diff > 0) {
              let minutes = Math.floor(diff / 60000);
              let seconds = Math.floor((diff % 60000) / 1000);
              document.getElementById("countdown").textContent =
                `Try again in ${minutes}:${seconds.toString().padStart(2,'0')}`;
            } else {
              document.getElementById("countdown").textContent = "You can now try logging in.";
            }
          }
          updateCountdown();
          setInterval(updateCountdown, 1000);
        </script>
      <?php endif; ?>
    <?php endif; ?>


    <form method="POST" action="">
      <label>Email:</label>
      <input type="email" name="email" required>

      <label>Password:</label>
      <input type="password" name="password" required>

      <button type="submit">Login</button>
    </form>

    <p style="text-align:center; margin-top:10px;">
      Don’t have an account? <a href="register.php">Register here</a>
    </p>
  </div>
</body>

</html>