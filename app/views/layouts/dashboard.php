<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?= $_SESSION['csrf_token'] ?>">

    <title>🌩 WeatherNotify | Dashboard</title>

    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="/assets/css/alert.css">
    <link rel="stylesheet" href="/assets/css/toast.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script defer src="/assets/js/toast.js"></script>
    <script defer src="/assets/js/dashboard.js"></script>
    <script defer src="/assets/js/pricing.js"></script>
    <script defer src="/assets/js/dashboard/create_alert.js"></script>
    <script defer src="/assets/js/dashboard/create_alert_confirm.js"></script>
    <script defer src="/assets/js/dashboard/sidebar.js"></script>
    <script defer src="/assets/js/dashboard/alerts.js"></script>
    <script defer src="/assets/js/dashboard/settings.js"></script>
    <script defer src="/assets/js/dashboard/subscriptions.js"></script>



</head>
<body>

<?= $content ?>

</body>
</html>
