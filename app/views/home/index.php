<?php
declare(strict_types=1);
?>

<!-- =========================
     HERO SECTION
========================= -->
<section class="hero">

    <div class="hero-content">

        <div class="hero-text">
            <div class="brand-tile">
                <span>🌩</span> WeatherNotify
            </div>
            <h1>
                Get Weather Alerts <br>
                <span class="highlight">Before It Happens</span>
            </h1>

            <p class="hero-subtitle">
                Never get caught in the rain again. Our automated system monitors
                weather conditions 24/7 and sends instant alerts when your
                thresholds are met.
            </p>

            <div class="hero-actions">
                <a href="/register" class="btn-primary">
                    Get Started Free
                </a>
                <a href="/pricing" class="btn-outline">
                    View Pricing
                </a>
            </div>
        </div>

        <div class="hero-visual">
            <div class="alert-mockup">
                <div class="alert-card">
                    <strong>Weather Alert</strong>
                    <p>Heavy Rain expected in New Delhi</p>
                    <span class="alert-badge">Now</span>
                </div>
            </div>
        </div>

    </div>
</section>



<!-- =========================
     PRICING PREVIEW
========================= -->
<section class="home-pricing">
    <div class="section-header">
        <h2>Pricing Plans</h2>
        <p>Choose the plan that works for you.</p>

        <div class="billing-toggle">
            <span>Monthly</span>
            <label class="switch">
                <input type="checkbox" id="billingToggle">
                <span class="slider round"></span>
            </label>
            <span>Yearly <strong class="save" style="color: #22c55e; font-size: 0.8em; margin-left: 5px;">SAVE 20%</strong></span>
        </div>
    </div>

    <?php require __DIR__ . '/../partials/pricing-cards.php'; ?>

    <div class="view-all">
        <a href="/pricing" class="btn btn-outline">
            View Full Pricing
        </a>
    </div>
</section>

<!-- =========================
     FINAL CTA
========================= -->
<section class="cta">

    <h2>
        Start receiving automated weather alerts today
    </h2>

    <p>
        Join thousands of users who stay ahead of the weather.
        No credit card required.
    </p>

    <a href="/register" class="btn-primary">
        Create Account
    </a>
</section>
