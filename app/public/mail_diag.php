<?php
// Email diagnostics — DELETE THIS FILE after debugging!
header('Content-Type: text/plain');

echo "=== EMAIL DIAGNOSTICS ===\n\n";

// 1. PHP mail() function
echo "1. mail() function available: " . (function_exists('mail') ? "YES" : "NO") . "\n";

// 2. popen available
echo "2. popen() available: " . (function_exists('popen') ? "YES" : "NO") . "\n";

// 3. fsockopen available
echo "3. fsockopen() available: " . (function_exists('fsockopen') ? "YES" : "NO") . "\n";

// 4. sendmail binary
$paths = ['/usr/sbin/sendmail', '/usr/lib/sendmail', '/usr/bin/sendmail'];
foreach ($paths as $p) {
    echo "4. {$p} exists: " . (file_exists($p) ? "YES" : "NO") . 
         " | executable: " . (is_executable($p) ? "YES" : "NO") . "\n";
}

// 5. disabled functions
echo "\n5. Disabled functions: " . (ini_get('disable_functions') ?: 'none') . "\n";

// 6. sendmail_path
echo "6. sendmail_path: " . (ini_get('sendmail_path') ?: 'not set') . "\n";

// 7. SMTP port test
echo "\n=== SMTP PORT TESTS ===\n";
foreach ([25, 587, 465] as $port) {
    $conn = @fsockopen('127.0.0.1', $port, $errno, $errstr, 3);
    if ($conn) {
        fclose($conn);
        echo "Port {$port}: OPEN\n";
    } else {
        echo "Port {$port}: CLOSED ({$errstr})\n";
    }
}

// 8. Try actually sending with mail() if available
if (function_exists('mail')) {
    echo "\n=== TEST SEND VIA mail() ===\n";
    $to      = 'lewis@gmail.com'; // change to your real email
    $subject = 'MailService Diagnostic Test';
    $headers = "From: info@konigsweg.com\r\nContent-Type: text/plain";
    $result  = mail($to, $subject, 'This is a diagnostic test email.', $headers);
    echo "mail() send result: " . ($result ? "SUCCESS" : "FAILED") . "\n";
}

// 9. Try sendmail popen
if (function_exists('popen')) {
    echo "\n=== TEST SEND VIA popen/sendmail ===\n";
    $sendmailPath = '/usr/sbin/sendmail';
    if (is_executable($sendmailPath)) {
        $handle = popen($sendmailPath . ' -t -i -f info@konigsweg.com', 'w');
        if ($handle) {
            fwrite($handle, "To: lewis@gmail.com\r\nFrom: info@konigsweg.com\r\nSubject: Sendmail Test\r\n\r\nTest via popen.\r\n");
            $code = pclose($handle);
            echo "popen sendmail exit code: {$code} (" . ($code === 0 ? "SUCCESS" : "FAILED") . ")\n";
        } else {
            echo "popen() returned false\n";
        }
    } else {
        echo "sendmail not executable at {$sendmailPath}\n";
    }
}
echo "\n=== DONE ===\n";
