<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Alerts</title>
    <link rel="stylesheet" href="/public/assets/css/auth.css">
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2 class="logo">Weather Alerts</h2>
            <h1>Create your account</h1>
               <p class="subtitle">Start receiving real-time weather alerts today.</p>

               <?php if(!empty($errors)): ?>
                    <div class="error-box">
                        <?php foreach ($errors as $e): ?>
                            <p><?= htmlspecialchars($e) ?></p>
                        <?php endforeach; ?>
                    </div>
               <?php endif; ?>
     
               <form action="/register" method="post" novalidate>
                    <div class="field">
                        <label>Email Address</label> 
                        <input type= "email" name="email" placeholder="Enter your email" required>
                    </div>
                
                    <div class="field">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Enter your password" required>
                    </div>

                    <div class="field">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" placeholder="Confirm your password" required>
                    </div>
                    <button>Register</button>
                    <!-- continue with google button -->

                    <p class="footer-text">
                        Already have an aacount? <a href="/login">Log In</a>
                    </p>
                </form>
        </div>  
    </div>
</body>
</html>