<?php
include '../includes/db_connect.php';
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $cuisine = $_POST['cuisine_type'];
    $dietary = $_POST['dietary_preference'];
    $difficulty = $_POST['difficulty'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $user_id = $_SESSION['user_id'] ?? null;

    // Handle cover image upload
    $cover_img_src = null;
    // if (!empty($_FILES['cover_img']['name'])) {
    //     $uploadFolder = "../uploads/";
    //     if (!is_dir($uploadFolder)) mkdir($uploadFolder, 0777, true);

    //     $fileName = time() . "_" . basename($_FILES["cover_img"]["name"]);
    //     $img_dir = "./uploads/";
    //     $targetFile = $img_dir . $fileName;

    //     if (move_uploaded_file($_FILES["cover_img"]["tmp_name"], $targetFile)) {
    //         $cover_img_src = $targetFile;
    //     }
    // }
    if (!empty($_FILES['cover_img']['name'])) {
        $uploadFolder = __DIR__ . "/../uploads/"; 
        if (!is_dir($uploadFolder)) mkdir($uploadFolder, 0777, true);

        $fileName = time() . "_" . basename($_FILES["cover_img"]["name"]);
        $dbPath = "./uploads/" . $fileName;     
        $targetFile = $uploadFolder . $fileName;   

        if (move_uploaded_file($_FILES["cover_img"]["tmp_name"], $targetFile)) {
            $cover_img_src = $dbPath; 
        }
    }


    $stmt = $conn->prepare("
        INSERT INTO recipes (user_id, title, description, cuisine_type, dietary_preference, difficulty, cover_img_src, is_featured)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issssssi", $user_id, $title, $description, $cuisine, $dietary, $difficulty, $cover_img_src, $is_featured);

    if ($stmt->execute()) {
        $message = "Recipe added successfully!";
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Create Recipe</title>
    <link rel="stylesheet" href="../assets/css/admin_recipe_form.css">
</head>

<body>

    <div class="admin-container">
        <?php if ($message): ?><p class="message"><?= $message ?></p><?php endif; ?>
        <div class="page-header">
            <h1>Add New Recipe</h1>
        </div>

        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label for="title">Recipe Title</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="cuisine_type">Cuisine Type</label>
                <input type="text" name="cuisine_type" required>
            </div>
            <div class="form-group">
                <label for="dietary_preference">Dietary Preference</label>
                <input type="text" name="dietary_preference">
            </div>
            <div class="form-group">
                <label for="difficulty">Difficulty</label>
                <select name="difficulty" required>
                    <option value="Easy">Easy</option>
                    <option value="Medium">Medium</option>
                    <option value="Hard">Hard</option>
                </select>
            </div>
            <div class="form-group">
                <label for="cover_img">Cover Image</label>
                <input type="file" name="cover_img" accept="image/*">
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="is_featured"> Mark as Featured</label>
            </div>
            <!-- <button type="submit">Add Recipe</button> -->
            <div class="form-actions">
                <button type="submit">Add Recipe</button>
                <a href="recipes.php" class="btn-back">← Back</a>
            </div>
        </form>
    </div>
</body>

</html>