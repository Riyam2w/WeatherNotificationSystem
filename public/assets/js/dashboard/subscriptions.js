/**
 * Subscriptions Page JavaScript
 * Handles subscription management functionality
 */
document.addEventListener('DOMContentLoaded', function () {
    // Change Plan button - smooth scroll to plans grid
    const changePlanBtn = document.getElementById('changePlanBtn');
    if (changePlanBtn) {
        changePlanBtn.addEventListener('click', function () {
            const plansGrid = document.getElementById('plans-grid');
            if (plansGrid) {
                plansGrid.scrollIntoView({ behavior: 'smooth' });
            }
        });
    }
});
