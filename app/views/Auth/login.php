<!DOCTYPE html>
<html lang="en">
<head>
    <title>WeatherNotify | Login</title>

    <link rel="stylesheet" href="/assets/css/auth.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>

<main class="container">
    <!-- LEFT HERO -->
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
        <h2>Welcome back</h2>
        <p>Please enter your details to sign in.</p>

        <?php if (!empty($error)): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/login">

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
</main>

<script src="/assets/js/login.js"></script>
</body>
</html>
