<!DOCTYPE html>
<html lang="en">
<head>
    <title>WeatherNotify | Register</title>

    <!-- Shared Auth CSS -->
    <link rel="stylesheet" href="/assets/css/auth.css">

    <!-- Font Awesome for eye icons -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body class="auth register-page">

<main class="container">

    <!-- LEFT HERO (same as login) -->
    <section class="hero">
        <div class="logo">🌩 WeatherNotify</div>

        <h1>Monitor the skies<br>with precision.</h1>
        <p>
            Real-time alerts and detailed forecasts to keep you one
            step ahead of the storm.
        </p>
    </section>

    <!-- RIGHT CARD -->
    <section class="card">
        <h2>Create your account</h2>
        <p>Start receiving real-time weather alerts today.</p>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" id="registerForm" action="/register">

            <label>Full Name</label>
            <input
                type="text"
                name="full_name"
                placeholder="Enter your full name"
                required
            >

            <label>Email Address</label>
            <input
                type="email"
                name="email"
                placeholder="user@example.com"
                required
            >

            <label>Password</label>
            <div class="password">
                <input
                    type="password"
                    name="password"
                    id="password-field"
                    required
                >
                <span id="togglePassword" class="fa fa-eye-slash"></span>
            </div>

            <label>Confirm Password</label>
            <div class="password">
                <input
                    type="password"
                    name="confirm_password"
                    id="confirm-password-field"
                    required
                >
                <span id="toggleConfirmPassword" class="fa fa-eye-slash"></span>
            </div>

            <button type="submit">Create Account</button>

            <p class="switch">
                Already have an account?
                <a href="/login">Log in</a>
            </p>
        </form>
    </section>

</main>

<script src="/assets/js/register.js"></script>
</body>
</html>
