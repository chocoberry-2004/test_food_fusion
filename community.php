<?php
include './includes/db_connect.php';
if (!isset($_SESSION)) {
    session_start();
}
$message = "";

/* -------------------------------
   1. Handle New Entry Submission
---------------------------------*/
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit'])) {
    $title   = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'] ?? null;
    $cuisine = trim($_POST['cuisine']);

    if ($user_id) {
        // Upload image if provided
        $image_url = null;
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "./uploads/";
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

            $fileName   = time() . "_" . basename($_FILES["image"]["name"]);
            $targetFile = $targetDir . $fileName;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $image_url = $targetFile;
            }
        }

        // Save recipe
        $stmt = $conn->prepare("INSERT INTO community_cookbook (user_id, title, content, image_url, cuisine_type) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $user_id, $title, $content, $image_url, $cuisine);
        $message = $stmt->execute() ? "Your recipe has been added!" : "Error: " . $conn->error;
    } else {
        $message = "You must be logged in to submit a recipe.";
    }
}

/* -------------------------------
   2. Handle Filters & Sorting
---------------------------------*/
$sort    = $_GET['sort'] ?? 'recent';
$cuisine = $_GET['cuisine'] ?? '';

$orderBy = ($sort === 'liked') ? "c.claps DESC" : "c.created_at DESC";

$sql = "
    SELECT c.entry_id, c.title, c.content, c.image_url, c.claps, c.cuisine_type, c.created_at,
           u.first_name, u.last_name, u.profile_picture
    FROM community_cookbook c
    LEFT JOIN users u ON c.user_id = u.user_id
    WHERE 1=1
";

$params = [];
$types  = "";

if (!empty($cuisine)) {
    $sql .= " AND c.cuisine_type = ?";
    $params[] = $cuisine;
    $types   .= "s";
}

$sql .= " ORDER BY $orderBy";

/* -------------------------------
   3. Fetch Entries
---------------------------------*/
if (!empty($params)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

$entries = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }
}

/* -------------------------------
   4. Fetch Cuisine Types for Filter
---------------------------------*/
$cuisines = [];
$cuisineResult = $conn->query("SELECT DISTINCT cuisine_type FROM community_cookbook WHERE cuisine_type IS NOT NULL AND cuisine_type <> ''");
if ($cuisineResult) {
    while ($row = $cuisineResult->fetch_assoc()) {
        $cuisines[] = $row['cuisine_type'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Community Cookbook</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/community.css">
    <script src="./assets/js/community.js" defer></script>
</head>

<body class="community-page">
    <?php include("./includes/header.php"); ?>

    <section class="community-hero">
        <h1>Community Cookbook</h1>
        <p>Share your favourite recipes, tips, and food stories with FoodFusion.</p>
    </section>

    <main class="community-main">
        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <!-- Contribution form -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <section class="contribute-form">
                <h2>Contribute Your Recipe</h2>
                <form method="POST" action="community.php" enctype="multipart/form-data">
                    <div class="form-control">
                        <label for="title">Title</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-control">
                        <label for="cuisine">Cuisine</label>
                        <input type="text" name="cuisine" required>
                    </div>
                    <div class="form-control">
                        <label for="content">Content</label>
                        <textarea name="content" required></textarea>
                    </div>
                    <div class="form-control">
                        <label for="image">Upload Image</label>
                        <input type="file" name="image" accept="image/*" required>
                    </div>
                    <button type="submit" name="submit" class="btn-submit">Submit</button>
                </form>
            </section>
        <?php else: ?>
            <p class="login-message">Please <a href="auth/login.php">login</a> to share your recipe.</p>
        <?php endif; ?>

        <section class="filters">
            <form method="GET" action="community.php">
                <label for="sort">Sort by:</label>
                <select name="sort" id="sort">
                    <option value="recent" <?= ($_GET['sort'] ?? '') === 'recent' ? 'selected' : '' ?>>Most Recent</option>
                    <option value="liked" <?= ($_GET['sort'] ?? '') === 'liked' ? 'selected' : '' ?>>Most Liked</option>
                </select>

                <label for="cuisine">Cuisine:</label>
                <select name="cuisine" id="cuisine">
                    <option value="">All</option>
                    <?php foreach ($cuisines as $c): ?>
                        <option value="<?= htmlspecialchars($c) ?>" <?= $cuisine == $c ? "selected" : "" ?>>
                            <?= htmlspecialchars($c) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn-filter">Apply</button>
                <a href="community.php" class="btn-clear">Clear</a>
            </form>
        </section>


        <div class="community-list">
            <?php foreach ($entries as $entry): ?>
                <div class="community-card">
                    <div class="c-header">
                        <img src="<?= !empty($entry['profile_picture']) ? 'uploads/' . htmlspecialchars($entry['profile_picture']) : 'assets/images/default_user.jpeg' ?>"
                            class="profile-pic" alt="">
                        <span><?= htmlspecialchars($entry['first_name'] . ' ' . $entry['last_name']) ?></span>
                    </div>
                    <h3><?= htmlspecialchars($entry['title']) ?></h3>
                    <?php if ($entry['image_url']): ?>
                        <img src="<?= htmlspecialchars($entry['image_url']) ?>" alt="Recipe image" class="entry-img">
                    <?php endif; ?>
                    <p><?= htmlspecialchars(substr($entry['content'], 0, 120)) ?>...</p>
                    <div class="entry-footer">
                        <button class="like-btn" data-id="<?= $entry['entry_id'] ?>">👏 <?= $entry['claps'] ?? 0 ?></button>
                        <span><?= date("M d, Y", strtotime($entry['created_at'])) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>


    </main>
    <script src="./assets/js/hamburger_menu.js"></script>
    <script src="./assets/js/cookie.js"></script>
    <?php include("./includes/footer.php"); ?>
    <?php include("./includes/cookie_consent.php"); ?>
</body>

</html>