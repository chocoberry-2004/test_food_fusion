<?php
include './includes/db_connect.php';
if (!isset($_SESSION)) {
    session_start();
}
// Fetch all resources in "Educational" category grouped by type
$sql = "SELECT * FROM resources WHERE category = 'Educational' ORDER BY uploaded_at DESC";
$result = $conn->query($sql);

$resources = [
    'RecipeCard' => [],
    'Tutorial' => [],
    'Video' => [],
    'Infographic' => []
];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $resources[$row['resource_type']][] = $row;
    }
}

$conn->close();

// Assign shortcuts for template
$recipeCards  = $resources['RecipeCard'];
$tutorials    = $resources['Tutorial'];
$videos       = $resources['Video'];
$infographics = $resources['Infographic'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Educational Resources</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/educational_resources.css">
</head>

<body class="resources-page">
    <!-- Header -->
    <?php include("./includes/header.php"); ?>

    <!-- Hero Section -->
    <section class="resources-hero">
        <div class="overlay">
            <h1>Educational Resources</h1>
            <p>Providing downloadable resources, infographics, and videos on renewable energy and sustainability topics.</p>
        </div>
    </section>

    <main class="resources-main">

        <!-- Recipe Cards / Guides -->
        <section class="resource-section">
            <h2>📄 Guides & Recipe Cards</h2>
            <p class="section-subtitle">Printable study materials and structured guides.</p>
            <div class="card-grid">
                <?php if (!empty($recipeCards)): ?>
                    <?php foreach ($recipeCards as $card): ?>
                        <div class="resource-card">
                            <h3><?= htmlspecialchars($card['title']) ?></h3>
                            <a href="<?= htmlspecialchars($card['file_url']) ?>" download class="btn-download">Download</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No guides available yet.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Tutorials -->
        <section class="resource-section">
            <h2>📚 Tutorials</h2>
            <p class="section-subtitle">Step-by-step lessons on renewable energy and green practices.</p>
            <div class="card-grid">
                <?php if (!empty($tutorials)): ?>
                    <?php foreach ($tutorials as $tutorial): ?>
                        <div class="resource-card">
                            <h3><?= htmlspecialchars($tutorial['title']) ?></h3>
                            <a href="<?= htmlspecialchars($tutorial['file_url']) ?>" target="_blank" class="btn-download">View</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No tutorials available yet.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Videos -->
        <section class="resource-section">
            <h2>🎥 Educational Videos</h2>
            <p class="section-subtitle">Watch experts explain renewable energy concepts.</p>
            <div class="card-grid">
                <?php if (!empty($videos)): ?>
                    <?php foreach ($videos as $video): ?>
                        <div class="resource-card video-card">
                            <h3><?= htmlspecialchars($video['title']) ?></h3>
                            <div class="video-wrapper">
                                <iframe src="<?= htmlspecialchars($video['file_url']) ?>" frameborder="0" allowfullscreen></iframe>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No videos available yet.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Infographics -->
        <section class="resource-section">
            <h2>🖼️ Infographics</h2>
            <p class="section-subtitle">Visual resources to understand clean energy at a glance.</p>
            <div class="card-grid">
                <?php if (!empty($infographics)): ?>
                    <?php foreach ($infographics as $info): ?>
                        <div class="resource-card">
                            <h3><?= htmlspecialchars($info['title']) ?></h3>
                            <a href="<?= htmlspecialchars($info['file_url']) ?>" target="_blank" class="btn-download">View</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No infographics available yet.</p>
                <?php endif; ?>
            </div>
        </section>

    </main>
    <script src="./assets/js/hamburger_menu.js"></script>
    <script src="./assets/js/cookie.js"></script>
    <?php include("./includes/footer.php"); ?>
    <?php include("./includes/cookie_consent.php"); ?>
</body>

</html>