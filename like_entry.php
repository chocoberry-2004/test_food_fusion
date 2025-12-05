
<?php
include './includes/db_connect.php';
if (!isset($_SESSION)) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entry_id'])) {
    $entry_id = (int)$_POST['entry_id'];

    $conn->query("UPDATE community_cookbook SET claps = COALESCE(claps, 0) + 1 WHERE entry_id = $entry_id");

    $res = $conn->query("SELECT COALESCE(claps, 0) AS claps FROM community_cookbook WHERE entry_id = $entry_id");
    if ($row = $res->fetch_assoc()) {
        echo (int)$row['claps'];
    }
}
?>
