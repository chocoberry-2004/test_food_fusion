<header class="navbar">
  <div class="nav-container">
    <a href="index.php" class="logo">FoodFusion</a>

    <!-- Hamburger Menu Button -->
    <button class="hamburger" id="hamburger" aria-label="Toggle navigation">
      ☰
    </button>

    <!-- Navigation Links -->
    <nav class="nav-links" id="nav-links">
      <a href="index.php">Home</a>
      <a href="recipes.php">Recipes</a>
      <a href="community.php">Community</a>
      <!-- <a href="resources.php">Resources</a> -->
      <!-- Dropdown for Resources -->
      <div class="dropdown">
        <a href="#" class="dropbtn">Resources ▼</a>
        <div class="dropdown-content">
          <a href="culinary_resources.php?type=culinary">Culinary Resources</a>
          <a href="educational_resources.php?type=educational">Educational Resources</a>
        </div>
      </div>
      <a href="about.php">About</a>
      <a href="contact.php">Contact</a>

      <?php if (isset($_SESSION['user_id'])): ?>
        <!-- <a href="/auth/logout.php" class="auth-link">Logout</a> -->
        <a href="auth/logout.php" class="auth-link">Logout</a>
        <a href="profile.php">Profile</a>
      <?php else: ?>
        <a href="auth/login.php" class="auth-link">Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>