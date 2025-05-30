@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0 fw-bold">Edit Question</h3>
                        <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-outline-secondary btn-sm" id="closeButton"  data-bs-toggle="tooltip" data-bs-placement="top" title="Go Back To Home">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                    <p class="text-muted mb-0">Survey: {{ $survey->title }}</p>
                </div>

                <div class="card-body p-4">
                    <form id="updateQuestionForm" action="{{ route('admin.surveys.questions.update', [$survey, $question]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="text" class="form-label">Question Text</label>
                            <input type="text" class="form-control form-control-lg @error('text') is-invalid @enderror"
                                id="text" name="text" value="{{ old('text', $question->text) }}" required>
                            @error('text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="flex-grow-1">
                                <label for="type" class="form-label">Question Type</label>
                                <select class="form-select form-select-lg @error('type') is-invalid @enderror"
                                    id="type" name="type" required>
                                    <option value="" disabled>Select question type</option>
                                    <option value="radio" {{ old('type', $question->type) === 'radio' ? 'selected' : '' }}>Radio Buttons</option>
                                    <option value="star" {{ old('type', $question->type) === 'star' ? 'selected' : '' }}>Star Rating</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="required" class="form-label">Required</label>
                            <div class="form-check form-switch">
                                <input type="hidden" name="required" value="0">
                                <input type="checkbox" class="form-check-input" id="required" name="required" value="1"
                                    {{ old('required', $question->required) ? 'checked' : '' }}>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-check-lg me-1"></i>Update Question
                            </button>
                        </div>
                    </form>

                    <form id="delete-form" action="{{ route('admin.surveys.questions.destroy', [$survey, $question]) }}"
                        method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Close button confirmation
    document.getElementById('closeButton').addEventListener('click', function(event) {
        event.preventDefault();
        
        swalWithBootstrapButtons.fire({
            title: "Are you sure?",
            text: "Any unsaved changes will be lost!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, leave page",
            cancelButtonText: "No, stay here",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = this.getAttribute('href');
            }
        });
    });

    const typeSelect = document.getElementById('type');
    const form = document.getElementById('updateQuestionForm');
    const textInput = document.getElementById('text');
    
    // SweetAlert2 configuration
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success me-3",
            cancelButton: "btn btn-outline-danger",
            actions: 'gap-2 justify-content-center'
        },
        buttonsStyling: false
    });

    // Add form submission handler to check for punctuation and show confirmation
    form.addEventListener('submit', function(event) {
        // Prevent the default submission temporarily
        event.preventDefault();
        
        // Add period if question doesn't end with punctuation
        if (textInput.value && !/[.?!:;]$/.test(textInput.value.trim())) {
            textInput.value = textInput.value.trim() + '.';
        }
        
        // Capitalize first letter
        if (textInput.value) {
            textInput.value = textInput.value.charAt(0).toUpperCase() + textInput.value.slice(1);
        }
        
        // Show confirmation dialog
        swalWithBootstrapButtons.fire({
            title: "Are you sure?",
            text: "Do you want to update this question?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, update it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form if confirmed
                console.log('Form submitted with text: ' + textInput.value);
                form.submit();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire({
                    title: "Cancelled",
                    text: "Your question remains unchanged",
                    icon: "error"
                });
            }
        });
    });

    
});

</script>

<style>
.card-header {
    background-color: var(--primary-color) !important;
    color: #fff;
}
.card-header p {
    color: #fff !important;
}
.card-header a.btn.btn-outline-secondary {
    color: #fff !important;
    border-color: #fff !important;
}
.card-header a.btn.btn-outline-secondary:hover, .card-header a.btn.btn-outline-secondary:focus {
    background-color: rgba(255,255,255,0.15) !important;
    color: #fff !important;
    border-color: #fff !important;
}
.form-control, .form-select {
    border-radius: 6px;
}

.form-control:focus, .form-select:focus {
    border-color: var(--accent-color) !important;
    box-shadow: 0 0 0 0.25rem rgba(var(--accent-color), 0.25); 
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

.form-check.form-switch {
    padding-left: 0;
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
    padding-top: 2px;
}
</style>
@endsection
