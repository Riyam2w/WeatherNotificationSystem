$(document).ready(function () {

    // Password toggle
    $("#togglePassword").click(function () {
        const input = $("#password-field");

        if (input.attr("type") === "password") {
            // Show password
            input.attr("type", "text");
            $(this).removeClass("fa-eye-slash").addClass("fa-eye");
        } else {
            // Hide password
            input.attr("type", "password");
            $(this).removeClass("fa-eye").addClass("fa-eye-slash");
        }
    });

    // Confirm password toggle
    $("#toggleConfirmPassword").click(function () {
        const input = $("#confirm-password-field");

        if (input.attr("type") === "password") {
            input.attr("type", "text");
            $(this).removeClass("fa-eye-slash").addClass("fa-eye");
        } else {
            input.attr("type", "password");
            $(this).removeClass("fa-eye").addClass("fa-eye-slash");
        }
    });

});
