<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🌩 WeatherNotify | <?= htmlspecialchars($title ?? 'Instant Weather Alerts') ?></title>

    <link rel="stylesheet" href="/assets/css/home.css">
        <link rel="stylesheet" href="/assets/css/navbar.css">
        <link rel="stylesheet" href="/assets/css/footer.css">
        <link rel="stylesheet" href="/assets/css/auth.css">
        <link rel="stylesheet" href="/assets/css/pricing.css">
        <link rel="stylesheet" href="/assets/css/features.css">
        <link rel="stylesheet" href="/assets/css/toast.css">
        <script defer src="/assets/js/toast.js"></script>
        <script defer src="/assets/js/pricing.js"></script>



</head>
<body>

<?php require __DIR__ . '/../partials/navbar.php'; ?>


<main>
    <?= $content ?> 
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

</body>
</html>
