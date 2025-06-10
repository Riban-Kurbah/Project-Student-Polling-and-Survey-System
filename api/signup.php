<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the request
file_put_contents('debug.log', "\n\n" . date('Y-m-d H:i:s') . " - NEW REQUEST", FILE_APPEND);

// Get input data
$json = file_get_contents('php://input');
file_put_contents('debug.log', "\nRAW INPUT: " . $json, FILE_APPEND);

$data = json_decode($json, true);
if (!$data) {
    file_put_contents('debug.log', "\nINVALID JSON", FILE_APPEND);
    die(json_encode(["message" => "Invalid JSON data"]));
}

file_put_contents('debug.log', "\nDECODED DATA: " . print_r($data, true), FILE_APPEND);

// Database config
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "eduvote";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    file_put_contents('debug.log', "\nCONNECTION ERROR: " . $conn->connect_error, FILE_APPEND);
    die(json_encode(["message" => "Database connection failed"]));
}

// Process data
$username = $conn->real_escape_string($data['username'] ?? '');
$email = $conn->real_escape_string($data['email'] ?? '');
$password = $data['password'] ?? '';

// Validate
if (empty($username) || empty($email) || empty($password)) {
    file_put_contents('debug.log', "\nVALIDATION FAILED", FILE_APPEND);
    die(json_encode(["message" => "All fields are required"]));
}

// Check if user exists
$check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$check->bind_param("ss", $username, $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    file_put_contents('debug.log', "\nUSER EXISTS", FILE_APPEND);
    die(json_encode(["message" => "Username or email already exists"]));
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);
file_put_contents('debug.log', "\nHASHED PW: " . $hashedPassword, FILE_APPEND);

// Insert user
$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $hashedPassword);

if ($stmt->execute()) {
    file_put_contents('debug.log', "\nSUCCESS", FILE_APPEND);
    echo json_encode(["message" => "User created successfully"]);
} else {
    file_put_contents('debug.log', "\nINSERT ERROR: " . $stmt->error, FILE_APPEND);
    echo json_encode(["message" => "Registration failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>