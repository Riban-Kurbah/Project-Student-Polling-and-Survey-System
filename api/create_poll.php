<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$data = json_decode(file_get_contents("php://input"), true);

$question = $data['question'] ?? '';
$options = $data['options'] ?? [];

if (empty($question) || !is_array($options) || count($options) < 2) {
    echo json_encode(["success" => false, "message" => "Invalid input. At least two options required."]);
    exit;
}

$conn = new mysqli("localhost", "root", "", "eduvote");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed"]);
    exit;
}

// Insert into polls table
$stmt = $conn->prepare("INSERT INTO polls (question) VALUES (?)");
$stmt->bind_param("s", $question);

if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Failed to create poll question."]);
    $stmt->close();
    $conn->close();
    exit;
}

$poll_id = $stmt->insert_id;
$stmt->close();

// Insert options
$optionStmt = $conn->prepare("INSERT INTO poll_options (poll_id, option_text) VALUES (?, ?)");
foreach ($options as $opt) {
    $opt = trim($opt);
    if ($opt !== '') {
        $optionStmt->bind_param("is", $poll_id, $opt);
        $optionStmt->execute();
    }
}
$optionStmt->close();
$conn->close();

echo json_encode(["success" => true, "message" => "Poll created successfully."]);
?>
