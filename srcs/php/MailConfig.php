<?php


class MailConfig {  
    private string $host = 'smtp.gmail.com';
    private int $port = 587;
    private string $username = 'esusagence@gmail.com';
    private string $password = 'nbxrjksvrwsspatq';
    private string $secure = 'tls';
    private string $appUrl = 'http://localhost:8000';



    public function sendVerificationMail(string $to, string $token): bool {
        $subject = "Verify your Camagru account";
        $body = $this->getVerificationEmailTemplate($token);
        return $this->sendMail($to, $subject, $body);
    }

    private function sendMail(string $to, string $subject, string $body): bool {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->username,
            'Reply-To: ' . $this->username,
            'X-Mailer: PHP/' . phpversion()
        ];

        $success = mail($to, $subject, $body, implode("\r\n", $headers));
        
        if (!$success) {
            error_log("Failed to send email to $to: " . error_get_last()['message']);
            return false;
        }
        
        return true;
    }

    private function getVerificationEmailTemplate(string $token): string {
        $link = $this->appUrl . "/verify?token=" . $token;
        return "
        <html>
        <body style='font-family: Arial, sans-serif; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #2c3e50;'>Welcome to Camagru!</h2>
                <p>Please click the button below to verify your account:</p>
                <a href='$link' style='display: inline-block; padding: 10px 20px; background-color: #3498db; color: white; text-decoration: none; border-radius: 5px;'>
                    Verify Account
                </a>
                <p style='color: #7f8c8d; margin-top: 20px;'>If you didn't create an account, please ignore this email.</p>
            </div>
        </body>
        </html>";
    }
}