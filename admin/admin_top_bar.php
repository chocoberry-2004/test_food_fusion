  <header class="admin-topbar">
      <button id="sidebarToggle" class="sidebar-toggle">☰</button>
      <div class="topbar-right">
          <div class="admin-welcome">
              <span class="hello">Welcome,</span>
              <strong><?= htmlspecialchars($userRow['first_name'] . ' ' . $userRow['last_name']) ?></strong>
          </div>
      </div>
  </header>