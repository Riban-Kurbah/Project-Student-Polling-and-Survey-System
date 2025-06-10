const addOption = () => {
      const div = document.createElement('div');
      div.classList.add('option-group');
      div.innerHTML = `<input type="text" name="options[]" placeholder="Another option" required>`;
      document.getElementById('optionsContainer').appendChild(div);
    };

    document.getElementById('pollForm').addEventListener('submit', async (e) => {
      e.preventDefault();

      const question = document.getElementById('question').value.trim();
      const optionInputs = document.querySelectorAll('input[name="options[]"]');
      const options = Array.from(optionInputs).map(input => input.value.trim()).filter(Boolean);

      if (!question || options.length < 2) {
        alert('Please enter a question and at least two options.');
        return;
      }

    const res = await fetch('http://localhost/ITA/api/create_poll.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ question, options })
});

const text = await res.text(); // get raw response
console.log('Raw response:', text);

const data = JSON.parse(text); // this will throw if not valid JSON

    });