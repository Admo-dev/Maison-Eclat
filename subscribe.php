<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Check if this is a full contact message or a regular newsletter signup
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Please enter a valid email address."]);
        exit;
    }

    $current_date = date("Y-m-d H:i:s");

    // ROUTE A: Handle full message log if name and message are provided
    if (!empty($name) && !empty($message)) {
        $file = 'messages.txt';
        // Format layout sequence safely with clear data breaks
        $data_line = $current_date . " | " . $name . " | " . $email . " | " . $message . PHP_EOL;
        $success_msg = "Your message has been sent successfully to the Atelier!";
    } 
    // ROUTE B: Handle typical newsletter signup standard
    else {
        $file = 'subscribers.txt';
        $data_line = $current_date . " - " . $email . PHP_EOL;
        $success_msg = "Thank you for joining Maison Éclat!";
    }

    // Save to the respective flat-file target directory
    if (file_put_contents($file, $data_line, FILE_APPEND | LOCK_EX) !== false) {
        http_response_code(200);
        echo json_encode(["status" => "success", "message" => $success_msg]);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Server storage issue. Please try again."]);
        exit;
    }
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method Not Allowed."]);
    exit;
}
?>
