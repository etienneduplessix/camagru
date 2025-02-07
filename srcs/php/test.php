<?php
function smtp_mail($to, $subject, $message, $from = 'noreply@example.com', $smtp_server = 'localhost', $smtp_port = 80) {
    $socket = fsockopen($smtp_server, $smtp_port, $errno, $errstr, 30);
    if (!$socket) {
        echo "Failed to connect: $errstr ($errno)";
        return false;
    }
    
    // Read the initial server response
    fgets($socket, 515);
    
    // Say EHLO
    fputs($socket, "EHLO localhost\r\n");
    fgets($socket, 515);
    
    // MAIL FROM
    fputs($socket, "MAIL FROM:<$from>\r\n");
    fgets($socket, 515);
    
    // RCPT TO
    fputs($socket, "RCPT TO:<$to>\r\n");
    fgets($socket, 515);
    
    // DATA
    fputs($socket, "DATA\r\n");
    fgets($socket, 515);
    
    // Send email headers and message
    fputs($socket, "Subject: $subject\r\n");
    fputs($socket, "From: $from\r\n");
    fputs($socket, "To: $to\r\n");
    fputs($socket, "\r\n");  // End headers
    fputs($socket, "$message\r\n");
    fputs($socket, ".\r\n");
    fgets($socket, 515);
    
    // QUIT
    fputs($socket, "QUIT\r\n");
    fclose($socket);
    return true;
}

// Usage:
$result = smtp_mail('recipient@example.com', 'Test SMTP Email', "Hello,\nThis is a test email sent using a basic SMTP client in PHP.");
echo $result ? "Email sent." : "Failed to send email.";
?>
