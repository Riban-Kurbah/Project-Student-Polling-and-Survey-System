<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");

// Include your database connection
$conn = new mysqli("localhost", "root", "", "your_database_name");

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Get raw POST data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (!isset($data['title']) || !is_array($data['questions']) || count($data['questions']) == 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid survey data."]);
    exit;
}

$title = $conn->real_escape_string($data['title']);

// Insert into surveys table
$stmt = $conn->prepare("INSERT INTO surveys (title) VALUES (?)");
$stmt->bind_param("s", $title);
if (!$stmt->execute()) {
    echo json_encode(["success" => false, "message" => "Failed to create survey."]);
    exit;
}
$survey_id = $stmt->insert_id;

// Insert each question and its options
foreach ($data['questions'] as $q) {
    $question_text = $conn->real_escape_string($q['question']);
    $stmt = $conn->prepare("INSERT INTO survey_questions (survey_id, question_text) VALUES (?, ?)");
    $stmt->bind_param("is", $survey_id, $question_text);
    if (!$stmt->execute()) continue;

    $question_id = $stmt->insert_id;

    foreach ($q['options'] as $opt) {
        $option_text = $conn->real_escape_string($opt);
        $stmt_opt = $conn->prepare("INSERT INTO survey_options (question_id, option_text) VALUES (?, ?)");
        $stmt_opt->bind_param("is", $question_id, $option_text);
        $stmt_opt->execute();
    }
}

echo json_encode(["success" => true, "message" => "Survey created successfully."]);
$conn->close();
