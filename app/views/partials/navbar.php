<?php declare(strict_types=1); ?>

<nav class="navbar">
    <div class="nav-container">

        <!-- Logo -->
        <a href="/" class="nav-logo">
            🌩 WeatherNotify
        </a>

        <!-- Navigation -->
        <ul class="nav-links">
            <li><a href="/">Home</a></li>
            <li><a href="/#features">Features</a></li>
            <li><a href="/pricing">Pricing</a></li>

                      <?php if (!empty($_SESSION['user_id'])): ?>

                <li><a href="/dashboard">Dashboard</a></li>
            <?php endif; ?>
        </ul>

        <!-- Actions -->
         <div class="nav-actions">
            <?php if (!empty($_SESSION['user_id'])): ?>
                <a href="/dashboard" class="btn-outline">Dashboard</a>
                <a href="/logout" class="btn-outline">Logout</a>
            <?php else: ?>
                <a href="/register" class="btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>

    </div>
</nav>
