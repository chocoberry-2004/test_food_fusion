<?php
include '../includes/db_connect.php';

// robust session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// auth
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
$user_id = (int)$_SESSION['user_id'];

// check admin privilege
$stmt = $conn->prepare("SELECT is_admin, first_name, last_name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userRow = $stmt->get_result()->fetch_assoc();
if (!$userRow || (int)$userRow['is_admin'] !== 1) {
    http_response_code(403);
    echo "Access denied. Admins only.";
    exit;
}

// Search handling
$searchRaw = trim((string)($_GET['q'] ?? ''));
$hasSearch = $searchRaw !== '';
$like = '%' . $searchRaw . '%';

// Prepare single statement (avoid SQL path divergence)
$sql = "
    SELECT r.recipe_id, r.title, r.cuisine_type, r.dietary_preference, r.difficulty,
           r.is_featured, r.cover_img_src, r.created_at, u.first_name, u.last_name
    FROM recipes r
    LEFT JOIN users u ON r.user_id = u.user_id
    " . ($hasSearch ? "WHERE r.title LIKE ? " : "") . "
    ORDER BY r.created_at DESC
    LIMIT 500
";
$stmt = $conn->prepare($sql);
if ($hasSearch) {
    $stmt->bind_param("s", $like);
}
$stmt->execute();
$result = $stmt->get_result();

// total count for UI — basic
$totalRow = $conn->query("SELECT COUNT(*) AS cnt FROM recipes")->fetch_assoc();
$totalRecipes = (int)$totalRow['cnt'];

// Helper: resolve image URL (supports http(s) URLs and server-relative paths)
function resolve_image_url_from_admin($rawPath)
{
    if (empty($rawPath)) return '';

    $raw = trim($rawPath);

    // if it's already an absolute URL, return as-is (trust but escape later when printing)
    if (preg_match('#^https?://#i', $raw)) {
        return $raw;
    }

    // Normalize leading ./ or / or ../
    $clean = preg_replace('#^(\./|\.\./|/)+#', '', $raw);

    // server path relative to project root (admin file is in admin/)
    $serverPath1 = __DIR__ . '/../' . $clean;
    $urlPath1 = '../' . $clean;

    if (file_exists($serverPath1)) return $urlPath1;

    // try alternative: raw string as-is relative to project root
    $serverPath2 = __DIR__ . '/../' . ltrim($raw, './\\/');
    $urlPath2 = '../' . ltrim($raw, './\\/');

    if (file_exists($serverPath2)) return $urlPath2;

    // not found: return empty -> caller can show fallback
    return '';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Admin — Recipes | FoodFusion</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <link rel="stylesheet" href="../assets/css/admin_recipes.css">
  <link rel="stylesheet" href="../assets/css/admin_common.css">
</head>
<body class="admin-recipes">
  <?php include('./admin_sidebar.php'); ?>
  <div class="main">
    <?php include './admin_top_bar.php'; ?>

    <section class="content">
      <div class="page-header">
        <h1>Recipes</h1>
        <div class="actions">
          <form method="GET" class="search-form" action="recipes.php" role="search" aria-label="Search recipes">
            <input type="search" name="q" placeholder="Search recipes..." value="<?= htmlspecialchars($searchRaw, ENT_QUOTES) ?>">
            <button type="submit">Search</button>
          </form>
          <a href="recipe_create.php" class="btn btn-primary">+ Create Recipe</a>
        </div>
      </div>

      <div class="meta-row">
        <div class="meta-item">Total recipes: <span class="meta-count"><?= $totalRecipes ?></span></div>
        <?php if ($hasSearch): ?>
          <div class="meta-item">Showing results for: <em><?= htmlspecialchars($searchRaw, ENT_QUOTES) ?></em></div>
        <?php endif; ?>
      </div>

      <div class="table-wrap">
        <table class="recipes-table" aria-describedby="recipes-list">
          <thead>
            <tr>
              <th class="col-thumb">Thumb</th>
              <th>Title</th>
              <th>Cuisine</th>
              <th>Dietary</th>
              <th>Difficulty</th>
              <th>Featured</th>
              <th>Author</th>
              <th>Created</th>
              <th class="col-actions">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($r = $result->fetch_assoc()): ?>
                <?php
                  $imgUrl = resolve_image_url_from_admin($r['cover_img_src'] ?? '');
                  $author = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? ''));
                ?>
                <tr>
                  <td class="col-thumb" data-label="Thumb">
                    <?php if ($imgUrl !== ''): ?>
                      <img src="<?= htmlspecialchars($imgUrl, ENT_QUOTES) ?>" alt="<?= htmlspecialchars($r['title'] ?? 'Recipe image', ENT_QUOTES) ?>" loading="lazy">
                    <?php else: ?>
                      <div class="no-thumb">No image</div>
                    <?php endif; ?>
                  </td>

                  <td class="title-cell" data-label="Title"><?= htmlspecialchars($r['title'] ?? '', ENT_QUOTES) ?></td>
                  <td data-label="Cuisine"><?= htmlspecialchars($r['cuisine_type'] ?? '', ENT_QUOTES) ?></td>
                  <td data-label="Dietary"><?= htmlspecialchars($r['dietary_preference'] ?? '', ENT_QUOTES) ?></td>
                  <td data-label="Difficulty"><?= htmlspecialchars($r['difficulty'] ?? '', ENT_QUOTES) ?></td>
                  <td data-label="Featured">
                    <?php if ((int)$r['is_featured']): ?>
                      <span class="chip">Featured</span>
                    <?php else: ?>
                      —
                    <?php endif; ?>
                  </td>
                  <td data-label="Author"><?= htmlspecialchars($author, ENT_QUOTES) ?></td>
                  <td data-label="Created"><?= htmlspecialchars(date('M d, Y', strtotime($r['created_at'] ?? '')), ENT_QUOTES) ?></td>
                  <td class="col-actions" data-label="Actions">
                    <a class="btn small outline" href="recipe_edit.php?id=<?= (int)$r['recipe_id'] ?>">Edit</a>
                    <a class="btn small danger" href="recipe_delete.php?id=<?= (int)$r['recipe_id'] ?>"
                       onclick="return confirm('Are you sure you want to delete this recipe?');">Delete</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="9">
                  <div class="empty">No recipes yet — click <a href="recipe_create.php">Create Recipe</a> to add one.</div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </section>
    <?php include('./admin_footer.php'); ?>
    <script src="../assets/js/admin_side_bar.js"></script>
  </div>
</body>
</html>
