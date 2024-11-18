<?php
require_once __DIR__ . '/vendor/autoload.php';
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

date_default_timezone_set('Europe/London');
// Database connection
$conn = new mysqli('localhost', $_ENV['DBUSER'], $_ENV['DBPASSWORD'], $_ENV['DBNAME']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the username and password from POST request
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate input (this is a simple check, add more validation if needed)
    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Both fields are required.']);
        exit;
    }

    // Prepare the SQL query to fetch the user by username
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $storedUsername, $storedPassword);
    $stmt->fetch();

    if ($stmt->num_rows == 1) {

        // User exists, now verify the password
        if (password_verify($password, $storedPassword)) {
            // Password is correct, log the user in
            session_start();
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $storedUsername;
            $_SESSION['logged_in']=true;

//             // Get system details
// $ip_address = $_SERVER['REMOTE_ADDR'];  // User's IP address
// $user_agent = $_SERVER['HTTP_USER_AGENT']; // Browser/Device info
// $login_time = date("Y-m-d H:i:s"); // Current login time

// // For more detailed information, you can use libraries like `get_browser()`
// // For now, let's assume we are storing this info in a JSON object.
// $system_details_json = json_encode([
//     'ip_address' => $_SERVER['REMOTE_ADDR'],
  
//     'login_time' => date('Y-m-d H:i:s'),
// ]);
// // Update login_logs table to append new log entry
// $sql_update = "UPDATE users SET logs = JSON_ARRAY_APPEND(logs, '$', ?) WHERE user_id = ?";
// $stmt_update = $conn->prepare($sql_update);
// $stmt_update->bind_param("si", $system_details_json, $user_id);
// $stmt_update->execute();
// if ($stmt_last_login->affected_rows > 0) {
//     echo "New log entry added and last login time updated.";
// } else {
//     echo "Failed to update last login time.";
// }


            // Return success response in JSON format
            echo json_encode(['status' => 'success', 'message' => 'Login successful', 'redirect' => 'index.php']);
            exit;
        } else {
            // Invalid password
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
            exit;
        }
    } else {
        // No user found with that username
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password.']);
        exit;
    }

    $stmt->close();
}

$conn->close();
?>
