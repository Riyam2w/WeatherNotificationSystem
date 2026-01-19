<div class="history-page">
    <div class="page-header">
        <h1>Activity History</h1>
        <p>A timeline of your alerts and system notifications.</p>
    </div>

    <div class="timeline-container">
        <?php if (empty($activities)): ?>
            <div class="empty-state">
                <div class="empty-icon">📜</div>
                <h3>No activity found</h3>
                <p>Your alert creations and notifications will appear here.</p>
            </div>
        <?php else: ?>
            <div class="timeline">
                <?php foreach ($activities as $activity): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker status-<?= $activity['status_class'] ?>"></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <span class="event-type type-<?= $activity['type'] ?>">
                                    <?= $activity['type'] === 'alert_created' ? 'Alert Created' : 'Notification Sent' ?>
                                </span>
                                <time class="event-date">
                                    <?= date('M d, Y • h:i A', strtotime($activity['event_date'])) ?>
                                </time>
                            </div>
                            <div class="event-message">
                                <?= htmlspecialchars($activity['message']) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<link rel="stylesheet" href="/assets/css/dashboard/history.css">
