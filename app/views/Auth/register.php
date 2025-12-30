<!DOCTYPE html>
<html lang="en">
<head>
    <title>WeatherNotify | Register</title>

    <!-- Font Awesome for eye icons -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

</head>

<body class="auth register-page">
    <main class="page-wrapper">
<div class="auth-wrapper">
<section class="card">
        <h2>Create your account</h2>
        <p>Start receiving real-time weather alerts today.</p>

        <!-- <div id="formMessage"></div> -->

        <form id="registerForm" novalidate>

            <label>Full Name</label>
            <input
                type="text"
                name="full_name"
                placeholder="Enter your full name"
                id="full_name"
                required
            >
            <small class="field-error" id="error_full_name"></small>
            <label>Email Address</label>
            <input
                type="email"
                name="email"
                placeholder="user@example.com"
                id="email"
                required
            >
            <small class="field-error" id="error_email"></small>
            <label>Password</label>
            <div class="password">
                <input
                    type="password"
                    name="password"
                    id="password"
                    required
                >
                <span id="togglePassword" class="fa fa-eye-slash"></span>
            </div>
            <small class="field-error" id="error_password"></small>
            <label>Confirm Password</label>
            <div class="password">
                <input
                    type="password"
                    name="confirm_password"
                    id="confirm_password"
                    required
                >
                <span id="toggleConfirmPassword" class="fa fa-eye-slash"></span>
            </div>
            <small class="field-error" id="error_confirm_password"></small>
            <button type="submit">Create Account</button>

            <p class="auth-switch">
                Already have an account?
                <a href="/login">Log in</a>
            </p>
        </form>
    </section>

</div>
</main>
<script src="/assets/js/auth/register.js" defer></script>
</body>
</html>
