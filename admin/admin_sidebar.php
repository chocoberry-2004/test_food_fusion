<?php
if (!isset($userRow)) {
  $userRow = ['first_name' => 'Admin', 'last_name' => ''];
}
?>

<aside id="sidebar" class="sidebar">
  <div class="brand">
    <a href="../index.php" class="brand-link">
      <span>FoodFusion Admin</span>
    </a>
  </div>

  <nav class="nav">
    <a href="index.php" class="nav-item <?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Dashboard</a>
    <a href="recipes.php" class="nav-item <?= (basename($_SERVER['PHP_SELF']) == 'recipes.php') ? 'active' : '' ?>">Recipes</a>
    <a href="messages.php" class="nav-item <?= (basename($_SERVER['PHP_SELF']) == 'messages.php') ? 'active' : '' ?>" >Messages</a>
    <a href="users.php" class="nav-item <?= (basename($_SERVER['PHP_SELF']) == 'users.php') ? 'active' : '' ?>" >Users</a>

  </nav>


  <div class="sidebar-footer">
    <a class="btn-logout" href="../auth/logout.php">Logout</a>
  </div>
</aside>