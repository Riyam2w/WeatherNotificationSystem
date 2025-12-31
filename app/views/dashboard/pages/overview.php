<?php
declare(strict_types=1);


?>

<div class="overview">
    <h2 class="page-title">Dashboard Overview</h2>

    <div class="stats-grid">

    <div class="stat-card">
        <span class="stat-label">Total Alerts</span>
        <strong class="stat-value"><?= $totalAAlerts ?></strong>
    </div>

    <div class="stat-card">
        <span class="stat-label">Active Alerts</span>
        <strong class="stat-value"><?= $activeAlerts ?></strong>    

    </div>

    <div class="stat-card">
        <span class="stat-label">Cities Monitored</span>
        <strong class="stat-value"><?= $citiesCount ?></strong>
    </div>

    <div class="stat-card">
        <span class="stat-label">Current Plan</span>
        <strong class="stat-value"><?= htmlspecialchars($currentPlan['name'] ?? 'Free') ?></strong>
        <?php if (!empty($currentPlan['expires_at'])): ?>
            <small class="stat-sub">Expires on <?= date('d M Y', strtotime($currentPlan['expires_at'])) ?>
        </small>
        <?php endif; ?>
    </div>
</div>