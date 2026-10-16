<?php
// Secure response headers to handle background AJAX requests smoothly
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Load the core PHPMailer engine classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Sanitize incoming parameters safely to block injection attempts
    $to_email = filter_input(INPUT_POST, 'customer_email', FILTER_SANITIZE_EMAIL);
    $to_name  = filter_input(INPUT_POST, 'customer_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $reply_msg = filter_input(INPUT_POST, 'reply_message', FILTER_SANITIZE_SPECIAL_CHARS);

    // Validate parameter completeness before firing server relays
    if (!filter_var($to_email, FILTER_VALIDATE_EMAIL) || empty($reply_msg)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Recipient parameters or response fields are incomplete."]);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // 1. Live Server SMTP Gateway Authentication Settings
        $mail->isSMTP();                                            // Set mailer to use SMTP network relays
        $mail->Host       = '://maisoneclat.com';                 // Replace with your live host Outgoing Mail Server
        $mail->SMTPAuth   = true;                                   // Enable strict security verification credentials
        $mail->Username   = 'info@maisoneclat.com';                 // Official business email mailbox username account
        $mail->Password   = 'YourStrongEmailPasswordHere';          // Password created inside your hosting email manager panel
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // High grade SSL encryption standard encryption link
        $mail->Port       = 465;                                    // Native port protocol used for secure SMTPS data streams

        // 2. Sender and Recipient Configuration
        $mail->setFrom('info@maisoneclat.com', 'Maison Éclat Atelier'); // Match your authorized username mailbox account
        $mail->addAddress($to_email, $to_name);                     // Map targeted user address parameters safely
        $mail->addReplyTo('info@maisoneclat.com', 'Atelier Support');

        // 3. Email Layout Composition (Styled to match your theme palette)
        $mail->isHTML(true);                                        // Enable luxury style elements rendering layers
        $mail->Subject = 'Response from Maison Éclat Atelier';       // Set email Subject header string text
        
        // Premium Luxury Styled HTML Message Body Content
        $mail->Body    = "
            <div style='font-family: \"Playfair Display\", Georgia, serif; max-width: 600px; margin: 0 auto; padding: 30px; border: 1px solid #ebdada; background-color: #fbf9f9; color: #2b1b17;'>
                <div style='text-align: center; margin-bottom: 30px; border-bottom: 1px solid #ebdada; padding-bottom: 20px;'>
                    <h2 style='color: #4a2c3a; text-transform: uppercase; letter-spacing: 4px; margin: 0; font-size: 24px;'>Maison Éclat</h2>
                    <p style='font-size: 10px; text-transform: uppercase; color: #a38b97; letter-spacing: 2px; margin-top: 5px;'>Restricted Operations Response</p>
                </div>
                
                <p style='font-size: 15px; color: #4a2c3a; font-weight: 600;'>Dear " . htmlspecialchars($to_name) . ",</p>
                <p style='font-size: 14px; color: #2b1b17; line-height: 1.8; margin-bottom: 25px;'>
                    Thank you for contacting the Maison Éclat Atelier. We have processed your message inquiry regarding our custom fashion silhouette configurations. 
                </p>
                
                <div style='font-size: 14px; color: #555555; font-style: italic; padding: 15px 20px; background: #ffffff; border-left: 3px solid #6b1d2f; border-top: 1px solid #f5ecee; border-right: 1px solid #f5ecee; border-bottom: 1px solid #f5ecee; margin-bottom: 25px; line-height: 1.6;'>
                    \"" . nl2br(htmlspecialchars($reply_msg)) . "\"
                </div>
                
                <p style='font-size: 14px; color: #2b1b17; line-height: 1.8; margin-bottom: 30px;'>
                    If you require immediate personalized booking consultations, please reach back out to our concierge channel.
                </p>
                
                <div style='text-align: center; border-top: 1px solid #ebdada; padding-top: 20px; margin-top: 30px;'>
                    <p style='font-size: 11px; color: #a38b97; text-transform: uppercase; letter-spacing: 1px; margin: 0;'>
                        Warmest Regards,<br>
                        <span style='color: #4a2c3a; font-weight: bold;'>The Atelier Management Team</span>
                    </p>
                </div>
            </div>
        ";

        // Dispatch out over network verification gateway relays
        $mail->send();
        
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => "Reply transmitted securely to " . htmlspecialchars($to_email)]);
        exit;
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Mail delivery system malfunction: {$mail->ErrorInfo}"]);
        exit;
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed."]);
    exit;
}
?>
