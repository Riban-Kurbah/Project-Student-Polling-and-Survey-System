<?php
header('Content-Type: application/json');

// Direct DB connection (without db.php)
$host = 'localhost';
$db   = 'eduvote';   // Your DB name
$user = 'root';      // XAMPP default user
$pass = '';          // XAMPP default password
$conn = new mysqli($host, $user, $pass, $db);

// Connection error handling
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$pollId = isset($_GET['poll_id']) ? intval($_GET['poll_id']) : 0;
if ($pollId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid poll ID']);
    exit;
}

// Fetch poll
$pollResult = $conn->query("SELECT * FROM polls WHERE id = $pollId");
if ($pollResult && $pollResult->num_rows > 0) {
    $poll = $pollResult->fetch_assoc();

    // Fetch options for this poll
    $optionResult = $conn->query("SELECT * FROM poll_options WHERE poll_id = $pollId");
    $options = [];
    while ($optRow = $optionResult->fetch_assoc()) {
        $options[] = [
            'id' => $optRow['id'],
            'option_text' => $optRow['option_text'],
            'votes' => (int)$optRow['votes']
        ];
    }

    echo json_encode([
        'success' => true,
        'poll' => [
            'id' => $poll['id'],
            'question' => $poll['question'],
            'created_at' => $poll['created_at'],
            'options' => $options
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Poll not found']);
}

$conn->close();
?>
