<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$data = json_decode(file_get_contents("php://input"), true);

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "eduvote";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
}

// Get user input
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(["success" => false, "message" => "Username and password are required"]);
    exit;
}

// Find user with is_admin check
$stmt = $conn->prepare("SELECT id, username, email, password, is_admin FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
    exit;
}

$user = $result->fetch_assoc();

// ✅ Log user info for debugging (do not use in production)
file_put_contents("signin-debug.log", print_r($user, true));

// Verify password
if (password_verify($password, $user['password'])) {
    // Remove sensitive data before sending response
    unset($user['password']);
    
    // Prepare response with role information
    $response = [
        'success' => true,
        'authenticated' => true,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'is_admin' => (bool)$user['is_admin']
        ],
        'redirect' => $user['is_admin'] ? 'admin-dashboard' : 'home'
    ];
    
    echo json_encode($response);
} else {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
}

$stmt->close();
$conn->close();
?>
