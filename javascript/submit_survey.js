function submitSurvey(surveyId) {
  const responses = [];
  document.querySelectorAll(`input[type="radio"]:checked`).forEach(input => {
    const questionId = input.name.split('_')[1];
    responses.push({ question_id: parseInt(questionId), option_id: parseInt(input.value) });
  });

  fetch('http://localhost/ITA/api/submit_survey.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ username: 'user123', responses })
  })
  .then(res => res.json())
  .then(data => alert(data.message))
  .catch(err => alert('Error submitting survey.'));
}
