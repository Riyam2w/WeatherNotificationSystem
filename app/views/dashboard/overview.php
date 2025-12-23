<?php
declare(strict_types=1);

?>
<div class="dashboard">

    <div class="dashboard-header">
        <div>
            <h1>Welcome back, 
                <!-- <?= htmlspecialchars($_SESSION['UserName']) ?> -->
            </h1>
            <p>Here's what's happening with your weather alerts today.</p>
        </div>

        <div class="plan-card">
            <div>
                <small>CURRENT PLAN</small>
                <strong>
                    <!-- <?= htmlspecialchars($plan['name']) ?> -->
                </strong>
                <span class="badge active">
                    <!-- <?= htmlspecialchars($plan['status']) ?> -->
                </span>
            </div>
            <div>
                <small>EXPIRES</small>
                <strong>
                    <!-- <?= htmlspecialchars($plan['expiry']) ?> -->
                </strong>
                <a href="/upgrade">Upgrade</a>
            </div>
        </div>
    </div>

    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card">
            <span>Cities Monitored</span>
            <strong>
                <!-- <?= $stats['cities_count'] ?> -->
            </strong>
        </div>

        <div class="stat-card">
            <span>Active Alerts</span>
            <strong>
                <!-- <?= $stats['active_alerts'] ?> -->
            </strong>
        </div>

        <div class="stat-card">
            <span>Triggered Today</span>
            <strong>
                <!-- <?= $stats['triggered_today'] ?> -->
            </strong>
        </div>

        <div class="stat-card">
            <span>Last Alert Sent</span>
            <strong>
                <!-- <?= $stats['last_alert_time'] ?> -->
            </strong>
        </div>
    </div>

    <!-- MAIN GRID -->
    <div class="dashboard-grid">

        <!-- ACTIVE ALERTS -->
        <section class="card">
            <header>
                <h3>Active Alerts</h3>
                <a href="/alerts">View All</a>
            </header>

            <table>
                <thead>
                <tr>
                    <th>City</th>
                    <th>Condition</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($alerts as $alert): ?>
                    <tr>
                        <td><?= htmlspecialchars($alert['city']) ?></td>
                        <td><?= htmlspecialchars($alert['condition']) ?></td>
                        <td>
                            <span class="status <?= $alert['status'] ?>">
                                <?= ucfirst($alert['status']) ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="#">⏸</a>
                            <a href="#">✏️</a>
                            <a href="#">🗑</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <!-- QUICK ADD ALERT -->
        <section class="card">
            <h3>Quick Add Alert</h3>
            <p>Get notified instantly when weather changes.</p>

            <form method="post" action="/alerts/create">
                <label>City Name</label>
                <input type="text" name="city" required>

                <label>Condition</label>
                <select name="condition">
                    <option value="temp_above">Temperature Above</option>
                    <option value="rain">Rain</option>
                    <option value="storm">Storm</option>
                </select>

                <label>Threshold Value</label>
                <div class="inline">
                    <input type="number" name="threshold" required>
                    <span>°C</span>
                </div>

                <button type="submit">Create Alert</button>
            </form>
        </section>

    </div>

    <!-- HISTORY -->
    <section class="card">
        <h3>Recent Alert History</h3>

        <table>
            <thead>
            <tr>
                <th>Time Sent</th>
                <th>City</th>
                <th>Trigger Event</th>
                <th>Delivery</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($history as $row): ?>
                <tr>
                    <td><?= $row['time'] ?></td>
                    <td><?= $row['city'] ?></td>
                    <td><?= $row['event'] ?></td>
                    <td>
                        <span class="delivery <?= $row['status'] ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <a class="view-history" href="/history">View Full History</a>
    </section>

</div>
