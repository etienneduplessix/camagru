<?php
session_start();

require_once('includes/loader.php');
require_once('includes/db.php');


function sendVerificationEmail() {
    $mail =  "etienne.dpl01@gmail.com"
    $token = bin2hex(random_bytes(32));
    $verificationLink = "http://localhost:8000/verify.php" . "/verify?token=" . urlencode($token);
      $message = "
      <!DOCTYPE html>
      <html>
      <head>
          <meta charset='UTF-8'>
          <title>Verify Your Camagru Account</title>
      </head>
      <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
          <h2>Welcome to Camagru!</h2>
          <p>Please verify your email by clicking the link below:</p>
          <p><a href='{$verificationLink}' style='display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none;'>Verify Email</a></p>
          <p>If the button doesn't work, copy and paste this link into your browser:</p>
          <p>{$verificationLink}</p>
      </body>
      </html>";

      $headers = [
          'MIME-Version: 1.0',
          'Content-Type: text/html; charset=UTF-8',
          'From: Camagru <noreply@camagru.com>'
      ];

      mail($email, "Verify Your Camagru Account", $message, implode("\r\n", $headers));

    }

error_log("👀 Showing forgot password form.");
showForgotPasswordForm();
?>
