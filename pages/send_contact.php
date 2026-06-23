<?php
header('Content-Type: application/json');

// Include PHPMailer files
require_once '../phpmailer/PHPMailer.php';
require_once '../phpmailer/SMTP.php';
require_once '../phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $found_through = $_POST['found_through'] ?? '';
    $message = $_POST['message'] ?? '';

    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields']);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please enter a valid email address']);
        exit;
    }

    try {
        // Create an instance of PHPMailer
        $mail = new PHPMailer(true);

        // Enable debug output
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        
        // Capture debug output
        ob_start();

        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'samanyousafzai101@gmail.com';
        $mail->Password = 'wtsh nqds ucso qbjy';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('samanyousafzai101@gmail.com', 'Contact Form');
        $mail->addAddress('samanyousafzai101@gmail.com');
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'New Contact Form Submission';
        
        // Email body
        $emailBody = "
        <div style='font-family: Arial, sans-serif; padding: 20px;'>
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>
                <h2>New Contact Form Submission</h2>
            </div>
            <div style='margin: 20px 0;'>
                <div style='margin: 10px 0;'>
                    <strong style='color: #666;'>Name:</strong> " . htmlspecialchars($name) . "
                </div>
                <div style='margin: 10px 0;'>
                    <strong style='color: #666;'>Email:</strong> " . htmlspecialchars($email) . "
                </div>
                <div style='margin: 10px 0;'>
                    <strong style='color: #666;'>Phone:</strong> " . htmlspecialchars($phone) . "
                </div>
                <div style='margin: 10px 0;'>
                    <strong style='color: #666;'>Found Through:</strong> " . htmlspecialchars($found_through) . "
                </div>
                <div style='margin: 10px 0;'>
                    <strong style='color: #666;'>Message:</strong><br>
                    " . nl2br(htmlspecialchars($message)) . "
                </div>
            </div>
        </div>";

        $mail->Body = $emailBody;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>'], "\n", $emailBody));

        // Send email
        $mail->send();
        
        // Get debug output
        $debug = ob_get_clean();

        // Send auto-reply
        $autoReply = new PHPMailer(true);
        $autoReply->isSMTP();
        $autoReply->Host = 'smtp.gmail.com';
        $autoReply->SMTPAuth = true;
        $autoReply->Username = 'samanyousafzai101@gmail.com';
        $autoReply->Password = 'wtsh nqds ucso qbjy';
        $autoReply->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $autoReply->Port = 587;

        // Auto-reply will come from your email
        $autoReply->setFrom('samanyousafzai101@gmail.com', 'Chatbot Team');
        $autoReply->addAddress($email, $name);  // Send to the user's email

        $autoReply->isHTML(true);
        $autoReply->Subject = 'Thank you for contacting us';
        
        $autoReplyBody = "
        <div style='font-family: Arial, sans-serif; padding: 20px;'>
            <div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>
                <h2>Thank you for reaching out!</h2>
            </div>
            <div style='margin: 20px 0;'>
                <p>Dear " . htmlspecialchars($name) . ",</p>
                <p>Thank you for contacting us. We have received your message and will get back to you as soon as possible.</p>
                <p>Best regards,<br>Chatbot Team</p>
            </div>
        </div>";

        $autoReply->Body = $autoReplyBody;
        $autoReply->AltBody = strip_tags(str_replace(['<br>', '<br/>'], "\n", $autoReplyBody));

        $autoReply->send();

        echo json_encode(['success' => true, 'message' => 'Thank you for your message. We will get back to you soon!']);
    } catch (Exception $e) {
        // Get debug output
        $debug = ob_get_clean();
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage(),
            'debug' => $debug
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 