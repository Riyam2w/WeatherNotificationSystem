<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'WeatherNotify') ?></title>

    <link rel="stylesheet" href="/assets/css/home.css">
        <link rel="stylesheet" href="/assets/css/navbar.css">
        <link rel="stylesheet" href="/assets/css/footer.css">
        <link rel="stylesheet" href="/assets/css/auth.css">
        <link rel="stylesheet" href="/assets/css/pricing.css">
        <link rel="stylesheet" href="">



</head>
<body>

<?php require __DIR__ . '/../partials/navbar.php'; ?>


<main>
    <?= $content ?> 
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

</body>
</html>
