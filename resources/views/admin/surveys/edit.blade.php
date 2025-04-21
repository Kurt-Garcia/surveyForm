@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-edit me-2"></i>{{ __('Edit Survey') }}</h4>
                    <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-light btn-sm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.surveys.update', $survey) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group row mb-4">
                            <label for="title" class="col-md-3 col-form-label">{{ __('Survey Title') }}</label>

                            <div class="col-md-9">
                                <input id="title" type="text" 
                                    class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                    name="title" value="{{ old('title', $survey->title) }}" 
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
                            @foreach($survey->questions as $index => $question)
                            <div class="card shadow-sm mb-3 question-card">
                                <div class="card-body">
                                    <h5 class="mb-3">Question {{ $index + 1 }}</h5>
                                    <input type="text" class="form-control mb-3" 
                                        name="questions[{{ $index }}][text]" 
                                        value="{{ $question->text }}" 
                                        placeholder="Enter your question here" required>
                                    <select class="form-select" 
                                        name="questions[{{ $index }}][type]" required>
                                        <option value="text" {{ $question->type === 'text' ? 'selected' : '' }}>Text Input</option>
                                        <option value="radio" {{ $question->type === 'radio' ? 'selected' : '' }}>Radio Buttons</option>
                                        <option value="star" {{ $question->type === 'star' ? 'selected' : '' }}>Star Rating</option>
                                        <option value="select" {{ $question->type === 'select' ? 'selected' : '' }}>Dropdown</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-danger btn-sm mt-3" 
                                        onclick="removeQuestion(this)">
                                        <i class="fas fa-trash me-2"></i>Remove Question
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="form-group row mb-4">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-info btn-lg mb-4" onclick="addQuestion()">
                                    <i class="fas fa-plus-circle me-2"></i>{{ __('Add Question') }}
                                </button>
                            </div>
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>{{ __('Update Survey') }}
                                </button>
                                <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-2"></i>{{ __('Close') }}
                                </a>
                            </div>
                        </div>

                        <style>
                        .question-card {
                            transition: all 0.3s ease;
                        }
                        </style>

                        <script>
                        function addQuestion() {
                            const container = document.getElementById('questions-container');
                            const questionIndex = container.children.length;

                            const questionDiv = document.createElement('div');
                            questionDiv.className = 'card shadow-sm mb-3 question-card';
                            questionDiv.style.opacity = '0';
                            questionDiv.innerHTML = `
                                <div class="card-body">
                                    <h5 class="mb-3">Question ${questionIndex + 1}</h5>
                                    <input type="text" class="form-control mb-3" 
                                        name="questions[${questionIndex}][text]" 
                                        placeholder="Enter your question here" required>
                                    <select class="form-select" 
                                        name="questions[${questionIndex}][type]" required>
                                        <option value="text">Text Input</option>
                                        <option value="radio">Radio Buttons</option>
                                        <option value="star">Star Rating</option>
                                        <option value="select">Dropdown</option>
                                    </select>
                                    <button type="button" class="btn btn-outline-danger btn-sm mt-3" 
                                        onclick="removeQuestion(this)">
                                        <i class="fas fa-trash me-2"></i>Remove Question
                                    </button>
                                </div>
                            `;

                            container.appendChild(questionDiv);
                            setTimeout(() => questionDiv.style.opacity = '1', 10);
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
                        </script>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection