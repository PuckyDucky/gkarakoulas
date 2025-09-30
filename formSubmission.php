<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://gkarakoulas.accountant');

// Conditional that checks if the type of the request is "POST"
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Variable that holds raw data from "POST" body
$json_data = file_get_contents('php://input');

// Variable that holds a PHP associative array, which was originally a JSON object
$data = json_decode($json_data, true);

// Conditional that checks if decoding has been successful
if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data.']);
    exit;
}

// Conditional that checks if the required fields of the form exist
$required_fields = ['firstName', 'lastName', 'email', 'phoneNumber', 'address', 'zipCode', 'fiscalProfile', 'additionalInfo'];
foreach ($required_fields as $field) {
    if (!isset($data[$field])) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => "Missing required field: $field."]);
        exit;
    }
}

// Storing the values of each field to variables
$firstName = htmlspecialchars($data['firstName']);
$lastName = htmlspecialchars($data['lastName']);
$email = htmlspecialchars($data['email']);
$phoneNumber = htmlspecialchars($data['phoneNumber']);
$address = htmlspecialchars($data['address']);
$zipCode = htmlspecialchars($data['zipCode']);
$fiscalProfile = htmlspecialchars($data['fiscalProfile']);
$additionalInfo = htmlspecialchars($data['additionalInfo']);

// Creating the e-mail
$to = 'pliatsiosgiorgos@gmail.com';
$subject = 'Νέα υποβολή από τη φόρμα επικοινωνίας';

$email_content = "Όνομα: $firstName\n";
$email_content .= "Επώνυμο: $lastName\n";
$email_content .= "E-mail: $email\n";
$email_content .= "Τηλέφωνο: $phoneNumber\n";
$email_content .= "Διεύθυνση: $address\n";
$email_content .= "Τ.Κ.: $zipCode\n";
$email_content .= "Ιδιότητα: $fiscalProfile\n\n";
$email_content .= "Επιπλέον πληροφορίες:\n$additionalInfo";

$headers = 'From: ' . $email . "\r\n" .
    'Reply-To: ' . $email . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

// Sending the e-mail
if (mail($to, $subject, $email_content, $headers)) {
    // Success
    echo json_encode(['success' => true, 'message' => 'Email sent successfully!']);
} else {
    // Failure
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Failed to send email.']);
}

?>