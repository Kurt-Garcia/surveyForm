@extends('layouts.customer')

@section('content')
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
    :root {
        --primary-color: {{ $activeTheme->primary_color ?? '#E53935' }};
        --text-color: {{ $activeTheme->text_color?? '#333' }};
        --secondary-color: {{ $activeTheme->secondary_color ?? '#2C3E50' }};
        --accent-color: {{ $activeTheme->accent_color ?? '#F1C40F' }};
        --button-hover-color: {{ $activeTheme->button_hover_color ?? '#B71C1C' }};
        --body-font: '{{ $activeTheme->body_font ?? "Inter" }}', sans-serif;
        --heading-font: '{{ $activeTheme->heading_font ?? "Inter" }}', sans-serif;
    }

    .input-error {
        border: 2px solid #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220,53,69,.25);
    }
    
    /* Survey metadata styling */
    .survey-metadata {
        margin-top: 10px;
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .survey-metadata .badge {
        font-size: 0.85rem;
        padding: 5px 10px;
        border-radius: 20px;
    }
    
    .survey-metadata .text-muted {
        font-size: 0.85rem;
    }
</style>

<div class="survey-wrapper">
    @if($hasResponded)
        <div class="container">
            @if($allowResubmit)
                <div class="notification-card warning font-theme" id="warningNotification">
                    <i class="fas fa-info-circle me-2"></i>
                    <p>You have previously submitted this survey, but resubmission has been enabled by an administrator. You may submit a new response.</p>
                    <button type="button" class="notification-close" onclick="closeNotification('warningNotification')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @else
                <div class="notification-card info font-theme" id="infoNotification">
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
            <h1 class="survey-title font-theme-heading">{{ strtoupper($survey->title) }}</h1>
            
            @if($survey->sbu || $survey->sites->count() > 0)
            <div class="survey-metadata">
                @if($survey->sbu)
                <span class="badge bg-primary me-2">{{ $survey->sbu->name }}</span>
                @endif
                
                @if($survey->sites->count() > 0)
                <small class="text-muted">
                    <i class="fas fa-map-marker-alt me-1"></i> 
                    @formatSitesList($survey->sites)
                </small>
                @endif
            </div>
            @endif
        </div>

        <form id="surveyForm" method="POST" action="{{ route('customer.survey.submit', $survey) }}" class="modern-form">
            @csrf
            <input type="hidden" name="survey_id" value="{{ $survey->id }}">
            <input type="hidden" name="start_time" id="start_time">
            <input type="hidden" name="end_time" id="end_time">
            <input type="hidden" name="duration" id="duration">
            
            <!-- Validation Alert Container -->
            <div id="validationAlertContainer font-theme" class="alert alert-danger mb-4 d-none">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Please Fill In All Required Fields!</h6>
                <div id="validationErrorsList">
                    <ul></ul>
                </div>
            </div>
            
            <div class="form-grid font-theme">
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
                <h2 class="section-title font-theme-heading">Satisfaction Level</h2>
                <div class="rating-legend font-theme">
                    <span class="rating-item">1 - Poor</span>
                    <span class="rating-item">2 - Needs Improvement</span>
                    <span class="rating-item">3 - Satisfactory</span>
                    <span class="rating-item">4 - Very Satisfactory</span>
                    <span class="rating-item">5 - Excellent</span>
                </div>

                <div class="questions-container font-theme">
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
                <h2 class="section-title font-theme-heading">Recommendation</h2>
                <div class="recommendation-container font-theme">
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
                <h2 class="section-title font-theme-heading">Additional Feedback</h2>
                <textarea class="modern-textarea font-theme" name="comments" rows="5" placeholder="We value your thoughts. Please share any additional feedback..." maxlength="150"></textarea>
                <div class="validation-message" id="comments_error"></div>
            </div>

            <div class="form-footer">
                <button type="submit" class="submit-button font-theme-heading" style="background-color: var(--primary-color); border-color: var(--primary-color); color: white;">
                    <span>Submit Survey</span>
                    <i class="fas fa-paper-plane ms-2"></i>
                </button>
            </div>
        </form>
        
        <div class="thank-you-message font-theme-heading">
            <h3>THANK YOU!</h3>
            <h3>WE APPRECIATE YOUR FEEDBACK!</h3>
            <p>Your input helps us serve you better.</p>
            <button type="button" class="submit-button small-button" onclick="showResponseSummaryModal()" style="background-color: var(--secondary-color); border-color: var(--accent-color); color: white;">
                <span>View Response</span>
                <i class="fas fa-eye ms-2"></i>
            </button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Check if the survey was already submitted
    const surveyId = {{ $survey->id }};
    const accountName = $('#account_name').val();
    const submissionKey = `survey_${surveyId}_${accountName}_submitted`;
    const surveyDataKey = `survey_${surveyId}_${accountName}_data`;
    
    // Check if resubmission is allowed from PHP variable
    const allowResubmit = {{ $allowResubmit ? 'true' : 'false' }};
    
    // Only show the thank you message if the survey was submitted AND resubmission is not allowed
    if (localStorage.getItem(submissionKey) === 'true' && !allowResubmit) {
        // Use dedicated function to display thank you message and hide form
        displayThankYouMessage();
        
        // Retrieve saved data and update summary
        const savedData = JSON.parse(localStorage.getItem(surveyDataKey) || '{}');
        if (Object.keys(savedData).length > 0) {
            updateResponseSummary(savedData);
        }
    } else {
        // Make sure the thank-you-message is hidden when the form is shown
        $('.thank-you-message').removeClass('show').hide();
        
        // If survey was not submitted OR resubmission is allowed, show the form
        // For resubmission, we need to clear the previous submission flag
        if (allowResubmit && localStorage.getItem(submissionKey) === 'true') {
            // Keep the data but reset the submission flag to allow resubmission
            localStorage.removeItem(submissionKey);
        }
        
        // Initialize start time when the form is first loaded
        const startTime = new Date();
        $('#start_time').val(startTime.toLocaleString('en-US', { timeZone: 'Asia/Singapore' }));
    }
    
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
            const response = data.responses ? data.responses[questionId] : null;
            const questionType = $(this).find('.question-input').children('div').first().hasClass('modern-star-rating') ? 'star' : 'radio';
            
            if (response) {
                let ratingHtml = '';
                
                if (questionType === 'star') {
                    // Display stars for star rating questions
                    ratingHtml = Array.from({length: 5}, (_, i) => {
                        const starClass = i < response ? 'text-warning' : 'text-muted';
                        return `<i class="fas fa-star ${starClass}"></i>`;
                    }).join('');
                    ratingHtml += `<span class="ms-2">${response}/5</span>`;
                } else {
                    // Display radio buttons for radio questions
                    const ratingText = {
                        1: 'Poor',
                        2: 'Needs Improvement',
                        3: 'Satisfactory',
                        4: 'Very Satisfactory',
                        5: 'Excellent'
                    }[response];
                    ratingHtml = `
                        <div class="rating-display d-flex flex-wrap align-items-center">
                            <div class="modern-rating-group me-3 mb-2 summary-radio-group">
                                ${Array.from({length: 5}, (_, i) => {
                                    const isSelected = i + 1 === Number(response);
                                    return `<div class="modern-radio-display summary-radio${isSelected ? ' selected' : ''}">${i + 1}</div>`;
                                }).join('')}
                            </div>
                            <span class="rating-text">${ratingText}</span>
                        </div>
                    `;
                }
                
                responsesContainer.append(`
                    <div class="response-item mb-3 p-3 bg-light rounded">
                        <div class="question-text mb-2 fw-bold">${questionText}</div>
                        <div class="rating-wrapper w-100">
                            ${ratingHtml}
                        </div>
                    </div>
                `);
                
                // Add responsive styles for radio buttons if not already added
                if (!$('#responsive-radio-styles').length) {
                    $('head').append(`
                        <style id="responsive-radio-styles">
                            @media (max-width: 576px) {
                                .modern-rating-group, .summary-radio-group {
                                    display: flex;
                                    flex-wrap: wrap;
                                    justify-content: flex-start;
                                    margin-bottom: 0.5rem;
                                    width: 100%;
                                }
                                .modern-radio-display, .summary-radio {
                                    width: 35px;
                                    height: 35px;
                                    margin-right: 5px;
                                    margin-bottom: 5px;
                                }
                                .rating-display {
                                    flex-direction: column;
                                    align-items: flex-start !important;
                                }
                                .rating-text {
                                    margin-top: 0.5rem;
                                }
                            }
                            .summary-radio-group {
                                gap: 4px !important;
                            }
                            .summary-radio {
                                width: 32px;
                                height: 32px;
                                border-radius: 50%;
                                border: 2px solid var(--primary-color);
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin-right: 4px;
                                margin-bottom: 0;
                                background: #fff;
                                color: var(--primary-color);
                                font-weight: 600;
                                font-size: 1.1rem;
                                transition: background 0.2s, color 0.2s, border 0.2s;
                            }
                            .summary-radio.selected {
                                background: var(--primary-color);
                                color: #fff;
                                border-color: var(--primary-color);
                                box-shadow: 0 0 0 2px var(--accent-color);
                            }
                        </style>
                    `);
                }
            }
        });
    }
    
    // Function to show response summary directly
    // Note: We've removed the thank you modal functionality

    // Function to validate all inputs and display errors
    function validateForm() {
        let isValid = true;
        let errorList = [];
        // Clear previous error messages
        $('.validation-message').text('');
        $('#validationErrorsList ul').empty();
        $('.modern-input, .modern-select, .modern-textarea, .modern-rating-group, .modern-star-rating').removeClass('input-error error');
        $('.question-card').removeClass('has-error');
        // Validate account name
        if (!$('#account_name').val().trim()) {
            isValid = false;
            $('#account_name').addClass('input-error error');
            $('#account_name').parent().addClass('has-error');
            $('#account_name_error').text('Account name is required').addClass('text-danger');
            errorList.push('Account name is required');
        }
        // Validate account type
        if (!$('#account_type').val().trim()) {
            isValid = false;
            $('#account_type').addClass('input-error error');
            $('#account_type').parent().addClass('has-error');
            $('#account_type_error').text('Account type is required').addClass('text-danger');
            errorList.push('Account type is required');
        }
        // Validate date
        if (!$('#date').val()) {
            isValid = false;
            $('#date').addClass('input-error error');
            $('#date').parent().addClass('has-error');
            $('#date_error').text('Date is required').addClass('text-danger');
            errorList.push('Date is required');
        }
        // Validate required questions
        $('.question-card').each(function() {
            const questionId = $(this).data('question-id');
            const isRequired = $(this).find('.badge.required').length > 0;
            const questionText = $(this).find('.question-text').contents().first().text().trim();
            const hasResponse = $(`input[name="responses[${questionId}]"]:checked`).length > 0;
            if (isRequired && !hasResponse) {
                isValid = false;
                $(this).addClass('has-error');
                $(`#question_${questionId}_error`).text('This question requires an answer').addClass('text-danger');
                errorList.push(`Question \"${questionText}\" requires an answer`);
                $(this).find('.modern-rating-group, .modern-star-rating').addClass('input-error error');
            }
        });
        // Validate recommendation
        if (!$('#survey-number').val()) {
            isValid = false;
            $('#survey-number').addClass('input-error error');
            $('#survey-number').parent().addClass('has-error');
            $('#recommendation_error').text('Recommendation is required').addClass('text-danger');
            errorList.push('Recommendation is required');
        }
        // Optionally validate comments if required (not required here)
        // Show alert if not valid
        if (!isValid) {
            $('#validationAlertContainer').removeClass('d-none');
            let errorListHtml = '';
            errorList.forEach(function(err) {
                errorListHtml += `<li>${err}</li>`;
            });
            $('#validationErrorsList ul').html(errorListHtml);
        } else {
            $('#validationAlertContainer').addClass('d-none');
        }
        return isValid;
    }
    // AJAX form submission
    $('#surveyForm').on('submit', function(event) {
        event.preventDefault();
        // Set end time
        const endTime = new Date();
        $('#end_time').val(endTime.toLocaleString('en-US', { timeZone: 'Asia/Singapore' }));
        
        // Calculate duration in minutes
        const startTime = new Date($('#start_time').val());
        const durationInMinutes = Math.round((endTime - startTime) / (1000 * 60));
        $('#duration').val(durationInMinutes);

        // Client-side validation
        if (!validateForm()) {
            return;
        }
        
        // Initialize SweetAlert2 with custom styling
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success me-3",
                cancelButton: "btn btn-outline-danger",
                actions: 'gap-2 justify-content-center'
            },
            buttonsStyling: false
        });
        
        // Show confirmation dialog
        swalWithBootstrapButtons.fire({
            title: "Are you sure?",
            text: "Please confirm if you want to submit this survey. You won't be able to modify your answers after submission!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, submit it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // User confirmed, proceed with submission
                const formData = $(this).serialize();
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message with SweetAlert2
                    swalWithBootstrapButtons.fire({
                        title: "Thank You!",
                        text: "Your survey has been successfully submitted.",
                        icon: "success"
                    });
                    
                    // Save form data for response summary
                    const surveyData = {
                        account_name: $('#account_name').val(),
                        account_type: $('#account_type').val(),
                        date: $('#date').val(),
                        recommendation: $('#survey-number').val(),
                        comments: $('textarea[name="comments"]').val(),
                        responses: {}
                    };
                    
                    // Save question responses
                    $('.question-card').each(function() {
                        const questionId = $(this).data('question-id');
                        const response = $(`input[name="responses[${questionId}]"]:checked`).val();
                        if (response) {
                            surveyData.responses[questionId] = response;
                        }
                    });
                    
                    // Save submission data in localStorage
                    localStorage.setItem(submissionKey, 'true');
                    localStorage.setItem(surveyDataKey, JSON.stringify(surveyData));
                    
                    // Update response summary with form data
                    updateResponseSummary(surveyData);
                    
                    // Use dedicated function to display thank you message
                    displayThankYouMessage();
                }
            },
            error: function(xhr) {
                // Show error message with SweetAlert2
                swalWithBootstrapButtons.fire({
                    title: "Error!",
                    text: "There was a problem submitting your survey. Please check your inputs and try again.",
                    icon: "error"
                });
                
                // Try to parse validation errors from server response
                let errors = {};
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errors = xhr.responseJSON.errors;
                }
                // Remove previous error states
                $('.modern-input, .modern-select, .modern-star-rating, .modern-rating-group').removeClass('input-error error');
                $('.validation-message').text('');
                $('#validationAlertContainer').removeClass('d-none');
                let errorListHtml = '';
                // Highlight fields and show messages
                if (Object.keys(errors).length > 0) {
                    Object.keys(errors).forEach(function(key) {
                        // For question responses, key will be like responses.12
                        if (key.startsWith('responses.')) {
                            const qid = key.split('.')[1];
                            $(`#question_${qid}_error`).text(message).addClass('text-danger');
                            $(`.question-card[data-question-id="${qid}"]`).addClass('has-error');
                            $(`.question-card[data-question-id="${qid}"] .modern-rating-group, .question-card[data-question-id="${qid}"] .modern-star-rating`).addClass('input-error error');
                        } else {
                            // For normal fields
                            $('#' + key).addClass('input-error');
                            $('#' + key + '_error').text(errors[key][0]);
                        }
                        errorListHtml += `<li>${errors[key][0]}</li>`;
                    });
                } else {
                    // Fallback generic error
                    errorListHtml = '<li>There was an error submitting the form. Please try again.</li>';
                }
                $('#validationErrorsList ul').html(errorListHtml);
            },
            error: function() {
                swalWithBootstrapButtons.fire({
                    title: "Error!",
                    text: "There was an error submitting the form. Please try again.",
                    icon: "error"
                });
            }
        });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                // User cancelled the submission
                swalWithBootstrapButtons.fire({
                    title: "Cancelled",
                    text: "Your survey has not been submitted. You can continue editing your responses.",
                    icon: "info"
                });
            }
        });
    });
});

/**
 * Display thank you message and hide all form content
 * Dedicated function to ensure consistent behavior across the application
 */
function displayThankYouMessage() {
    // Hide form content keeping only logo, title, and footer
    $('.form-grid, .survey-section, .recommendation-section, .comments-section').hide();
    $('.form-footer').hide();
    
    // Show thank you message
    $('.thank-you-message').addClass('show').css('display', 'flex');
    
    // Create and show a simple footer if needed
    if ($('.survey-footer').length === 0) {
        $('<div class="survey-footer mt-5 text-center font-theme">').html(`
            <p class="text-muted small">© ${new Date().getFullYear()} ${$('.survey-title').text()}. All rights reserved.</p>
        `).insertAfter('.thank-you-message');
    }
}

// Function to validate all inputs and display errors
function validateForm() {
    let isValid = true;
    let errorList = [];
    // Clear previous error messages
    $('.validation-message').text('');
    $('#validationErrorsList ul').empty();
    $('.modern-input, .modern-select, .modern-textarea, .modern-rating-group, .modern-star-rating').removeClass('input-error error');
    $('.question-card').removeClass('has-error');
    // Validate account name
    if (!$('#account_name').val().trim()) {
        isValid = false;
        $('#account_name').addClass('input-error error');
        $('#account_name').parent().addClass('has-error');
        $('#account_name_error').text('Account name is required').addClass('text-danger');
        errorList.push('Account name is required');
    }
    // Validate account type
    if (!$('#account_type').val().trim()) {
        isValid = false;
        $('#account_type').addClass('input-error error');
        $('#account_type').parent().addClass('has-error');
        $('#account_type_error').text('Account type is required').addClass('text-danger');
        errorList.push('Account type is required');
    }
    // Validate date
    if (!$('#date').val()) {
        isValid = false;
        $('#date').addClass('input-error error');
        $('#date').parent().addClass('has-error');
        $('#date_error').text('Date is required').addClass('text-danger');
        errorList.push('Date is required');
    }
    // Validate required questions
    $('.question-card').each(function() {
        const questionId = $(this).data('question-id');
        const isRequired = $(this).find('.badge.required').length > 0;
        const questionText = $(this).find('.question-text').contents().first().text().trim();
        const hasResponse = $(`input[name="responses[${questionId}]"]:checked`).length > 0;
        if (isRequired && !hasResponse) {
            isValid = false;
            $(this).addClass('has-error');
            $(`#question_${questionId}_error`).text('This question requires an answer').addClass('text-danger');
            errorList.push(`Question \"${questionText}\" requires an answer`);
            $(this).find('.modern-rating-group, .modern-star-rating').addClass('input-error error');
        }
    });
    // Validate recommendation
    if (!$('#survey-number').val()) {
        isValid = false;
        $('#survey-number').addClass('input-error error');
        $('#survey-number').parent().addClass('has-error');
        $('#recommendation_error').text('Recommendation is required').addClass('text-danger');
        errorList.push('Recommendation is required');
    }
    // Optionally validate comments if required (not required here)
    // Show alert if not valid
    if (!isValid) {
        $('#validationAlertContainer').removeClass('d-none');
        let errorListHtml = '';
        errorList.forEach(function(err) {
            errorListHtml += `<li>${err}</li>`;
        });
        $('#validationErrorsList ul').html(errorListHtml);
    } else {
        $('#validationAlertContainer').addClass('d-none');
    }
    return isValid;
}

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
        const response = data.responses ? data.responses[questionId] : null;
        const questionType = $(this).find('.question-input').children('div').first().hasClass('modern-star-rating') ? 'star' : 'radio';
        
        if (response) {
            let ratingHtml = '';
            
            if (questionType === 'star') {
                // Display stars for star rating questions
                ratingHtml = Array.from({length: 5}, (_, i) => {
                    const starClass = i < response ? 'text-warning' : 'text-muted';
                    return `<i class="fas fa-star ${starClass}"></i>`;
                }).join('');
                ratingHtml += `<span class="ms-2">${response}/5</span>`;
            } else {
                // Display radio buttons for radio questions
                const ratingText = {
                    1: 'Poor',
                    2: 'Needs Improvement',
                    3: 'Satisfactory',
                    4: 'Very Satisfactory',
                    5: 'Excellent'
                }[response];
                ratingHtml = `
                    <div class="rating-display d-flex flex-wrap align-items-center">
                        <div class="modern-rating-group me-3 mb-2">
                            ${Array.from({length: 5}, (_, i) => {
                                const isSelected = i + 1 <= response;
                                return `<div class="modern-radio-display ${isSelected ? 'selected' : ''}">${i + 1}</div>`;
                            }).join('')}
                        </div>
                        <span class="rating-text">${ratingText}</span>
                    </div>
                `;
            }
            
            responsesContainer.append(`
                <div class="response-item mb-3 p-3 bg-light rounded">
                    <div class="question-text mb-2 fw-bold">${questionText}</div>
                    <div class="rating-wrapper w-100">
                        ${ratingHtml}
                    </div>
                </div>
            `);
            
            // Add responsive styles for radio buttons if not already added
            if (!$('#responsive-radio-styles').length) {
                $('head').append(`
                    <style id="responsive-radio-styles">
                        @media (max-width: 576px) {
                            .modern-rating-group {
                                display: flex;
                                flex-wrap: wrap;
                                justify-content: flex-start;
                                margin-bottom: 0.5rem;
                                width: 100%;
                            }
                            .modern-radio-display {
                                width: 35px;
                                height: 35px;
                                margin-right: 5px;
                                margin-bottom: 5px;
                            }
                            .rating-display {
                                flex-direction: column;
                                align-items: flex-start !important;
                            }
                            .rating-text {
                                margin-top: 0.5rem;
                            }
                        }
                    </style>
                `);
            }
        }
    });
}

// Function to show response summary modal
function showResponseSummaryModal() {
    // Show response summary modal directly
    $('#responseSummaryModal').modal('show');
}
</script>

<!-- Thank You Modal has been removed -->

<!-- Response Summary Modal -->
<div class="modal fade" id="responseSummaryModal" tabindex="-1" aria-labelledby="responseSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content font-theme">
            <div class="modal-header">
                <h5 class="modal-title font-theme-heading" id="responseSummaryModalLabel">Survey Response Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="responseSummary">
                    <h5 class="border-bottom pb-2 font-theme-heading">Account Information</h5>
                    <div class="row mb-4">
                        <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
                            <strong>Account Name:</strong>
                            <p id="summary-account-name"></p>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-3 mb-md-0">
                            <strong>Account Type:</strong>
                            <p id="summary-account-type"></p>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <strong>Date:</strong>
                            <p id="summary-date"></p>
                        </div>
                    </div>
                    
                    <h5 class="border-bottom pb-2 font-theme-heading">Survey Responses</h5>
                    <div id="summary-responses" class="mb-4">
                        <!-- Responses will be dynamically inserted here -->
                    </div>
                    
                    <h5 class="border-bottom pb-2 font-theme-heading">Recommendation Score</h5>
                    <div class="mb-4">
                        <p>How likely to recommend: <span id="summary-recommendation"></span>/10</p>
                    </div>
                    
                    <h5 class="border-bottom pb-2 font-theme-heading">Additional Comments</h5>
                    <div class="mb-4">
                        <p id="summary-comments" style="word-wrap: break-word; overflow-wrap: break-word; max-width: 100%;"></p>
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
    .font-theme{
        font-family: var(--body-font);
    }

    .font-theme-heading{
        font-family: var(--heading-font);
    }
</style>
@endsection