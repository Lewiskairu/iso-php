<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

/**
 * Service for sending transactional emails.
 */
final class MailService
{
    private string $fromEmail;
    private string $siteName;

    public function __construct()
    {
        $this->fromEmail = (string) $this->getSetting('smtp_user', 'no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        $this->siteName = (string) $this->getSetting('company_name', 'ISO Compliance Hub');
    }

    /**
     * Send a basic email.
     */
    public function send(string $to, string $subject, string $message): bool
    {
        $headers = [
            'From' => "{$this->siteName} <{$this->fromEmail}>",
            'Reply-To' => $this->fromEmail,
            'Return-Path' => $this->fromEmail,
            'MIME-Version' => '1.0',
            'Content-Type' => 'text/html; charset=UTF-8',
            'X-Mailer' => 'PHP/' . phpversion(),
        ];

        // Format message as HTML if it isn't already
        if (strpos($message, '<html>') === false) {
            $message = "
            <html>
            <head>
                <meta charset='UTF-8'>
                <title>{$subject}</title>
            </head>
            <body style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #1e293b; background-color: #f8fafc; margin: 0; padding: 40px;'>
                <div style='max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; padding: 40px; border-radius: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);'>
                    <div style='text-align: center; margin-bottom: 30px;'>
                        <h1 style='color: #14b8a6; margin: 0; font-size: 24px; font-weight: 800;'>{$this->siteName}</h1>
                    </div>
                    <div style='border-top: 1px solid #e2e8f0; padding-top: 30px;'>
                        {$message}
                    </div>
                    <div style='margin-top: 40px; padding-top: 20px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; text-align: center;'>
                        <p>© " . date('Y') . " {$this->siteName}. All rights reserved.</p>
                        <p>This is an automated system email. Please do not reply directly to this message.</p>
                    </div>
                </div>
            </body>
            </html>";
        }

        // Use -f parameter for cPanel/Sendmail to set the Envelope-From address
        $additionalParams = "-f " . $this->fromEmail;

        return \mail($to, $subject, $message, $this->flattenHeaders($headers), $additionalParams);
    }

    /**
     * Send Welcome/Verification Email
     */
    public function sendWelcome(string $to, string $name, string $verificationLink): bool
    {
        $subject = "Welcome to {$this->siteName} - Verify your account";
        $message = "
        <p>Hello <strong>{$name}</strong>,</p>
        <p>Thank you for signing up for {$this->siteName}. We're excited to have you on board!</p>
        <p>Please click the button below to verify your email address and activate your account:</p>
        <p style='margin: 30px 0;'>
            <a href='{$verificationLink}' style='background: #14b8a6; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Verify My Account</a>
        </p>
        <p>If the button doesn't work, copy and paste this link into your browser:</p>
        <p style='font-size: 13px; color: #666;'>{$verificationLink}</p>";

        return $this->send($to, $subject, $message);
    }

    /**
     * Send Password Reset Email
     */
    public function sendPasswordReset(string $to, string $resetLink): bool
    {
        $subject = "Reset your password - {$this->siteName}";
        $message = "
        <p>Hello,</p>
        <p>We received a request to reset your password for your account on {$this->siteName}.</p>
        <p>Click the button below to set a new password:</p>
        <p style='margin: 30px 0;'>
            <a href='{$resetLink}' style='background: #f97316; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Reset Password</a>
        </p>
        <p>If you did not request this, you can safely ignore this email. This link will expire in 1 hour.</p>";

        return $this->send($to, $subject, $message);
    }

    /**
     * Send Nomination Notification Email
     */
    public function sendNominationAlert(string $to, string $nominator, string $nominee, string $type, string $recipientType = 'nominator'): bool
    {
        $subject = "Organization Nomination: {$nominee}";
        
        if ($recipientType === 'organization') {
            $message = "
            <p>New nomination alert!</p>
            <p><strong>{$nominator}</strong> has nominated <strong>{$nominee}</strong> for the <strong>{$type}</strong> category.</p>
            <p>Please review this nomination in the admin panel.</p>";
        } elseif ($recipientType === 'nominee') {
            $message = "
            <p>Congratulations!</p>
            <p>You/Your organization <strong>{$nominee}</strong> has been nominated for <strong>{$type}</strong> by <strong>{$nominator}</strong>.</p>
            <p>We will review the nomination and get back to you soon.</p>";
        } else {
            $message = "
            <p>Success!</p>
            <p>Your nomination of <strong>{$nominee}</strong> for <strong>{$type}</strong> has been successfully submitted.</p>
            <p>We have notified the nominee and our team will review the submission.</p>";
        }

        return $this->send($to, $subject, $message);
    }

    /**
     * Send Order Confirmation
     */
    public function sendOrderConfirmation(string $to, string $orderId, string $total, string $currency): bool
    {
        $subject = "Order Confirmed #{$orderId} - {$this->siteName}";
        $message = "
        <p>Thank you for your order!</p>
        <p>We've received your payment and are processing order <strong>#{$orderId}</strong>.</p>
        <p><strong>Total Paid:</strong> {$currency} {$total}</p>
        <p>You can track your order status by visiting your dashboard.</p>
        <div style='margin-top: 20px;'>
            <a href='" . url('/orders/track?id=' . $orderId) . "'>Track Order Status</a>
        </div>";

        return $this->send($to, $subject, $message);
    }

    private function getSetting(string $key, $fallback = null): ?string
    {
        try {
            $row = Database::query('SELECT value FROM site_settings WHERE `key` = :key LIMIT 1', ['key' => $key])->fetch();
            return $row ? (string) $row['value'] : (string) $fallback;
        } catch (\Throwable $e) {
            return (string) $fallback;
        }
    }

    private function flattenHeaders(array $headers): string
    {
        $flat = '';
        foreach ($headers as $key => $val) {
            $flat .= "{$key}: {$val}\r\n";
        }
        return $flat;
    }
}
