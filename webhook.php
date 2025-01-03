<?php
require_once('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

\Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET_KEY"]);

$input = @file_get_contents("php://input");
$logFile = 'webhook_log.txt';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - " . $input . "\n", FILE_APPEND);
$event = null;

$webhookSecret = 'whsec_yCEte7nYpV3yX6w97gv9toOUefNH6kw0';

$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];

try {
    $event = \Stripe\Webhook::constructEvent($input, $sigHeader, $webhookSecret);
} catch (Exception $e) {
    file_put_contents('error_log.txt', "Webhook Error: " . $e->getMessage() . "\n", FILE_APPEND);
    http_response_code(400);
    exit();
}

$conn = new mysqli('localhost', $_ENV["DBUSER"], $_ENV["DBPASSWORD"], $_ENV['DBNAME']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


switch ($event->type) {
    case 'payment_intent.payment_failed':
        $paymentIntent = $event->data->object;
        $transactionId = $paymentIntent->id;
        $errorMessage = isset($paymentIntent->last_payment_error) ? $paymentIntent->last_payment_error->message : null;
        $paymentMethod = isset($paymentIntent->last_payment_error->payment_method->card->last4) ? $paymentIntent->last_payment_error->payment_method->card->last4 : null;
        $paymentStatus = "Failed";
        //file_put_contents('error_log.txt', "Payment failed for transaction: {$paymentIntent->id}, Error: {$errorMessage}, Card last 4: {$paymentMethod}\n", FILE_APPEND);
        break;

    case 'payment_intent.requires_payment_method':
        $paymentIntent = $event->data->object;
        $errorMessage = isset($paymentIntent->last_payment_error) ? $paymentIntent->last_payment_error->message : null;
        $paymentMethod = $paymentIntent->payment_method;
        $paymentStatus = "Failed";
        break;

    case 'payment_intent.succeeded':
        $paymentIntent = $event->data->object;
        $transactionId = $paymentIntent->id;
        $errorMessage = isset($paymentIntent->last_payment_error) ? $paymentIntent->last_payment_error->message : null;
        $paymentStatus = "Completed";
        //file_put_contents('webhook_log.txt', "Payment succeeded for transaction: {$transactionId}, Status: {$paymentStatus}\n", FILE_APPEND);
        break;

    case 'payment_intent.canceled':
        $paymentIntent = $event->data->object;
        $transactionId = $paymentIntent->id;
        $errorMessage = isset($paymentIntent->last_payment_error) ? $paymentIntent->last_payment_error->message : null;
        $paymentStatus = "Cancelled";
       // file_put_contents('error_log.txt', "Payment canceled for transaction: {$transactionId}\n", FILE_APPEND);
        break;

    default:
        break;
}

$stmt = $conn->prepare("UPDATE payments SET payment_status = ?, error_message = ?, response = ?, c_updated_date = ?  WHERE transaction_id = ?");
if (!$stmt) {
    // Log the error if prepare fails
    file_put_contents('error_log.txt', "SQL Prepare Error: " . $conn->error . "\n", FILE_APPEND);
    die("Prepare failed: " . $conn->error);
}
date_default_timezone_set('Europe/London'); 
$payment_date =date('Y-m-d H:i:s');
$stmt->bind_param("sssss", $paymentStatus, $errorMessage, json_encode($paymentIntent),$payment_date, $transactionId);

if ($stmt->execute()) {
    // Success, update completed
    echo "Payment status updated successfully!";
    file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - Payment status updated successfully for transaction_id: $transactionId\n", FILE_APPEND);
} else {
    // Failure, log the error
    echo "Error updating payment status: " . $stmt->error;
    file_put_contents('error_log.txt', "Error updating payment status: " . $stmt->error . "\n", FILE_APPEND);
}

$stmt->close();

$conn->close();

http_response_code(200);
?>
