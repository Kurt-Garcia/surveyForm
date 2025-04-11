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
                    <div class="star-rating">
                        @for($i = 5; $i >= 1; $i--)
                            <input type="radio" 
                                   id="star{{ $i }}_{{ $question->id }}" 
                                   name="responses[{{ $question->id }}]" 
                                   value="{{ $i }}" 
                                   required>
                            <label for="star{{ $i }}_{{ $question->id }}" 
                                   class="star">★</label>
                        @endfor
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