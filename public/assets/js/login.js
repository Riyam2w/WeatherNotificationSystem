document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("togglePassword");
    const input = document.getElementById("password-field");

    if (!toggle || !input) return;

    toggle.addEventListener("click", function () {
        const isPassword = input.type === "password";
        input.type = isPassword ? "text" : "password";

        toggle.classList.toggle("fa-eye");
        toggle.classList.toggle("fa-eye-slash");
    });
});
