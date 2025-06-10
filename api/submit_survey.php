<?php
$conn = new mysqli("localhost", "root", "", "eduvote");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'] ?? 'anonymous';
$survey_id = intval($_POST['survey_id']);

// Loop through all responses
foreach ($_POST as $key => $value) {
  if (strpos($key, 'q_') === 0) {
    $question_id = intval(str_replace('q_', '', $key));
    $option_text = $conn->real_escape_string($value);

    $stmt = $conn->prepare("INSERT INTO survey_votes (username, survey_id, question_id, vote_option, voted_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("siis", $username, $survey_id, $question_id, $option_text);
    $stmt->execute();
  }
}

echo "Survey submitted successfully!";
?>
