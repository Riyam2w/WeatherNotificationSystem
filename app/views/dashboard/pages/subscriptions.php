<div class="subscription-management" 
     data-current-plan-id="<?= $currentSubscription['plan_id'] ?? 4 ?>"
     data-current-cycle="<?= $currentSubscription['billing_cycle'] ?? 'monthly' ?>">
    <div class="page-header">
        <h1>Subscription Management</h1>
        <p>Manage your plan, billing, and payment methods.</p>
    </div>

    <div class="active-sub-card">
        <div class="sub-info">
            <div class="plan-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            <div class="plan-details">
                <div class="plan-header">
                    <h2><?= htmlspecialchars($currentSubscription['name'] ?? 'Free Plan') ?></h2>
                    <span class="status-badge active"><?= ucfirst(htmlspecialchars($currentSubscription['status'] ?? 'Active')) ?></span>
                </div>
                <p class="billing-info">
                    <strong>Billing Cycle:</strong> <?= ucfirst(htmlspecialchars($currentSubscription['billing_cycle'] ?? 'Monthly')) ?> <br>
                    <?php if (isset($currentSubscription['valid_till'])): ?>
                        <strong>Valid Until:</strong> <span class="date"><?= date('M d, Y', strtotime($currentSubscription['valid_till'])) ?></span>
                    <?php else: ?>
                        <strong>Valid Until:</strong> <span class="date">Forever</span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <div class="sub-actions">
            <div class="price-display">
                <span class="amount">₹<?= isset($currentSubscription['monthly_price']) ? number_format((float)($currentSubscription['billing_cycle'] === 'yearly' ? $currentSubscription['yearly_price'] : $currentSubscription['monthly_price']), 2) : '0.00' ?></span>
                <span class="period">/<?= $currentSubscription['billing_cycle'] ?? 'mo' ?></span>
            </div>
            <div class="action-buttons">
                <button class="btn btn-outline-primary" id="changePlanBtn">Change Plan</button>
            </div>
        </div>
    </div>

    <div class="plans-section" id="plans-grid">
        <h3>Upgrade or Change Plan</h3>

        <div class="billing-toggle" style="margin-bottom: 30px;">
            <span>Monthly</span>
            <label class="switch">
                <input type="checkbox" id="billingToggle">
                <span class="slider round"></span>
            </label>
            <span>Yearly <strong class="save" style="color: #22c55e; font-size: 0.8em; margin-left: 5px;">SAVE 20%</strong></span>
        </div>

        <!-- Reuse public pricing component -->
        <?php 
            // Normalize variables for the partial
            $userPlanId = $currentSubscription['plan_id'] ?? 4; 
            require __DIR__ . '/../../partials/pricing-cards.php'; 
        ?>
    </div>

    <div class="bottom-grid">
        <!-- Payment Method -->
        <div class="info-card payment-method">
            <h3>Payment Method</h3>
            <div class="card-details-box">
                <div class="card-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                        <line x1="1" y1="10" x2="23" y2="10"/>
                    </svg>
                </div>
                <div class="card-info">
                    <?php if (!empty($billingHistory)): ?>
                        <p class="card-number">Last gateway: <?= htmlspecialchars($billingHistory[0]['payment_gateway']) ?></p>
                        <p class="card-expiry">Txn: <?= htmlspecialchars(substr($billingHistory[0]['transaction_id'], 0, 15)) ?>...</p>
                    <?php else: ?>
                        <p class="card-number">No payment method on file</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Billing History -->
        <div class="info-card billing-history">
            <h3>Billing History</h3>
            <div class="table-responsive">
                <table class="billing-table">
                    <thead>
                        <tr>
                            <th>DATE</th>
                            <th>PLAN</th>
                            <th>AMOUNT</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($billingHistory)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">No billing records found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($billingHistory as $payment): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($payment['created_at'])) ?></td>
                                    <td><?= htmlspecialchars($payment['plan_name']) ?></td>
                                    <td>₹<?= number_format((float)$payment['amount'], 2) ?></td>
                                    <td><span class="status-pill <?= strtolower($payment['status']) ?>"><?= strtoupper(htmlspecialchars($payment['status'])) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="/assets/css/dashboard/subscriptions.css">
<link rel="stylesheet" href="/assets/css/pricing.css">
