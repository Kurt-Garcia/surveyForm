@extends('layouts.customer')

@section('content')
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
            @if($survey->logo)
            <img src="{{ asset('storage/' . $survey->logo) }}" alt="{{ $survey->title }} Logo" class="survey-logo">
            @else
            <img src="{{ asset('img/logo.JPG') }}" alt="Default Logo" class="survey-logo">
            @endif
            <h1 class="survey-title">{{ strtoupper($survey->title) }}</h1>
        </div>

        <form id="surveyForm" method="POST" action="{{ route('customer.survey.submit', $survey) }}" class="modern-form">
            @csrf
            <input type="hidden" name="survey_id" value="{{ $survey->id }}">
            <input type="hidden" name="start_time" id="start_time">
            <input type="hidden" name="end_time" id="end_time">
            
            <!-- Validation Alert Container -->
            <div id="validationAlertContainer" class="alert alert-danger mb-4 d-none">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Please Fill In All Required Fields!</h6>
                <div id="validationErrorsList">
                    <ul></ul>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="form-field">
                    <label for="account_name" class="form-label">Account Name</label>
                    <input type="text" class="modern-input" id="account_name" name="account_name" value="{{ $prefillAccountName ?? '' }}" readonly>
                    <div class="validation-message" id="account_name_error"></div>
                </div>
                <div class="form-field">
                    <label for="account_type" class="form-label">Account Type</label>
                    <input type="text" class="modern-input" id="account_type" name="account_type" value="{{ $prefillAccountType ?? '' }}" readonly>
                    <div class="validation-message" id="account_type_error"></div>
                </div>
                <div class="form-field">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="modern-input" id="date" name="date" value="{{ date('Y-m-d') }}" readonly>
                    <div class="validation-message" id="date_error"></div>
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
                    <div class="question-card" data-question-id="{{ $question->id }}">
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
                                                    value="{{ $i }}">
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
                                                value="{{ $i }}">
                                            <label for="star{{ $question->id }}_{{ $i }}" class="star-label"></label>
                                        @endfor
                                    </div>
                                    @break
                            @endswitch
                        </div>
                        <div class="validation-message" id="question_{{ $question->id }}_error"></div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="recommendation-section mt-5">
                <h2 class="section-title">Recommendation</h2>
                <div class="recommendation-container">
                    <h6>How likely is it that you would recommend our company to a friend or colleague?</h6>
                    <select id="survey-number" name="recommendation" class="modern-select">
                        <option value="" disabled selected>Select a rating</option>
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    <div class="validation-message" id="recommendation_error"></div>
                </div>
            </div>

            <div class="comments-section mt-5">
                <h2 class="section-title">Additional Feedback</h2>
                <textarea class="modern-textarea" name="comments" rows="5" placeholder="We value your thoughts. Please share any additional feedback..."></textarea>
                <div class="validation-message" id="comments_error"></div>
            </div>

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
            <button type="button" class="submit-button small-button" onclick="showResponseSummaryModal()">
                <span>View Response</span>
                <i class="fas fa-eye ms-2"></i>
            </button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize start time when the form is first loaded
    const startTime = new Date();
    $('#start_time').val(startTime.toLocaleString('en-US', { timeZone: 'Asia/Singapore' }));
    
    // Function to close notification
    window.closeNotification = function(notificationId) {
        $(`#${notificationId}`).fadeOut();
    };
    
    // Function to update response summary
    function updateResponseSummary(data) {
        $('#summary-account-name').text(data.account_name);
        $('#summary-account-type').text(data.account_type);
        $('#summary-date').text(data.date);
        $('#summary-recommendation').text(data.recommendation);
        $('#summary-comments').text(data.comments || 'No additional comments provided.');
        
        // Clear and update responses
        const responsesContainer = $('#summary-responses');
        responsesContainer.empty();
        
        $('.question-card').each(function() {
            const questionId = $(this).data('question-id');
            const questionText = $(this).find('.question-text').contents().first().text().trim();
            const response = $(`input[name="responses[${questionId}]"]:checked`).val();
            
            if (response) {
                responsesContainer.append(`
                    <div class="mb-3">
                        <strong>${questionText}</strong>
                        <p>Rating: ${response}/5</p>
                    </div>
                `);
            }
        });
    }
    
    // Function to show thank you modal
    function showThankYouModal() {
        $('#thankYouModal').modal('show');
    }

    // AJAX form submission
    $('#surveyForm').on('submit', function(event) {
        event.preventDefault();
        
        // Set end time
        const endTime = new Date();
        $('#end_time').val(endTime.toLocaleString('en-US', { timeZone: 'Asia/Singapore' }));
        
        const formData = $(this).serialize();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Save form data for response summary
                    const surveyData = {
                        account_name: $('#account_name').val(),
                        account_type: $('#account_type').val(),
                        date: $('#date').val(),
                        recommendation: $('#survey-number').val(),
                        comments: $('textarea[name="comments"]').val()
                    };
                    
                    // Update response summary with form data
                    updateResponseSummary(surveyData);
                    
                    // Show thank you message
                    $('.thank-you-message').addClass('show');
                    
                    showThankYouModal();
                }
            },
            error: function() {
                alert('There was an error submitting the form. Please try again.');
            }
        });
    });
});

// Function to show response summary modal
function showResponseSummaryModal() {
    // Hide thank you modal if it's open
    $('#thankYouModal').modal('hide');
    
    // Show response summary modal
    $('#responseSummaryModal').modal('show');
}
</script>

<!-- Thank You Modal -->
<div class="modal fade" id="thankYouModal" tabindex="-1" aria-labelledby="thankYouModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="thankYouModalLabel">Thank You!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Thank you for submitting the survey. Your feedback is valuable to us.
                <div class="text-center mt-3">
                    <button type="button" class="btn btn-primary" onclick="showResponseSummaryModal()">
                        View Your Response
                    </button>
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
@endsection