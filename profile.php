<?php
if (!isset($_SESSION)) {
    session_start();
}
include 'includes/db_connect.php';
$form_response = "";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT first_name, last_name, email, profile_picture FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();


// Handle profile update
if (isset($_POST['update_profile'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    // Handle profile picture upload
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "./uploads/";
        $target_file = $target_dir . time() . "_" . basename($_FILES["profile_picture"]["name"]);
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($file_ext, $allowed_types)) {
            $new_filename = time() . "_" . basename($_FILES["profile_picture"]["name"]);
            $target_file = $target_dir . $new_filename;
            move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);
            $profile_picture = $new_filename;
        } else {
            $profile_picture = $user['profile_picture'];
        }


        $sql_update = "UPDATE users SET first_name=?, last_name=?, email=?, profile_picture=? WHERE user_id=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssssi", $first_name, $last_name, $email, $profile_picture, $user_id);
    } else {
        $sql_update = "UPDATE users SET first_name=?, last_name=?, email=? WHERE user_id=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $first_name, $last_name, $email, $user_id);
    }

    // $stmt_update->execute();
    if ($stmt_update->execute()) {
        $_SESSION['form_response'] = "Profile updated successfully.";
    } else {
        $_SESSION['form_response'] = "Error: " . $stmt_update->error;
    }

    header("Location: profile.php");
    exit();
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $sql_pass = "SELECT password_hash FROM users WHERE user_id=?";
    $stmt_pass = $conn->prepare($sql_pass);
    $stmt_pass->bind_param("i", $user_id);
    $stmt_pass->execute();
    $result_pass = $stmt_pass->get_result();
    $row_pass = $result_pass->fetch_assoc();

    if (password_verify($current_password, $row_pass['password_hash'])) {
        if ($new_password === $confirm_password) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $sql_update_pass = "UPDATE users SET password_hash=? WHERE user_id=?";
            $stmt_update_pass = $conn->prepare($sql_update_pass);
            $stmt_update_pass->bind_param("si", $new_hash, $user_id);
            // $stmt_update_pass->execute();
            if ($stmt_update_pass->execute()) {
                $_SESSION['form_response'] = "Password updated successfully.";
            } else {
                $_SESSION['form_response'] = "Error: " . $stmt_update_pass->error;
            }
            $password_msg = "Password updated successfully.";
        } else {
            $password_msg = "New passwords do not match.";
        }
    } else {
        $password_msg = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>My Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/profile.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <?php include("./includes/header.php"); ?>
    <div class="profile-page">

        <h1>Welcome, <?php echo htmlspecialchars($user['first_name']); ?></h1>

        <div class="profile-card">
            <img src="<?= !empty($user['profile_picture']) ? 'uploads/' . htmlspecialchars($user['profile_picture']) : 'assets/images/default_user.jpeg' ?>" alt="Profile Picture">

            <h2>Update Profile</h2>
            <form action="profile.php" method="post" enctype="multipart/form-data">
                <label>First Name:</label>
                <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>

                <label>Last Name:</label>
                <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>

                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label>Profile Picture:</label>
                <input type="file" name="profile_picture">

                <input type="submit" name="update_profile" value="Update Profile">
            </form>
        </div>

        <div class="profile-card">
            <h2>Change Password</h2>
            <?php if (isset($password_msg)) {
                echo "<p class='msg'>$password_msg</p>";
            } ?>
            <form action="profile.php" method="post">
                <label>Current Password:</label>
                <input type="password" name="current_password" required>

                <label>New Password:</label>
                <input type="password" name="new_password" required>

                <label>Confirm New Password:</label>
                <input type="password" name="confirm_password" required>

                <input type="submit" name="change_password" value="Change Password">
            </form>
        </div>

        <p><a href="auth/logout.php">Logout</a></p>
    </div>
    <?php if (isset($_SESSION['form_response'])): ?>
        <script>
            alert("<?php echo addslashes($_SESSION['form_response']); ?>");
        </script>
        <?php unset($_SESSION['form_response']); ?>
    <?php endif; ?>
    <script src="./assets/js/hamburger_menu.js"></script>

</body>

</html>