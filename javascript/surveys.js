fetch('http://localhost/ITA/api/get_all_surveys.php')
  .then(response => response.json())
  .then(data => {
    const container = document.getElementById('surveyContainer');
    container.innerHTML = '';

    if (data.success && Array.isArray(data.surveys) && data.surveys.length > 0) {
      data.surveys.forEach(survey => {
        const createdAt = new Date(survey.created_at).toLocaleString("en-IN", { timeZone: "Asia/Kolkata" });

        let questionsHtml = '';
        survey.questions.forEach(q => {
          const optionsHtml = q.options.map(opt => `<li>${opt.text}</li>`).join('');
          questionsHtml += `
            <div class="question">
              <p><strong>${q.text}</strong></p>
              <ul>${optionsHtml}</ul>
            </div>
          `;
        });

        const surveyCard = `
          <div class="poll_card">
            <div class="card-content">
              <div class="status-open">Status: Open</div>
              <h3>${survey.title}</h3>
              <div class="deadline">Created At: ${createdAt}</div>
              <div class="questions">${questionsHtml}</div>
            </div>
            <a href="survey.php?survey_id=${survey.id}" class="btn">Take Survey</a>
          </div>
        `;

        container.innerHTML += surveyCard;
      });
    } else {
      container.innerHTML = '<p>No surveys available.</p>';
    }
  })
  .catch(err => {
    console.error('Failed to fetch surveys:', err);
    document.getElementById('surveyContainer').innerHTML = '<p>Error loading surveys.</p>';
  });
