<?php declare(strict_types=1); 

ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<link rel="stylesheet" href="/assets/css/dashboard.css">

<div class="app">

    <?php require __DIR__ . '/partials/sidebar.php'; ?>

    <main class="main" id="dashboard-content">
    <?php require __DIR__ . '/overview.php'; ?>
</main>


</div>

<script src="/assets/js/dashboard.js"></script>
