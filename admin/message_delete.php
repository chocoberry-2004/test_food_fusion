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

// --- Validate message_id ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: messages.php");
    exit;
}

$message_id = (int)$_GET['id'];

// --- Delete query ---
$stmt = $conn->prepare("DELETE FROM contact_messages WHERE message_id = ?");
$stmt->bind_param("i", $message_id);

if ($stmt->execute()) {
    $_SESSION['flash_success'] = "Message deleted successfully.";
} else {
    $_SESSION['flash_error'] = "Failed to delete message.";
}

header("Location: messages.php");
exit;
