@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Create New Survey') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.surveys.store') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="title" class="col-md-4 col-form-label text-md-right">{{ __('Survey Title') }}</label>

                            <div class="col-md-6">
                                <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required autocomplete="title" autofocus>

                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        

                        <div id="questions-container">
                            <!-- Questions will be added here dynamically -->
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="button" class="btn btn-secondary" onclick="addQuestion()">
                                    {{ __('Add Question') }}
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Create Survey') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function addQuestion() {
        const container = document.getElementById('questions-container');
        const questionIndex = container.children.length;
        
        const questionDiv = document.createElement('div');
        questionDiv.className = 'form-group row question';
        questionDiv.innerHTML = `
            <label class="col-md-4 col-form-label text-md-right">Question ${questionIndex + 1}</label>
            <div class="col-md-6">
                <input type="text" class="form-control" name="questions[${questionIndex}][text]" placeholder="Question text" required>
                <select class="form-control mt-2" name="questions[${questionIndex}][type]" required>
                    <option value="text">Text Answer</option>
                    <option value="radio">Radio Button</option>
                    <option value="star">Star Rating</option>
                    <option value="select">Dropdown</option>
                </select>
                <button type="button" class="btn btn-sm btn-danger mt-2" onclick="this.parentNode.parentNode.remove()">Remove</button>
            </div>
        `;
        
        container.appendChild(questionDiv);
    }
    
    // Add first question by default
    window.onload = addQuestion;
</script>
@endsection