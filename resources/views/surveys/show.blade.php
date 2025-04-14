@extends('layouts.app')

@section('title', $survey->title)

@section('content')
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">

<div class="container mt-5 survey-container">
    <div class="text-center mb-5">
        <img src="{{ asset('img/logo.JPG') }}" alt="Logo" class="d-block mx-auto logo" style="max-width: 180px;">
        <h2 class="mt-4" style="color: #2c3e50; font-weight: 600;">{{ $survey->title }}</h2>
    </div>

        <form id="surveyForm" method="POST" action="{{ route('survey-responses.store') }}">
            @csrf
            <input type="hidden" name="survey_id" value="{{ $survey->id }}">
            
            <!-- Account Info Section -->
            <div class="row mb-4">
                <div class="col-md-4 col-12">
                    <label for="account_name" class="form-label">Account Name</label>
                    <input type="text" class="form-control" id="account_name" name="account_name" required>
                </div>
                <div class="col-md-4 col-12">
                    <label for="account_type" class="form-label">Account Type</label>
                    <input type="text" class="form-control" id="account_type" name="account_type" required>
                </div>
                <div class="col-md-4 col-12">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
            </div>
            
            <!-- Survey Questions Section -->
            <h6 class="text-center mt-4 mb-4">Please select the number which more accurately reflects your satisfaction level</h6>
            
            <table class="table table-bordered survey-table">
                <thead class="text-center">
                    <tr>
                        <th>Questions</th>
                        <th>1 - Poor</th>
                        <th>2 - Needs Improvement</th>
                        <th>3 - Satisfactory</th>
                        <th>4 - Very Satisfactory</th>
                        <th>5 - Excellent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($questions as $question)
                    <tr>
                        <td>{{ $question->text }}</td>
                        <td colspan="5" class="text-center">
                            @switch($question->type)
                                @case('radio')
                                    <div class="d-flex justify-content-around">
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
        
        <div class="text-center mt-2 mb-4">
            <h5>WE APPRECIATE YOUR FEEDBACK! THANK YOU SO MUCH!</h5>
        </div>
    </div>
</div>

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1" aria-labelledby="responseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalLabel">Survey Response</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="successMessage" class="d-none">
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

<style>
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
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
<script>
$(document).ready(function() {
    const modal = new bootstrap.Modal(document.getElementById('responseModal'));
    
    $('#surveyForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#successMessage').removeClass('d-none');
                $('#errorMessage').addClass('d-none');
                modal.show();
                $('#surveyForm')[0].reset();
                
                // Auto close modal after 2 seconds on success
                setTimeout(function() {
                    modal.hide();
                }, 2000);
            },
            error: function(xhr) {
                $('#successMessage').addClass('d-none');
                $('#errorMessage').removeClass('d-none');
                modal.show();
            }
        });
    });
    
    $('#responseModal').on('hidden.bs.modal', function () {
        $('#successMessage').addClass('d-none');
        $('#errorMessage').addClass('d-none');
    });
});
</script>
@endsection