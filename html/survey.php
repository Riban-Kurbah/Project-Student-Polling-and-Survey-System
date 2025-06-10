<?php
// survey.php

$surveyId = $_GET['survey_id'] ?? null;

if (!$surveyId) {
  echo "Survey ID not provided.";
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Take Survey</title>
  <style>
    body { font-family: Arial; padding: 20px; max-width: 800px; margin: auto; }
    .question { margin-bottom: 20px; }
  </style>
</head>
<body>

<h2>Survey #<?= htmlspecialchars($surveyId) ?></h2>

<div id="surveyContainer">Loading survey...</div>

<script>
  const surveyId = <?= json_encode($surveyId) ?>;

  fetch(`http://localhost/ITA/api/get_survey_by_id.php?survey_id=${surveyId}`)
    .then(res => res.json())
    .then(data => {
      const container = document.getElementById('surveyContainer');
      if (data.success && data.survey) {
        const form = document.createElement('form');
        form.method = "POST";
        form.action = "../api/submit_survey.php";

        form.innerHTML += `<input type="hidden" name="survey_id" value="${data.survey.id}" />`;
        form.innerHTML += `<h3>${data.survey.title}</h3>`;

       data.survey.questions.forEach((q, idx) => {
  let block = `<div class="question"><p><strong>${q.question_text}</strong></p>`;
  q.options.forEach(opt => {
    block += `
      <label>
        <input type="radio" name="answer_${q.id}" value="${opt.id}" required>
        ${opt.option_text}
      </label><br>`;
  });
  block += '</div>';
  form.innerHTML += block;
});


        form.innerHTML += `<button type="submit">Submit Survey</button>`;
        container.innerHTML = '';
        container.appendChild(form);
      } else {
        container.innerHTML = 'Survey not found.';
      }
    })
    .catch(err => {
      console.error(err);
      document.getElementById('surveyContainer').innerText = 'Error loading survey.';
    });
</script>

</body>
</html>
