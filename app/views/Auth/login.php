<!DOCTYPE html>
<html lang="en">
<head>
    <title>WeatherNotify | Login</title>

    <link rel="stylesheet" href="/assets/css/auth.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
</head>
<body>
    <main class="page-wrapper">
<div class="auth-wrapper">
<section class="card">
        <h2>Welcome back</h2>
        <p>Please enter your details to sign in.</p>

        <form id="loginForm" novalidate>

            <label>Email Address</label>
            <input type="email" name="email" placeholder="user@example.com" required>

            <label>Password</label>
            <div class="password">
                <input type="password" name="password" id="password-field" required>
                <span id="togglePassword" class="fa fa-eye-slash"></span>
            </div>

            <div class="forgot-password">
                <a href="/forget_password" class="forgot-link">Forgot Password?</a>
            </div>

            <button type="submit">Sign in</button>

            <p class="switch">
                Don’t have an account?
                <a href="/register">Sign up for free</a>
            </p>
        </form>
    </section>
</div>
</main>
<script src="/assets/js/auth/login.js"></script>
</body>
</html>
