<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed"]);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if (!$name || !$email || !$message || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["error" => "Please fill out all fields with a valid email."]);
    exit;
}

$to = 'hwaier@uwindsor.ca';
$subject = "LUXE | New Contact Form Submission";

$htmlBody = "
<html>
<head>
  <title>New Contact Submission</title>
</head>
<body style=\"font-family: Arial, sans-serif; line-height: 1.6;\">
  <h2 style=\"color: #333;\">You have a new message from LUXE Contact Form</h2>
  <p><strong>Name:</strong> {$name}</p>
  <p><strong>Email:</strong> {$email}</p>
  <p><strong>Message:</strong><br>{$message}</p>
</body>
</html>
";

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: hwaier.myweb.cs.uwindsor.ca\r\n";
$headers .= "Reply-To: {$email}\r\n";

if (mail($to, $subject, $htmlBody, $headers)) {
    echo json_encode(["success" => true]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to send message. Try again later."]);
}
