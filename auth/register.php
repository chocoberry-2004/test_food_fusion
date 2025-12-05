<?php
include '../includes/db_connect.php'; 
if (!isset($_SESSION)) {
    session_start();
}
$message = "";
$first_name = "";
$last_name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect and trim form data
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];
    $confirm    = $_POST['confirm_password'];

     // 2. Check if email already exist
    $checkSql="SELECT * FROM users WHERE email = ?";
    $checkStmt=$conn->prepare($checkSql);
    $checkStmt->bind_param("s",$email);
    $checkStmt->execute();
    $checkStmt-> store_result();
    if($checkStmt->num_rows>0){
      $_SESSION["message"]="This email is already registered";
      $_SESSION['form_data'] = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email
        ];
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit();
    }

    // 3. Check password match
    if ($password !== $confirm) {
        $_SESSION['message'] = "Passwords do not match!";
        $_SESSION['form_data'] = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email
        ];
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        // 4. Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // 5. Insert user
        $sql = "INSERT INTO users (first_name, last_name, email, password_hash) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $first_name, $last_name, $email, $hashedPassword);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Registration successful! You can now login.";
            header("Location: login.php"); 
            exit();
        } else {
            $_SESSION['message'] = "Error: " . $stmt->error;
            $_SESSION['form_data'] = [
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email
            ];
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}

// 6. Retrieve session message and form data
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if (isset($_SESSION['form_data'])) {
    $first_name = $_SESSION['form_data']['first_name'];
    $last_name = $_SESSION['form_data']['last_name'];
    $email = $_SESSION['form_data']['email'];
    unset($_SESSION['form_data']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FoodFusion - Register</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet" href="../assets/css/forms.css">
</head>
<body class="register-page">
  <div class="form-container">
    <h2>Join FoodFusion</h2>

    <?php if ($message): ?>
      <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <label>First Name:</label>
      <input type="text" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required>

      <label>Last Name:</label>
      <input type="text" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required>

      <label>Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

      <label>Password:</label>
      <input type="password" name="password" required>

      <label>Confirm Password:</label>
      <input type="password" name="confirm_password" required>

      <button type="submit">Register</button>
    </form>

    <p style="text-align:center; margin-top:10px;">
      Already part of FoodFusion? <a href="login.php">Login here</a>
    </p>
  </div>
</body>
</html>
