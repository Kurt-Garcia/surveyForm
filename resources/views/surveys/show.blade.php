@extends($isCustomerMode ? 'layouts.customer' : 'layouts.app-user')

@section('title', $survey->title)

@section('content')
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    :root {
        --primary-color: {{ isset($activeTheme) ? $activeTheme->primary_color : '#E53935' }};
        --secondary-color: {{ isset($activeTheme) ? $activeTheme->secondary_color : '#2C3E50' }};
        --accent-color: {{ isset($activeTheme) ? $activeTheme->accent_color : '#F1C40F' }};
        --button-hover-color: {{ isset($activeTheme) ? ($activeTheme->button_hover_color ?? '#B71C1C') : '#B71C1C' }};
        @if($isCustomerMode)
        --text-color: {{ isset($activeTheme) ? $activeTheme->text_color : '#333' }};
        --body-font: '{{ isset($activeTheme) ? $activeTheme->body_font : "Inter" }}', sans-serif;
        --heading-font: '{{ isset($activeTheme) ? $activeTheme->heading_font : "Inter" }}', sans-serif;
        @endif
    }

    /* Hide thank-you message by default */
    .thank-you-message {
        display: none;
        opacity: 0;
        visibility: hidden;
    }
    
    /* Consent modal styles */
    #consentModal .modal-header {
        border-bottom: 2px solid var(--primary-color);
    }
    
    #consentModal .modal-title {
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }
    
    #consentModal .text-muted {
        font-size: 1.1rem;
    }
    
    #consentModal ol li {
        padding: 8px 0;
    }
    
    #consentModal .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    #consentModal #consentContinueBtn {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        transition: all 0.3s ease;
    }
    
    #consentModal #consentContinueBtn:hover {
        background-color: var(--button-hover-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    #consentModal #consentContinueBtn:disabled {
        background-color: #6c757d;
        border-color: #6c757d;
        transform: none;
        box-shadow: none;
    }
    
    /* Survey disabled overlay */
    .survey-disabled-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 1050;
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
        padding: 8px 16px;
        border-radius: 25px;
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
        color: white !important;
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .survey-metadata .badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .survey-metadata .badge:hover::before {
        left: 100%;
    }

    .survey-metadata .badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }
    
    .survey-metadata .text-muted {
        font-size: 0.85rem;
    }

    /* Submit button gradient styling */
    .submit-button {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
        border: none !important;
        color: white !important;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .submit-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .submit-button:hover::before {
        left: 100%;
    }

    .submit-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
        filter: brightness(1.1);
    }

    .submit-button:active {
        transform: translateY(0);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    /* Thank you message gradient styling */
    .thank-you-message {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        padding: 2rem;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        margin-top: 2rem;
    }

    .thank-you-message h3 {
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        margin-bottom: 1rem;
    }

    .thank-you-message p {
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 1.5rem;
    }

    .thank-you-message .small-button {
        background: rgba(255, 255, 255, 0.2) !important;
        border: 2px solid rgba(255, 255, 255, 0.5) !important;
        color: white !important;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .thank-you-message .small-button:hover {
        background: rgba(255, 255, 255, 0.3) !important;
        border-color: rgba(255, 255, 255, 0.8) !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
    
    /* Improvement areas styling */
    .improvement-category {
        border-radius: 8px;
        padding: 12px;
        transition: all 0.3s ease;
    }
    
    .improvement-category:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
    
    .improvement-category .form-check-label.fw-bold {
        font-size: 1.05rem;
    }
    
    .improvement-category .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .improvement-category .form-check-input:disabled {
        opacity: 0.5;
    }
    
    #summary-improvement-details .list-group-item {
        border-left: 3px solid var(--primary-color);
    }
    
    #summary-improvement-details .fas.fa-angle-right {
        color: var(--primary-color) !important;
    }
</style>

<div class="survey-wrapper">
    @if($hasResponded)
        <div class="container">
            @if($allowResubmit)
                <div class="notification-card warning {{ $isCustomerMode ? 'font-theme' : '' }}" id="warningNotification">
                    <i class="fas fa-info-circle me-2"></i>
                    <p>You have previously submitted this survey, but resubmission has been enabled by an administrator. You may submit a new response.</p>
                    <button type="button" class="notification-close" onclick="closeNotification('warningNotification')">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @else
                <div class="notification-card info {{ $isCustomerMode ? 'font-theme' : '' }}" id="infoNotification">
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
            @if(!$isCustomerMode)
            <a href="javascript:void(0);" class="close-button" id="closeFormButton" data-bs-toggle="tooltip" data-bs-placement="top" title="Go Back To Home">
                <i class="fas fa-times"></i>
            </a>
            @endif
            @if($survey->logo)
            <img src="{{ asset('storage/' . $survey->logo) }}" alt="{{ $survey->title }} Logo" class="survey-logo">
            @else
            <img src="{{ asset('img/logo.JPG') }}" alt="Default Logo" class="survey-logo">
            @endif
            <h1 class="survey-title {{ $isCustomerMode ? 'font-theme-heading' : '' }}">{{ strtoupper($survey->title) }}</h1>
            
            @if($survey->sbus->count() > 0 || $survey->sites->count() > 0)
            <div class="survey-metadata">
                @if($survey->sbus->count() > 0)
                    @foreach($survey->sbus as $sbu)
                        <span class="badge bg-primary me-2">{{ $sbu->name }}</span>
                    @endforeach
                @endif
                
                @if($survey->sites->count() > 0)
                <small class="text-muted">
                    <i class="fas fa-map-marker-alt me-1"></i> 
                    Deployed to: {{ $survey->sites->pluck('name')->implode(', ') }}
                </small>
                @endif
            </div>
            @endif
        </div>

        <form id="surveyForm" method="POST" action="{{ $isCustomerMode ? route('customer.survey.submit', $survey) : route('surveys.store', $survey) }}" class="modern-form">
            @csrf
            <input type="hidden" name="survey_id" value="{{ $survey->id }}">
            <input type="hidden" name="start_time" id="start_time">
            <input type="hidden" name="end_time" id="end_time">
            
            <!-- Validation Alert Container -->
            <div id="validationAlertContainer" class="alert alert-danger mb-4 d-none">
                <h6><i class="fas fa-exclamation-triangle me-2"></i>Please Fill In All Required Fields!</h6>
            </div>
            
            <div class="form-grid {{ $isCustomerMode ? 'font-theme' : '' }}">
                <div class="form-field">
                    <label for="account_name" class="form-label {{ $isCustomerMode ? 'font-theme' : '' }}">Account Name</label>
                    <input type="text" class="modern-input {{ $isCustomerMode ? 'font-theme' : '' }}" id="account_name" name="account_name" value="{{ $prefillAccountName ?? '' }}" placeholder="Enter customer name or code"{{ $isCustomerMode ? ' readonly' : '' }}>
                    <div id="customer_name_display" class="customer-name-display mt-1"></div>
                    <div class="validation-message" id="account_name_error"></div>
                </div>
                <div class="form-field">
                    <label for="account_type" class="form-label {{ $isCustomerMode ? 'font-theme' : '' }}">Account Type</label>
                    <input type="text" class="modern-input {{ $isCustomerMode ? 'font-theme' : '' }}" id="account_type" name="account_type" value="{{ $prefillAccountType ?? '' }}" readonly>
                    <div class="validation-message" id="account_type_error"></div>
                </div>
                <div class="form-field">
                    <label for="date" class="form-label {{ $isCustomerMode ? 'font-theme' : '' }}">Date</label>
                    <input type="date" class="modern-input {{ $isCustomerMode ? 'font-theme' : '' }}" id="date" name="date" value="{{ date('Y-m-d') }}">
                    <div class="validation-message" id="date_error"></div>
                </div>
            </div>

            @if(!$isCustomerMode)
            <div id="copyLinkSection" class="mb-4 d-none">
                <button type="button" id="copyLinkBtn" class="btn btn-outline-primary">
                    <i class="fas fa-link me-2"></i>Copy Link for Customer
                </button>
                <span id="copySuccess" class="text-success ms-2 d-none"><i class="fas fa-check-circle"></i> Link copied!</span>
            </div>
            @endif

            <div class="survey-section">
                <h2 class="section-title {{ $isCustomerMode ? 'font-theme-heading' : '' }}">Satisfaction Level</h2>
                <div class="rating-legend {{ $isCustomerMode ? 'font-theme' : '' }}">
                    <span class="rating-item {{ $isCustomerMode ? 'font-theme' : '' }}">1 - Poor</span>
                    <span class="rating-item {{ $isCustomerMode ? 'font-theme' : '' }}">2 - Needs Improvement</span>
                    <span class="rating-item {{ $isCustomerMode ? 'font-theme' : '' }}">3 - Satisfactory</span>
                    <span class="rating-item {{ $isCustomerMode ? 'font-theme' : '' }}">4 - Very Satisfactory</span>
                    <span class="rating-item {{ $isCustomerMode ? 'font-theme' : '' }}">5 - Excellent</span>
                </div>

                <div class="questions-container {{ $isCustomerMode ? 'font-theme' : '' }}">
                    @foreach($questions as $question)
                    <div class="question-card {{ $isCustomerMode ? 'font-theme' : '' }}" data-question-id="{{ $question->id }}">
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
                                    <div class="modern-rating-group-display">
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
                <h2 class="section-title {{ $isCustomerMode ? 'font-theme-heading' : '' }}">Recommendation</h2>
                <div class="recommendation-container {{ $isCustomerMode ? 'font-theme' : '' }}">
                    <p>How likely is it that you would recommend our company to a friend or colleague?</p>
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
                <h2 class="section-title {{ $isCustomerMode ? 'font-theme-heading' : '' }}">Areas for Improvement Suggestions</h2>
                <p class="mb-3 {{ $isCustomerMode ? 'font-theme' : '' }}">Select all that apply:</p>
                
                <div class="improvement-areas mb-4 {{ $isCustomerMode ? 'font-theme' : '' }}">
                    <!-- Product/Service Quality -->
                    <div class="improvement-category mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="product_quality" name="improvement_areas[]" value="product_quality">
                            <label class="form-check-label fw-bold" for="product_quality">
                                🧾 Product / Service Quality
                            </label>
                        </div>
                        <div class="ms-4 mt-2">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="product_availability" name="improvement_details[]" value="We hope products are always available. Some items are often out of stock." data-category="product_quality">
                                <label class="form-check-label" for="product_availability">
                                    We hope products are always available. Some items are often out of stock.
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="product_expiration" name="improvement_details[]" value="Please monitor product expiration dates more carefully. We sometimes receive items that are near expiry." data-category="product_quality">
                                <label class="form-check-label" for="product_expiration">
                                    Please monitor product expiration dates more carefully. We sometimes receive items that are near expiry.
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="product_damage" name="improvement_details[]" value="Some products arrive with dents, leaks, or damaged packaging. Kindly ensure all items are in good condition." data-category="product_quality">
                                <label class="form-check-label" for="product_damage">
                                    Some products arrive with dents, leaks, or damaged packaging. Kindly ensure all items are in good condition.
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Delivery & Logistics -->
                    <div class="improvement-category mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="delivery_logistics" name="improvement_areas[]" value="delivery_logistics">
                            <label class="form-check-label fw-bold" for="delivery_logistics">
                                🚚 Delivery & Logistics
                            </label>
                        </div>
                        <div class="ms-4 mt-2">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="delivery_time" name="improvement_details[]" value="We'd appreciate it if deliveries consistently arrive on time, as promised." data-category="delivery_logistics">
                                <label class="form-check-label" for="delivery_time">
                                    We'd appreciate it if deliveries consistently arrive on time, as promised.
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="missing_items" name="improvement_details[]" value="There have been a few instances of missing items in our deliveries. Please double-check orders for completeness." data-category="delivery_logistics">
                                <label class="form-check-label" for="missing_items">
                                    There have been a few instances of missing items in our deliveries. Please double-check orders for completeness.
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sales & Customer Service -->
                    <div class="improvement-category mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="customer_service" name="improvement_areas[]" value="customer_service">
                            <label class="form-check-label fw-bold" for="customer_service">
                                👩‍💼 Sales & Customer Service
                            </label>
                        </div>
                        <div class="ms-4 mt-2">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="response_time" name="improvement_details[]" value="It would be helpful if our concerns or follow-ups were responded to more quickly." data-category="customer_service">
                                <label class="form-check-label" for="response_time">
                                    It would be helpful if our concerns or follow-ups were responded to more quickly.
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="clear_communication" name="improvement_details[]" value="We appreciate clear communication. Kindly ensure that all interactions remain polite and professional." data-category="customer_service">
                                <label class="form-check-label" for="clear_communication">
                                    We appreciate clear communication. Kindly ensure that all interactions remain polite and professional.
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Timeliness -->
                    <div class="improvement-category mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="timeliness" name="improvement_areas[]" value="timeliness">
                            <label class="form-check-label fw-bold" for="timeliness">
                                🕐 Timeliness
                            </label>
                        </div>
                        <div class="ms-4 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="schedule_adherence" name="improvement_details[]" value="Please try to follow the agreed delivery or visit schedule to avoid disruptions in our store operations." data-category="timeliness">
                                <label class="form-check-label" for="schedule_adherence">
                                    Please try to follow the agreed delivery or visit schedule to avoid disruptions in our store operations.
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Returns / BO Handling -->
                    <div class="improvement-category mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="returns_handling" name="improvement_areas[]" value="returns_handling">
                            <label class="form-check-label fw-bold" for="returns_handling">
                                🔁 Returns / BO Handling
                            </label>
                        </div>
                        <div class="ms-4 mt-2">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="return_process" name="improvement_details[]" value="I hope the return process can be made quicker and more convenient." data-category="returns_handling">
                                <label class="form-check-label" for="return_process">
                                    I hope the return process can be made quicker and more convenient.
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="bo_coordination" name="improvement_details[]" value="Please improve coordination when it comes to picking up bad order items." data-category="returns_handling">
                                <label class="form-check-label" for="bo_coordination">
                                    Please improve coordination when it comes to picking up bad order items.
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Others -->
                    <div class="improvement-category">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="others" name="improvement_areas[]" value="others">
                            <label class="form-check-label fw-bold" for="others">
                                ✍️ Others (please specify)
                            </label>
                        </div>
                        <div class="ms-4 mt-2">
                            <textarea class="modern-textarea {{ $isCustomerMode ? 'font-theme' : '' }}" name="other_comments" rows="3" placeholder="Please specify other areas for improvement..."></textarea>
                        </div>
                    </div>
                </div>
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
                    <span style="font-family: var(--heading-font)">Submit Survey</span>
                    <i class="fas fa-paper-plane ms-2"></i>
                </button>
            </div>
            
            <div class="thank-you-message {{ $isCustomerMode ? 'font-theme-heading' : '' }}">
                @if($isCustomerMode)
                <h3>THANK YOU!</h3>
                <h3>WE APPRECIATE YOUR FEEDBACK!</h3>
                <p>Your input helps us serve you better.</p>
                <button type="button" class="submit-button small-button" onclick="showResponseSummaryModal()" style="background-color: var(--secondary-color); border-color: var(--accent-color); color: white;">
                    <span>View Response</span>
                    <i class="fas fa-eye ms-2"></i>
                </button>
                @else
                <div class="message-content mb-3">
                    <h3 class="mb-1">WE APPRECIATE YOUR FEEDBACK!</h3>
                    <p>Your input helps us serve you better.</p>
                </div>
                <button type="button" class="submit-button small-button" onclick="showResponseSummaryModal()" style="background-color: var(--secondary-color); border-color: var(--accent-color); color: white;">
                    <span>View Response</span>
                    <i class="fas fa-eye ms-2"></i>
                </button>
                @endif
            </div>
                </button>
            </div>
        </form>
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
        <div class="modal-content {{ $isCustomerMode ? 'font-theme' : '' }}">
            <div class="modal-header" style="background-color: var(--primary-color); color: white; border-bottom: 2px solid var(--primary-color);">
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
                    
                    <h5 class="border-bottom pb-2">Areas for Improvement</h5>
                    <div id="summary-improvement-details" class="mb-4">
                        <!-- Improvement details will be dynamically inserted here -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Consent Modal -->
<div class="modal fade" id="consentModal" tabindex="-1" aria-labelledby="consentModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content {{ $isCustomerMode ? 'font-theme' : '' }}">
            <div class="modal-header bg-light py-3">
                <div class="row w-100">
                    <div class="col-4 text-start">
                        @if($survey->logo)
                        <img src="{{ asset('storage/' . $survey->logo) }}" alt="{{ $survey->title }} Logo" class="img-fluid" style="max-height: 60px;">
                        @else
                        <img src="{{ asset('img/logo.JPG') }}" alt="Default Logo" class="img-fluid" style="max-height: 60px;">
                        @endif
                    </div>
                    <div class="col-4"></div>
                    <div class="col-4 text-end">
                        @if($survey->department_logo)
                        <img src="{{ asset('storage/' . $survey->department_logo) }}" alt="Department Logo" class="img-fluid" style="max-height: 60px;">
                        @elseif($survey->logo)
                        <img src="{{ asset('storage/' . $survey->logo) }}" alt="{{ $survey->title }} Logo" class="img-fluid" style="max-height: 60px;">
                        @else
                        <img src="{{ asset('img/logo.JPG') }}" alt="Default Logo" class="img-fluid" style="max-height: 60px;">
                        @endif
                    </div>
                </div>
            </div>

            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <h4 class="modal-title fw-bold" id="consentModalLabel">Survey Consent Statement</h4>
                    <h5 class="text-muted">Customer Satisfaction Survey</h5>
                </div>

                <p class="mb-4">We appreciate your participation in this customer satisfaction survey and your willingness to share your thoughts. Your insights will assist us in enhancing our services, and client satisfaction.</p>
                <p class="mb-4">By completing this survey, you acknowledge and agree to the following:</p>
                
                <ol class="mb-4">
                    <li class="mb-3"><strong>Voluntary Participation.</strong> This survey is entirely voluntary. You may skip the question or close the survey at any time.</li>
                    <li class="mb-3"><strong>Purpose of the Survey.</strong> To gather valuable feedback from our customers to evaluate and enhance our service quality.</li>
                    <li class="mb-3"><strong>Collection of Personal Information.</strong> Your name, phone number, and email address might be requested for channel identification purposes.</li>
                    <li class="mb-3"><strong>Confidentiality and Data Use.</strong> All personal data and responses will be treated with utmost confidentiality and used exclusively for internal evaluation and improvement. Your information will not be shared with third parties without your consent.</li>
                    <li class="mb-3"><strong>Data Protection.</strong> We are committed to protecting your privacy and handling your personal information in accordance with the applicable data privacy laws and our company's data protection policies.</li>
                </ol>
                
                <p>If you agree with the terms above, please proceed by completing the survey sent via text/email.</p>
                
                <div class="mt-4 d-flex flex-column align-items-center">
                    <div class="form-check mb-3 w-100 text-center">
                        <input class="form-check-input" type="checkbox" name="consentAccept" id="consentAccept" value="accept">
                        <label class="form-check-label" for="consentAccept">
                            <strong>I accept the terms and conditions</strong>
                        </label>
                    </div>
                    <div class="form-check mb-3 w-100 text-center">
                        <input class="form-check-input" type="checkbox" name="consentDecline" id="consentDecline" value="decline">
                        <label class="form-check-label" for="consentDecline">
                            <strong>I do not accept the terms and conditions</strong>
                        </label>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary px-4 py-2" id="consentContinueBtn" disabled>Continue</button>
            </div>
            <div class="text-center pb-3">
                <p class="m-0">Thank you for your valuable feedback.</p>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
@if(!$isCustomerMode)
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
@endif

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    @if($isCustomerMode)
    // Customer-specific logic
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
    @else
    // Surveyor-specific logic
    @endif
    
    // Show consent modal when page loads
    const consentModal = new bootstrap.Modal(document.getElementById('consentModal'));
    consentModal.show();
    
    // Handle improvement areas checkbox interactions
    $('input[name="improvement_areas[]"]').on('change', function() {
        const categoryId = $(this).attr('id');
        const isChecked = $(this).prop('checked');
        
        // Find all detail checkboxes under this category
        const detailsContainer = $(this).closest('.improvement-category').find('.ms-4');
        
        // Enable/disable child checkboxes based on parent state
        detailsContainer.find('input[type="checkbox"]').prop('disabled', !isChecked);
        
        // If unchecking the parent, also uncheck all children
        if (!isChecked) {
            detailsContainer.find('input[type="checkbox"]').prop('checked', false);
        }
        
        // Special handling for "Others" text area
        if (categoryId === 'others') {
            detailsContainer.find('textarea').prop('disabled', !isChecked);
            if (!isChecked) {
                detailsContainer.find('textarea').val('');
            }
        }
    });
    
    // Initialize the state of all detail checkboxes and text areas
    $('input[name="improvement_areas[]"]').each(function() {
        $(this).trigger('change');
    });
    
    // Enable the continue button only when the accept checkbox is selected
    $('#consentAccept, #consentDecline').on('change', function() {
        $('#consentContinueBtn').prop('disabled', false);
        
        // If both are checked, uncheck the other one
        if (this.id === 'consentAccept' && $(this).is(':checked')) {
            $('#consentDecline').prop('checked', false);
        }
        if (this.id === 'consentDecline' && $(this).is(':checked')) {
            $('#consentAccept').prop('checked', false);
        }
    });
    
    // Handle continue button click based on consent choice
    $('#consentContinueBtn').on('click', function() {
        const acceptChecked = $('#consentAccept').is(':checked');
        const declineChecked = $('#consentDecline').is(':checked');
        
        if (!acceptChecked && !declineChecked) {
            // No option selected
            Swal.fire({
                title: 'Selection Required',
                text: 'Please select whether you accept or decline the terms.',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        if (acceptChecked) {
            // User accepted - close modal and allow access to the survey
            consentModal.hide();
        } else if (declineChecked) {
            // User declined - show message and disable form
            consentModal.hide();
            
            // Show SweetAlert2 message
            Swal.fire({
                title: 'Survey Access Denied',
                text: 'You must accept the terms and conditions to proceed with the survey.',
                icon: 'info',
                confirmButtonText: 'Understood'
            }).then((result) => {
                // Disable the form elements
                $('#surveyForm :input').prop('disabled', true);
                
                // Add overlay to indicate survey is not accessible
                $('body').append('<div class="survey-disabled-overlay d-flex align-items-center justify-content-center"><div class="text-center p-4 bg-white rounded shadow"><i class="fas fa-lock fa-3x text-warning mb-3"></i><h4>Survey Access Denied</h4><p>You must accept the terms and conditions to access this survey.</p><button class="btn btn-primary mt-3" onclick="window.location.reload()">Try Again</button></div></div>');
            });
        }
    });
    
    // User's site IDs for filtering customers
    let userSiteIds = @json($userSiteIds ?? []);
    
    // Validate that userSiteIds is an array
    if (!Array.isArray(userSiteIds)) {
        console.warn('User site IDs not available, customers will not be filtered by site');
        userSiteIds = [];
    }
    
    // Debug log to show which site IDs are being used for filtering
    console.log('Filtering customers by site IDs:', userSiteIds);
    
    // Auto-hide notification after 2 seconds
    if ($('#infoNotification').length) {
        setTimeout(function() {
            $('#infoNotification').fadeOut('fast');
        }, 5000);
    }
    // Initialize start time when the form is first loaded
    const startTime = new Date();
    $('#start_time').val(startTime.toLocaleString('en-US', { timeZone: 'Asia/Singapore' }));
    
    const thankYouModal = new bootstrap.Modal(document.getElementById('responseModal'));
    const summaryModal = new bootstrap.Modal(document.getElementById('responseSummaryModal'));
    
    @if(!$isCustomerMode)
    // Show Copy Link button when both account name and account type have values (Surveyor mode only)
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
                if (response.exists && !response.allow_resubmit) {
                    // Account exists and resubmission is not allowed, hide copy link and show message
                    $('#copyLinkSection').addClass('d-none');
                    $('#account_name_error').text('This account has already submitted a response.')
                        .addClass('text-warning')
                        .removeClass('text-danger');
                } else {
                    // Account doesn't exist or resubmission is allowed, show copy link
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
    @endif
    
    // Function to validate all inputs and display errors
    function validateForm() {
        let isValid = true;
        let errorList = [];
        
        // Clear previous error messages
        $('.validation-message').text('');
        $('#validationErrorsList ul').empty();
        @if($isCustomerMode)
        $('.modern-input, .modern-select, .modern-textarea, .modern-rating-group, .modern-rating-group-display, .modern-star-rating').removeClass('input-error error');
        @else
        $('.modern-input, .modern-select, .modern-textarea, .modern-rating-group, .modern-star-rating').removeClass('error');
        @endif
        $('.question-card').removeClass('has-error');
        
        // Validate account name
        if (!$('#account_name').val().trim()) {
            isValid = false;
            @if($isCustomerMode)
            $('#account_name').addClass('input-error error');
            @else
            $('#account_name').addClass('error');
            @endif
            $('#account_name').parent().addClass('has-error');
            $('#account_name_error').text('Account name is required').addClass('text-danger');
            errorList.push('Account name is required');
        }
        
        // Validate account type
        if (!$('#account_type').val().trim()) {
            isValid = false;
            @if($isCustomerMode)
            $('#account_type').addClass('input-error error');
            @else
            $('#account_type').addClass('error');
            @endif
            $('#account_type').parent().addClass('has-error');
            $('#account_type_error').text('Account type is required').addClass('text-danger');
            errorList.push('Account type is required');
        }
        
        // Validate date
        if (!$('#date').val()) {
            isValid = false;
            @if($isCustomerMode)
            $('#date').addClass('input-error error');
            @else
            $('#date').addClass('error');
            @endif
            $('#date').parent().addClass('has-error');
            $('#date_error').text('Date is required').addClass('text-danger');
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
                $(`#question_${questionId}_error`).text('This question requires an answer').addClass('text-danger');
                errorList.push(`Question "${questionText}" requires an answer`);
                @if($isCustomerMode)
                $(this).find('.modern-rating-group, .modern-rating-group-display, .modern-star-rating').addClass('input-error error');
                @else
                $(this).find('.modern-rating-group, .modern-star-rating').addClass('error');
                @endif
            }
        });
        
        // Validate recommendation
        if (!$('#survey-number').val()) {
            isValid = false;
            @if($isCustomerMode)
            $('#survey-number').addClass('input-error error');
            @else
            $('#survey-number').addClass('error');
            @endif
            $('#survey-number').parent().addClass('has-error');
            $('#recommendation_error').text('Recommendation is required').addClass('text-danger');
            errorList.push('Recommendation is required');
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
        
        // Enhance the form data to include data-category attributes for improvement details
        // This will help the backend associate details with the correct categories
        const detailsWithCategories = [];
        $('input[name="improvement_details[]"]:checked').each(function() {
            const detail = $(this).val();
            const category = $(this).data('category');
            if (category) {
                detailsWithCategories.push({
                    detail: detail,
                    category: category
                });
            }
        });
        
        // Add this data to a hidden field
        if (!$('#details_categories_map').length) {
            $('<input>').attr({
                type: 'hidden',
                id: 'details_categories_map',
                name: 'details_categories_map',
                value: JSON.stringify(detailsWithCategories)
            }).appendTo('#surveyForm');
        } else {
            $('#details_categories_map').val(JSON.stringify(detailsWithCategories));
        }
        
        // Show SweetAlert2 confirmation before submission
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success me-3",
                cancelButton: "btn btn-outline-danger",
                actions: 'gap-2 justify-content-center'
            },
            buttonsStyling: false
        });
        
        swalWithBootstrapButtons.fire({
            title: "{{ $isCustomerMode ? 'Are you sure?' : 'Submit Survey?' }}",
            text: "Please confirm if you want to submit this survey. You won't be able to modify your answers after submission!",
            icon: "{{ $isCustomerMode ? 'warning' : 'question' }}",
            showCancelButton: true,
            confirmButtonText: "Yes, submit it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                @if($isCustomerMode)
                // Customer mode - Handle different submission logic
                // Set end time
                const endTime = new Date();
                $('#end_time').val(endTime.toLocaleString('en-US', { timeZone: 'Asia/Singapore' }));
                
                // Calculate duration in minutes
                const startTime = new Date($('#start_time').val());
                const durationInMinutes = Math.round((endTime - startTime) / (1000 * 60));
                $('#duration').val(durationInMinutes);

                // AJAX form submission for customer
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
                            // Store submission data in localStorage
                            const surveyData = {
                                account_name: $('#account_name').val(),
                                account_type: $('#account_type').val(),
                                date: $('#date').val(),
                                recommendation: $('#survey-number').val(),
                                responses: {},
                                improvementAreas: [],
                                improvementDetails: []
                            };
                            
                            // Collect survey responses
                            $('.question-card').each(function() {
                                const questionId = $(this).data('question-id');
                                const rating = $(`input[name="responses[${questionId}]"]:checked`).val();
                                if (rating) {
                                    surveyData.responses[questionId] = rating;
                                }
                            });
                            
                            // Collect improvement areas
                            $('input[name="improvement_areas[]"]:checked').each(function() {
                                surveyData.improvementAreas.push($(this).val());
                            });
                            
                            // Collect improvement details
                            $('input[name="improvement_details[]"]:checked').each(function() {
                                surveyData.improvementDetails.push($(this).val());
                            });
                            
                            // Store data in localStorage
                            const surveyId = {{ $survey->id }};
                            const accountName = $('#account_name').val();
                            localStorage.setItem(`survey_${surveyId}_${accountName}_submitted`, 'true');
                            localStorage.setItem(`survey_${surveyId}_${accountName}_data`, JSON.stringify(surveyData));
                            
                            // Show success message and then thank you page
                            Swal.fire({
                                title: 'Thank You!',
                                text: 'Your survey has been successfully submitted.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Update response summary with collected data
                                updateResponseSummary(surveyData);
                                
                                // Use dedicated function to display thank you message
                                displayThankYouMessage();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'An error occurred while submitting the survey.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred while submitting the survey.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage = errors.join(', ');
                        }
                        
                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
                @else
                // Surveyor mode - Original logic
                // Continue with form submission
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
                    // Show SweetAlert2 success message
                    swalWithBootstrapButtons.fire({
                        title: "Thank You!",
                        text: "Your survey has been successfully submitted.",
                        icon: "success"
                    });
                    
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
                                <div class="rating-display d-flex align-items-center flex-wrap">
                                    <div class="modern-rating-group me-3 mb-2">
                                        ${Array.from({length: 5}, (_, i) => {
                                            const isSelected = i + 1 == response.rating; // Only highlight the exact selected rating
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
                    
                    // Populate recommendation score
                    $('#summary-recommendation').text(formData.get('recommendation'));
                    
                    // Collect and display improvement areas
                    const improvementDetailsContainer = $('#summary-improvement-details');
                    improvementDetailsContainer.empty();
                    
                    // Get selected improvement areas
                    const selectedAreas = [];
                    const selectedDetails = [];
                    const otherComments = formData.get('other_comments');
                    
                    // Map for area display names
                    const areasMap = {
                        'product_quality': '🧾 Product / Service Quality',
                        'delivery_logistics': '🚚 Delivery & Logistics',
                        'customer_service': '👩‍💼 Sales & Customer Service',
                        'timeliness': '🕐 Timeliness',
                        'returns_handling': '🔁 Returns / BO Handling',
                        'others': '✍️ Others'
                    };
                    
                    // Collect selected improvement areas
                    document.querySelectorAll('input[name="improvement_areas[]"]:checked').forEach(checkbox => {
                        selectedAreas.push(checkbox.value);
                    });
                    
                    // Collect selected improvement details
                    document.querySelectorAll('input[name="improvement_details[]"]:checked').forEach(checkbox => {
                        selectedDetails.push(checkbox.value);
                    });
                    
                    if (selectedAreas.length > 0) {
                        // Create list of improvement areas
                        const areasList = $('<ul class="list-group list-group-flush"></ul>');
                        
                        selectedAreas.forEach(area => {
                            const areaItem = $(`<li class="list-group-item bg-light"><strong>${areasMap[area] || area}</strong></li>`);
                            areasList.append(areaItem);
                        });
                        
                        // Add improvement details if available
                        if (selectedDetails.length > 0) {
                            const detailsList = $('<ul class="list-unstyled ms-3 mt-2"></ul>');
                            
                            selectedDetails.forEach(detail => {
                                detailsList.append(`<li><i class="fas fa-angle-right me-2 text-primary"></i>${detail}</li>`);
                            });
                            
                            // Add "Other" comments if specified
                            if (otherComments && selectedAreas.includes('others')) {
                                detailsList.append(`<li><i class="fas fa-angle-right me-2 text-primary"></i>${otherComments}</li>`);
                            }
                            
                            areasList.append($('<li class="list-group-item"></li>').append(detailsList));
                        }
                        
                        improvementDetailsContainer.append(areasList);
                    } else {
                        improvementDetailsContainer.html('<p>No improvement areas selected.</p>');
                    }
                    
                    // Show thank you message with animation without hiding the form
                    $('.thank-you-message').css('display', 'flex').addClass('show');
                    
                    // Scroll to the thank you message
                    $('html, body').animate({
                        scrollTop: $('.thank-you-message').offset().top - 100
                    }, 500);
                    
                    // Show modal and reset form
                    // thankYouModal.show(); // Hide modal, only show the thank-you message in the form
                    $('#surveyForm')[0].reset();
                }
            },
            error: function(xhr) {
                console.log('Error response:', xhr.responseJSON);
                
                // Show SweetAlert2 error message
                swalWithBootstrapButtons.fire({
                    title: "Error!",
                    text: "There was a problem submitting your survey. Please check your inputs and try again.",
                    icon: "error"
                });
                
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
                @endif
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
    
    $('#responseModal').on('hidden.bs.modal', function () {
        $('#successMessage').addClass('d-none');
        $('#errorMessage').addClass('d-none');
    });
    
    @if(!$isCustomerMode)
    // Autocomplete for account_name (Surveyor mode only)
    $('#account_name').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '{{ route('customers.autocomplete') }}',
                dataType: 'json',
                data: {
                    term: request.term,
                    site_ids: userSiteIds // Pass user's site IDs for filtering
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
    @endif
    
    @if(!$isCustomerMode)
    // Add code to lookup customer by code when entered (Surveyor mode only)
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
                data: { 
                    code: input,
                    site_ids: userSiteIds // Pass user's site IDs for filtering
                },
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
    @endif
});

function showResponseSummaryModal() {
    @if($isCustomerMode)
    // Show response summary modal directly for customer mode
    $('#responseSummaryModal').modal('show');
    @else
    $('#responseModal').modal('hide');
    $('#responseSummaryModal').modal('show');
    @endif
}

@if($isCustomerMode)
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

// Function to update response summary
function updateResponseSummary(data) {
    $('#summary-account-name').text(data.account_name);
    $('#summary-account-type').text(data.account_type);
    $('#summary-date').text(data.date);
    $('#summary-recommendation').text(data.recommendation);
    
    // Add improvement areas to summary
    const improvementDetailsContainer = $('#summary-improvement-details');
    if (improvementDetailsContainer.length > 0) {
        improvementDetailsContainer.empty();
        
        // Check if we have improvement areas data
        if (data.improvementAreas && data.improvementAreas.length > 0) {
            const areasMap = {
                'product_quality': '🧾 Product / Service Quality',
                'customer_service': '👨‍💼 Customer Service',
                'delivery': '🚚 Delivery',
                'pricing': '💰 Pricing',
                'communication': '📞 Communication',
                'facilities': '🏢 Facilities',
                'others': '🔧 Others'
            };
            
            // Create list of improvement areas
            const areasList = $('<ul class="list-group list-group-flush"></ul>');
            
            data.improvementAreas.forEach(area => {
                const areaItem = $(`<li class="list-group-item d-flex align-items-center">
                    <i class="fas fa-angle-right me-2"></i>
                    ${areasMap[area] || area}
                </li>`);
                areasList.append(areaItem);
            });
            
            // Add improvement details if available
            if (data.improvementDetails && data.improvementDetails.length > 0) {
                const detailsList = $('<ul class="list-group list-group-flush mt-2"></ul>');
                data.improvementDetails.forEach(detail => {
                    const detailItem = $(`<li class="list-group-item d-flex align-items-center ps-4">
                        <i class="fas fa-angle-right me-2" style="font-size: 0.8rem;"></i>
                        ${detail}
                    </li>`);
                    detailsList.append(detailItem);
                });
                areasList.append(detailsList);
            }
            
            improvementDetailsContainer.append(areasList);
        } else {
            improvementDetailsContainer.html('<p>No improvement areas selected.</p>');
        }
    }
    
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
                ratingHtml += `<span class="ms-2">${response}/5</span>`;
            } else {
                // For radio buttons, create numbered circles
                for (let i = 1; i <= 5; i++) {
                    const isSelected = i == response;
                    ratingHtml += `<div class="modern-radio-display ${isSelected ? 'selected' : ''}">${i}</div>`;
                }
            }
            
            responsesContainer.append(`
                <div class="response-item border-bottom py-3">
                    <div class="question-text fw-bold mb-2">${questionText}</div>
                    <div class="rating-display d-flex align-items-center">
                        <div class="modern-rating-group d-flex">
                            ${ratingHtml}
                        </div>
                        <span class="rating-text ms-3">Rating: ${response}/5</span>
                    </div>
                </div>
            `);
            
            // Add responsive styles for radio buttons if not already added
            if (!$('#responsive-radio-styles').length) {
                $('<style id="responsive-radio-styles">').html(`
                    .modern-radio-display {
                        width: 35px; height: 35px; border-radius: 50%;
                        display: flex; align-items: center; justify-content: center;
                        border: 2px solid var(--primary-color); color: var(--primary-color);
                        margin-right: 8px; font-weight: 600; font-size: 14px;
                        background-color: white;
                    }
                    .modern-radio-display.selected {
                        background-color: var(--primary-color); color: white;
                    }
                `).appendTo('head');
            }
        }
    });
}
@endif

function closeNotification(id) {
    document.getElementById(id).style.display = 'none';
}

// Close form button with SweetAlert2 confirmation
@if(!$isCustomerMode)
$(document).ready(function() {
    $('#closeFormButton').on('click', function(e) {
        e.preventDefault();
        
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success me-3",
                cancelButton: "btn btn-outline-danger",
                actions: 'gap-2 justify-content-center'
            },
            buttonsStyling: false
        });
        
        swalWithBootstrapButtons.fire({
            title: "Exit Survey?",
            text: "Are you sure you want to leave? Any unsaved responses will be lost.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, exit!",
            cancelButtonText: "No, stay!",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to the index page
                window.location.href = "{{ route('index', $survey) }}";
            }
        });
    });
});
@endif
</script>

<style>
.thank-you-message.show {
    opacity: 1;
    visibility: visible;
}

.thank-you-message .message-content {
    display: flex;
    flex-direction: column;
}

.thank-you-message h3 {
    font-size: 1.2rem;
    margin-bottom: 0.2rem;
}

.thank-you-message p {
    margin-bottom: 0;
}

.thank-you-message .small-button {
    padding: 0.5rem 1.5rem;
    font-size: 0.9rem;
    white-space: nowrap;
}

/* Fix for radio buttons on mobile */
@media (max-width: 576px) {
    .modern-rating-group {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        margin-bottom: 0.5rem;
    }
    
    .modern-radio {
        margin-right: 5px;
        margin-bottom: 5px;
    }
    
    .modern-radio label {
        width: 40px;
        height: 40px;
    }
    
    .radio-number {
        font-size: 14px;
    }
    
    .rating-legend {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .rating-legend .rating-item {
        margin-bottom: 5px;
    }
    
    /* Specific styles for the response summary modal */
    #responseSummaryModal .modern-rating-group {
        flex-wrap: wrap;
        justify-content: flex-start;
        gap: 5px;
    }
    
    #responseSummaryModal .modern-radio-display {
        width: 30px;
        height: 30px;
        font-size: 12px;
        margin-right: 1px;
        margin-bottom: 3px;
        flex-shrink: 0;
    }
    
    #responseSummaryModal .rating-display {
        flex-direction: column;
        align-items: flex-start;
    }
    
    #responseSummaryModal .rating-text {
        margin-top: 5px;
        font-size: 12px;
    }
    
    /* Additional responsive styles for very small screens */
    @media (max-width: 320px) {
        #responseSummaryModal .modern-rating-group {
            width: 100%;
            justify-content: space-between;
        }
        
        #responseSummaryModal .modern-radio-display {
            width: 28px;
            height: 28px;
            font-size: 11px;
            margin-right: 2px;
            margin-bottom: 2px;
        }
    }
    
    #responseSummaryModal .response-item {
        padding: 10px !important;
    }
}

.customer-name-display {
    display: none;
    margin-top: 5px;
    font-size: 0.9em;
}

.font-theme{
    font-family: var(--body-font);
}

@if($isCustomerMode)
.font-theme-heading{
    font-family: var(--heading-font);
}

.input-error {
    border: 2px solid #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220,53,69,.25);
}
@endif
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Bootstrap tooltips for other elements that might need it
    function initializeTooltips() {
        // Dispose of existing tooltips first
        const existingTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        existingTooltips.forEach(element => {
            const tooltip = bootstrap.Tooltip.getInstance(element);
            if (tooltip) {
                tooltip.dispose();
            }
        });

        // Initialize Bootstrap tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                trigger: 'hover focus',
                html: false,
                sanitize: true
            });
        });
    }
    
    // Initial tooltip initialization
    initializeTooltips();
});
</script>
@endsection
