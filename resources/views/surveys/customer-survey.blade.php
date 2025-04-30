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
            const response = $(`input[name="responses[${questionId}]"]`).val();
            
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
    
    // Form submission handling
    $('#surveyForm').on('submit', function(e) {
        e.preventDefault();
        
        // Set end time right before validation
        const endTime = new Date();
        $('#end_time').val(endTime.toLocaleString('en-US', { timeZone: 'Asia/Singapore' }));
        
        if (!validateForm()) {
            return false;
        }
        
        // Collect form data
        const formData = new FormData(this);
        
        // Submit form via AJAX
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Hide form and show thank you message
                $('#surveyForm').fadeOut('fast', function() {
                    $('.thank-you-message').fadeIn();
                });
                
                // Update response summary
                const summaryData = {
                    account_name: $('#account_name').val(),
                    account_type: $('#account_type').val(),
                    date: $('#date').val(),
                    recommendation: $('#survey-number').val(),
                    comments: $('textarea[name="comments"]').val()
                };
                
                updateResponseSummary(summaryData);
            },
            error: function(xhr) {
                console.error('Error submitting form:', xhr);
                alert('An error occurred while submitting the survey. Please try again.');
            }
        });
    });
    
    // Function to validate all inputs and display errors
    function validateForm() {
        let isValid = true;
        let errorList = [];
        
        // Clear previous error messages
        $('.validation-message').text('');
        $('#validationErrorsList ul').empty();
        $('.modern-input, .modern-select, .modern-textarea, .modern-rating-group, .modern-star-rating').removeClass('error');
        $('.question-card').removeClass('has-error');
        
        // Account name and type are pre-filled and readonly, so no validation needed
        
        // Validate date
        if (!$('#date').val()) {
            isValid = false;
            $('#date').addClass('error');
            $('#date_error').text('Date is required');
            errorList.push('Date is required');
        }
        
        // Validate required questions
        let requiredQuestionsEmpty = false;
        $('.question-card').each(function() {
            const questionId = $(this).data('question-id');
            const isRequired = $(this).find('.badge.required').length > 0;
            const questionText = $(this).find('.question-text').contents().first().text().trim();
            const hasResponse = $(`input[name="responses[${questionId}]"]:checked`).length > 0;
            
            if (isRequired && !hasResponse) {
                isValid = false;
                $(this).addClass('has-error');
                $(`#question_${questionId}_error`).text('This question requires an answer')
                    .addClass('text-danger');
                errorList.push(`Question "${questionText}" requires an answer`);
                requiredQuestionsEmpty = true;
                
                // Add red border to the question card
                $(this).find('.modern-rating-group, .modern-star-rating').addClass('error');
            }
        });
        
        // Validate recommendation
        if (!$('#survey-number').val()) {
            isValid = false;
            $('#survey-number').addClass('error');
            $('#survey-number').parent().addClass('has-error');
            $('#recommendation_error').text('Please select a recommendation rating').addClass('text-danger');
            errorList.push('Recommendation rating is required');
        }
        
        // Show validation errors summary if any
        if (!isValid) {
            errorList.forEach(function(error) {
                $('#validationErrorsList ul').append(`<li>${error}</li>`);
            });
            
            $('#validationAlertContainer').removeClass('d-none').show();
            
            const firstError = $('.error, .has-error').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
                firstError.addClass('shake-animation');
                setTimeout(() => {
                    firstError.removeClass('shake-animation');
                }, 500);
            }
        } else {
            $('#validationAlertContainer').hide();
        }
        
        return isValid;
    }
    
    // Live validation on input change
    $('.modern-input, .modern-select, .modern-textarea').on('input change', function() {
        if ($(this).val().trim()) {
            $(this).removeClass('error');
            $(this).parent().removeClass('has-error');
            const fieldId = $(this).attr('id');
            if (fieldId) {
                $(`#${fieldId}_error`).text('');
            } else if ($(this).attr('name') === 'comments') {
                $('#comments_error').text('');
            }
            
            // Check if all required fields are filled to hide the validation alert
            if ($('.error, .has-error').length === 0) {
                $('#validationAlertContainer').addClass('d-none');
            }
        }
    });
    
    // Live validation for radio buttons
    $('input[type="radio"]').on('change', function() {
        const questionId = $(this).attr('name').match(/\d+/)[0];
        $(`#question_${questionId}_error`).text('');
        $(this).closest('.question-card').removeClass('has-error');
        $(this).closest('.modern-rating-group, .modern-star-rating').removeClass('error');
        
        // Check if all required fields are filled to hide the validation alert
        if ($('.error, .has-error').length === 0) {
            $('#validationAlertContainer').addClass('d-none');
        }
    });
    
    // Add live validation for recommendation select
    $('#survey-number').on('change', function() {
        if ($(this).val()) {
            $(this).removeClass('error');
            $(this).parent().removeClass('has-error');
            $('#recommendation_error').text('');
            
            // Check if all required fields are filled to hide the validation alert
            if ($('.error, .has-error').length === 0) {
                $('#validationAlertContainer').addClass('d-none');
            }
        }
    });
    
    // Form submission with validation
    $('#surveyForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');
        
        // Set end time right before validation
        const endTime = new Date();
        $('#end_time').val(endTime.toISOString());
        
        // First validate the form
        if (!validateForm()) {
            console.log('Form validation failed');
            return false;
        }
        
        // Record end time
        $('#end_time').val(new Date().toLocaleString('en-US', { timeZone: 'Asia/Singapore' }));
        
        // Collect form data
        const formData = new FormData(this);
        const surveyResponses = [];
        
        // Process survey responses
        $('.question-card').each(function() {
            const questionText = $(this).find('.question-text').contents().first().text().trim();
            const questionType = $(this).find('.question-input').children('div').first().hasClass('modern-star-rating') ? 'star' : 'radio';
            const questionId = $(this).data('question-id');
            const rating = $(`input[name="responses[${questionId}]"]:checked`).val();
            
            if (rating) {
                surveyResponses.push({ 
                    questionText, 
                    rating,
                    type: questionType
                });
            }
        });
        
        console.log('Submitting to:', $(this).attr('action'));
        console.log('Form data:', $(this).serialize());
        
        // Use a more direct approach with simpler AJAX setup
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                console.log('Success response:', response);
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
                                <div class="rating-display d-flex align-items-center">
                                    <div class="modern-rating-group me-3">
                                        ${Array.from({length: 5}, (_, i) => {
                                            const isSelected = i + 1 <= response.rating;
                                            return `<div class="modern-radio-display ${isSelected ? 'selected' : ''}">${i + 1}</div>`;
                                        }).join('')}
                                    </div>
                                    <span class="rating-text">${ratingText}</span>
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
                    
                    // Populate recommendation score and comments (with check for empty comments)
                    $('#summary-recommendation').text(formData.get('recommendation'));
                    const commentText = formData.get('comments') || 'No additional feedback provided';
                    $('#summary-comments').text(commentText);
                    
                    // Show thank you message with animation
                    $('.thank-you-message').addClass('show');
                    
                    // Show modal and reset form
                    try {
                        thankYouModal.show();
                    } catch (e) {
                        console.error('Error showing modal:', e);
                        // Fallback to manual showing
                        $('#responseModal').addClass('show').css('display', 'block');
                        $('body').addClass('modal-open').append('<div class="modal-backdrop fade show"></div>');
                    }
                    
                    $('#surveyForm')[0].reset();
                    
                    // Restore pre-filled values
                    $('#account_name').val('{{ $prefillAccountName ?? '' }}');
                    $('#account_type').val('{{ $prefillAccountType ?? '' }}');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error response:', xhr.responseJSON, status, error);
                
                // Clear previous success message and show error message
                $('#successMessage').addClass('d-none');
                $('#errorMessage').removeClass('d-none');
                
                // Check for validation errors from server
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    console.log('Validation errors:', errors);
                    
                    // Build error list for error summary
                    let errorList = [];
                    
                    // Handle field-specific errors
                    for (const field in errors) {
                        if (field.startsWith('responses.')) {
                            // Extract question ID from the field name
                            const questionId = field.split('.')[1];
                            $(`.question-card[data-question-id="${questionId}"]`).addClass('has-error');
                            $(`#question_${questionId}_error`).text(errors[field][0]);
                            errorList.push(errors[field][0]);
                        } else if (field === 'comments') {
                            // Specifically handle comments field error
                            $('textarea[name="comments"]').addClass('error');
                            $('#comments_error').text(errors[field][0]);
                            errorList.push(errors[field][0]);
                        } else {
                            // Handle other form fields
                            $(`#${field}`).addClass('error');
                            $(`#${field}_error`).text(errors[field][0]);
                            errorList.push(errors[field][0]);
                        }
                    }
                    
                    // Update the modal with specific errors
                    $('#errorMessage').html(`
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 48px;"></i>
                        <h4 class="mt-3">Error! Questions are Empty.</h4>
                        <ul class="text-start">
                            ${errorList.map(err => `<li>${err}</li>`).join('')}
                        </ul>
                    `);
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    // Handle specific error message from the server
                    $('#errorMessage').html(`
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 48px;"></i>
                        <h4 class="mt-3">Unable to Submit</h4>
                        <p>${xhr.responseJSON.error}</p>
                    `);
                } else {
                    // General error case
                    $('#errorMessage').html(`
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 48px;"></i>
                        <h4 class="mt-3">Oops!</h4>
                        <p>An unexpected error occurred: ${error || 'Please try again.'}</p>
                    `);
                }
                
                try {
                    thankYouModal.show();
                } catch (e) {
                    console.error('Error showing error modal:', e);
                    // Fallback to manual showing
                    $('#responseModal').addClass('show').css('display', 'block');
                    $('body').addClass('modal-open').append('<div class="modal-backdrop fade show"></div>');
                }
            }
        });
    });
    
    $('#responseModal').on('hidden.bs.modal', function () {
        $('#successMessage').addClass('d-none');
        $('#errorMessage').addClass('d-none');
    });
});

function showResponseSummaryModal() {
    // Use the global summaryModal and thankYouModal variables from the enclosing scope
    try {
        // First hide the response modal if it's open
        if (document.getElementById('responseModal').classList.contains('show')) {
            thankYouModal.hide();
        }
        
        // Then show the summary modal
        summaryModal.show();
    } catch(e) {
        console.error('Error in showResponseSummaryModal:', e);
        
        // Fallback to direct DOM manipulation if the modal methods fail
        $('#responseModal').removeClass('show').css('display', 'none');
        $('.modal-backdrop').remove();
        
        $('#responseSummaryModal').addClass('show').css('display', 'block');
        $('body').addClass('modal-open').append('<div class="modal-backdrop fade show"></div>');
    }
}

function closeNotification(id) {
    document.getElementById(id).style.display = 'none';
}
</script>
@endsection