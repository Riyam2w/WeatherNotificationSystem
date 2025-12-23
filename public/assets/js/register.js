document.addEventListener("DOMContentLoaded", function () {

    function togglePassword(fieldId, toggleId) {
        const field = document.getElementById(fieldId);
        const toggle = document.getElementById(toggleId);

        if (!field || !toggle) return;

        toggle.addEventListener("click", function () {
            const isPassword = field.type === "password";
            field.type = isPassword ? "text" : "password";

            toggle.classList.toggle("fa-eye");
            toggle.classList.toggle("fa-eye-slash");
        });
    }

    // Password field
    togglePassword("password-field", "togglePassword");

    // Confirm password field
    togglePassword("confirm-password-field", "toggleConfirmPassword");

});
