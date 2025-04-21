@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0 fw-bold">Add Question</h3>
                        <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                    <p class="text-muted mb-0">Survey: {{ $survey->title }}</p>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('admin.surveys.questions.store', $survey) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="text" class="form-label">Question Text</label>
                            <input type="text" class="form-control form-control-lg @error('text') is-invalid @enderror" 
                                id="text" name="text" value="{{ old('text') }}" required>
                            @error('text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="2">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1">
                                    <label for="type" class="form-label">Question Type</label>
                                    <select class="form-select form-select-lg @error('type') is-invalid @enderror" 
                                        id="type" name="type" required>
                                        <option value="" disabled {{ !old('type') ? 'selected' : '' }}>Select question type</option>
                                        <option value="radio" {{ old('type') === 'radio' ? 'selected' : '' }}>Radio Buttons</option>
                                        <option value="star" {{ old('type') === 'star' ? 'selected' : '' }}>Star Rating</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="ms-4 d-flex align-items-center" style="padding-top: 2rem;">
                                    <div class="form-check form-switch mb-0">
                                        <input type="hidden" name="required" value="0">
                                        <input type="checkbox" class="form-check-input" id="required" name="required" value="1" 
                                            {{ old('required') ? 'checked' : '' }}>
                                        <label class="form-check-label ms-2" for="required">
                                            Required
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-light">
                                <i class="bi bi-arrow-left me-1"></i>Back to Survey
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i>Save Question
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const optionsContainer = document.getElementById('optionsContainer');

    typeSelect.addEventListener('change', function() {
        if (this.value === 'radio' || this.value === 'select') {
            optionsContainer.style.display = 'block';
            // Add at least two options if none exist
            const optionsList = document.getElementById('optionsList');
            if (optionsList.children.length === 0) {
                addOption();
                addOption();
            }
        } else {
            optionsContainer.style.display = 'none';
        }
    });

    // Show options container if type is radio/select and has old input
    if (typeSelect.value === 'radio' || typeSelect.value === 'select') {
        optionsContainer.style.display = 'block';
    }
});

function addOption() {
    const optionsList = document.getElementById('optionsList');
    const optionDiv = document.createElement('div');
    optionDiv.className = 'input-group mb-2';
    optionDiv.innerHTML = `
        <input type="text" class="form-control" name="options[]" placeholder="Enter option">
        <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
            <i class="bi bi-trash"></i>
        </button>
    `;
    optionsList.appendChild(optionDiv);
}

function removeOption(button) {
    const optionsList = document.getElementById('optionsList');
    if (optionsList.children.length > 2) {
        button.closest('.input-group').remove();
    } else {
        alert('A minimum of 2 options is required.');
    }
}
</script>

<style>
.form-control, .form-select {
    border-radius: 6px;
}

.form-control:focus, .form-select:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.btn {
    border-radius: 6px;
}

.form-check-input:checked {
    background-color: #4e73df;
    border-color: #4e73df;
}

.input-group > .btn {
    border-top-right-radius: 6px !important;
    border-bottom-right-radius: 6px !important;
}

.form-switch .form-check-input {
    width: 3em;
    height: 1.5em;
    margin-left: 0;
    background-color: rgba(78, 115, 223, 0.1);
    border-color: rgba(78, 115, 223, 0.2);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='rgba(78, 115, 223, 0.5)'/%3e%3c/svg%3e");
    transition: background-position 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    cursor: pointer;
}

.form-switch .form-check-input:checked {
    background-color: #4e73df;
    border-color: #4e73df;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
}

.form-switch .form-check-input:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
}

.form-check-label {
    color: #4e73df;
    font-weight: 500;
    cursor: pointer;
}
</style>
@endsection