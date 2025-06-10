<?php
$conn = new mysqli("localhost", "root", "", "eduvote");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'] ?? 'anonymous';
$poll_id = isset($_POST['poll_id']) ? intval($_POST['poll_id']) : 0;
$option_id = isset($_POST['answer']) ? intval($_POST['answer']) : 0;

if ($poll_id > 0 && $option_id > 0) {
  $stmt = $conn->prepare("INSERT INTO poll_votes (username, poll_id, vote_option, voted_at) VALUES (?, ?, ?, NOW())");
  $stmt->bind_param("sii", $username, $poll_id, $option_id);
  $stmt->execute();

  echo "Poll submitted successfully!";
} else {
  echo "Invalid poll or option.";
}
?>
