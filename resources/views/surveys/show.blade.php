@extends('layouts.app-user')

@section('title', $survey->title)

@section('content')
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

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
            <a href="#" class="close-button" onclick="confirmClose(event)">
                <i class="fas fa-times"></i>
            </a>
            <script>
            function confirmClose(event) {
                event.preventDefault();
                if (confirm('Are you sure you want to close this form? Any unsaved changes will be lost.')) {
                    window.location.href = '{{ route("index") }}';
                }
            }
            </script>
            @if($survey->logo)
            <img src="{{ asset('storage/' . $survey->logo) }}" alt="{{ $survey->title }} Logo" class="survey-logo">
            @else
            <img src="{{ asset('img/logo.JPG') }}" alt="Default Logo" class="survey-logo">
            @endif
            <h1 class="survey-title">{{ strtoupper($survey->title) }}</h1>
        </div>

        <form id="surveyForm" method="POST" action="{{ route('surveys.store', $survey) }}" class="modern-form">
            @csrf
            <input type="hidden" name="survey_id" value="{{ $survey->id }}">
            <input type="hidden" name="start_time" id="start_time">
            <input type="hidden" name="end_time" id="end_time">
            
            <!-- Validation Alert Container -->
            <div id="validationAlertContainer" class="alert alert-danger mb-4 d-none">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Please Fill In All Required Fields!</h6>
            </div>
            
            <div class="form-grid">
                <div class="form-field">
                    <label for="account_name" class="form-label">Account Name</label>
                    <input type="text" class="modern-input" id="account_name" name="account_name" value="{{ $prefillAccountName ?? '' }}" placeholder="Enter customer name or code">
                    <div id="customer_name_display" class="customer-name-display mt-1"></div>
                    <div class="validation-message" id="account_name_error"></div>
                </div>
                <div class="form-field">
                    <label for="account_type" class="form-label">Account Type</label>
                    <input type="text" class="modern-input" id="account_type" name="account_type" value="{{ $prefillAccountType ?? '' }}" readonly>
                    <div class="validation-message" id="account_type_error"></div>
                </div>
                <div class="form-field">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="modern-input" id="date" name="date" value="{{ date('Y-m-d') }}">
                    <div class="validation-message" id="date_error"></div>
                </div>
            </div>

            <div id="copyLinkSection" class="mb-4 d-none">
                <button type="button" id="copyLinkBtn" class="btn btn-outline-primary">
                    <i class="fas fa-link me-2"></i>Copy Link for Customer
                </button>
                <span id="copySuccess" class="text-success ms-2 d-none"><i class="fas fa-check-circle"></i> Link copied!</span>
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
            <button type="button" class="submit-button small-button" onclick="showResponseSummaryModal()">
                <span>View Response</span>
                <i class="fas fa-eye ms-2"></i>
            </button>
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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize start time when the form is first loaded
    const startTime = new Date();
    $('#start_time').val(startTime.toLocaleString('en-US', { timeZone: 'Asia/Singapore' }));
    
    const thankYouModal = new bootstrap.Modal(document.getElementById('responseModal'));
    const summaryModal = new bootstrap.Modal(document.getElementById('responseSummaryModal'));
    
    // Show Copy Link button when both account name and account type have values
    function updateCopyLinkVisibility() {
        const accountName = $('#account_name').val().trim();
        const accountType = $('#account_type').val().trim();
        
        // Hide copy link section if either field is empty
        if (!accountName || !accountType) {
            $('#copyLinkSection').addClass('d-none');
            $('#copySuccess').addClass('d-none');
            $('#account_name_error').text('');
            return;
        }
        
        // Check if account name exists
        $.ajax({
            url: `{{ route('check.account.exists') }}`,
            method: 'POST',
            data: {
                account_name: accountName,
                survey_id: {{ $survey->id }},
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.exists) {
                    // Account exists, show message and hide copy link
                    $('#copyLinkSection').addClass('d-none');
                    $('#account_name_error').text('This account has already submitted a response.')
                        .addClass('text-warning')
                        .removeClass('text-danger');
                } else {
                    // Account doesn't exist, show copy link
                    $('#copyLinkSection').removeClass('d-none');
                    $('#account_name_error').text('');
                }
                $('#copySuccess').addClass('d-none');
            }
        });
    }
    
    // Check initial state for pre-filled values
    updateCopyLinkVisibility();
    
    // Update button visibility whenever account fields change
    $('#account_name').on('input', function() {
        if (!$(this).val().trim()) {
            $('#account_type').val('');
        }
        updateCopyLinkVisibility();
    });
    
    $('#account_type').on('input', function() {
        if ($('#account_name').val().trim()) {
            updateCopyLinkVisibility();
        }
    });
    
    // Copy Link button functionality
    $('#copyLinkBtn').on('click', function() {
        const accountName = encodeURIComponent($('#account_name').val().trim());
        const accountType = encodeURIComponent($('#account_type').val().trim());
        
        // Create sharable URL with account details using the customer-specific route
        const baseUrl = "{{ url('/survey/' . $survey->id) }}";
        const shareableUrl = `${baseUrl}?account_name=${accountName}&account_type=${accountType}`;
        
        // Create a temporary element to copy the URL
        const tempInput = document.createElement('input');
        tempInput.value = shareableUrl;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        
        // Show success message
        $('#copySuccess').removeClass('d-none');
        
        // Hide success message after 3 seconds
        setTimeout(() => {
            $('#copySuccess').addClass('d-none');
        }, 3000);
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
        
        // Validate account name
        if (!$('#account_name').val().trim()) {
            isValid = false;
            $('#account_name').addClass('error');
            $('#account_name').parent().addClass('has-error');
            $('#account_name_error').text('Account name is required').addClass('text-danger');
            errorList.push('Account name is required');
        }
        
        // Validate account type
        if (!$('#account_type').val().trim()) {
            isValid = false;
            $('#account_type').addClass('error');
            $('#account_type').parent().addClass('has-error');
            $('#account_type_error').text('Account type is required').addClass('text-danger');
            errorList.push('Account type is required');
        }
        
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
        
        // Set end time right before validation
        const endTime = new Date();
        $('#end_time').val(endTime.toISOString());
        
        // First validate the form
        if (!validateForm()) {
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

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('input[name="_token"]').val()
            },
            dataType: 'json',
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
                    thankYouModal.show();
                    $('#surveyForm')[0].reset();
                }
            },
            error: function(xhr) {
                console.log('Error response:', xhr.responseJSON);
                
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
                        <h4 class="mt-3">Error! Qestions are Empty.</h4>
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
                        <div class="mt-3">
                            <button type="button" class="btn btn-primary" onclick="window.location.reload();">Refresh the page</button>
                        </div>
                    `);
                } else {
                    // General error case
                    $('#errorMessage').html(`
                        <i class="fas fa-exclamation-circle text-danger" style="font-size: 48px;"></i>
                        <h4 class="mt-3">Oops!</h4>
                        <p>An unexpected error occurred. Please try again.</p>
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
    
    // Autocomplete for account_name
    $('#account_name').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '{{ route('customers.autocomplete') }}',
                dataType: 'json',
                data: {
                    term: request.term
                },
                success: function(data) {
                    // Format the autocomplete items to display both code and name
                    const formatted = data.map(item => {
                        return {
                            label: `${item.custcode} - ${item.label}`,
                            value: item.label,
                            custcode: item.custcode,
                            custtype: item.custtype
                        };
                    });
                    response(formatted);
                }
            });
        },
        minLength: 0, // Show suggestions even when empty
        select: function(event, ui) {
            $('#account_name').val(ui.item.value);
            $('#account_type').val(ui.item.custtype); // Set account type automatically
            updateCopyLinkVisibility(); // Show copy link button immediately
            return false;
        }
    }).focus(function() {
        // Trigger search on focus to show all suggestions
        $(this).autocomplete('search', '');
    });
    
    // Add code to lookup customer by code when entered
    $('#account_name').on('input', function() {
        const input = $(this).val().trim();
        // Clear the customer name display when input is empty
        if (!input) {
            $('#customer_name_display').html('').hide();
            return;
        }
        // If input looks like it might be a customer code (alphanumeric without spaces),
        // try to look up the customer
        if (input.length >= 3 && !input.includes(' ')) {
            $.ajax({
                url: '{{ route('customers.lookup-by-code') }}',
                dataType: 'json',
                data: { code: input },
                success: function(response) {
                    if (response.success && response.customer) {
                        // Display the customer name and update account type
                        $('#customer_name_display').html(`<span class="text-success"><i class="fas fa-check-circle"></i> ${response.customer.custname}</span>`).show();
                        $('#account_type').val(response.customer.custtype);
                        updateCopyLinkVisibility();
                    } else {
                        // Show 'No Data Found!' if no match found
                        $('#customer_name_display').html('<span class="text-danger">No Data Found!</span>').show();
                        $('#account_type').val('');
                        updateCopyLinkVisibility();
                    }
                },
                error: function() {
                    $('#customer_name_display').html('<span class="text-danger">No Data Found!</span>').show();
                    $('#account_type').val('');
                    updateCopyLinkVisibility();
                }
            });
        } else {
            $('#customer_name_display').html('').hide();
        }
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