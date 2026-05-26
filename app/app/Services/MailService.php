<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

/**
 * Service for sending transactional emails via direct SMTP (socket-based).
 * Uses cPanel's local SMTP server — no external libraries required.
 */
final class MailService
{
    private string $fromEmail;
    private string $fromName;

    // SMTP settings — cPanel local relay (no auth needed for same-server sending)
    private string $smtpHost = '127.0.0.1';
    private int    $smtpPort = 25;
    private int    $smtpTimeout = 10;

    public function __construct()
    {
        $this->fromEmail = (string) $this->getSetting('smtp_user', 'no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        $this->fromName  = (string) $this->getSetting('company_name', 'ISO Compliance Hub');

        // Allow override via DB settings
        $host = (string) $this->getSetting('smtp_host', '');
        $port = (int)   $this->getSetting('smtp_port', '0');
        if ($host !== '') $this->smtpHost = $host;
        if ($port > 0)    $this->smtpPort = $port;
    }

    /**
     * Send an email using a direct SMTP socket connection.
     */
    public function send(string $to, string $subject, string $body): bool
    {
        // Wrap plain content in HTML template
        if (stripos($body, '<html') === false) {
            $body = $this->wrapHtml($subject, $body);
        }

        try {
            return $this->smtpSend($to, $subject, $body);
        } catch (\Throwable $e) {
            // Log silently — never crash the app because of email
            error_log('[MailService] SMTP error: ' . $e->getMessage());
            return false;
        }
    }

    // ─── Email Builders ────────────────────────────────────────────────────────

    public function sendWelcome(string $to, string $name, string $verificationLink): bool
    {
        $subject = "Welcome to {$this->fromName} – Verify your account";
        $body = "
        <p>Hello <strong>{$name}</strong>,</p>
        <p>Thank you for signing up for {$this->fromName}. We're excited to have you on board!</p>
        <p>Please click the button below to verify your email address and activate your account:</p>
        <p style='margin: 30px 0;'>
            <a href='{$verificationLink}' style='background:#14b8a6;color:#fff;padding:12px 24px;text-decoration:none;border-radius:6px;font-weight:bold;'>Verify My Account</a>
        </p>
        <p style='font-size:13px;color:#666;'>Or copy and paste this link: {$verificationLink}</p>";
        return $this->send($to, $subject, $body);
    }

    public function sendPasswordReset(string $to, string $resetLink): bool
    {
        $subject = "Reset your password – {$this->fromName}";
        $body = "
        <p>Hello,</p>
        <p>We received a request to reset your password for your {$this->fromName} account.</p>
        <p style='margin: 30px 0;'>
            <a href='{$resetLink}' style='background:#f97316;color:#fff;padding:12px 24px;text-decoration:none;border-radius:6px;font-weight:bold;'>Reset Password</a>
        </p>
        <p>If you didn't request this, you can safely ignore this email. The link expires in 1 hour.</p>";
        return $this->send($to, $subject, $body);
    }

    public function sendNominationAlert(string $to, string $nominator, string $nominee, string $type, string $recipientType = 'nominator'): bool
    {
        $subject = "Organization Nomination: {$nominee}";
        if ($recipientType === 'organization') {
            $body = "<p>New nomination alert!</p><p><strong>{$nominator}</strong> has nominated <strong>{$nominee}</strong> for the <strong>{$type}</strong> category.</p>";
        } elseif ($recipientType === 'nominee') {
            $body = "<p>Congratulations! <strong>{$nominee}</strong> has been nominated for <strong>{$type}</strong> by <strong>{$nominator}</strong>.</p>";
        } else {
            $body = "<p>Your nomination of <strong>{$nominee}</strong> for <strong>{$type}</strong> has been submitted successfully.</p>";
        }
        return $this->send($to, $subject, $body);
    }

    public function sendOrderConfirmation(string $to, string $orderId, string $total, string $currency): bool
    {
        $subject = "Order Confirmed #{$orderId} – {$this->fromName}";
        $body = "
        <p>Thank you for your order!</p>
        <p>We've received your payment and are processing order <strong>#{$orderId}</strong>.</p>
        <p><strong>Total Paid:</strong> {$currency} {$total}</p>
        <p><a href='" . url('/orders/track?id=' . $orderId) . "' style='color:#14b8a6;'>Track your order</a></p>";
        return $this->send($to, $subject, $body);
    }

    // ─── Core SMTP ─────────────────────────────────────────────────────────────

    private function smtpSend(string $to, string $subject, string $body): bool
    {
        $socket = @fsockopen($this->smtpHost, $this->smtpPort, $errno, $errstr, $this->smtpTimeout);

        if ($socket === false) {
            throw new \RuntimeException("Cannot connect to SMTP {$this->smtpHost}:{$this->smtpPort} — {$errstr} ({$errno})");
        }

        stream_set_timeout($socket, $this->smtpTimeout);

        $this->smtpRead($socket); // 220 greeting

        $hostname = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $this->smtpWrite($socket, "EHLO {$hostname}");
        $this->smtpRead($socket);

        $this->smtpWrite($socket, "MAIL FROM:<{$this->fromEmail}>");
        $this->smtpRead($socket);

        // Support comma-separated multiple recipients
        foreach (array_map('trim', explode(',', $to)) as $recipient) {
            $this->smtpWrite($socket, "RCPT TO:<{$recipient}>");
            $this->smtpRead($socket);
        }

        $this->smtpWrite($socket, 'DATA');
        $this->smtpRead($socket);

        $date    = date('r');
        $msgId   = '<' . bin2hex(random_bytes(8)) . '@' . $hostname . '>';
        $headers = implode("\r\n", [
            "Date: {$date}",
            "From: {$this->fromName} <{$this->fromEmail}>",
            "To: {$to}",
            "Subject: {$subject}",
            "Message-ID: {$msgId}",
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=UTF-8",
            "Content-Transfer-Encoding: 7bit",
            "X-Mailer: PHP-MailService/1.0",
        ]);

        $this->smtpWrite($socket, $headers . "\r\n\r\n" . $body . "\r\n.");
        $response = $this->smtpRead($socket);

        $this->smtpWrite($socket, 'QUIT');
        fclose($socket);

        // 250 = success
        return strpos($response, '250') === 0;
    }

    private function smtpWrite($socket, string $data): void
    {
        fwrite($socket, $data . "\r\n");
    }

    private function smtpRead($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 512)) {
            $response .= $line;
            // Lines ending without a dash mean the response is complete
            if (isset($line[3]) && $line[3] === ' ') break;
        }
        return $response;
    }

    // ─── HTML Template ─────────────────────────────────────────────────────────

    private function wrapHtml(string $subject, string $body): string
    {
        $year = date('Y');
        return <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>{$subject}</title></head>
<body style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;line-height:1.6;color:#1e293b;background:#f8fafc;margin:0;padding:40px;">
  <div style="max-width:600px;margin:0 auto;background:#fff;border:1px solid #e2e8f0;padding:40px;border-radius:20px;">
    <div style="text-align:center;margin-bottom:30px;padding-bottom:20px;border-bottom:1px solid #e2e8f0;">
      <h1 style="color:#14b8a6;margin:0;font-size:22px;font-weight:800;">{$this->fromName}</h1>
    </div>
    <div>{$body}</div>
    <div style="margin-top:40px;padding-top:20px;border-top:1px solid #e2e8f0;font-size:12px;color:#94a3b8;text-align:center;">
      <p>© {$year} {$this->fromName}. All rights reserved.</p>
      <p>This is an automated message — please do not reply directly.</p>
    </div>
  </div>
</body>
</html>
HTML;
    }

    // ─── DB Helper ─────────────────────────────────────────────────────────────

    private function getSetting(string $key, $fallback = null): ?string
    {
        try {
            $row = Database::query('SELECT value FROM site_settings WHERE `key` = :key LIMIT 1', ['key' => $key])->fetch();
            return $row ? (string) $row['value'] : (string) $fallback;
        } catch (\Throwable $e) {
            return (string) $fallback;
        }
    }
}
