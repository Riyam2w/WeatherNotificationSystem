<!DOCTYPE html>
<html lang="en">
<head>
    <title>WeatherNotify | Forgot Password</title>

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
        <h2>Forgot password?</h2>
        <p>No worries, we’ll send you reset instructions.</p>

        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post" action="/forgot-password">
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
    </section>
</main>

</body>
</html>
