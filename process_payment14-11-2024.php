<?php
require 'vendor/autoload.php';
error_reporting(E_ALL); // Report all errors
ini_set('display_errors', 1); // Display errors

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__); // This assumes .env is in the root directory
$dotenv->load();


// Access environment variables
// $dbHost = $_ENV["DB_HOST"];
$dbUser = $_ENV["DBUSER"];
$dbPassword = $_ENV["DBPASSWORD"];
$dbName = $_ENV['DBNAME'];

$server = 'localhost'; // Or you can use $dbHost if you're using that variable directly

// Create connection
$conn = new mysqli($server, $dbUser, $dbPassword, $dbName);


$secret_key=$_ENV["STRIPE_SECRET_KEY"];

\Stripe\Stripe::setApiKey($secret_key); // Replace with your actual Stripe secret key

header('Content-Type: application/json');

// Get the amount from the POST request
$amount = $_POST['amount'];

// Validate received data
if (!isset($amount) || !is_numeric($amount)) {
    echo json_encode(['error' => 'Invalid payment amount']);
    http_response_code(400); // Bad Request
    exit;
}

try {
    // Create a new PaymentIntent with the specified amount and currency
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $amount * 100, // Amount in cents (multiply by 100)
        'currency' => 'gbp', // Adjust currency as needed
        'payment_method_types' => ['card'], // Supported payment method types
    ]);
    // Prepare the SQL query
$stmt = $conn->prepare("INSERT INTO payments (name, email, phone, transaction_id, client_id, payment_date, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    die("Prepare failed: " . $conn->error);  // Output the error for debugging
}
date_default_timezone_set('Europe/London');  // Set timezone to UK

// Define the status and current timestamp
$payment_status = 'pending';
$payment_date =date('Y-m-d H:i:s'); // Current timestamp

// Check if required POST fields are set
if (!isset($_POST['name'], $_POST['email'], $_POST['phone'])) {
    die('Required fields are missing');
}

// Bind parameters (adjust the type string accordingly)
$stmt->bind_param("sssssss", $_POST['name'], $_POST['email'], $_POST['phone'], $paymentIntent->id, $paymentIntent->client_secret, $payment_date, $payment_status);

// Execute the statement
if ($stmt->execute()) {
    // echo "Data inserted successfully!";
} else {
    // echo "Error: " . $stmt->error;
}

// Close statement and connection
$stmt->close();
$conn->close();

    // Return the client secret for use on the client-side
    echo json_encode(['clientSecret' => $paymentIntent->client_secret]);


} catch (\Stripe\Exception\ApiErrorException $e) {
    // Return error message if something goes wrong
    echo json_encode(['error' => $e->getMessage()]);
    http_response_code(500); // Internal Server Error
}
