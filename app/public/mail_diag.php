<?php
// SMTP Step-by-step diagnostic — DELETE AFTER DEBUGGING
header('Content-Type: text/plain');
echo "=== SMTP CONVERSATION TEST ===\n\n";

$host    = '127.0.0.1';
$port    = 25;
$timeout = 10;
$from    = 'info@konigsweg.com';
$to      = 'lewis@gmail.com';

echo "Connecting to {$host}:{$port}...\n";
$sock = @fsockopen($host, $port, $errno, $errstr, $timeout);
if (!$sock) {
    die("FAILED: {$errstr} ({$errno})\n");
}
stream_set_timeout($sock, $timeout);
echo "Connected OK\n\n";

function smtp_read($sock) {
    $response = '';
    while (!feof($sock)) {
        $line = fgets($sock, 512);
        if ($line === false) break;
        echo "  << " . rtrim($line) . "\n";
        $response .= $line;
        if (strlen($line) >= 4 && $line[3] === ' ') break;
    }
    return trim($response);
}

function smtp_write($sock, $cmd) {
    echo "  >> {$cmd}\n";
    fwrite($sock, $cmd . "\r\n");
}

// Step 1: Greeting
echo "[1] GREETING:\n";
smtp_read($sock);

// Step 2: EHLO
echo "\n[2] EHLO:\n";
smtp_write($sock, 'EHLO konigsweg.com');
smtp_read($sock);

// Step 3: MAIL FROM
echo "\n[3] MAIL FROM:\n";
smtp_write($sock, "MAIL FROM:<{$from}>");
smtp_read($sock);

// Step 4: RCPT TO
echo "\n[4] RCPT TO:\n";
smtp_write($sock, "RCPT TO:<{$to}>");
smtp_read($sock);

// Step 5: DATA
echo "\n[5] DATA:\n";
smtp_write($sock, 'DATA');
smtp_read($sock);

// Step 6: Send message
echo "\n[6] SENDING MESSAGE:\n";
$msg = "Date: " . date('r') . "\r\n"
     . "From: Konigsweg <{$from}>\r\n"
     . "To: {$to}\r\n"
     . "Subject: SMTP Diagnostic Test\r\n"
     . "MIME-Version: 1.0\r\n"
     . "Content-Type: text/plain; charset=UTF-8\r\n"
     . "\r\n"
     . "This is a diagnostic test email from konigsweg.com SMTP.\r\n";
echo "  >> [message body + terminator]\n";
fwrite($sock, $msg . "\r\n.\r\n");
smtp_read($sock);

// Step 7: QUIT
echo "\n[7] QUIT:\n";
smtp_write($sock, 'QUIT');
smtp_read($sock);

fclose($sock);
echo "\n=== DONE ===\n";
