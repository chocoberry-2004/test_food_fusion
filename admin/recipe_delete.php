<?php
include '../includes/db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$user_id = (int)$_SESSION['user_id'];

$stmt = $conn->prepare("SELECT is_admin FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userRow = $stmt->get_result()->fetch_assoc();
if (!$userRow || (int)$userRow['is_admin'] !== 1) {
    http_response_code(403);
    echo "Access denied. Admins only.";
    exit;
}

// --- Validate recipe id ---
$recipeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($recipeId <= 0) {
    header("Location: recipes.php?msg=Invalid+recipe+id");
    exit;
}

// --- Fetch cover image (for deletion later) ---
$imgStmt = $conn->prepare("SELECT cover_img_src FROM recipes WHERE recipe_id = ?");
$imgStmt->bind_param("i", $recipeId);
$imgStmt->execute();
$imgRow = $imgStmt->get_result()->fetch_assoc();

if (!$imgRow) {
    header("Location: recipes.php?msg=Recipe+not+found");
    exit;
}

// --- Delete recipe ---
$delStmt = $conn->prepare("DELETE FROM recipes WHERE recipe_id = ?");
$delStmt->bind_param("i", $recipeId);

if ($delStmt->execute()) {
    $coverPath = $imgRow['cover_img_src'];
    if ($coverPath && file_exists(__DIR__ . '/../' . ltrim($coverPath, './\\/'))) {
        unlink(__DIR__ . '/../' . ltrim($coverPath, './\\/'));
    }

    header("Location: recipes.php?msg=Recipe+deleted+successfully");
    exit;
} else {
    header("Location: recipes.php?msg=Error+deleting+recipe");
    exit;
}
