<?php
declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class MailService
{
    private mysqli $conn;
    private array $config;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
        $this->config = require __DIR__ . '/../config/app_config.php';
    }

    /**
     * Send a weather alert email
     */
    public function sendWeatherAlert(string $to, array $alertData, int $userId): bool
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();                                            
            $mail->Host       = $this->config['mail']['host']; 
            $mail->SMTPAuth   = $this->config['mail']['auth'] ?? true;                                   
            $mail->Port       = $this->config['mail']['port']; 
            $mail->Username   = $this->config['mail']['username'] ?? '';
            $mail->Password   = $this->config['mail']['password'] ?? '';
            $mail->SMTPSecure = $this->config['mail']['encryption'] ?? PHPMailer::ENCRYPTION_STARTTLS;

            $mail->Timeout     = 10; // 10 seconds timeout

            // Check if host is default or empty
            if (empty($mail->Host) || $mail->Host === 'smtp.example.com' || str_contains($mail->Host, 'your-smtp')) {
                error_log("SMTP Host not configured in app_config.php");
                return false;
            }

            // Recipients
            $mail->setFrom($this->config['mail']['from_email'] ?? 'noreply@weather.com', $this->config['mail']['from_name'] ?? 'WeatherNotify');
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Weather Alert: {$alertData['city_name']}";

            // Load template
            ob_start();
            $data = $alertData;
            include __DIR__ . '/../views/emails/weather_alert.php';
            $body = ob_get_clean();

            $mail->Body    = $body;
            $mail->AltBody = "Weather Alert for {$alertData['city_name']}: {$alertData['condition_label']} threshold reached. Current value: {$alertData['current_value']}{$alertData['unit']}.";

            $mail->Username   = $this->config['mail']['username'] ?? '';
            $mail->Password   = $this->config['mail']['password'] ?? '';
            $sent = @$mail->send();

            $status = $sent ? 'sent' : 'failed';
            $this->logNotification($to, $alertData, $status, '', $userId);
            return $sent;

        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            $this->logNotification($to, $alertData, 'failed', $mail->ErrorInfo, $userId);
        }

        return false;
    }


    /**
     * Log notification to database
     */
    private function logNotification(string $to, array $alertData, string $status, string $error = '', ?int $userId = null): void
    {
        $subId = (int)($alertData['notif_sub_id'] ?? 0);
        $subParam = ($subId > 0) ? $subId : null;

        // Schema: subscription_id, condition_type, actual_value, message, status, sent_at
        $conditionId = (int)($alertData['condition_id'] ?? 1);
        $conditionType = (in_array($conditionId, [1, 2])) ? 'temperature' : 'weather';
        $actualValue = (string)($alertData['current_value'] ?? 'N/A') . ($alertData['unit'] ?? '');
        $message = "Alert triggered for {$alertData['city_name']}: " . ($alertData['condition_label'] ?? 'Condition') . " at {$actualValue}. " . $error;

        $sql = "INSERT INTO notifications (subscription_id, user_id, condition_type, actual_value, message, status, sent_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iissss", $subParam, $userId, $conditionType, $actualValue, $message, $status);
        if (!$stmt->execute()) {
            error_log("Failed to log notification: " . $stmt->error);
        }
    }
}
