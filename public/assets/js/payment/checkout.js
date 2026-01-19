document.addEventListener('DOMContentLoaded', function () {
    const checkoutContainer = document.getElementById('checkout-container');
    if (!checkoutContainer) return;

    const stripeKey = checkoutContainer.getAttribute('data-stripe-key');
    const monthlyPrice = parseFloat(checkoutContainer.getAttribute('data-monthly-price'));
    const yearlyPrice = parseFloat(checkoutContainer.getAttribute('data-yearly-price'));

    const stripe = Stripe(stripeKey);
    const form = document.getElementById('checkout-form');
    const payBtn = document.getElementById('pay-button');
    const status = document.getElementById('payment-status');
    const displayPrice = document.getElementById('display-price');
    const displayCycle = document.getElementById('display-cycle');

    document.querySelectorAll('input[name="billing_cycle"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            if (e.target.value === 'yearly') {
                displayPrice.textContent = '₹' + yearlyPrice.toFixed(2);
                displayCycle.textContent = '/year';
            } else {
                displayPrice.textContent = '₹' + monthlyPrice.toFixed(2);
                displayCycle.textContent = '/month';
            }
        });
    });

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        payBtn.classList.add('d-none');
        status.classList.remove('d-none');

        const formData = new FormData(form);

        try {
            const response = await fetch('/payment/process', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();

            if (result.success && result.id) {
                // Redirect to Stripe Checkout
                const { error } = await stripe.redirectToCheckout({
                    sessionId: result.id
                });

                if (error) {
                    throw new Error(error.message);
                }
            } else {
                throw new Error(result.error || 'Failed to initiate checkout');
            }
        } catch (error) {
            status.innerHTML = `
                <div class="text-danger">
                    <i class="bi bi-ex-octagon-fill fs-1"></i>
                    <h4 class="mt-3">Checkout Error</h4>
                    <p>${error.message}</p>
                    <button onclick="location.reload()" class="btn btn-outline-danger btn-sm mt-2">Try Again</button>
                </div>
            `;
        }
    });
});
