@extends('layouts.app')

@section('content')
<!-- Prevent flash of unstyled content -->
<script>
    document.documentElement.style.setProperty('--transition-duration', '0ms');
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            document.documentElement.style.removeProperty('--transition-duration');
        }, 100);
    });
</script>

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
                                <div class="sbu-selection-container">
                                    <p class="text-muted mb-4 fs-6">Select one or both SBUs where you want to deploy this survey:</p>
                                    <div class="row g-3">
                                        @foreach($sbus as $sbu)
                                            <div class="col-md-6">
                                                <div class="sbu-card {{ in_array($sbu->id, old('sbu_ids', [])) ? 'selected' : '' }}" data-sbu-id="{{ $sbu->id }}">
                                                    <input class="sbu-checkbox d-none" type="checkbox" 
                                                           id="sbu_{{ $sbu->id }}" 
                                                           name="sbu_ids[]" 
                                                           value="{{ $sbu->id }}"
                                                           {{ in_array($sbu->id, old('sbu_ids', [])) ? 'checked' : '' }}>
                                                    
                                                    <div class="sbu-card-content">
                                                        <div class="sbu-card-header">
                                                            <div class="sbu-check-indicator">
                                                                <i class="fas fa-check"></i>
                                                            </div>
                                                        </div>
                                                        <div class="sbu-card-body">
                                                            <h5 class="sbu-name">{{ $sbu->name }}</h5>
                                                            <p class="sbu-sites-count">{{ $sbu->sites->count() }} sites available</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('sbu_ids')
                                        <div class="text-danger mt-3" role="alert">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="site_ids" class="col-md-3 col-form-label">{{ __('Deployment Sites') }}</label>
                            <div class="col-md-9">
                                <div class="sites-selection-container">
                                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
                                        <p class="text-muted mb-0 fs-6">Select deployment sites for your survey:</p>
                                        <div class="selection-controls d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                                            <button type="button" id="selectAllSites" class="btn btn-outline-primary btn-sm flex-fill flex-sm-grow-0 disabled" disabled>
                                                <i class="fas fa-check-double me-1"></i><span class="d-none d-sm-inline">Select All</span><span class="d-sm-none">All</span>
                                            </button>
                                            <button type="button" id="deselectAllSites" class="btn btn-outline-secondary btn-sm flex-fill flex-sm-grow-0 disabled" disabled>
                                                <i class="fas fa-times me-1"></i><span class="d-none d-sm-inline">Deselect All</span><span class="d-sm-none">None</span>
                                            </button>
                                        </div>
                                    </div>
                                    <select id="site_ids" class="form-select select2 form-select-lg @error('site_ids') is-invalid @enderror" name="site_ids[]" multiple required>
                                        <option value="" disabled>Select SBU first</option>
                                        @if(old('site_ids'))
                                            @foreach(old('site_ids') as $siteId)
                                                <option value="{{ $siteId }}" selected>Loading...</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <small class="text-muted mt-2 d-block">
                                        <i class="fas fa-info-circle me-1"></i>You can search and select multiple sites. Use the buttons above for quick selection.
                                    </small>
                                </div>
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

    // SBU card click functionality
    document.querySelectorAll('.sbu-card').forEach(card => {
        const checkbox = card.querySelector('.sbu-checkbox');
        
        // Note: Initial state is already set by Blade template, no need to set it here
        
        card.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Add selecting animation
            card.classList.add('selecting');
            setTimeout(() => card.classList.remove('selecting'), 300);
            
            // Toggle checkbox
            checkbox.checked = !checkbox.checked;
            
            // Toggle visual state
            if (checkbox.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
            
            // Update site options
            updateSiteOptions();
        });
        
        // Prevent checkbox from being clicked directly
        checkbox.addEventListener('click', function(e) {
            e.stopPropagation();
        });
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
                
                // Preserve old selected values
                const oldSiteIds = @json(old('site_ids', []));
                if (oldSiteIds.includes(site.id.toString()) || oldSiteIds.includes(site.id)) {
                    option.selected = true;
                }
                
                sitesSelect.appendChild(option);
            });
        }
        
        // Destroy existing Select2 instance if it exists
        if ($(sitesSelect).data('select2')) {
            $(sitesSelect).select2('destroy');
        }
        
        // Initialize Select2 with minimal flashing
        $(sitesSelect).select2({
            placeholder: selectedSBUs.length > 0 ? 'Select Sites' : 'Select SBU first',
            allowClear: true,
            width: '100%',
            dropdownParent: $(sitesSelect).parent(), // Ensure dropdown is properly positioned
            adaptContainerCssClass: function(clazz) {
                return clazz; // Copy classes from original element
            },
            templateSelection: function(data) {
                // Truncate long site names on mobile
                if (window.innerWidth < 576 && data.text && data.text.length > 20) {
                    return data.text.substring(0, 17) + '...';
                }
                return data.text;
            }
        });
        
        // Ensure the Select2 container is visible after initialization
        setTimeout(() => {
            $('.sites-selection-container .select2-container').css('opacity', '1');
        }, 50);
        
        // Update Select All/Deselect All button states
        updateSelectionButtons();
    }
    
    // Function to update Select All/Deselect All button states
    function updateSelectionButtons() {
        const selectAllBtn = document.getElementById('selectAllSites');
        const deselectAllBtn = document.getElementById('deselectAllSites');
        const hasSites = sitesSelect.options.length > 0 && !sitesSelect.options[0].disabled;
        
        if (hasSites) {
            selectAllBtn.disabled = false;
            deselectAllBtn.disabled = false;
            selectAllBtn.classList.remove('disabled');
            deselectAllBtn.classList.remove('disabled');
        } else {
            selectAllBtn.disabled = true;
            deselectAllBtn.disabled = true;
            selectAllBtn.classList.add('disabled');
            deselectAllBtn.classList.add('disabled');
        }
    }
    
    // Select All Sites functionality
    document.getElementById('selectAllSites').addEventListener('click', function() {
        if (this.disabled) return;
        
        const allOptions = Array.from(sitesSelect.options).filter(option => !option.disabled);
        const allValues = allOptions.map(option => option.value);
        
        $(sitesSelect).val(allValues).trigger('change');
        
        // Show feedback
        if (allValues.length > 0) {
            const siteCount = allValues.length;
            $(this).html('<i class="fas fa-check me-1"></i>All Selected');
            setTimeout(() => {
                $(this).html('<i class="fas fa-check-double me-1"></i>Select All');
            }, 2000);
        }
    });
    
    // Deselect All Sites functionality
    document.getElementById('deselectAllSites').addEventListener('click', function() {
        if (this.disabled) return;
        
        $(sitesSelect).val(null).trigger('change');
        
        // Show feedback
        $(this).html('<i class="fas fa-check me-1"></i>All Deselected');
        setTimeout(() => {
            $(this).html('<i class="fas fa-times me-1"></i>Deselect All');
        }, 2000);
    });
    
    // Update button states when selection changes
    $(sitesSelect).on('change', function() {
        const selectedCount = $(this).val() ? $(this).val().length : 0;
        const totalCount = Array.from(this.options).filter(option => !option.disabled).length;
        
        const selectAllBtn = document.getElementById('selectAllSites');
        const deselectAllBtn = document.getElementById('deselectAllSites');
        
        // Update Select All button appearance based on selection state
        if (selectedCount === totalCount && totalCount > 0) {
            selectAllBtn.classList.remove('btn-outline-primary');
            selectAllBtn.classList.add('btn-success');
            selectAllBtn.innerHTML = '<i class="fas fa-check me-1"></i>All Selected';
        } else {
            selectAllBtn.classList.remove('btn-success');
            selectAllBtn.classList.add('btn-outline-primary');
            selectAllBtn.innerHTML = '<i class="fas fa-check-double me-1"></i>Select All';
        }
        
        // Update Deselect All button appearance
        if (selectedCount > 0) {
            deselectAllBtn.classList.remove('btn-outline-secondary');
            deselectAllBtn.classList.add('btn-outline-warning');
        } else {
            deselectAllBtn.classList.remove('btn-outline-warning');
            deselectAllBtn.classList.add('btn-outline-secondary');
        }
    });
    
    // Initialize site options based on initial checkbox values
    $(document).ready(function() {
        // Mark JavaScript as initialized to prevent visual flashes
        document.body.classList.add('js-initialized');
        
        updateSiteOptions();
    });
    
    // Update site options when SBU checkboxes change (programmatically)
    sbuCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Update visual state of card if changed programmatically
            const card = this.closest('.sbu-card');
            if (card) {
                if (this.checked) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            }
            updateSiteOptions();
        });
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
    
    // Add responsive handling for Select2 on window resize
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            // Refresh Select2 to handle responsive changes
            const sitesSelect = document.getElementById('site_ids');
            if ($(sitesSelect).data('select2')) {
                $(sitesSelect).select2('close'); // Close dropdown if open
            }
        }, 250);
    });
    
    // Add first question by default
    window.onload = addQuestion;
</script>

<style>
/* Prevent layout shift during initialization */
body:not(.js-initialized) .sites-selection-container .select2-container {
    opacity: 0;
    transition: opacity 0.2s ease;
}

body.js-initialized .sites-selection-container .select2-container {
    opacity: 1;
}

/* Smooth initialization for the entire form */
.card-body {
    transition: opacity 0.3s ease;
}

body:not(.js-initialized) .sbu-card {
    transition: none !important;
}

body.js-initialized .sbu-card {
    transition: all var(--transition-duration, 0.3s) cubic-bezier(0.4, 0, 0.2, 1);
}

/* Ensure initial button states are properly styled */
.selection-controls .btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

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

.card-header {
    border-bottom: none;
    border-radius: 8px 8px 0 0 !important;
}

/* Modern SBU Card Styling */
.sbu-selection-container {
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #f1f3f8 100%);
    border-radius: 12px;
    border: 2px solid #e9ecf3;
    transition: all 0.3s ease;
}

.sbu-card {
    background: white;
    border: 2px solid #e9ecf3;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.sbu-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}

.sbu-card:hover::before {
    left: 100%;
}

.sbu-card:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px) scale(1.01);
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

.sbu-card.selected {
    border-color: var(--primary-color);
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 8px 25px rgba(var(--primary-color-rgb), 0.4);
}

.sbu-card.selected:hover {
    transform: translateY(-3px) scale(1.01);
    box-shadow: 0 10px 30px rgba(var(--primary-color-rgb), 0.5);
}

.sbu-card-content {
    text-align: center;
    position: relative;
    z-index: 2;
    width: 100%;
    padding: 0.75rem;
}

.sbu-card-header {
    position: relative;
    margin-bottom: 0.5rem;
}

.sbu-check-indicator {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 20px;
    height: 20px;
    background: var(--secondary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.7rem;
    opacity: 0;
    transform: scale(0);
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(var(--secondary-color-rgb), 0.3);
}

.sbu-card.selected .sbu-check-indicator {
    opacity: 1;
    transform: scale(1);
}

.sbu-card-body {
    text-align: center;
}

.sbu-name {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.125rem;
    transition: all 0.3s ease;
}

.sbu-card.selected .sbu-name {
    color: white;
}

.sbu-sites-count {
    font-size: 0.8rem;
    color: #6b7280;
    margin: 0;
    font-weight: 500;
    transition: all 0.3s ease;
}

.sbu-card.selected .sbu-sites-count {
    color: rgba(255,255,255,0.9);
}

/* Hover effects for unselected cards */
.sbu-card:not(.selected):hover .sbu-name {
    color: var(--primary-color);
}

.sbu-card:not(.selected):hover .sbu-sites-count {
    color: #4b5563;
}

/* Animation for selection */
@keyframes pulse-select {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.sbu-card.selecting {
    animation: pulse-select 0.3s ease-in-out;
}

/* Sites Selection Container Styling */
.sites-selection-container {
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fc 0%, #f1f3f8 100%);
    border-radius: 12px;
    border: 2px solid #e9ecf3;
    transition: all 0.3s ease;
}

.sites-selection-container:hover {
    border-color: #d1d9e6;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.selection-controls {
    display: flex;
    gap: 0.5rem;
}

.selection-controls .btn {
    font-size: 0.85rem;
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
    transition: all 0.3s ease;
    font-weight: 500;
    min-width: 110px;
}

.selection-controls .btn:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.selection-controls .btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

.selection-controls .btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-color: #28a745;
    color: white;
}

.selection-controls .btn-outline-warning {
    border-color: #ffc107;
    color: #856404;
}

.selection-controls .btn-outline-warning:hover:not(:disabled) {
    background-color: #ffc107;
    color: #212529;
}

/* Enhanced Select2 styling for sites */
.sites-selection-container .select2-container--default .select2-selection--multiple {
    border: 2px solid #e9ecf3;
    border-radius: 8px;
    min-height: 120px;
    background: white;
    transition: all 0.3s ease;
}

.sites-selection-container .select2-container--default .select2-selection--multiple:focus-within {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(var(--primary-color-rgb), 0.15);
}

.sites-selection-container .select2-container--default .select2-selection--multiple .select2-selection__choice {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    border: none;
    color: white;
    border-radius: 6px;
    padding: 6px 35px 6px 25px;
    margin: 6px;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.sites-selection-container .select2-container--default .select2-selection--multiple .select2-selection__choice:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.sites-selection-container .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: rgba(255,255,255,0.8);
    margin-right: 8px;
    border: none;
    font-size: 1.1rem;
    transition: color 0.2s ease;
}

.sites-selection-container .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: white;
    background: rgba(255,255,255,0.2);
    border-radius: 3px;
}

.sites-selection-container .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
}

.sites-selection-container .select2-container--default .select2-search--inline .select2-search__field {
    margin-top: 8px;
    font-size: 0.95rem;
}

.sites-selection-container .select2-container--default .select2-search--inline .select2-search__field::placeholder {
    color: #6c757d;
    font-style: italic;
}

/* Responsive design */
@media (max-width: 768px) {
    .sbu-selection-container {
        padding: 1rem;
    }
    
    .sites-selection-container {
        padding: 1rem;
    }
    
    .selection-controls {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .selection-controls .btn {
        width: 100%;
        min-width: auto;
    }
    
    .sbu-card {
        height: 90px;
    }
    
    .sbu-name {
        font-size: 0.95rem;
    }
    
    .sbu-sites-count {
        font-size: 0.75rem;
    }
    
    .sites-selection-container .select2-container--default .select2-selection--multiple {
        min-height: 100px;
    }
}

/* Mobile Responsive Styles for Deployment Sites */
@media (max-width: 575.98px) {
    .sites-selection-container {
        padding: 1rem;
    }
    
    /* Make selection controls full width on mobile */
    .selection-controls {
        width: 100% !important;
    }
    
    .selection-controls .btn {
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }
    
    /* Reduce Select2 height on mobile */
    .sites-selection-container .select2-container--default .select2-selection--multiple {
        min-height: 80px;
        font-size: 0.875rem;
    }
    
    /* Reduce choice tag size on mobile */
    .sites-selection-container .select2-container--default .select2-selection--multiple .select2-selection__choice {
        padding: 4px 30px 4px 20px;
        margin: 4px;
        font-size: 0.8rem;
        max-width: calc(100% - 8px);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* Stack the description and controls vertically on mobile */
    .sites-selection-container .d-flex.flex-column.flex-md-row {
        gap: 1rem !important;
    }
    
    .sites-selection-container p {
        font-size: 0.875rem;
    }
}

@media (max-width: 767.98px) {
    /* Reduce margins on tablet */
    .sites-selection-container .select2-container--default .select2-selection--multiple .select2-selection__choice {
        margin: 3px;
        padding: 5px 32px 5px 22px;
    }
}

/* Ensure Select2 dropdown is responsive */
.select2-dropdown {
    max-width: 100vw;
    overflow-x: hidden;
}

.select2-results__option {
    word-wrap: break-word;
    white-space: normal;
}
</style>
@endsection