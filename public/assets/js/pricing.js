/**
 * Pricing Toggle Logic
 * Handles switching between monthly and yearly pricing views.
 */

window.initPricingToggle = function () {
    const toggle = document.getElementById('billingToggle');
    const pricingGrid = document.querySelector('.pricing-grid');

    // In dashboard, it might be in a specific container
    const priceElements = document.querySelectorAll('.pricing-grid .price, .plans-section .price');

    console.log('Price toggle initialization. Toggle found:', !!toggle, 'Prices found:', priceElements.length);

    if (toggle && priceElements.length > 0) {
        // Remove existing listener if any to prevent multiple attachments
        if (window._pricingToggleHandler) {
            toggle.removeEventListener('change', window._pricingToggleHandler);
        }

        window._pricingToggleHandler = function (e) {
            const isYearly = e.target.checked;
            const currentCycle = isYearly ? 'yearly' : 'monthly';
            console.log('Billing toggle changed. Yearly:', isYearly);

            // Update prices
            priceElements.forEach(el => {
                const monthly = el.getAttribute('data-monthly');
                const yearly = el.getAttribute('data-yearly');
                const valSpan = el.querySelector('.val');
                const periodSpan = el.querySelector('.period');

                if (isYearly) {
                    if (valSpan) valSpan.textContent = yearly;
                    if (periodSpan) periodSpan.textContent = '/year';
                } else {
                    if (valSpan) valSpan.textContent = monthly;
                    if (periodSpan) periodSpan.textContent = '/month';
                }
            });

            // Update Dashboard Button States
            const subContainer = document.querySelector('.subscription-management');
            if (subContainer) {
                const currentPlanId = subContainer.getAttribute('data-current-plan-id');
                const registeredCycle = subContainer.getAttribute('data-current-cycle');

                const cards = document.querySelectorAll('.pricing-card');
                cards.forEach(card => {
                    const cardPlanId = card.getAttribute('data-plan-id');
                    const btn = card.querySelector('.plan-btn');
                    if (!btn) return;

                    // If it matches the user's base plan
                    if (cardPlanId === currentPlanId) {
                        // If it ALSO matches the toggle cycle, it's the CURRENT plan
                        if (currentCycle === registeredCycle) {
                            btn.textContent = 'Current Plan';
                            btn.disabled = true;
                            btn.classList.add('btn-outline');
                            btn.classList.remove('btn-primary');
                            btn.style.pointerEvents = 'none';
                            btn.style.opacity = '0.7';
                        } else {
                            // Same plan tier, different cycle -> Upgrade/Switch option
                            btn.textContent = `Switch to ${isYearly ? 'Yearly' : 'Monthly'}`;
                            btn.disabled = false;
                            btn.classList.add('btn-primary');
                            btn.classList.remove('btn-outline');
                            btn.style.pointerEvents = 'auto';
                            btn.style.opacity = '1';
                        }
                    } else {
                        // Different plan tier
                        btn.textContent = 'Switch to this';
                        btn.disabled = false;
                        btn.classList.add('btn-primary');
                        btn.classList.remove('btn-outline');
                        btn.style.pointerEvents = 'auto';
                        btn.style.opacity = '1';
                    }

                    // Update href to include cycle
                    if (btn.tagName === 'A') {
                        const baseUrl = btn.href.split('?')[0];
                        btn.href = `${baseUrl}?cycle=${currentCycle}`;
                    }
                });
            }
        };

        toggle.addEventListener('change', window._pricingToggleHandler);

        // Initial sync in case the toggle is already checked (e.g. browser back button)
        if (toggle.checked) {
            toggle.dispatchEvent(new Event('change'));
        }
    }
};

// Auto-init on standard page load
document.addEventListener('DOMContentLoaded', () => {
    window.initPricingToggle();
});
