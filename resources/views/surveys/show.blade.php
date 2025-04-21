@extends('layouts.app')

@section('title', $survey->title)

@section('content')
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">

@if($hasResponded)
    <div class="container">
        @if($allowResubmit)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>You have previously submitted this survey, but resubmission has been enabled by an administrator. You may submit a new response.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @else
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>You have already submitted this survey. You can view the questions, but submitting again with the same account name will not be allowed.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
@endif

<div class="container mt-5 survey-container">
    <div class="position-relative">
        <a href="{{ route('index') }}" class="btn btn-close position-absolute top-0 end-0" style="font-size: 0.8rem;" aria-label="Close"></a>
    </div>

    <div class="text-center mb-5">
        <img src="{{ asset('img/logo.JPG') }}" alt="Logo" class="d-block mx-auto logo mt-5" style="max-width: 180px;">
        <h2 class="mt-4" style="color: #2c3e50; font-weight: 600;">{{ $survey->title }}</h2>
    </div>

        <form id="surveyForm" method="POST" action="{{ route('surveys.store', $survey) }}">
            @csrf
            <input type="hidden" name="survey_id" value="{{ $survey->id }}">
            <input type="hidden" name="start_time" id="start_time">
            <input type="hidden" name="end_time" id="end_time">
            
            <!-- Account Info Section -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-4">
                    <label for="account_name" class="form-label">Account Name</label>
                    <input type="text" class="form-control" id="account_name" name="account_name" required>
                </div>
                <div class="col-12 col-md-4">
                    <label for="account_type" class="form-label">Account Type</label>
                    <input type="text" class="form-control" id="account_type" name="account_type" required>
                </div>
                <div class="col-12 col-md-4">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                </div>
            </div>
            
            <!-- Survey Questions Section -->
            <h6 class="text-center mt-5 mb-4">Please select the number which more accurately reflects your satisfaction level</h6>
            <h6 class="text-center mt-4 mb-4" style="font-size: 13px">
                1 - Poor&nbsp;&nbsp;&nbsp;&nbsp;
                2 - Needs Improvement&nbsp;&nbsp;&nbsp;&nbsp;
                3 - Satisfactory&nbsp;&nbsp;&nbsp;&nbsp;
                4 - Very Satisfactory&nbsp;&nbsp;&nbsp;&nbsp;
                5 - Excellent
            </h6>
            
            <div class="container mt-5">
                <div class="table-responsive">
                    <table class="table table-bordered survey-table">
                        <thead class="text-center">
                            <tr>
                                <th>Questions</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questions as $question)
                            <tr>
                                <td>{{ $question->text }}</td>
                                <td>
                                    @switch($question->type)
                                        @case('radio')
                                            <div class="rating-group">
                                                <div class="rating-options">
                                                    <div class="rating-label">1</div>
                                                    <div class="rating-label">2</div>
                                                    <div class="rating-label">3</div>
                                                    <div class="rating-label">4</div>
                                                    <div class="rating-label">5</div>
                                                </div>
                                                <div class="rating-inputs">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <div class="custom-radio">
                                                            <input type="radio" 
                                                                id="q{{ $question->id }}_rating{{ $i }}" 
                                                                name="responses[{{ $question->id }}]" 
                                                                value="{{ $i }}" 
                                                                required>
                                                            <label for="q{{ $question->id }}_rating{{ $i }}" class="radio-label">
                                                                <span class="radio-circle"></span>
                                                            </label>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                            @break
                                        @case('star')
                                            <div class="star-rating">
                                                @for($i = 5; $i >= 1; $i--)
                                                    <input type="radio" id="star{{ $question->id }}_{{ $i }}" name="responses[{{ $question->id }}]" value="{{ $i }}" required>
                                                    <label for="star{{ $question->id }}_{{ $i }}" title="{{ $i }} stars"></label>
                                                @endfor
                                            </div>
                                            @break
                                    @endswitch
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <hr style="height:10px;border-width:0;color:gray;background-color:gray">
            
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-8">
                        <p>How likely is it that you would recommend our company to a friend or colleague? Please select a number from 1 to 10</p>
                    </div>
                    <div class="col-12 col-md-4">
                        <select id="survey-number" name="recommendation" class="form-select form-select-sm" required>
                            <option value="" disabled selected>Select a rating</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <hr style="height:10px;border-width:0;color:gray;background-color:gray">

            <div class="mb-3">
                <textarea class="form-control form-control-m" name="comments" rows="5" placeholder="Please leave a comment..." required></textarea>
            </div>

            @foreach($questions as $question)
                @error('responses.' . $question->id)
                    <div class="text-danger mt-1">
                        {{ $message }}
                    </div>
                @enderror
            @endforeach

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    Submit Survey
                </button>
            </div>
        </form>
        
        <div class="text-center mt-4 mb-4">
            <h5>WE APPRECIATE YOUR FEEDBACK! THANK YOU SO MUCH!</h5>
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
    .position-relative {
        min-height: 40px;
    }
    
    .btn-close {
        padding: 0.5rem;
        margin: 1rem;
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    
    .btn-close:hover {
        opacity: 1;
    }
    
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
    }
    .star-rating input {
        display: none;
    }
    .star-rating label {
        cursor: pointer;
        width: 35px;
        height: 40px;
        margin: 0 10px;
        background: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="%23ddd"/></svg>') no-repeat center;
        background-size: contain;
    }
    .star-rating input:checked ~ label,
    .star-rating label:hover,
    .star-rating label:hover ~ label {
        background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="%23ffd700"/></svg>');
    }

    .rating-group {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    .rating-options {
        display: flex;
        justify-content: space-between;
        width: 100%;
        margin-bottom: 5px;
    }

    .rating-label {
        font-size: 12px;
        text-align: center;
        flex: 1;
        padding: 0 5px;
    }

    .rating-inputs {
        display: flex;
        justify-content: space-between;
        width: 100%;
        gap: 10px;
    }

    .custom-radio {
        text-align: center;
        flex: 1;
    }

    .survey-table {
        width: 100%;
    }

    .survey-table th:first-child {
        width: 40%;
    }

    .survey-table th:last-child {
        width: 60%;
    }

    @media (max-width: 768px) {
        .rating-label {
            font-size: 10px;
        }
        
        .survey-table th:first-child {
            width: 35%;
        }

        .survey-table th:last-child {
            width: 65%;
        }
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
        $('table.survey-table tbody tr').each(function() {
            const questionText = $(this).find('td:first').text().trim();
            const questionId = $(this).find('input[type="radio"], input[type="hidden"]').first().attr('name').match(/\d+/)[0];
            const rating = formData.get(`responses[${questionId}]`);
            
            if (rating) {
                surveyResponses.push({ questionText, rating });
            }
        });
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#successMessage').removeClass('d-none');
                    $('#errorMessage').addClass('d-none');
                    
                    // Populate account information
                    $('#summary-account-name').text(formData.get('account_name'));
                    $('#summary-account-type').text(formData.get('account_type'));
                    $('#summary-date').text(formData.get('date'));
                    
                    // Populate survey responses
                    const responsesHtml = surveyResponses.map(response => `
                        <div class="mb-3">
                            <strong>${response.questionText}</strong>
                            <p>Rating: ${response.rating}</p>
                        </div>
                    `).join('');
                    
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
                            <a href="{{ route('index') }}" class="btn btn-primary">Return to Surveys</a>
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
</script>
@endsection