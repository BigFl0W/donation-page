<?php
require_once __DIR__ . '/config/autoload.php';

use App\Database;
use App\Mailer;
use App\Env;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $lastname = trim((string)($_POST['lastname'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $subject = trim((string)($_POST['subject'] ?? 'New Contact Form Submission'));
    $message = trim((string)($_POST['comment'] ?? ''));

    if ($name && $email && $message) {
        $fullName = $name . ' ' . $lastname;

        // Save to DB
        Database::execute(
            "INSERT INTO contact_messages (sender_name, sender_email, subject, message, status, created_at)
             VALUES (:name, :email, :subject, :message, 'unread', NOW())",
            [
                'name' => $fullName,
                'email' => $email,
                'subject' => $subject,
                'message' => $message
            ]
        );

        // Send Email
        $adminEmail = Env::get('DEFAULT_ADMIN_EMAIL', 'admin@graciouscharity.org');
        $htmlBody = "
            <h3>New Contact Form Submission</h3>
            <p><strong>Name:</strong> " . htmlspecialchars($fullName) . "</p>
            <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
            <p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>
            <p><strong>Message:</strong></p>
            <p>" . nl2br(htmlspecialchars($message)) . "</p>
        ";

        Mailer::send($adminEmail, "Contact Form: " . $subject, $htmlBody, '', $email, $fullName);

        echo "<div class='alert alert-success mt-4'>Thank you for your message! We will get back to you soon.</div>";
    } else {
        echo "<div class='alert alert-danger mt-4'>Please fill in all required fields.</div>";
    }
}
