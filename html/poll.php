<?php
$conn = new mysqli("localhost", "root", "", "eduvote");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'] ?? 'anonymous';
$poll_id = intval($_POST['poll_id']);
$option_id = intval($_POST['answer']);

if (!$poll_id || !$option_id) {
  die("Invalid poll or option.");
}

$stmt = $conn->prepare("INSERT INTO poll_votes (username, poll_id, vote_option, voted_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("sii", $username, $poll_id, $option_id);
$stmt->execute();

echo "Poll vote submitted successfully!";
?>
