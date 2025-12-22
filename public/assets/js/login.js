$(document).ready(function () {

    $("#togglePassword").click(function () {
        const input = $("#password-field");

        if (input.attr("type") === "password") {
            input.attr("type", "text");
            $(this).removeClass("fa-eye-slash").addClass("fa-eye");
        } else {
            input.attr("type", "password");
            $(this).removeClass("fa-eye").addClass("fa-eye-slash");
        }
    });

});
