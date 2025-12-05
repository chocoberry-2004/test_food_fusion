<?php
include './includes/db_connect.php';
if (!isset($_SESSION)) {
    session_start();
}
// Get filter values from GET request
$cuisine = isset($_GET['cuisine']) ? trim($_GET['cuisine']) : '';
$dietary = isset($_GET['dietary']) ? trim($_GET['dietary']) : '';
$difficulty = isset($_GET['difficulty']) ? trim($_GET['difficulty']) : '';

// Base query
$sql = "
    SELECT r.recipe_id, r.title, r.description, r.cuisine_type, r.dietary_preference, 
           r.difficulty, r.cover_img_src, r.created_at, u.first_name, u.last_name
    FROM recipes r
    LEFT JOIN users u ON r.user_id = u.user_id
    WHERE 1=1
";

// Apply filters dynamically
if ($cuisine !== '') {
    $sql .= " AND r.cuisine_type = '" . $conn->real_escape_string($cuisine) . "'";
}
if ($dietary !== '') {
    $sql .= " AND r.dietary_preference = '" . $conn->real_escape_string($dietary) . "'";
}
if ($difficulty !== '') {
    $sql .= " AND r.difficulty = '" . $conn->real_escape_string($difficulty) . "'";
}

$sql .= " ORDER BY r.created_at DESC";

$result = $conn->query($sql);
$recipes = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
}
// Fetch distinct cuisines
$cuisineSql = "SELECT DISTINCT cuisine_type FROM recipes WHERE cuisine_type IS NOT NULL AND cuisine_type <> ''";
$cuisineResult = $conn->query($cuisineSql);
$cuisines = [];
if ($cuisineResult && $cuisineResult->num_rows > 0) {
    while ($row = $cuisineResult->fetch_assoc()) {
        $cuisines[] = $row['cuisine_type'];
    }
}
// Fetch distinct dietary preferences
$dietSql = "SELECT DISTINCT dietary_preference 
            FROM recipes 
            WHERE dietary_preference IS NOT NULL AND dietary_preference <> ''";
$dietResult = $conn->query($dietSql);
$diets = [];
if ($dietResult && $dietResult->num_rows > 0) {
    while ($row = $dietResult->fetch_assoc()) {
        $diets[] = $row['dietary_preference'];
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Recipe Collection - FoodFusion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/recipes.css">
</head>

<body class="recipes-page">

    <?php include("./includes/header.php"); ?>

    <section class="recipes-hero">
        <div class="overlay">
            <h1>Recipe Collection</h1>
            <p>A curated collection of diverse recipes from around the world.</p>
        </div>
    </section>

    <!-- Filter Form -->
    <section class="filter-section">
        <div class="container">
            <form method="GET" action="recipes.php" class="filter-form">
                <label for="cuisine">Cuisine:</label>
                <select name="cuisine" id="cuisine">
                    <option value="">All</option>
                    <?php foreach ($cuisines as $c): ?>
                        <option value="<?= htmlspecialchars($c) ?>" <?= $cuisine == $c ? "selected" : "" ?>>
                            <?= htmlspecialchars($c) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="dietary">Dietary Preference:</label>
                <select name="dietary" id="dietary">
                    <option value="">All</option>
                    <?php foreach ($diets as $d): ?>
                        <option value="<?= htmlspecialchars($d) ?>" <?= $diet == $d ? "selected" : "" ?>>
                            <?= htmlspecialchars($d) ?>
                        </option>
                    <?php endforeach; ?>
                </select>



                <label for="difficulty">Difficulty:</label>
                <select name="difficulty" id="difficulty">
                    <option value="">All</option>
                    <option value="Easy" <?= $difficulty == "Easy" ? "selected" : "" ?>>Easy</option>
                    <option value="Medium" <?= $difficulty == "Medium" ? "selected" : "" ?>>Medium</option>
                    <option value="Hard" <?= $difficulty == "Hard" ? "selected" : "" ?>>Hard</option>
                </select>

                <button type="submit" class="btn-submit">Filter</button>
                <a href="recipes.php" class="btn-clear">Clear</a>
            </form>
        </div>
    </section>

    <!-- Recipe List -->
    <section class="recipes-list">
        <div class="container">
            <h2>All Recipes</h2>
            <div class="recipe-grid">
                <?php if (!empty($recipes)): ?>
                    <?php foreach ($recipes as $recipe): ?>
                        <div class="recipe-card">
                            <img src="<?= htmlspecialchars($recipe['cover_img_src']) ?: './assets/images/kitchen_bg.jpg' ?>" alt="">
                            <div class="card-content">
                                <h3><?= htmlspecialchars($recipe['title']) ?></h3>
                                <p><?= htmlspecialchars(substr($recipe['description'], 0, 100)) ?>...</p>
                                <small>
                                    <?= htmlspecialchars($recipe['cuisine_type']) ?> |
                                    <?= htmlspecialchars($recipe['dietary_preference']) ?> |
                                    <?= htmlspecialchars($recipe['difficulty']) ?>
                                </small><br>
                                <small>By <?= htmlspecialchars($recipe['first_name'] . ' ' . $recipe['last_name']) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No recipes found matching your filters.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <script src="./assets/js/hamburger_menu.js"></script>
    <script src="./assets/js/cookie.js"></script>
    <?php include("./includes/footer.php"); ?>
    <?php include("./includes/cookie_consent.php"); ?>
</body>

</html>