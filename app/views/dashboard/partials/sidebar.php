<?php
/**
 * Sidebar navigation
 * Expected variables:
 * - $role (admin | user)
 * - $activePage (string)
 */
?>

<aside class="sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-icon">
            <!-- replace with svg/logo if needed -->
            <span class="logo-dot"></span>
        </div>
        <span class="brand-text">WeatherNotify</span>
    </div>

    <!-- Navigation -->
    <nav class="sidebar-nav">

        <a href="#"
           class="nav-item <?= $activePage === 'overview' ? 'active' : '' ?>"
           data-page="overview">
            <span class="icon">🏠</span>
            <span>Dashboard</span>
        </a>

        <a href="#"
           class="nav-item <?= $activePage === 'alerts' ? 'active' : '' ?>"
           data-page="alerts">
            <span class="icon">🔔</span>
            <span>My Alerts</span>
        </a>

        <a href="#"
           class="nav-item <?= $activePage === 'history' ? 'active' : '' ?>"
           data-page="history">
            <span class="icon">🕘</span>
            <span>History</span>
        </a>

        <a href="#"
           class="nav-item <?= $activePage === 'subscriptions' ? 'active' : '' ?>"
           data-page="subscriptions">
            <span class="icon">💳</span>
            <span>Subscriptions</span>
        </a>

        <?php if ($role === 'admin'): ?>
            <a href="#"
               class="nav-item <?= $activePage === 'users' ? 'active' : '' ?>"
               data-page="users">
                <span class="icon">👥</span>
                <span>Users</span>
            </a>
        <?php endif; ?>

        <a href="#"
           class="nav-item <?= $activePage === 'settings' ? 'active' : '' ?>"
           data-page="settings">
            <span class="icon">⚙️</span>
            <span>Settings</span>
        </a>

    </nav>

    <!-- User footer -->
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="avatar">
                <?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?>
            </div>
            <div>
                <div class="user-name">
                    <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>
                </div>
                <div class="user-plan">
                    <?= htmlspecialchars($_SESSION['plan_name'] ?? 'Free Plan') ?>
                </div>
            </div>
        </div>

        <a href="/logout" class="logout-btn">Logout</a>
    </div>

</aside>
