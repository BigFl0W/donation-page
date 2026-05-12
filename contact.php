<?php
require_once __DIR__ . '/config/autoload.php';

use App\Database;
use App\Mailer;
use App\Env;
use App\Helpers;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $lastname = trim((string)($_POST['lastname'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $subject = trim((string)($_POST['subject'] ?? 'New Contact Form Submission'));
    $message = trim((string)($_POST['comment'] ?? ''));

    if ($name && $email && $message) {
        $fullName = trim($name . ' ' . $lastname);

        $recentDuplicate = Database::fetchOne(
            "SELECT id
             FROM contact_messages
             WHERE email = :email
               AND subject = :subject
               AND message = :message
               AND created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
             ORDER BY id DESC
             LIMIT 1",
            [
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
            ]
        );

        if ($recentDuplicate) {
            echo "<div class='alert alert-success mt-4'>Thank you for your message! We already received it and will get back to you soon.</div>";
            exit;
        }

        // Save to DB
        Database::execute(
            "INSERT INTO contact_messages (name, email, phone, subject, message, status, created_at)
             VALUES (:name, :email, :phone, :subject, :message, 'unread', NOW())",
            [
                'name' => $fullName,
                'email' => $email,
                'phone' => $phone !== '' ? $phone : null,
                'subject' => $subject,
                'message' => $message
            ]
        );

        // Send Email to admin inbox
        $adminEmail = Env::get('DEFAULT_ADMIN_EMAIL', 'info@fahwi.org');
        $brandName = Helpers::brandName('Friends At Heart Welfare Initiative');
        $htmlBody = "
            <h3>New Contact Form Submission</h3>
            <p><strong>Name:</strong> " . htmlspecialchars($fullName) . "</p>
            <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
            <p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>
            <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
            <p><strong>Message:</strong></p>
            <p>" . nl2br(htmlspecialchars($message)) . "</p>
        ";

        Mailer::send($adminEmail, "Contact Form: " . $subject, $htmlBody, '', $email, $fullName);

        // Send acknowledgement to sender
        $ackBody = "
            <h3>We received your message</h3>
            <p>Hello " . htmlspecialchars($fullName) . ",</p>
            <p>Thank you for contacting " . htmlspecialchars($brandName) . ". Our team has received your message and will get back to you as soon as possible.</p>
            <p><strong>Your subject:</strong> " . htmlspecialchars($subject) . "</p>
            <p><strong>Your message:</strong></p>
            <p>" . nl2br(htmlspecialchars($message)) . "</p>
            <p>Warm regards,<br>" . htmlspecialchars($brandName) . "</p>
        ";
        Mailer::send($email, "We received your message - " . $brandName, $ackBody);

        // Notify admin dashboard
        Database::execute(
            "INSERT INTO admin_notifications (title, message, icon, link, created_at)
             VALUES (:title, :message, :icon, :link, NOW())",
            [
                'title' => 'New Contact Message',
                'message' => $fullName . " sent a new inquiry: " . $subject,
                'icon' => 'fas fa-envelope',
                'link' => '?page=messages',
            ]
        );

        echo "<div class='alert alert-success mt-4'>Thank you for your message! We will get back to you soon.</div>";
    } else {
        echo "<div class='alert alert-danger mt-4'>Please fill in all required fields.</div>";
    }
}
