<!DOCTYPE html>
<html lang="en">
<head>
    <title>WeatherNotify | Forgot Password</title>

    <link rel="stylesheet" href="/assets/css/auth.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <meta name="csrf-token" content="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
</head>
<body>
    <main class="page-wrapper">
<div class="auth-wrapper">
<section class="card">
        <h2>Forgot password?</h2>
        <p>No worries, we’ll send you reset instructions.</p>


        <form id="forgotpasswordForm" novalidate>
            <label>Email Address</label>
            <div class="password">
                <input type="email" name="email" placeholder="user@example.com" required>
                <span class="fa fa-envelope"></span>
            </div>

            <button type="submit">Send reset link</button>

            <div class="forgot-password" style="margin-top:16px;">
                <a href="/login" class="forgot-link">← Back to log in</a>
            </div>
        </form>

        <div id="formMessage"></div>
    </section>
</div>
</main>
<script src="/assets/js/auth/forgot_password.js" defer></script>
</body>
</html>
