<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

/**
 * Sends email via direct SMTP socket (fsockopen).
 * Compatible with cPanel where mail()/popen() are disabled.
 */
final class MailService
{
    private string $fromEmail;
    private string $fromName;
    private string $smtpHost = '127.0.0.1';
    private int    $smtpPort = 25;
    private int    $timeout  = 15;

    public function __construct()
    {
        $this->fromEmail = (string) $this->getSetting('smtp_user', 'no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        $this->fromName  = (string) $this->getSetting('company_name', 'ISO Compliance Hub');
    }

    // ─── Public send API ───────────────────────────────────────────────────────

    public function send(string $to, string $subject, string $body): bool
    {
        if (stripos($body, '<html') === false) {
            $body = $this->wrapHtml($subject, $body);
        }

        // Try each port: 25 (local relay), 587, 465
        foreach ([25, 587] as $port) {
            $this->smtpPort = $port;
            try {
                if ($this->smtpSend($to, $subject, $body)) {
                    return true;
                }
            } catch (\Throwable $e) {
                error_log("[MailService] Port {$port} failed: " . $e->getMessage());
            }
        }

        error_log("[MailService] All delivery methods failed for: {$to}");
        return false;
    }

    public function sendWelcome(string $to, string $name, string $verificationLink): bool
    {
        $subject = "Welcome to {$this->fromName} – Verify your account";
        $body = "
        <p>Hello <strong>{$name}</strong>,</p>
        <p>Thank you for signing up for {$this->fromName}. We're excited to have you on board!</p>
        <p>Please click the button below to verify your email address:</p>
        <p style='margin:30px 0;'>
            <a href='{$verificationLink}' style='background:#14b8a6;color:#fff;padding:12px 24px;text-decoration:none;border-radius:6px;font-weight:bold;'>Verify My Account</a>
        </p>
        <p style='font-size:13px;color:#666;'>Or copy this link: {$verificationLink}</p>";
        return $this->send($to, $subject, $body);
    }

    public function sendPasswordReset(string $to, string $resetLink): bool
    {
        $subject = "Reset your password – {$this->fromName}";
        $body = "
        <p>Hello,</p>
        <p>We received a request to reset your {$this->fromName} account password.</p>
        <p style='margin:30px 0;'>
            <a href='{$resetLink}' style='background:#f97316;color:#fff;padding:12px 24px;text-decoration:none;border-radius:6px;font-weight:bold;'>Reset Password</a>
        </p>
        <p>If you didn't request this, ignore this email. The link expires in 1 hour.</p>";
        return $this->send($to, $subject, $body);
    }

    public function sendNominationAlert(string $to, string $nominator, string $nominee, string $type, string $recipientType = 'nominator'): bool
    {
        $subject = "Organization Nomination: {$nominee}";
        if ($recipientType === 'organization') {
            $body = "<p><strong>{$nominator}</strong> nominated <strong>{$nominee}</strong> for <strong>{$type}</strong>.</p>";
        } elseif ($recipientType === 'nominee') {
            $body = "<p>Congratulations! <strong>{$nominee}</strong> was nominated for <strong>{$type}</strong> by <strong>{$nominator}</strong>.</p>";
        } else {
            $body = "<p>Your nomination of <strong>{$nominee}</strong> for <strong>{$type}</strong> was submitted successfully.</p>";
        }
        return $this->send($to, $subject, $body);
    }

    public function sendOrderConfirmation(string $to, string $orderId, string $total, string $currency): bool
    {
        $subject = "Order Confirmed #{$orderId} – {$this->fromName}";
        $body = "
        <p>Thank you for your order!</p>
        <p>Order <strong>#{$orderId}</strong> has been received.</p>
        <p><strong>Total Paid:</strong> {$currency} {$total}</p>";
        return $this->send($to, $subject, $body);
    }

    // ─── Core SMTP via fsockopen ────────────────────────────────────────────────

    private function smtpSend(string $to, string $subject, string $body): bool
    {
        $socket = @fsockopen($this->smtpHost, $this->smtpPort, $errno, $errstr, $this->timeout);
        if ($socket === false) {
            error_log("[MailService] fsockopen failed on port {$this->smtpPort}: {$errstr} ({$errno})");
            return false;
        }
        stream_set_timeout($socket, $this->timeout);

        // Read greeting
        $resp = $this->read($socket);
        if (!$this->isOk($resp, '220')) {
            error_log("[MailService] Bad greeting: {$resp}");
            fclose($socket);
            return false;
        }

        // EHLO
        $hostname = $_SERVER['HTTP_HOST'] ?? 'konigsweg.com';
        $this->write($socket, "EHLO {$hostname}");
        $resp = $this->read($socket);
        if (!$this->isOk($resp, '250')) {
            // Try HELO fallback
            $this->write($socket, "HELO {$hostname}");
            $resp = $this->read($socket);
        }

        // MAIL FROM
        $this->write($socket, "MAIL FROM:<{$this->fromEmail}>");
        $resp = $this->read($socket);
        if (!$this->isOk($resp, '250')) {
            error_log("[MailService] MAIL FROM rejected: {$resp}");
            fclose($socket);
            return false;
        }

        // RCPT TO (support multiple comma-separated)
        foreach (array_map('trim', explode(',', $to)) as $recipient) {
            $this->write($socket, "RCPT TO:<{$recipient}>");
            $resp = $this->read($socket);
            if (!$this->isOk($resp, '250') && !$this->isOk($resp, '251')) {
                error_log("[MailService] RCPT TO rejected for {$recipient}: {$resp}");
                fclose($socket);
                return false;
            }
        }

        // DATA
        $this->write($socket, 'DATA');
        $resp = $this->read($socket);
        if (!$this->isOk($resp, '354')) {
            error_log("[MailService] DATA command rejected: {$resp}");
            fclose($socket);
            return false;
        }

        // Build message — use CRLF throughout
        $hostname = $_SERVER['HTTP_HOST'] ?? 'konigsweg.com';
        $msgId    = '<' . bin2hex(random_bytes(8)) . '@' . $hostname . '>';
        $date     = date('r');

        // Dot-stuff: any line starting with "." must be doubled
        $safebody  = preg_replace('/^\./', '..', $body);

        $message = implode("\r\n", [
            "Date: {$date}",
            "From: =?UTF-8?B?" . base64_encode($this->fromName) . "?= <{$this->fromEmail}>",
            "Reply-To: <{$this->fromEmail}>",
            "Return-Path: <{$this->fromEmail}>",
            "To: {$to}",
            "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=",
            "Message-ID: {$msgId}",
            "X-Priority: 3 (Normal)",
            "X-Mailer: PHP/" . phpversion(),
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=UTF-8",
            "Content-Transfer-Encoding: base64",
            "",
            chunk_split(base64_encode($body), 76, "\r\n"),
        ]);

        // Send message body followed by terminator on its own line
        fwrite($socket, $message . "\r\n.\r\n");

        $resp = $this->read($socket);
        $success = $this->isOk($resp, '250');
        if (!$success) {
            error_log("[MailService] Message rejected after DATA: {$resp}");
        }

        $this->write($socket, 'QUIT');
        $this->read($socket);
        fclose($socket);

        return $success;
    }

    private function write($socket, string $cmd): void
    {
        fwrite($socket, $cmd . "\r\n");
    }

    private function read($socket): string
    {
        $response = '';
        while (!feof($socket)) {
            $line = fgets($socket, 512);
            if ($line === false) break;
            $response .= $line;
            // Line with space at position 3 = last line of response
            if (strlen($line) >= 4 && $line[3] === ' ') break;
        }
        return trim($response);
    }

    private function isOk(string $response, string $code): bool
    {
        return strpos($response, $code) === 0;
    }

    // ─── HTML Template ─────────────────────────────────────────────────────────

    private function wrapHtml(string $subject, string $body): string
    {
        $year = date('Y');
        $name = htmlspecialchars($this->fromName, ENT_QUOTES, 'UTF-8');
        return "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>{$subject}</title></head>"
            . "<body style='font-family:Arial,sans-serif;line-height:1.6;color:#1e293b;background:#f8fafc;margin:0;padding:40px;'>"
            . "<div style='max-width:600px;margin:0 auto;background:#fff;border:1px solid #e2e8f0;padding:40px;border-radius:16px;'>"
            . "<div style='text-align:center;margin-bottom:30px;padding-bottom:20px;border-bottom:1px solid #e2e8f0;'>"
            . "<h1 style='color:#14b8a6;margin:0;font-size:22px;'>{$name}</h1></div>"
            . "<div>{$body}</div>"
            . "<div style='margin-top:40px;padding-top:20px;border-top:1px solid #e2e8f0;font-size:12px;color:#94a3b8;text-align:center;'>"
            . "<p>&copy; {$year} {$name}. All rights reserved.</p></div>"
            . "</div></body></html>";
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
