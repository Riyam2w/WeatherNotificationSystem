<?php declare(strict_types=1); ?>
<link rel="stylesheet" href="/assets/css/dashboard.css">

<div class="app">

    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <main class="main" id="dashboard-content">
        <?php
        // Load dashboard section (default: overview)
        require __DIR__ . '/' . $page . '.php';
        ?>
    </main>

</div>

<script src="/assets/js/dashboard.js"></script>
