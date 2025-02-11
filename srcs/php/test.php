<?php
// Example usage
require_once 'MailConfig.php';

// Create a new mailer instance
$mailer = new MailConfig();

// Send a verification email
$to = "etienne.dpl01@gmail.com";
$subject = "Verify your Camagru account";
$body = "<h1>Welcome to Camagru!</h1>
         <p>Please click the link below to verify your account:</p>
         <a href='http://yourdomain.com/verify?token=123456'>Verify Account</a>";

// Send the email
$result = $mailer->sendVerificationMail($to, $subject, $body);

if ($result) {
    echo "Verification email sent successfully";
} else {
    echo "Failed to send verification email";
}