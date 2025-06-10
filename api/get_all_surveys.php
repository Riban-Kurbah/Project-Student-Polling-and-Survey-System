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

// Get all surveys
$survey_sql = "SELECT * FROM surveys ORDER BY created_at DESC";
$survey_result = $conn->query($survey_sql);

$surveys = [];

while ($survey = $survey_result->fetch_assoc()) {
  $survey_id = $survey['id'];

  // Get questions for this survey
  $question_sql = "SELECT * FROM survey_questions WHERE survey_id = $survey_id";
  $question_result = $conn->query($question_sql);

  $questions = [];

  while ($question = $question_result->fetch_assoc()) {
    $question_id = $question['id'];

    // Get options for this question
    $option_sql = "SELECT * FROM survey_options WHERE question_id = $question_id";
    $option_result = $conn->query($option_sql);

    $options = [];
    while ($option = $option_result->fetch_assoc()) {
      $options[] = [
        "id" => $option['id'],
        "text" => $option['option_text']
      ];
    }

    $questions[] = [
      "id" => $question_id,
      "text" => $question['question_text'],
      "options" => $options
    ];
  }

  $surveys[] = [
    "id" => $survey_id,
    "title" => $survey['title'],
    "created_at" => $survey['created_at'],
    "questions" => $questions
  ];
}

echo json_encode([
  "success" => true,
  "surveys" => $surveys
]);
?>
