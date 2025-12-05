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

// --- Get recipe id ---
$recipeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($recipeId <= 0) {
    header("Location: recipes.php?msg=Invalid+recipe+id");
    exit;
}

// --- Fetch recipe data ---
$stmt = $conn->prepare("SELECT * FROM recipes WHERE recipe_id = ?");
$stmt->bind_param("i", $recipeId);
$stmt->execute();
$recipe = $stmt->get_result()->fetch_assoc();

if (!$recipe) {
    header("Location: recipes.php?msg=Recipe+not+found");
    exit;
}

$message = "";

// --- Handle update ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $cuisine = $_POST['cuisine_type'];
    $dietary = $_POST['dietary_preference'];
    $difficulty = $_POST['difficulty'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // Handle optional cover image upload
    $cover_img_src = $recipe['cover_img_src']; 
    if (!empty($_FILES['cover_img']['name'])) {
        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["cover_img"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["cover_img"]["tmp_name"], $targetFile)) {
            // delete old image if exists
            if ($cover_img_src && file_exists(__DIR__ . '/../' . ltrim($cover_img_src, './\\/'))) {
                unlink(__DIR__ . '/../' . ltrim($cover_img_src, './\\/'));
            }
            $cover_img_src = $targetFile;
        }
    }

    $stmt = $conn->prepare("
        UPDATE recipes 
        SET title=?, description=?, cuisine_type=?, dietary_preference=?, difficulty=?, cover_img_src=?, is_featured=?
        WHERE recipe_id=?
    ");
    $stmt->bind_param("ssssssii", $title, $description, $cuisine, $dietary, $difficulty, $cover_img_src, $is_featured, $recipeId);

    if ($stmt->execute()) {
        $message = "Recipe updated successfully!";
        // refresh data
        $recipe = [
            'title' => $title,
            'description' => $description,
            'cuisine_type' => $cuisine,
            'dietary_preference' => $dietary,
            'difficulty' => $difficulty,
            'cover_img_src' => $cover_img_src,
            'is_featured' => $is_featured,
        ];
    } else {
        $message = "Error updating recipe: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Recipe</title>
    <link rel="stylesheet" href="../assets/css/admin_recipe_form.css">
</head>

<body>

    <div class="admin-container">
        <h1>Edit Recipe</h1>
        <?php if ($message): ?><p class="message"><?= $message ?></p><?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label for="title">Recipe Title</label>
                <input type="text" name="title" value="<?= htmlspecialchars($recipe['title']) ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" rows="4" required><?= htmlspecialchars($recipe['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="cuisine_type">Cuisine Type</label>
                <input type="text" name="cuisine_type" value="<?= htmlspecialchars($recipe['cuisine_type']) ?>" required>
            </div>
            <div class="form-group">
                <label for="dietary_preference">Dietary Preference</label>
                <input type="text" name="dietary_preference" value="<?= htmlspecialchars($recipe['dietary_preference']) ?>">
            </div>
            <div class="form-group">
                <label for="difficulty">Difficulty</label>
                <select name="difficulty" required>
                    <option value="Easy" <?= $recipe['difficulty'] === 'Easy' ? 'selected' : '' ?>>Easy</option>
                    <option value="Medium" <?= $recipe['difficulty'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="Hard" <?= $recipe['difficulty'] === 'Hard' ? 'selected' : '' ?>>Hard</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cover_img">Cover Image</label>
                <?php if ($recipe['cover_img_src']): ?>
                    <div class="current-img">
                        <img src="../<?= ltrim($recipe['cover_img_src'], './') ?>" alt="Current Image" height="80">
                    </div>
                <?php endif; ?>
                <input type="file" name="cover_img" accept="image/*">
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="is_featured" <?= $recipe['is_featured'] ? 'checked' : '' ?>> Mark as Featured</label>
            </div>
            <div class="form-actions">
                <button type="submit">Update Recipe</button>
                <a href="recipes.php" class="btn-back">← Back</a>
            </div>
        </form>
    </div>
</body>

</html>