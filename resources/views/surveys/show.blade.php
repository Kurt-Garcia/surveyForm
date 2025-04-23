@extends('layouts.app-user')

@section('title', $survey->title)

@section('content')
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<div class="survey-wrapper">
    @if($hasResponded)
        <div class="container">
            @if($allowResubmit)
                <div class="notification-card warning" id="warningNotification">
                    <i class="fas fa-info-circle me-2"></i>
                    <p>You have previously submitted this survey, but resubmission has been enabled by an administrator. You may submit a new response.</p>
                    <button type="button" class="notification-close" onclick="closeNotification('warningNotification')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @else
                <div class="notification-card info" id="infoNotification">
                    <i class="fas fa-info-circle me-2"></i>
                    <p>You have already submitted this survey. You can view the questions, but submitting again with the same account name will not be allowed.</p>
                    <button type="button" class="notification-close" onclick="closeNotification('infoNotification')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif
        </div>
    @endif

    <div class="survey-container">
        <div class="survey-header">
            <a href="{{ route('index') }}" class="close-button">
                <i class="fas fa-times"></i>
            </a>
            <img src="{{ asset('img/logo.JPG') }}" alt="Logo" class="survey-logo">
            <h1 class="survey-title">{{ strtoupper($survey->title) }}</h1>
        </div>

        <form id="surveyForm" method="POST" action="{{ route('surveys.store', $survey) }}" class="modern-form">
            @csrf
            <input type="hidden" name="survey_id" value="{{ $survey->id }}">
            <input type="hidden" name="start_time" id="start_time">
            <input type="hidden" name="end_time" id="end_time">
            
            <div class="form-grid">
                <div class="form-field">
                    <label for="account_name" class="form-label">Account Name</label>
                    <input type="text" class="modern-input" id="account_name" name="account_name" required>
                </div>
                <div class="form-field">
                    <label for="account_type" class="form-label">Account Type</label>
                    <input type="text" class="modern-input" id="account_type" name="account_type" required>
                </div>
                <div class="form-field">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="modern-input" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="survey-section">
                <h2 class="section-title">Satisfaction Level</h2>
                <div class="rating-legend">
                    <span class="rating-item">1 - Poor</span>
                    <span class="rating-item">2 - Needs Improvement</span>
                    <span class="rating-item">3 - Satisfactory</span>
                    <span class="rating-item">4 - Very Satisfactory</span>
                    <span class="rating-item">5 - Excellent</span>
                </div>

                <div class="questions-container">
                    @foreach($questions as $question)
                    <div class="question-card">
                        <div class="question-text">
                            {{ $question->text }}
                            @if($question->required)
                                <span class="badge required">Required</span>
                            @else
                                <span class="badge optional">Optional</span>
                            @endif
                        </div>
                        <div class="question-input">
                            @switch($question->type)
                                @case('radio')
                                    <div class="modern-rating-group">
                                        @for($i = 1; $i <= 5; $i++)
                                            <div class="modern-radio">
                                                <input type="radio" 
                                                    id="q{{ $question->id }}_rating{{ $i }}" 
                                                    name="responses[{{ $question->id }}]" 
                                                    value="{{ $i }}" 
                                                    {{ $question->required ? 'required' : '' }}>
                                                <label for="q{{ $question->id }}_rating{{ $i }}">
                                                    <span class="radio-number">{{ $i }}</span>
                                                </label>
                                            </div>
                                        @endfor
                                    </div>
                                    @break
                                @case('star')
                                    <div class="modern-star-rating">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" 
                                                id="star{{ $question->id }}_{{ $i }}" 
                                                name="responses[{{ $question->id }}]" 
                                                value="{{ $i }}" 
                                                {{ $question->required ? 'required' : '' }}>
                                            <label for="star{{ $question->id }}_{{ $i }}" class="star-label"></label>
                                        @endfor
                                    </div>
                                    @break
                            @endswitch
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="recommendation-section mt-5">
                <h2 class="section-title">Recommendation</h2>
                <div class="recommendation-container">
                    <p>How likely is it that you would recommend our company to a friend or colleague?</p>
                    <select id="survey-number" name="recommendation" class="modern-select" required>
                        <option value="" disabled selected>Select a rating (1-10)</option>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="comments-section mt-5">
                <h2 class="section-title">Additional Feedback</h2>
                <textarea class="modern-textarea" name="comments" rows="5" placeholder="We value your thoughts. Please share any additional feedback..." required></textarea>
            </div>

            @foreach($questions as $question)
                @error('responses.' . $question->id)
                    <div class="error-message">
                        {{ $message }}
                    </div>
                @enderror
            @endforeach

            <div class="form-footer">
                <button type="submit" class="submit-button">
                    <span>Submit Survey</span>
                    <i class="fas fa-paper-plane ms-2"></i>
                </button>
            </div>
        </form>
        
        <div class="thank-you-message">
            <h3>WE APPRECIATE YOUR FEEDBACK!</h3>
            <p>Your input helps us serve you better.</p>
        </div>
    </div>
</div>

<!-- Thank You Modal -->
<div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalLabel">Survey Submitted</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="successMessage" class="mb-4 d-none">
                    <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                    <h4 class="mt-3">Thank you for your feedback!</h4>
                    <p>Your response has been successfully submitted.</p>
                    <button type="button" class="btn btn-primary mt-3" onclick="showResponseSummaryModal()">View Response</button>
                </div>
                <div id="errorMessage" class="d-none">
                    <i class="fas fa-exclamation-circle text-danger" style="font-size: 48px;"></i>
                    <h4 class="mt-3">Oops!</h4>
                    <p>An error occurred. Please try again.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Response Summary Modal -->
<div class="modal fade" id="responseSummaryModal" tabindex="-1" aria-labelledby="responseSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseSummaryModalLabel">Survey Response Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="responseSummary">
                    <h5 class="border-bottom pb-2">Account Information</h5>
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <strong>Account Name:</strong>
                            <p id="summary-account-name"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Account Type:</strong>
                            <p id="summary-account-type"></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Date:</strong>
                            <p id="summary-date"></p>
                        </div>
                    </div>
                    
                    <h5 class="border-bottom pb-2">Survey Responses</h5>
                    <div id="summary-responses" class="mb-4">
                        <!-- Responses will be dynamically inserted here -->
                    </div>
                    
                    <h5 class="border-bottom pb-2">Recommendation Score</h5>
                    <div class="mb-4">
                        <p>How likely to recommend: <span id="summary-recommendation"></span>/10</p>
                    </div>
                    
                    <h5 class="border-bottom pb-2">Additional Comments</h5>
                    <div class="mb-4">
                        <p id="summary-comments"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --primary-color: #4A90E2;
        --secondary-color: #2C3E50;
        --success-color: #2ECC71;
        --warning-color: #F1C40F;
        --danger-color: #E74C3C;
        --text-color: #2C3E50;
        --background-color: #F8FAFC;
        --card-background: #FFFFFF;
        --border-radius: 12px;
        --transition: all 0.3s ease;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--background-color);
        color: var(--text-color);
    }

    .survey-wrapper {
        min-height: 100vh;
        padding: 2rem 1rem;
    }

    .survey-container {
        max-width: 1200px;
        margin: 0 auto;
        background: var(--card-background);
        border-radius: var(--border-radius);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }

    .survey-header {
        text-align: center;
        position: relative;
        padding: 2rem 0;
    }

    .survey-logo {
        max-width: 180px;
        margin-bottom: 1.5rem;
        transition: var(--transition);
    }

    .survey-logo:hover {
        transform: scale(1.05);
    }

    .survey-title {
        font-size: 2.5rem;
        color: var(--secondary-color);
        font-weight: 700;
        margin-bottom: 2rem;
    }

    .close-button {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--secondary-color);
        cursor: pointer;
        transition: var(--transition);
    }

    .close-button:hover {
        transform: rotate(90deg);
        color: var(--primary-color);
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .form-field {
        position: relative;
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
        color: var(--secondary-color);
    }

    .modern-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #E2E8F0;
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: var(--transition);
    }

    .modern-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        outline: none;
    }

    .notification-card {
        display: flex;
        align-items: center;
        padding: 1rem;
        border-radius: var(--border-radius);
        margin-bottom: 1.5rem;
        animation: slideIn 0.3s ease;
    }

    .notification-card.warning {
        background-color: #FEF9E7;
        border-left: 4px solid var(--warning-color);
    }

    .notification-card.info {
        background-color: #EBF5FB;
        border-left: 4px solid var(--primary-color);
    }

    .notification-close {
        margin-left: auto;
        background: none;
        border: none;
        color: inherit;
        cursor: pointer;
    }

    .section-title {
        font-size: 1.5rem;
        color: var(--secondary-color);
        margin-bottom: 1.5rem;
        text-align: center;
        font-weight: 600;
    }

    .rating-legend {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
        padding: 1rem;
        background: #F8FAFC;
        border-radius: var(--border-radius);
    }

    .rating-item {
        font-size: 0.875rem;
        color: var(--secondary-color);
        padding: 0.5rem 1rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .question-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        transition: var(--transition);
    }

    .question-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .question-text {
        margin-bottom: 1rem;
        font-size: 1.1rem;
        color: var(--secondary-color);
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        margin-left: 0.5rem;
    }

    .badge.required {
        background-color: #FECACA;
        color: #991B1B;
    }

    .badge.optional {
        background-color: #E5E7EB;
        color: #374151;
    }

    .modern-rating-group {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    .modern-radio input {
        display: none;
    }

    .modern-radio label {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid #E2E8F0;
        cursor: pointer;
        transition: var(--transition);
    }

    .modern-radio input:checked + label {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }

    .modern-star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 0.5rem;
    }

    .modern-star-rating input {
        display: none;
    }

    .modern-star-rating label.star-label {
        cursor: pointer;
        width: 35px;
        height: 35px;
        background: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="%23E2E8F0"/></svg>') no-repeat center;
        background-size: contain;
        transition: transform 0.2s ease;
    }

    .modern-star-rating input:checked ~ label.star-label,
    .modern-star-rating label.star-label:hover,
    .modern-star-rating label.star-label:hover ~ label.star-label {
        background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="%23FFD700"/></svg>');
        transform: scale(1.1);
    }

    .modern-select {
        width: 100%;
        max-width: 200px;
        padding: 0.75rem 1rem;
        border: 2px solid #E2E8F0;
        border-radius: var(--border-radius);
        font-size: 1rem;
        transition: var(--transition);
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%232C3E50'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1.5rem;
    }

    .modern-textarea {
        width: 100%;
        padding: 1rem;
        border: 2px solid #E2E8F0;
        border-radius: var(--border-radius);
        resize: vertical;
        min-height: 150px;
        font-size: 1rem;
        transition: var(--transition);
    }

    .modern-textarea:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        outline: none;
    }

    .form-footer {
        text-align: center;
        margin-top: 3rem;
    }

    .submit-button {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        border-radius: var(--border-radius);
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .submit-button:hover {
        background-color: #357ABD;
        transform: translateY(-2px);
    }

    .thank-you-message {
        text-align: center;
        margin-top: 3rem;
        padding: 2rem;
        background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
        border-radius: var(--border-radius);
        color: white;
    }

    .thank-you-message h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .thank-you-message p {
        opacity: 0.9;
    }

    .error-message {
        color: var(--danger-color);
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .survey-container {
            padding: 1rem;
        }

        .survey-title {
            font-size: 2rem;
        }

        .rating-legend {
            flex-direction: column;
            align-items: center;
        }

        .modern-rating-group {
            gap: 0.5rem;
        }

        .modern-radio label {
            width: 35px;
            height: 35px;
        }
    }

    .rating-display {
        display: flex;
        align-items: center;
    }

    .rating-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: var(--primary-color);
        color: white;
        font-weight: 600;
    }

    .rating-text {
        color: var(--secondary-color);
        font-weight: 500;
    }

    .text-warning {
        color: #FFD700 !important;
    }

    .response-item {
        border: 1px solid rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }

    .response-item:hover {
        transform: translateY(-2px);
    }
    
    .rating-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .rating-wrapper .fas {
        font-size: 1.2rem;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script>
$(document).ready(function() {
    // Record start time when page loads
    $('#start_time').val(new Date().toISOString());
    
    const thankYouModal = new bootstrap.Modal(document.getElementById('responseModal'));
    const summaryModal = new bootstrap.Modal(document.getElementById('responseSummaryModal'));
    
    $('#surveyForm').on('submit', function(e) {
        e.preventDefault();
        
        // Record end time
        $('#end_time').val(new Date().toISOString());
        
        // Collect form data
        const formData = new FormData(this);
        const surveyResponses = [];
        
        // Process survey responses
        $('.question-card').each(function() {
            const questionText = $(this).find('.question-text').contents().first().text().trim();
            const questionType = $(this).find('.question-input').children('div').first().hasClass('modern-star-rating') ? 'star' : 'radio';
            const questionId = $(this).find('input[type="radio"]').first().attr('name').match(/\d+/)[0];
            const rating = $(`input[name="responses[${questionId}]"]:checked`).val();
            
            if (rating) {
                surveyResponses.push({ 
                    questionText, 
                    rating,
                    type: questionType
                });
            }
        });

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#successMessage').removeClass('d-none');
                    $('#errorMessage').addClass('d-none');
                    
                    // Populate account information
                    $('#summary-account-name').text(formData.get('account_name'));
                    $('#summary-account-type').text(formData.get('account_type'));
                    $('#summary-date').text(formData.get('date'));
                    
                    // Populate survey responses with appropriate styling
                    const responsesHtml = surveyResponses.map(response => {
                        let ratingHtml = '';
                        if (response.type === 'star') {
                            ratingHtml = Array.from({length: 5}, (_, i) => {
                                const starClass = i < response.rating ? 'text-warning' : 'text-muted';
                                return `<i class="fas fa-star ${starClass}"></i>`;
                            }).join('');
                            ratingHtml += `<span class="ms-2">${response.rating}/5</span>`;
                        } else {
                            const ratingText = {
                                1: 'Poor',
                                2: 'Needs Improvement',
                                3: 'Satisfactory',
                                4: 'Very Satisfactory',
                                5: 'Excellent'
                            }[response.rating];
                            ratingHtml = `
                                <div class="rating-display">
                                    <span class="rating-number">${response.rating}</span>
                                    <span class="rating-text ms-2">${ratingText}</span>
                                </div>
                            `;
                        }

                        return `
                            <div class="response-item mb-3 p-3 bg-light rounded">
                                <div class="question-text mb-2 fw-bold">${response.questionText}</div>
                                <div class="rating-wrapper">
                                    ${ratingHtml}
                                </div>
                            </div>
                        `;
                    }).join('');
                    
                    $('#summary-responses').html(responsesHtml);
                    
                    // Populate recommendation score and comments
                    $('#summary-recommendation').text(formData.get('recommendation'));
                    $('#summary-comments').text(formData.get('comments'));
                    
                    thankYouModal.show();
                    $('#surveyForm')[0].reset();
                }
            },
            error: function(xhr) {
                $('#successMessage').addClass('d-none');
                $('#errorMessage').removeClass('d-none');
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    $('#errorMessage').html(`
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 48px;"></i>
                        <h4 class="mt-3">Unable to Submit</h4>
                        <p>${xhr.responseJSON.error}</p>
                        <div class="mt-3">
                            <a href="{{ route('index') }}" class="btn btn-primary">Back to Survey</a>
                        </div>
                    `);
                }
                thankYouModal.show();
            }
        });
    });
    
    $('#responseModal').on('hidden.bs.modal', function () {
        $('#successMessage').addClass('d-none');
        $('#errorMessage').addClass('d-none');
    });
});

function showResponseSummaryModal() {
    $('#responseModal').modal('hide');
    $('#responseSummaryModal').modal('show');
}

function closeNotification(id) {
    document.getElementById(id).style.display = 'none';
}
</script>
@endsection