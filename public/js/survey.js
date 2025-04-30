document.addEventListener('DOMContentLoaded', function() {
    const surveyForm = document.getElementById('surveyForm');
    const thankYouMessage = document.querySelector('.thank-you-message');

    if (surveyForm) {
        surveyForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide the form
                    surveyForm.style.display = 'none';
                    
                    // Show and animate the thank you message
                    if (thankYouMessage) {
                        thankYouMessage.classList.add('show');
                        thankYouMessage.scrollIntoView({ behavior: 'smooth' });
                    }
                } else {
                    // Handle error case
                    alert('There was an error submitting your response. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('There was an error submitting your response. Please try again.');
            });
        });
    }
});