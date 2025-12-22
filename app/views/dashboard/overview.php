<?php declare(strict_types=1); ?>

<!-- HEADER -->
<header class="top">
    <div>
        <h1>Welcome back, <?= htmlspecialchars($user['name']) ?></h1>
        <p>Here's what's happening with your weather alerts today.</p>
    </div>

    <div class="plan">
        <div>
            <small>CURRENT PLAN</small>
            <strong><?= htmlspecialchars($subscription['plan_name'] ?? 'Free') ?></strong>
            <span class="badge green">Active</span>
        </div>
        <div>
            <small>EXPIRES</small>
            <strong><?= htmlspecialchars($subscription['valid_till'] ?? '—') ?></strong>
            <a href="/subscription/upgrade" class="upgrade">Upgrade</a>
        </div>
    </div>
</header>

<!-- STATS -->
<section class="stats">
    <div class="stat">
        <span>Cities Monitored</span>
        <strong><?= (int)$stats['cities'] ?></strong>
    </div>
    <div class="stat">
        <span>Active Alerts</span>
        <strong><?= (int)$stats['active'] ?></strong>
    </div>
    <div class="stat">
        <span>Triggered Today</span>
        <strong><?= (int)$stats['today'] ?></strong>
    </div>
    <div class="stat">
        <span>Last Alert Sent</span>
        <strong><?= htmlspecialchars($stats['last'] ?? '—') ?></strong>
    </div>
</section>

<!-- CONTENT GRID -->
<section class="grid">

    <!-- ACTIVE ALERTS -->
    <div class="card">
        <header class="card-header">
            <h2>Active Alerts</h2>
            <a href="#" data-page="alerts" class="view-all">View All</a>
        </header>

        <table class="alerts-table">
            <thead>
                <tr>
                    <th>CITY</th>
                    <th>CONDITION</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($alerts as $a): ?>
                <tr>
                    <td>
                        <div class="city">
                            <img src="/assets/images/cities/<?= strtolower($a['city']) ?>.png" alt="">
                            <?= htmlspecialchars($a['city']) ?>
                        </div>
                    </td>
                    <td>
                        <span class="pill <?= $a['alert_type'] ?>">
                            <?= htmlspecialchars($a['label']) ?>
                        </span>
                    </td>
                    <td>
                        <span class="status <?= $a['is_active'] ? 'green' : 'yellow' ?>">
                            <?= $a['is_active'] ? 'Monitoring' : 'Paused' ?>
                        </span>
                    </td>
                    <td class="actions">
                        <button title="Pause">⏸</button>
                        <button title="Edit">✏️</button>
                        <button title="Delete" class="danger">🗑</button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- QUICK ADD ALERT -->
    <div class="card side">
        <h3>Quick Add Alert</h3>
        <p class="muted">Get notified instantly when weather changes.</p>

        <form method="post" action="/alerts/create">
            <label>City Name</label>
            <input type="text" name="city" placeholder="Search city..." required>

            <label>Condition</label>
            <select name="alert_type" required>
                <option value="temp_above">Temperature Above</option>
                <option value="rain">Rain</option>
                <option value="storm">Storm</option>
            </select>

            <label>Threshold Value</label>
            <div class="threshold">
                <input type="number" name="threshold_value" required>
                <span>°C</span>
            </div>

            <button type="submit" class="primary">
                🔔 Create Alert
            </button>
        </form>
    </div>

</section>

<!-- RECENT HISTORY -->
<section class="card">
    <h2>Recent Alert History</h2>

    <table class="history-table">
        <thead>
            <tr>
                <th>TIME SENT</th>
                <th>CITY</th>
                <th>TRIGGER EVENT</th>
                <th>DELIVERY</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($history as $h): ?>
            <tr>
                <td><?= htmlspecialchars($h['sent_at']) ?></td>
                <td><?= htmlspecialchars($h['city']) ?></td>
                <td><?= htmlspecialchars($h['event']) ?></td>
                <td>
                    <span class="badge <?= $h['status'] === 'sent' ? 'green' : 'red' ?>">
                        <?= ucfirst($h['status']) ?>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
