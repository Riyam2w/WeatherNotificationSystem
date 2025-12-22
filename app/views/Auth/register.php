<!DOCTYPE html>
<html lang="en">
<head>
    <title>WeatherNotify</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
     <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>


</head>
<body>

<!-- <header class="navbar">
    <div class="logo">🌩 WeatherNotify</div>
    <nav>
        <a href="/home">Home</a>
        <a href="/features">Features</a>
        <a href="/pricing">Pricing</a>
        <a class="btn" href="/login">Log In</a>
    </nav>
</header> -->

<main class="container">
    <section class="hero">
        <h1>Never get caught<br>in the rain again.</h1>
        <p>Real-time alerts, custom triggers, and severe weather warnings.</p>
        <div class="alert-box">⚠ Severe Thunderstorm Warning</div>
    </section>

    <section class="card">
        <h2>Create Account</h2>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $e): ?>
                    <p><?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" id="registerForm" action="/register">

            <label>Full Name</label>
            <input type="text" name="full_name" placeholder="Enter your                                                                                                                                                            full name" required>

            <label>Email Address</label>
            <input type="email" name="email" placeholder="enter your email address" required>

            <label>Password</label>
            <div class="password">
            <input type="password" name="password" id="password-field"  placeholder="enter password" required>
            <span class="fa fa-eye-slash toggle-password" id="togglePassword"></span>
            </div>
            <label>Confirm Password</label>
           <div class="password">
           <input type="password" name="confirm_password" id="confirm-password-field" placeholder="confirm password" required>
           <span class="fa fa-eye-slash toggle-password" id="toggleConfirmPassword"></span>
           </div>


            <!-- <label class="checkbox">
                <input type="checkbox" required>
                I agree to the Terms & Privacy Policy
            </label> -->

            <button type="submit">Create Account</button>

            <p class="switch">Already a member? <a href="/login">Log In</a></p>
        </form>
    </section>
</main>

<script src="/assets/js/register.js"></script>
</body>
</html>
