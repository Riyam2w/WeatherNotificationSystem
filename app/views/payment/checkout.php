<?php
/** @var array $plan */
/** @var string $stripe_publishable_key */
/** @var string $cycle */
$isYearly = ($cycle === 'yearly');
$currentPrice = $isYearly ? $plan['yearly_price'] : $plan['monthly_price'];
$currentCycleText = $isYearly ? '/year' : '/month';
?>
<link rel="stylesheet" href="/assets/css/payment/checkout.css">
<script src="https://js.stripe.com/v3/"></script>

<main class="checkout-container" id="checkout-container" 
      data-stripe-key="<?php echo $stripe_publishable_key; ?>"
      data-monthly-price="<?php echo (float)$plan['monthly_price']; ?>"
      data-yearly-price="<?php echo (float)$plan['yearly_price']; ?>">
    
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="checkout-card">
                    <!-- Left Side: Plan Info -->
                    <div class="checkout-info">
                        <a href="/pricing" class="text-decoration-none text-muted mb-4 d-inline-block">
                            <i class="bi bi-arrow-left"></i> Back to Plans
                        </a>
                        <h1 class="checkout-title">Order Summary</h1>
                        
                        <div class="plan-summary">
                            <div class="plan-name"><?php echo htmlspecialchars($plan['name']); ?> Plan</div>
                            <div class="plan-price">
                                <span id="display-price">₹<?php echo number_format((float)$currentPrice, 2); ?></span>
                                <span id="display-cycle"><?php echo $currentCycleText; ?></span>
                            </div>
                        </div>

                        <h5 class="mb-3">What's included:</h5>
                        <ul class="feature-check-list">
                            <li><i class="bi bi-check-circle-fill"></i> Real-time weather alerts</li>
                            <li><i class="bi bi-check-circle-fill"></i> Custom alert thresholds</li>
                            <li><i class="bi bi-check-circle-fill"></i> Email notifications</li>
                            <?php if ($plan['slug'] !== 'free'): ?>
                            <li><i class="bi bi-check-circle-fill"></i> Multi-city tracking</li>
                            <li><i class="bi bi-check-circle-fill"></i> Priority support</li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Right Side: Payment Process -->
                    <div class="checkout-form-section">
                        <h2 class="checkout-title">Payment Details</h2>
                        
                        <form id="checkout-form">
                            <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                            
                            <div class="billing-cycle-selector">
                                <label class="form-label fw-bold">Select Billing Cycle</label>
                                <div class="cycle-options">
                                    <label class="cycle-option">
                                        <input type="radio" name="billing_cycle" value="monthly" <?php echo !$isYearly ? 'checked' : ''; ?>>
                                        <div class="cycle-box">
                                            <span class="cycle-name">Monthly</span>
                                            <span class="text-muted">₹<?php echo number_format((float)$plan['monthly_price'], 2); ?></span>
                                        </div>
                                    </label>
                                    <label class="cycle-option">
                                        <input type="radio" name="billing_cycle" value="yearly" <?php echo $isYearly ? 'checked' : ''; ?>>
                                        <div class="cycle-box">
                                            <span class="cycle-name">Yearly</span>
                                            <span class="text-muted">₹<?php echo number_format((float)$plan['yearly_price'], 2); ?></span>
                                            <div class="cycle-save">Save ~15%</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div id="payment-status" class="d-none">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Redirecting to secure payment gateway...</p>
                            </div>

                            <button type="submit" id="pay-button" class="btn-pay">
                                <i class="bi bi-shield-lock-fill"></i> Proceed to Payment
                            </button>

                            <div class="secure-notice">
                                <i class="bi bi-lock"></i>
                                Secured by Stripe. No card details stored on our servers.
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="/assets/js/payment/checkout.js" defer></script>
