<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 20px auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px; }
        .header { background: #007bff; color: #fff; padding: 10px; border-radius: 8px 8px 0 0; text-align: center; }
        .content { padding: 20px; }
        .footer { font-size: 0.8em; color: #777; text-align: center; margin-top: 20px; }
        .alert-box { background: #fff3cd; border: 1px solid #ffeeba; padding: 15px; border-radius: 5px; margin: 15px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Weather Alert</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>This is an automated notification from your <strong>Weather Notification System</strong>.</p>
            
            <div class="alert-box">
                <p><strong>Condition Met in <?= htmlspecialchars($data['city_name']) ?>!</strong></p>
                <p>Condition: <?= htmlspecialchars($data['condition_label']) ?></p>
                <p>Threshold: <?= htmlspecialchars($data['operator']) ?> <?= htmlspecialchars((string)$data['threshold_value']) ?><?= htmlspecialchars($data['unit']) ?></p>
                <p><strong>Current Value: <?= htmlspecialchars((string)$data['current_value']) ?><?= htmlspecialchars($data['unit']) ?></strong></p>
            </div>
            
            <p>Stay safe!</p>
        </div>
        <div class="footer">
            <p>&copy; <?= date('Y') ?> Weather Notification System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
