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
                                <div class="position-relative">
                                    <input id="title" type="text" 
                                        class="form-control form-control-lg @error('title') is-invalid @enderror" 
                                        name="title" value="{{ old('title') }}" 
                                        required autocomplete="title" autofocus
                                        placeholder="Enter your survey title">
                                    
                                    <div id="title-validation-spinner" class="position-absolute top-50 end-0 translate-middle-y me-3" style="display: none;">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Checking...</span>
                                        </div>
                                    </div>
                                    
                                    <div id="title-validation-icon" class="position-absolute top-50 end-0 translate-middle-y me-3" style="display: none;">
                                        <i class="fas fa-check text-success"></i>
                                    </div>
                                </div>

                                <div id="title-validation-message" class="mt-2" style="display: none;"></div>

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
                                    @if($sbus->count() > 0)
                                        <p class="text-muted mb-4 fs-6">
                                            @if($sbus->count() == 1)
                                                Select your SBU to deploy this survey:
                                            @else
                                                Select {{ $sbus->count() == 1 ? 'the' : 'one or more' }} SBU{{ $sbus->count() > 1 ? 's' : '' }} where you want to deploy this survey:
                                            @endif
                                        </p>
                                        <div class="row g-3 {{ $sbus->count() == 1 ? 'justify-content-center' : '' }}">
                                            @foreach($sbus as $sbu)
                                                <div class="{{ $sbus->count() == 1 ? 'col-md-6 col-lg-4' : 'col-md-6' }}">
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
                                    @else
                                        <div class="alert alert-warning text-center" role="alert">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>No SBUs Available</strong><br>
                                            <small>You don't have access to any SBU. Please contact your administrator to get access permissions.</small>
                                        </div>
                                    @endif
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

                        <div class="form-group row mb-4">
                            <label for="department_logo" class="col-md-3 col-form-label">{{ __('Department Logo') }}</label>
                            <div class="col-md-9">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="department-logo-preview-container" style="display: none;">
                                        <img id="departmentLogoPreview" src="#" alt="Department Logo Preview" style="max-width: 100px; max-height: 100px; object-fit: contain;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" 
                                            class="form-control @error('department_logo') is-invalid @enderror" 
                                            id="department_logo" 
                                            name="department_logo" 
                                            accept="image/*">
                                        <small class="text-muted">Department logo for consent form (top right corner). Recommended size: 200x200px. Max file size: 2MB</small>
                                        @error('department_logo')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($sbus->count() > 0)
                        <!-- Bulk Question Creation Feature -->
                        <div class="card shadow-sm mb-4" style="border: 2px solid #e3f2fd; background: linear-gradient(135deg, #f8f9fc 0%, #e3f2fd 100%);">
                            <div class="card-header" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); color: white; border-bottom: none;">
                                <h5 class="mb-0"><i class="fas fa-magic me-2"></i>{{ __('Bulk Create Questions') }}</h5>
                                <small class="opacity-75">{{ __('Quickly create multiple questions at once') }}</small>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="bulk-question-count" class="form-label fw-bold">{{ __('Number of Questions') }}</label>
                                        <input type="number" id="bulk-question-count" class="form-control form-control-lg" 
                                               min="1" max="20" value="5" placeholder="5">
                                        <small class="text-muted">{{ __('1-20 questions') }}</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="bulk-question-type" class="form-label fw-bold">{{ __('Answer Type') }}</label>
                                        <select id="bulk-question-type" class="form-select form-select-lg">
                                            <option value="radio">{{ __('Radio Button') }}</option>
                                            <option value="star">{{ __('Star Rating') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">{{ __('Required Questions') }}</label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" type="checkbox" id="bulk-required" checked>
                                            <label class="form-check-label fw-semibold" for="bulk-required">
                                                {{ __('Make all required') }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="button" class="btn btn-success btn-lg w-100" onclick="addBulkQuestions()" 
                                                style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); border: none;">
                                            <i class="fas fa-bolt me-2"></i>{{ __('Create Questions') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div id="questions-container" class="mb-4">
                            <!-- Questions will be added here dynamically -->
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12 d-flex flex-column flex-md-row justify-content-center gap-3">
                                @if($sbus->count() > 0)
                                    <button type="button" class="btn btn-info btn-lg" onclick="addQuestion()" style="background-color: var(--secondary-color); border-color: var(--secondary-color); color: #fff;">
                                        <i class="fas fa-plus-circle me-2"></i>{{ __('Add Single Question') }}
                                    </button>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>{{ __('Create Survey') }}
                                    </button>
                                @else
                                    <div class="alert alert-info text-center w-100" role="alert">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Cannot create survey without SBU access. Please contact your administrator.
                                    </div>
                                @endif
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
        @if($sbus->count() > 0)
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
        @endif
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
        
        @if($sbus->count() > 0)
            updateSiteOptions();
        @endif
        
        // Initialize validation for any existing questions (e.g., from old form values)
        const existingQuestions = document.querySelectorAll('.question-text-input');
        existingQuestions.forEach(input => {
            addQuestionValidation(input);
        });
    });
    
    // Real-time title validation
    let titleValidationTimeout;
    let titleIsValid = false;
    
    document.getElementById('title').addEventListener('input', function() {
        const title = this.value.trim();
        const spinner = document.getElementById('title-validation-spinner');
        const icon = document.getElementById('title-validation-icon');
        const message = document.getElementById('title-validation-message');
        
        // Clear previous timeout
        clearTimeout(titleValidationTimeout);
        
        // Reset validation state
        titleIsValid = false;
        this.classList.remove('is-valid', 'is-invalid');
        spinner.style.display = 'none';
        icon.style.display = 'none';
        message.style.display = 'none';
        
        if (title.length < 2) {
            return; // Don't validate until at least 2 characters
        }
        
        // Show spinner
        spinner.style.display = 'block';
        
        // Debounce the validation request
        titleValidationTimeout = setTimeout(() => {
            fetch('{{ route("admin.surveys.check-title-uniqueness") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title: title
                })
            })
            .then(response => response.json())
            .then(data => {
                spinner.style.display = 'none';
                
                if (data.available) {
                    // Title is available
                    titleIsValid = true;
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                    icon.innerHTML = '<i class="fas fa-check text-success"></i>';
                    icon.style.display = 'block';
                    message.innerHTML = '<small class="text-success"><i class="fas fa-check-circle me-1"></i>' + data.message + '</small>';
                    message.style.display = 'block';
                } else {
                    // Title is not available
                    titleIsValid = false;
                    this.classList.add('is-invalid');
                    this.classList.remove('is-valid');
                    icon.innerHTML = '<i class="fas fa-times text-danger"></i>';
                    icon.style.display = 'block';
                    message.innerHTML = '<small class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>' + data.message + '</small>';
                    message.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error validating title:', error);
                spinner.style.display = 'none';
                titleIsValid = false;
            });
        }, 500); // Wait 500ms after user stops typing
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
                swalWithBootstrapButtons.fire({
                    title: "File too large!",
                    text: "Logo image must be less than 2MB. Please choose a smaller file.",
                    icon: "error"
                });
                this.value = '';
                document.querySelector('.logo-preview-container').style.display = 'none';
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

    // Department Logo preview functionality
    document.getElementById('department_logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) { // 2MB limit
                swalWithBootstrapButtons.fire({
                    title: "File too large!",
                    text: "Department logo image must be less than 2MB. Please choose a smaller file.",
                    icon: "error"
                });
                this.value = '';
                document.querySelector('.department-logo-preview-container').style.display = 'none';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('departmentLogoPreview');
                preview.src = e.target.result;
                document.querySelector('.department-logo-preview-container').style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            document.querySelector('.department-logo-preview-container').style.display = 'none';
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
                        <div class="position-relative">
                            <input type="text" class="form-control form-control-lg mb-3 question-text-input" 
                                name="questions[${questionIndex}][text]" 
                                placeholder="Enter your question here" required
                                data-question-index="${questionIndex}">
                            <div class="question-validation-spinner position-absolute top-50 end-0 translate-middle-y me-3" style="display: none;">
                                <div class="spinner-border spinner-border-sm text-warning" role="status">
                                    <span class="visually-hidden">Checking...</span>
                                </div>
                            </div>
                            <div class="question-validation-icon position-absolute top-50 end-0 translate-middle-y me-3" style="display: none;">
                                <i class="fas fa-check text-success"></i>
                            </div>
                        </div>
                        <div class="question-validation-message" style="display: none; margin-top: -0.75rem; margin-bottom: 0.75rem;"></div>
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
            
            // Add real-time validation for the new question
            const questionInput = questionDiv.querySelector('.question-text-input');
            addQuestionValidation(questionInput);
        }, 50);
    }
    
    function addBulkQuestions() {
        const count = parseInt(document.getElementById('bulk-question-count').value);
        const type = document.getElementById('bulk-question-type').value;
        const required = document.getElementById('bulk-required').checked;
        
        if (!count || count < 1 || count > 20) {
            swalWithBootstrapButtons.fire({
                title: "Invalid Number!",
                text: "Please enter a number between 1 and 20.",
                icon: "error"
            });
            return;
        }
        
        swalWithBootstrapButtons.fire({
            title: "Create Bulk Questions?",
            text: `Are you sure you want to create ${count} questions with ${type === 'radio' ? 'Radio Button' : 'Star Rating'} type${required ? ' (all required)' : ''}?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: `Yes, create ${count} questions!`,
            cancelButtonText: "No, cancel!",
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const container = document.getElementById('questions-container');
                let startIndex = container.children.length;
                let questionsCreated = 0;
                
                // Create questions with staggered animation
                for (let i = 0; i < count; i++) {
                    setTimeout(() => {
                        const questionIndex = startIndex + i;
                        const questionDiv = document.createElement('div');
                        questionDiv.className = 'card shadow-sm mb-3 question-card bulk-created';
                        questionDiv.style.opacity = '0';
                        questionDiv.style.transform = 'translateY(20px)';
                        
                        const typeDisplayName = type === 'radio' ? 'Radio Button' : 'Star Rating';
                        
                        questionDiv.innerHTML = `
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h5 class="mb-3">
                                            Question ${questionIndex + 1}
                                            <span class="badge bg-success ms-2 fs-6">
                                                <i class="fas fa-bolt me-1"></i>Bulk Created
                                            </span>
                                        </h5>
                                        <div class="position-relative">
                                            <input type="text" class="form-control form-control-lg mb-3 question-text-input" 
                                                name="questions[${questionIndex}][text]" 
                                                placeholder="Enter your question here" required
                                                data-question-index="${questionIndex}">
                                            <div class="question-validation-spinner position-absolute top-50 end-0 translate-middle-y me-3" style="display: none;">
                                                <div class="spinner-border spinner-border-sm text-warning" role="status">
                                                    <span class="visually-hidden">Checking...</span>
                                                </div>
                                            </div>
                                            <div class="question-validation-icon position-absolute top-50 end-0 translate-middle-y me-3" style="display: none;">
                                                <i class="fas fa-check text-success"></i>
                                            </div>
                                        </div>
                                        <div class="question-validation-message" style="display: none; margin-top: -0.75rem; margin-bottom: 0.75rem;"></div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <select class="form-select form-select-lg mb-3" 
                                                    name="questions[${questionIndex}][type]" required>
                                                    <option value="" disabled>Select answer type</option>
                                                    <option value="radio" ${type === 'radio' ? 'selected' : ''}>Radio Button</option>
                                                    <option value="star" ${type === 'star' ? 'selected' : ''}>Star Rating</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input type="hidden" name="questions[${questionIndex}][required]" value="0">
                                                    <input class="form-check-input" type="checkbox" 
                                                        id="required${questionIndex}"
                                                        name="questions[${questionIndex}][required]"
                                                        value="1" ${required ? 'checked' : ''}>
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
                        
                        // Animate the question into view
                        setTimeout(() => {
                            questionDiv.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                            questionDiv.style.opacity = '1';
                            questionDiv.style.transform = 'translateY(0)';
                            
                            // Add real-time validation for the new question
                            const questionInput = questionDiv.querySelector('.question-text-input');
                            addQuestionValidation(questionInput);
                        }, 50);
                        
                        questionsCreated++;
                        
                        // Show success message when all questions are created
                        if (questionsCreated === count) {
                            setTimeout(() => {
                                swalWithBootstrapButtons.fire({
                                    title: "Success!",
                                    text: `Successfully created ${count} questions with ${typeDisplayName} type.`,
                                    icon: "success",
                                    timer: 2000,
                                    timerProgressBar: true
                                });
                                
                                // Remove bulk-created badges and styling after 3 seconds
                                setTimeout(() => {
                                    document.querySelectorAll('.bulk-created .badge').forEach(badge => {
                                        badge.style.opacity = '0';
                                        badge.style.transform = 'scale(0)';
                                        setTimeout(() => badge.remove(), 300);
                                    });
                                    document.querySelectorAll('.bulk-created').forEach(card => {
                                        card.classList.remove('bulk-created');
                                        card.style.border = '';
                                        card.style.boxShadow = '';
                                    });
                                }, 3000);
                            }, 300);
                        }
                    }, i * 150); // Stagger the creation by 150ms each
                }
            }
        });
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
            // Preserve any badges that might exist
            const existingBadge = question.querySelector('.badge');
            const badgeHtml = existingBadge ? existingBadge.outerHTML : '';
            question.innerHTML = `Question ${index + 1} ${badgeHtml}`;
        });
        
        // Update input names to maintain correct indexing
        const questionCards = document.querySelectorAll('.question-card');
        questionCards.forEach((card, index) => {
            const textInput = card.querySelector('input[name*="[text]"]');
            const typeSelect = card.querySelector('select[name*="[type]"]');
            const hiddenRequired = card.querySelector('input[type="hidden"][name*="[required]"]');
            const checkboxRequired = card.querySelector('input[type="checkbox"][name*="[required]"]');
            
            if (textInput) {
                textInput.name = `questions[${index}][text]`;
                textInput.setAttribute('data-question-index', index);
            }
            if (typeSelect) typeSelect.name = `questions[${index}][type]`;
            if (hiddenRequired) hiddenRequired.name = `questions[${index}][required]`;
            if (checkboxRequired) {
                checkboxRequired.name = `questions[${index}][required]`;
                checkboxRequired.id = `required${index}`;
                const label = card.querySelector(`label[for*="required"]`);
                if (label) label.setAttribute('for', `required${index}`);
            }
        });
        
        // Re-validate all questions after renumbering
        setTimeout(() => {
            validateAllQuestions();
        }, 100);
    }

    // Question validation functionality
    let questionValidationTimeouts = {};
    const validatedQuestions = new Set();

    function addQuestionValidation(questionInput) {
        let validationTimeout;
        
        questionInput.addEventListener('input', function() {
            const currentValue = this.value.trim();
            const questionIndex = this.getAttribute('data-question-index');
            const spinner = this.parentElement.querySelector('.question-validation-spinner');
            const icon = this.parentElement.querySelector('.question-validation-icon');
            const message = this.parentElement.nextElementSibling;
            
            // Clear previous timeout
            clearTimeout(questionValidationTimeouts[questionIndex]);
            
            // Reset validation state
            validatedQuestions.delete(questionIndex);
            this.classList.remove('is-valid', 'is-invalid');
            spinner.style.display = 'none';
            icon.style.display = 'none';
            message.style.display = 'none';
            
            if (currentValue.length < 3) {
                return; // Don't validate until at least 3 characters
            }
            
            // Show spinner
            spinner.style.display = 'block';
            
            // Debounce the validation
            questionValidationTimeouts[questionIndex] = setTimeout(() => {
                validateQuestionUniqueness(this, currentValue, questionIndex);
            }, 500);
        });
    }

    function validateQuestionUniqueness(inputElement, questionText, currentQuestionIndex) {
        const spinner = inputElement.parentElement.querySelector('.question-validation-spinner');
        const icon = inputElement.parentElement.querySelector('.question-validation-icon');
        const message = inputElement.parentElement.nextElementSibling;
        
        // Get all question inputs except the current one
        const allQuestionInputs = document.querySelectorAll('.question-text-input');
        const otherQuestions = Array.from(allQuestionInputs).filter(input => {
            const index = input.getAttribute('data-question-index');
            return index !== currentQuestionIndex && input.value.trim().length > 0;
        });
        
        // Check for duplicates (case-insensitive)
        const normalizedCurrentText = questionText.toLowerCase().trim();
        const isDuplicate = otherQuestions.some(otherInput => {
            const otherText = otherInput.value.toLowerCase().trim();
            return otherText === normalizedCurrentText;
        });
        
        // Hide spinner
        spinner.style.display = 'none';
        
        if (isDuplicate) {
            // Question is duplicate
            validatedQuestions.delete(currentQuestionIndex);
            inputElement.classList.add('is-invalid');
            inputElement.classList.remove('is-valid');
            icon.innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i>';
            icon.style.display = 'block';
            message.innerHTML = '<small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>This question already exists in the survey. Please make it unique.</small>';
            message.style.display = 'block';
        } else {
            // Question is unique
            validatedQuestions.add(currentQuestionIndex);
            inputElement.classList.add('is-valid');
            inputElement.classList.remove('is-invalid');
            icon.innerHTML = '<i class="fas fa-check text-success"></i>';
            icon.style.display = 'block';
            message.innerHTML = '<small class="text-success"><i class="fas fa-check-circle me-1"></i>Question is unique and valid.</small>';
            message.style.display = 'block';
        }
    }

    function validateAllQuestions() {
        const allQuestionInputs = document.querySelectorAll('.question-text-input');
        allQuestionInputs.forEach(input => {
            const questionText = input.value.trim();
            const questionIndex = input.getAttribute('data-question-index');
            
            if (questionText.length >= 3) {
                setTimeout(() => {
                    validateQuestionUniqueness(input, questionText, questionIndex);
                }, 100 * parseInt(questionIndex)); // Stagger validation to avoid conflicts
            }
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
        
        // Check if title is valid
        const titleInput = document.getElementById('title');
        if (!titleIsValid || titleInput.value.trim().length < 2) {
            swalWithBootstrapButtons.fire({
                title: "Invalid Title!",
                text: "Please enter a valid and unique survey title.",
                icon: "error"
            });
            titleInput.focus();
            return false;
        }
        
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

        // Check for duplicate questions before submission
        const questionInputs = document.querySelectorAll('.question-text-input');
        const questionTexts = Array.from(questionInputs).map(input => input.value.trim().toLowerCase());
        const duplicateQuestions = questionTexts.filter((text, index) => 
            text.length > 0 && questionTexts.indexOf(text) !== index
        );

        if (duplicateQuestions.length > 0) {
            swalWithBootstrapButtons.fire({
                title: "Duplicate Questions Found!",
                text: "Please ensure all questions are unique. Some questions appear to be duplicated.",
                icon: "error"
            });
            
            // Highlight duplicate questions
            questionInputs.forEach(input => {
                const text = input.value.trim().toLowerCase();
                if (duplicateQuestions.includes(text)) {
                    input.classList.add('is-invalid');
                    input.focus();
                }
            });
            return false;
        }

        // Check for empty questions
        const emptyQuestions = Array.from(questionInputs).filter(input => input.value.trim().length < 3);
        if (emptyQuestions.length > 0) {
            swalWithBootstrapButtons.fire({
                title: "Empty Questions Found!",
                text: "Please fill in all questions with at least 3 characters.",
                icon: "error"
            });
            emptyQuestions[0].focus();
            return false;
        }

        const titleInputForm = document.getElementById('title');
        titleInputForm.value = capitalizeFirstLetter(titleInputForm.value);

        const allQuestionInputs = document.querySelectorAll('input[name^="questions"][name$="[text]"]');
        allQuestionInputs.forEach(input => {
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
    
    // Optional: Add first question by default (commented out to let users choose bulk or single creation)
    // window.onload = addQuestion;
</script>

<style>
/* Title validation styling */
#title.is-valid {
    border-color: #28a745;
    padding-right: calc(1.5em + 1rem);
    background-image: none;
}

#title.is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 1rem);
    background-image: none;
}

#title-validation-spinner .spinner-border {
    width: 1.2rem;
    height: 1.2rem;
    border-width: 2px;
}

#title-validation-icon {
    font-size: 1.1rem;
    z-index: 5;
}

#title-validation-message {
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Question validation styling */
.question-text-input.is-valid {
    border-color: #28a745;
    padding-right: calc(1.5em + 1rem);
    background-image: none;
}

.question-text-input.is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 1rem);
    background-image: none;
}

.question-validation-spinner .spinner-border {
    width: 1.2rem;
    height: 1.2rem;
    border-width: 2px;
}

.question-validation-icon {
    font-size: 1.1rem;
    z-index: 5;
}

.question-validation-message {
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.question-validation-message small {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

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

/* Bulk question creation styles */
.bulk-created .badge {
    transition: all 0.3s ease;
    animation: pulse-badge 2s infinite;
}

@keyframes pulse-badge {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.bulk-created {
    position: relative;
    border: 2px solid #28a745 !important;
    box-shadow: 0 0 10px rgba(40, 167, 69, 0.3) !important;
}

/* Smooth animations for bulk creation */
.question-card {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Enhanced form input styling */
#bulk-question-count:focus,
#bulk-question-type:focus,
#bulk-required:focus {
    border-color: #2196f3;
    box-shadow: 0 0 0 0.25rem rgba(33, 150, 243, 0.25);
}

/* Mobile responsive for bulk creation */
@media (max-width: 768px) {
    .bulk-creation-card .row.g-3 > div {
        margin-bottom: 1rem;
    }
    
    .bulk-creation-card .col-md-3 {
        width: 100%;
    }
}
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

/* Single SBU Card Styling - Enhanced centering and size */
.row.justify-content-center .sbu-card {
    max-width: 300px;
    height: 120px;
    margin: 0 auto;
}

.row.justify-content-center .sbu-card-content {
    padding: 1rem;
}

.row.justify-content-center .sbu-name {
    font-size: 1.1rem;
    font-weight: 700;
}

.row.justify-content-center .sbu-sites-count {
    font-size: 0.9rem;
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
    
    /* Single SBU responsive styling */
    .row.justify-content-center .sbu-card {
        max-width: 100%;
        height: 100px;
    }
    
    .row.justify-content-center .sbu-card-content {
        padding: 0.75rem;
    }
    
    .row.justify-content-center .sbu-name {
        font-size: 1rem;
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