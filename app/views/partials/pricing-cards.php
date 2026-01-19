<?php declare(strict_types=1); ?>

<div class="pricing-grid">
    <?php foreach ($plans as $plan): ?>
        <div class="pricing-card <?= $plan['is_popular'] ? 'popular' : '' ?>" data-plan-id="<?= $plan['id'] ?>">

            <?php if ($plan['is_popular']): ?>
                <span class="badge">MOST POPULAR</span>
            <?php endif; ?>

            <h3><?= htmlspecialchars($plan['name']) ?></h3>
            <p class="plan-desc"><?= htmlspecialchars($plan['description']) ?></p>

            <div class="price"
                 data-monthly="<?= number_format((float)$plan['monthly_price'], 0) ?>"
                 data-yearly="<?= number_format((float)$plan['yearly_price'], 0) ?>">
                ₹<span class="val"><?= number_format((float)$plan['monthly_price'], 0) ?></span>
                <span class="period">/month</span>
            </div>

            <?php 
            $isDashboard = isset($currentSubscription);
            $isMatch = false;
            $btnText = 'Switch to this';
            $isDisabled = false;

            if ($isDashboard) {
                $currentName = $currentSubscription['name'] ?? 'Free';
                $currentCycle = $currentSubscription['billing_cycle'] ?? 'monthly';
                
                // Matches Tier
                if (strcasecmp($currentName, $plan['name']) === 0) {
                    // Matches exact cycle
                    if ($currentCycle === 'monthly') {
                        $isMatch = true;
                        $btnText = 'Current Plan';
                        $isDisabled = true;
                    } else {
                        // User is on Yearly, but viewing Monthly
                        $btnText = 'Switch to Monthly';
                    }
                }
            }
            ?>

            <?php if ($isDashboard): ?>
                <a href="/checkout/<?= $plan['slug'] ?>?cycle=monthly" 
                   class="btn plan-btn <?= $isMatch ? 'btn-outline' : 'btn-primary' ?>" 
                   <?= $isDisabled ? 'style="pointer-events: none; opacity: 0.7;" disabled' : '' ?>
                   data-plan-name="<?= htmlspecialchars($plan['name']) ?>">
                    <?= $btnText ?>
                </a>

            <?php elseif ($plan['slug'] === 'free'): ?>
                <a href="/register" class="btn btn-outline plan-btn" data-plan-name="<?= htmlspecialchars($plan['name']) ?>">Get Started</a>

            <?php else: ?>
                <a href="/checkout/<?= $plan['slug'] ?>" class="btn btn-primary plan-btn" data-plan-name="<?= htmlspecialchars($plan['name']) ?>">
                    Subscribe Now
                </a>
            <?php endif; ?>

            <ul class="feature-list">
                <?php foreach ($plan['features'] as $feature): ?>
                    <?php 
                        $text = htmlspecialchars($feature['feature']);
                        if (strpos($text, '=') !== false) {
                            list($key, $val) = explode('=', $text);
                            $key = str_replace('_', ' ', $key);
                            $text = ucwords($key) . ': <strong>' . $val . '</strong>';
                        }
                    ?>
                    <li class="<?= $feature['is_available'] ? 'yes' : 'no' ?>">
                        <?= $text ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</div>

