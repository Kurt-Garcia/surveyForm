@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">

<div class="container mt-5 survey-container">
    <div class="text-center mb-5">
        <img src="img/logo.JPG" alt="Logo" class="d-block mx-auto logo" style="max-width: 180px;">
        <h2 class="mt-4" style="color: #2c3e50; font-weight: 600;">CUSTOMER SATISFACTION SURVEY</h2>
    </div>

    <form id="surveyForm" method="POST" action="{{ route('survey.submit') }}">
        @csrf
        <!-- Account Info Section -->
        <div class="row mb-4">
            <div class="col-md-4 col-12">
                <label for="accountName" class="form-label">Account Name</label>
                <input type="text" class="form-control" id="accountName" name="accountName" required>
            </div>
            <div class="col-md-4 col-12">
                <label for="accountType" class="form-label">Account Type</label>
                <input type="text" class="form-control" id="accountType" name="accountType" required>
            </div>
            <div class="col-md-4 col-12">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
        </div>
    
        <!-- Survey Questions Section -->
        <h6 class="text-center mt-4 mb-4">Please select the number which more accurately reflects your satisfaction level</h6>
    
        @php
    $questions = [
        'Q1' => 'Our salesperson is courteous and well-groomed',
        'Q2' => 'He visits your outlet regularly as scheduled',
        'Q3' => 'He conducts stock inventory and store checking during a store visit',
        'Q4' => 'Our delivery personnel are well-mannered, polite, and honest',
        'Q5' => 'Ordered stocks are delivered on the expected date and in good condition',
        'Q6' => 'We are responsive to your calls, queries, and/or concerns and provide resolutions on time',
    ];
@endphp

@if(session('rating_type') === 'radio')
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
            @foreach ($questions as $name => $text)
            <tr>
                <td>{{ $name }}: {{ $text }}</td>
                @for ($i = 1; $i <= 5; $i++)
                <td class="text-center">
                    <label>
                        <input type="radio" name="{{ $name }}" value="{{ $i }}" required>
                    </label>
                </td>
                @endfor
            </tr>
            @endforeach
        </tbody>
    </table>
@else
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

    <table class="table table-bordered survey-table">
        <thead class="text-center">
            <tr>
                <th>Questions</th>
                <th>Rating</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($questions as $name => $text)
            <tr>
                <td>{{ $name }}: {{ $text }}</td>
                <td>
                    <div class="star-rating">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" id="{{ $name }}-{{ $i }}" name="{{ $name }}" value="{{ $i }}" required>
                            <label for="{{ $name }}-{{ $i }}"></label>
                        @endfor
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif
        
    
        <hr style="height:10px;border-width:0;color:gray;background-color:gray">
        
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-8">
                    <p>How likely is it that you would recommend our company to a friend or colleague? Please select a number from 1 to 10</p>
                </div>
                <div class="col-12 col-md-4">
                    <select id="survey-number" name="surveyRating" class="form-select form-select-sm" required>
                        <option value="" disabled selected>Select a rating</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                </div>
            </div>
        </div>
        <hr style="height:10px;border-width:0;color:gray;background-color:gray">
    
        <div class="mb-3">
            <textarea class="form-control form-control-m" name="comments" rows="5" placeholder="Please leave a comment..." required></textarea>
        </div>

        <div class="text-center mt-2 mb-4">
            <h5>WE APPRECIATE YOUR FEEDBACK! THANK YOU SO MUCH!</h5>
        </div>
    
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Submit Survey</button>
        </div>
    </form>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="thankYouModal" tabindex="-1" aria-labelledby="thankYouModalLabel" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title" id="thankYouModalLabel">Thank You</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        Thank you for your response!
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
    </div>
</div>

@if(session('success'))
<script>
    window.addEventListener('load', function () {
        var thankYouModal = new bootstrap.Modal(document.getElementById('thankYouModal'));
        thankYouModal.show();

        // Auto-hide after 2 seconds (2000 ms)
        setTimeout(function () {
            thankYouModal.hide();
        }, 2000);
    });
</script>
@endif
@endsection
