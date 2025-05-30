@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-poll-h me-2"></i>{{ __('Create New Survey') }}</h4>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm" id="closeFormBtn"  data-bs-toggle="tooltip" data-bs-placement="top" title="Go Back To Home">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.surveys.store') }}" id="createSurveyForm" enctype="multipart/form-data">
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

                        <div class="form-group row mb-4">
                            <label class="col-md-3 col-form-label">{{ __('SBU') }}</label>
                            <div class="col-md-9">
                                <div class="sbu-selection-container p-3 border rounded bg-light">
                                    <p class="text-muted mb-3 small">Select one or both SBUs where you want to deploy this survey:</p>
                                    <div class="row">
                                        @foreach($sbus as $sbu)
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check form-check-modern">
                                                    <input class="form-check-input sbu-checkbox" type="checkbox" 
                                                           id="sbu_{{ $sbu->id }}" 
                                                           name="sbu_ids[]" 
                                                           value="{{ $sbu->id }}"
                                                           {{ in_array($sbu->id, old('sbu_ids', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="sbu_{{ $sbu->id }}">
                                                        <span class="sbu-name">{{ $sbu->name }}</span>
                                                        <small class="d-block text-muted">{{ $sbu->sites->count() }} sites available</small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('sbu_ids')
                                        <span class="text-danger small" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="site_ids" class="col-md-3 col-form-label">{{ __('Deployment Sites') }}</label>
                            <div class="col-md-9">
                                <select id="site_ids" class="form-select select2 form-select-lg @error('site_ids') is-invalid @enderror" name="site_ids[]" multiple required>
                                    <option value="" disabled>Select SBU first</option>
                                </select>
                                <small class="text-muted">You can search and select multiple sites</small>
                                @error('site_ids')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="logo" class="col-md-3 col-form-label">{{ __('Survey Logo') }}</label>
                            <div class="col-md-9">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="logo-preview-container" style="display: none;">
                                        <img id="logoPreview" src="#" alt="Logo Preview" style="max-width: 100px; max-height: 100px; object-fit: contain;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" 
                                            class="form-control @error('logo') is-invalid @enderror" 
                                            id="logo" 
                                            name="logo" 
                                            accept="image/*"
                                            required>
                                        <small class="text-muted">Recommended size: 200x200px. Max file size: 2MB</small>
                                        @error('logo')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="questions-container" class="mb-4">
                            <!-- Questions will be added here dynamically -->
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12 d-flex flex-column flex-md-row justify-content-center gap-3">
                                <button type="button" class="btn btn-info btn-lg" onclick="addQuestion()" style="background-color: var(--secondary-color); border-color: var(--secondary-color); color: #fff;">
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success me-3",
            cancelButton: "btn btn-outline-danger",
            actions: 'gap-2 justify-content-center'
        },
        buttonsStyling: false
    });

    // SBU checkboxes and Site dropdown relationship
    const sbuCheckboxes = document.querySelectorAll('.sbu-checkbox');
    const sitesSelect = document.getElementById('site_ids');
    
    // Store all sites organized by SBU for client-side filtering
    const sbuSites = {
        @foreach($sbus as $sbu)
            {{ $sbu->id }}: [
                @foreach($sbu->sites as $site)
                    {
                        id: {{ $site->id }},
                        name: "{{ $site->name }}",
                        sbu_name: "{{ $sbu->name }}",
                        is_main: {{ $site->is_main ? 'true' : 'false' }}
                    },
                @endforeach
            ],
        @endforeach
    };
    
    // Function to update site options based on selected SBUs
    function updateSiteOptions() {
        const selectedSBUs = Array.from(sbuCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => parseInt(checkbox.value));
        
        // Clear current options
        sitesSelect.innerHTML = '';
        
        if (selectedSBUs.length === 0) {
            // No SBUs selected
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.disabled = true;
            defaultOption.textContent = 'Select SBU first';
            sitesSelect.appendChild(defaultOption);
        } else {
            // Collect all sites from selected SBUs
            let allSites = [];
            selectedSBUs.forEach(sbuId => {
                if (sbuSites[sbuId]) {
                    allSites = allSites.concat(sbuSites[sbuId]);
                }
            });
            
            // Sort sites: main sites first, then alphabetically, with SBU prefix when multiple SBUs selected
            allSites.sort((a, b) => {
                if (a.is_main !== b.is_main) {
                    return a.is_main ? -1 : 1;
                }
                return a.name.localeCompare(b.name);
            });
            
            // Add sites to dropdown
            allSites.forEach(site => {
                const option = document.createElement('option');
                option.value = site.id;
                
                // Add SBU prefix if multiple SBUs are selected
                const siteLabel = selectedSBUs.length > 1 
                    ? `${site.sbu_name} - ${site.name}${site.is_main ? ' (Main)' : ''}` 
                    : `${site.name}${site.is_main ? ' (Main)' : ''}`;
                
                option.textContent = siteLabel;
                sitesSelect.appendChild(option);
            });
        }
        
        // Destroy existing Select2 instance if it exists
        if ($(sitesSelect).data('select2')) {
            $(sitesSelect).select2('destroy');
        }
        
        // Initialize Select2
        $(sitesSelect).select2({
            placeholder: selectedSBUs.length > 0 ? 'Select Sites' : 'Select SBU first',
            allowClear: true,
            width: '100%',
            dropdownParent: $(sitesSelect).parent() // Ensure dropdown is properly positioned
        });
    }
    
    // Initialize site options based on initial checkbox values
    $(document).ready(function() {
        updateSiteOptions();
    });
    
    // Update site options when SBU checkboxes change
    sbuCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSiteOptions);
    });

    // Logo preview functionality
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) { // 2MB limit
                alert('File size must be less than 2MB');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logoPreview');
                preview.src = e.target.result;
                document.querySelector('.logo-preview-container').style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            document.querySelector('.logo-preview-container').style.display = 'none';
        }
    });

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
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-select form-select-lg mb-3" 
                                    name="questions[${questionIndex}][type]" required>
                                    <option value="" disabled selected>Select answer type</option>
                                    <option value="radio">Radio Button</option>
                                    <option value="star">Star Rating</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch">
                                    <input type="hidden" name="questions[${questionIndex}][required]" value="0">
                                    <input class="form-check-input" type="checkbox" 
                                        id="required${questionIndex}"
                                        name="questions[${questionIndex}][required]"
                                        value="1" checked>
                                    <label class="form-check-label" for="required${questionIndex}">
                                        Required Question
                                    </label>
                                </div>
                            </div>
                        </div>
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
        swalWithBootstrapButtons.fire({
            title: "Remove Question?",
            text: "Are you sure you want to remove this question? This cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, remove it!",
            cancelButtonText: "No, keep it!",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const card = button.closest('.question-card');
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                    updateQuestionNumbers();
                    swalWithBootstrapButtons.fire({
                        title: "Removed!",
                        text: "The question has been removed.",
                        icon: "success"
                    });
                }, 300);
            }
        });
    }

    function updateQuestionNumbers() {
        const questions = document.querySelectorAll('.question-card h5');
        questions.forEach((question, index) => {
            question.textContent = `Question ${index + 1}`;
        });
    }

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    
    function addPunctuationIfMissing(text) {
        if (!text) return text;
        // Check if the text already ends with punctuation (.?!:;)
        if (!/[.?!:;]$/.test(text.trim())) {
            return text.trim() + '.';
        }
        return text;
    }

    document.getElementById('createSurveyForm').addEventListener('submit', function(e) {
        // Prevent default to handle form submission manually
        e.preventDefault();
        
        const logo = document.getElementById('logo');
        if (!logo.files || logo.files.length === 0) {
            swalWithBootstrapButtons.fire({
                title: "Error!",
                text: "Please upload a survey logo.",
                icon: "error"
            });
            logo.focus();
            return false;
        }

        // Check if at least one SBU is selected
        const selectedSBUs = Array.from(sbuCheckboxes).filter(checkbox => checkbox.checked);
        if (selectedSBUs.length === 0) {
            swalWithBootstrapButtons.fire({
                title: "Error!",
                text: "Please select at least one SBU.",
                icon: "error"
            });
            // Focus on the first SBU checkbox
            if (sbuCheckboxes.length > 0) {
                sbuCheckboxes[0].focus();
            }
            return false;
        }

        // Check if at least one deployment site is selected
        const sites = document.getElementById('site_ids');
        const selectedSites = Array.from(sites.selectedOptions);
        if (selectedSites.length === 0) {
            swalWithBootstrapButtons.fire({
                title: "Error!",
                text: "Please select at least one deployment site.",
                icon: "error"
            });
            sites.focus();
            return false;
        }

        if (document.getElementById('questions-container').children.length === 0) {
            swalWithBootstrapButtons.fire({
                title: "Error!",
                text: "Please add at least one question to the survey.",
                icon: "error"
            });
            return false;
        }

        const titleInput = document.getElementById('title');
        titleInput.value = capitalizeFirstLetter(titleInput.value);

        const questionInputs = document.querySelectorAll('input[name^="questions"][name$="[text]"]');
        questionInputs.forEach(input => {
            // Apply both capitalization and punctuation check
            input.value = addPunctuationIfMissing(capitalizeFirstLetter(input.value));
            console.log('Question updated: ' + input.value);
        });
        
        swalWithBootstrapButtons.fire({
            title: "Create Survey?",
            text: "Are you sure you want to create this survey?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Yes, create it!",
            cancelButtonText: "No, cancel!",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Continue with form submission
                this.submit();
            }
        });
    });

    document.getElementById('closeFormBtn').addEventListener('click', function(e) {
        e.preventDefault();
        swalWithBootstrapButtons.fire({
            title: "Close Form?",
            text: "Are you sure you want to close the form? Any unsaved changes will be lost!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, close it!",
            cancelButtonText: "No, keep editing!",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = this.href;
            }
        });
    });
    
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

.form-control:focus, .form-select:focus {
    border-color: var(--accent-color);
    box-shadow: 0 0 0 0.25rem rgba(var(--accent-color-rgb), 0.25);
}

/* Select2 customization */
.select2-container--default .select2-selection--multiple {
    border-radius: 6px;
    border-color: #ced4da;
    min-height: 100px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    border-radius: 4px;
    padding: 5px 25px;
    margin: 5px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: white;
    margin-right: 5px;
    border: none;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #f8f9fa;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: var(--primary-color);
}

.select2-container--default .select2-search--inline .select2-search__field {
    margin-top: 7px;
}
.card-header {
    border-bottom: none;
    border-radius: 8px 8px 0 0 !important;
}

/* Modern SBU Checkbox Styling */
.sbu-selection-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #e9ecef !important;
    border-radius: 12px !important;
    transition: all 0.3s ease;
}

.sbu-selection-container:hover {
    border-color: var(--primary-color) !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.form-check-modern {
    background: white;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 15px;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.form-check-modern:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.form-check-modern .form-check-input {
    width: 20px;
    height: 20px;
    margin-top: 2px;
    margin-right: 10px;
    border: 2px solid #ced4da;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.form-check-modern .form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
    transform: scale(1.1);
}

.form-check-modern .form-check-input:focus {
    box-shadow: 0 0 0 0.25rem rgba(var(--primary-color-rgb), 0.25);
}

.form-check-modern .form-check-label {
    cursor: pointer;
    user-select: none;
    flex-grow: 1;
}

.form-check-modern .sbu-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    display: block;
    margin-bottom: 2px;
}

.form-check-modern:hover .sbu-name {
    color: var(--primary-color);
}

.form-check-modern .form-check-input:checked ~ .form-check-label .sbu-name {
    color: var(--primary-color);
}

/* Responsive design for checkboxes */
@media (max-width: 768px) {
    .sbu-selection-container .row {
        flex-direction: column;
    }
    
    .form-check-modern {
        margin-bottom: 10px;
    }
}
</style>
@endsection