<?php

// Charger la configuration
$config = parse_ini_file('php.ini', true);

// Destinataire (remplace par ton email pour tester)
$to = 'etienne.dpl01@gmail.com';

// Sujet
$subject = 'Test Email Camagru';

// Message
$message = "Ceci est un email de test depuis Camagru.";

// En-têtes
$headers = "From: Camagru <noreply@yourdomain.com>\r\n";
$headers .= "Reply-To: noreply@yourdomain.com\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

// Envoi de l'email
if (mail($to, $subject, $message, $headers)) {
    echo "Email envoyé avec succès.";
} else {
    echo "Erreur lors de l'envoi de l'email.";
}
?>