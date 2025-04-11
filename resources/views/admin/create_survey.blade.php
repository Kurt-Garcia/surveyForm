@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0"><i class="fas fa-poll-h me-2"></i>{{ __('Create New Survey') }}</h4>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.surveys.store') }}">
                        @csrf

                        <div class="form-group row mb-4">
                            <label for="title" class="col-md-3 col-form-label">{{ __('Survey Title') }}</label>

                            <div class="col-md-9">
                                <input id="title" type="text" 
                                    class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                    name="title" value="{{ old('title') }}" 
                                    required autocomplete="title" autofocus
                                    placeholder="Enter your survey title">

                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div id="questions-container" class="mb-4">
                            <!-- Questions will be added here dynamically -->
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12 text-center">
                                <button type="button" class="btn btn-info btn-lg me-2" onclick="addQuestion()">
                                    <i class="fas fa-plus-circle me-2"></i>{{ __('Add Question') }}
                                </button>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>{{ __('Create Survey') }}
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
        questionDiv.className = 'card shadow-sm mb-3 question-card';
        questionDiv.style.opacity = '0';
        questionDiv.innerHTML = `
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-3">Question ${questionIndex + 1}</h5>
                        <input type="text" class="form-control form-control-lg mb-3" 
                            name="questions[${questionIndex}][text]" 
                            placeholder="Enter your question here" required>
                        <select class="form-select form-select-lg" 
                            name="questions[${questionIndex}][type]" required>
                            <option value="" disabled selected>Select answer type</option>
                            <option value="text"><i class="fas fa-paragraph"></i> Text Answer</option>
                            <option value="radio"><i class="fas fa-dot-circle"></i> Radio Button</option>
                            <option value="star"><i class="fas fa-star"></i> Star Rating</option>
                            <option value="select"><i class="fas fa-chevron-down"></i> Dropdown</option>
                        </select>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-outline-danger btn-lg" 
                            onclick="removeQuestion(this)">
                            <i class="fas fa-trash-alt me-2"></i>Remove
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(questionDiv);
        setTimeout(() => {
            questionDiv.style.transition = 'opacity 0.3s ease-in';
            questionDiv.style.opacity = '1';
        }, 50);
    }
    
    function removeQuestion(button) {
        const card = button.closest('.question-card');
        card.style.opacity = '0';
        setTimeout(() => {
            card.remove();
            updateQuestionNumbers();
        }, 300);
    }

    function updateQuestionNumbers() {
        const questions = document.querySelectorAll('.question-card h5');
        questions.forEach((question, index) => {
            question.textContent = `Question ${index + 1}`;
        });
    }
    
    // Add first question by default
    window.onload = addQuestion;
</script>

<style>
.question-card {
    transition: opacity 0.3s ease-out;
    border: none;
    border-radius: 8px;
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
}

.form-control, .form-select {
    border-radius: 6px;
}

.card-header {
    border-bottom: none;
    border-radius: 8px 8px 0 0 !important;
}
</style>
@endsection