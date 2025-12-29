<?php declare(strict_types=1); ?>

<!-- ===============================
 PRICING HERO + CARDS
================================ -->
<section class="pricing-section">
    <div class="pricing-header">
        <h1>Simple, transparent pricing</h1>
        <p>
            Choose the plan that fits your needs. Always know when severe
            weather is approaching with our reliable alert system.
        </p>

        <div class="billing-toggle">
            <span>Monthly</span>
            <label class="switch">
                <input type="checkbox" id="billingToggle">
                <span class="slider"></span>
            </label>
            <span>Yearly <strong class="save">SAVE 20%</strong></span>
        </div>
    </div>
    <!-- SAME CARDS, REUSED -->
    <?php require __DIR__ . '/../partials/pricing-cards.php'; ?>
</section>


<!-- ===============================
 FULL PAGE ONLY CONTENT
================================ -->

<?php require __DIR__ . '/../partials/compare-table.php'; ?>
<!-- <?php require __DIR__ . '/../partials/faq.php'; ?> -->