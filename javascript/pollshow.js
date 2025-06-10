
fetch('http://localhost/ITA/api/get_all_polls.php')
  .then(response => response.json())
  .then(data => {
    const container = document.getElementById('pollContainer');
    if (data.success && data.polls.length > 0) {
      data.polls.forEach(poll => {
        const deadline = new Date(poll.deadline).toLocaleString("en-IN", { timeZone: "Asia/Kolkata" });
        const pollCard = `
          <div class="poll_card">
            <div class="card-content">
              <div class="status-open">Status: Open</div>
              <h3>${poll.question}</h3>
              <div class="deadline">Last Date: ${deadline}</div>
            </div>
            <a href="poll.php?poll_id=${poll.id}" class="btn">Make Your Contribution</a>
          </div>
        `;
        container.innerHTML += pollCard;
      });
    } else {
      container.innerHTML = '<p>No polls available.</p>';
    }
  })
  .catch(err => {
    console.error('Failed to fetch polls:', err);
    document.getElementById('pollContainer').innerHTML = '<p>Error loading polls.</p>';
  });

