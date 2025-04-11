@extends('layouts.app')

@section('title', $survey->title)

@section('content')
<div class="container">
    <div class="survey-container">
        <h1 class="text-center mb-4">{{ $survey->title }}</h1>

        <form action="{{ route('surveys.store', $survey) }}" method="POST">
            @csrf
            
            @foreach($questions as $question)
            <div class="mb-4">
                <label for="question_{{ $question->id }}" class="form-label fw-bold">
                    {{ $question->text }}
                </label>

                @if($question->type === 'text')
                    <input type="text" 
                           class="form-control" 
                           id="question_{{ $question->id }}" 
                           name="responses[{{ $question->id }}]" 
                           required>
                @elseif($question->type === 'textarea')
                    <textarea class="form-control" 
                              id="question_{{ $question->id }}" 
                              name="responses[{{ $question->id }}]" 
                              rows="3" 
                              required></textarea>
                @elseif($question->type === 'rating')
                    <div class="rating-options">
                        @php
                            $ratings = [
                                5 => 'Excellent',
                                4 => 'Very Satisfactory',
                                3 => 'Satisfactory',
                                2 => 'Need Improvement',
                                1 => 'Poor'
                            ];
                        @endphp
                        @foreach($ratings as $value => $label)
                            <div class="form-check">
                                <input type="radio" 
                                       class="form-check-input" 
                                       id="rating{{ $value }}_{{ $question->id }}" 
                                       name="responses[{{ $question->id }}]" 
                                       value="{{ $value }}" 
                                       required>
                                <label class="form-check-label" 
                                       for="rating{{ $value }}_{{ $question->id }}">
                                    {{ $label }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                @endif

                @error('responses.' . $question->id)
                    <div class="text-danger mt-1">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            @endforeach

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    Submit Survey
                </button>
            </div>
        </form>
    </div>
</div>
@endsection