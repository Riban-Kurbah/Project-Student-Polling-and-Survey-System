<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "eduvote");

if ($conn->connect_error) {
  echo json_encode([
    "success" => false,
    "message" => "Database connection failed"
  ]);
  exit();
}

// Get all polls
$poll_sql = "SELECT * FROM polls ORDER BY created_at DESC";
$poll_result = $conn->query($poll_sql);

$polls = [];

while ($poll = $poll_result->fetch_assoc()) {
  $poll_id = $poll['id'];

  // Get options for this poll
  $option_sql = "SELECT * FROM poll_options WHERE poll_id = $poll_id";
  $option_result = $conn->query($option_sql);

  $options = [];
  while ($option = $option_result->fetch_assoc()) {
    $options[] = [
      "id" => $option['id'],
      "text" => $option['option_text'],
      "votes" => (int)$option['votes']
    ];
  }

  $polls[] = [
    "id" => $poll_id,
    "question" => $poll['question'],
    "created_at" => $poll['created_at'],
    "options" => $options
  ];
}

echo json_encode([
  "success" => true,
  "polls" => $polls
]);
?>
