<?php declare(strict_types=1); ?>

<div class="pricing-grid">
    <?php foreach ($plans as $plan): ?>
        <div class="pricing-card <?= $plan['is_popular'] ? 'popular' : '' ?>">

            <?php if ($plan['is_popular']): ?>
                <span class="badge">MOST POPULAR</span>
            <?php endif; ?>

            <h3><?= htmlspecialchars($plan['name']) ?></h3>
            <p class="plan-desc"><?= htmlspecialchars($plan['description']) ?></p>

            <div class="price"
                 data-monthly="<?= number_format((float)$plan['monthly_price'], 0) ?>"
                 data-yearly="<?= number_format((float)$plan['yearly_price'], 0) ?>">
                ₹<?= number_format((float)$plan['monthly_price'], 0) ?>
                <span>/month</span>
            </div>

            <?php if ($plan['slug'] === 'free'): ?>
                <a href="/register" class="btn btn-outline">Get Started</a>

            <?php elseif ($plan['slug'] === 'pro'): ?>
                <a href="/subscribe/pro" class="btn btn-primary">
                    Start <?= (int)$plan['trial_days'] ?>-Day Free Trial
                </a>

            <?php else: ?>
                <a href="/contact" class="btn btn-outline">Contact Sales</a>
            <?php endif; ?>

            <ul class="feature-list">
                <?php foreach ($plan['features'] as $feature): ?>
                    <li class="<?= $feature['is_available'] ? 'yes' : 'no' ?>">
                        <?= htmlspecialchars($feature['feature']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endforeach; ?>
</div>
