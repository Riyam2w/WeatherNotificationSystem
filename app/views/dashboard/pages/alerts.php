<section class="alerts-page">

    <!-- Header -->
    <div class="alerts-header header-flex">
        <div>
            <h1 class="page-title">My Alerts</h1>
            <p class="page-subtitle">
                Manage your active weather monitoring and notification settings.
            </p>
        </div>

        <div class="header-actions">
            <?php 
                $limit = $features['max_alerts'] ?? 3;
                $count = $currentAlertCount ?? 0;
                $isLimitReached = $count >= $limit;
            ?>
            <div class="alert-usage mb-2 usage-sub">
                Plan Usage: <strong><?= $count ?> / <?= $limit ?></strong> alerts
            </div>
            <button class="btn btn-primary <?= $isLimitReached ? 'limit-reached' : '' ?>" id="createAlertBtn" <?= $isLimitReached ? 'disabled' : '' ?>>
                <?= $isLimitReached ? 'Limit Reached' : '+ Create Alert' ?>
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="alerts-filters">
        <input
            type="text"
            id="alertSearch"
            class="filter-input"
            placeholder="Search locations..."
        >

        <select id="statusFilter" class="filter-select">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="paused">Paused</option>
            <option value="triggered">Triggered</option>
        </select>

        <div class="filter-right">
            <label>Sort by:</label>
            <select id="sortBy" class="filter-select">
                <option value="created_at">Date Created</option>
                <option value="city">City</option>
            </select>
        </div>
    </div>

    <!-- Alerts Table -->
    <div class="alerts-table-wrap">
        <table class="alerts-table">
            <thead>
                <tr>
                    <th>City / Location</th>
                    <th>Condition</th>
                    <th>Threshold</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="alertsTableBody">
                <?php if (empty($alerts)): ?>
                    <tr>
                        <td colspan="5" class="empty-state">
                            No alerts found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($alerts as $alert): ?>
                    <tr 
                        data-city="<?= htmlspecialchars(strtolower($alert['city_name'])) ?>"
                        data-status="<?= htmlspecialchars($alert['status']) ?>"
                        data-created="<?= strtotime($alert['created_at']) ?>"
                    >
                        <td><?= htmlspecialchars($alert['city_name']) ?></td>
                        <td><?= htmlspecialchars($alert['condition_label']) ?></td>
                        <td>
                            <?= htmlspecialchars($alert['operator']) ?> 
                            <?= htmlspecialchars($alert['threshold_value']) ?> 
                            <?= htmlspecialchars($alert['unit']) ?>
                        </td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($alert['status']) ?>">
                                    <?= ucfirst(htmlspecialchars($alert['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <button 
                                    class="btn btn-sm btn-danger delete-alert-btn" 
                                    data-id="<?= htmlspecialchars($alert['id']) ?>"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</section>
