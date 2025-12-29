<?php

?>

<aside class="sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
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

        <a href="#"
           class="nav-item <?= $activePage === 'pages/settings' ? 'active' : '' ?>"
           data-page="settings">
            <span class="icon">⚙️</span>
            <span>Settings</span>
        </a>

    </nav>

    <!-- User footer -->
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="avatar">
                <?= strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)) ?>
            </div>
            <div>
                <div class="user-name">
                    <?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?>
                </div>
                <div class="user-plan">
                    <?= htmlspecialchars($_SESSION['plan_name'] ?? 'Free Plan') ?>
                </div>
            </div>
        </div>

        <button id="logoutBtn" class="logout-btn" type="button">
            Logout
        </button>
    </div>

</aside>
