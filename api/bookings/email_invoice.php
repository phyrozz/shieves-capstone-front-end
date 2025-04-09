<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

$mail = new PHPMailer(true);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $env = parse_ini_file('../../.env');
        $requestBody = file_get_contents('php://input');
        $requestData = json_decode($requestBody, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON data: " . json_last_error_msg());
        }

        if (!isset($requestData['email'])) {
            throw new Exception("Email address is required");
        }
        $email = filter_var($requestData['email'], FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address: " . $email);
        }

        if (!isset($requestData['invoice'])) {
            throw new Exception("Invoice data is required");
        }
        $invoice = filter_var($requestData['invoice'], FILTER_SANITIZE_STRING);

        if (empty($invoice)) {
            throw new Exception("Invoice data cannot be empty");
        }

        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host       = $env["SMTP_HOST"];
        $mail->SMTPAuth   = true;
        $mail->Username   = $env["SMTP_EMAIL"]; 
        $mail->Password   = $env["SMTP_PASSWORD"];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 587;

        //Recipients
        $mail->setFrom($env["SMTP_EMAIL"], 'Museo de San Pedro');
        $mail->addAddress($email);

        //Attachments
        $mail->addAttachment('/var/tmp/file.tar.gz');
        $mail->addAttachment('/tmp/image.jpg', 'new.jpg');

        //Content
        $mail->isHTML(true);
        $mail->Subject = 'Here is the subject';
        $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
    } else {
        throw new Exception("Invalid request method");
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}