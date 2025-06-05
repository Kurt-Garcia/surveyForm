@extends('layouts.app')

@section('content')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid py-4 px-4" style="background: var(--background-color); min-height: 100vh;">
    <!-- Hero Section with Gradient Background -->

    <div class="row justify-content-center">
        <!-- Create New Admin Form - Centered -->
        <div class="col-lg-6 col-xl-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-gradient text-white py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0 fw-bold">
                                <i class="bi bi-plus-circle-fill me-2"></i>Add New Administrator
                            </h4>
                            <p class="mb-0 opacity-90 small mt-1">Create a new admin account with full privileges</p>
                        </div>
                        <a href="javascript:void(0)" onclick="confirmClose()" class="btn btn-light btn-sm rounded-pill" data-bs-toggle="tooltip" data-bs-placement="top" title="Go Back">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-3 shadow-sm">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger border-0 rounded-3 shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        </div>
                    @endif


                    <form action="{{ route('admin.admins.store') }}" method="POST" id="adminForm" onsubmit="return confirmSubmit(event)">
                        @csrf
                        
                        <!-- SBU Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark">
                                <i class="bi bi-building me-1 text-primary"></i>Strategic Business Units
                            </label>
                            <div class="sbu-selection-container">
                                <p class="text-muted mb-3 fs-6">Select one or more SBUs where this admin will have access:</p>
                                <div class="row g-3">
                                    @foreach($sbus as $sbu)
                                        <div class="col-md-6">
                                            <div class="sbu-card" data-sbu-id="{{ $sbu->id }}">
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
                        
                        <!-- Site Selection -->
                        <div class="mb-4">
                            <label for="site_ids" class="form-label fw-semibold text-dark">
                                <i class="bi bi-geo-alt me-1 text-success"></i>Site Locations
                            </label>
                            <div class="sites-selection-container">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                                    <p class="text-muted mb-0 fs-6 flex-grow-1 me-3 mb-md-0 mb-2">Select sites where this admin will have access:</p>
                                    <div class="selection-controls flex-shrink-0">
                                        <button type="button" id="selectAllSites" class="btn btn-outline-primary btn-sm me-2" disabled>
                                            <i class="fas fa-check-double me-1"></i>Select All
                                        </button>
                                        <button type="button" id="deselectAllSites" class="btn btn-outline-secondary btn-sm" disabled>
                                            <i class="fas fa-times me-1"></i>Deselect All
                                        </button>
                                    </div>
                                </div>
                                <select id="site_ids" class="form-select select2 form-select-lg @error('site_ids') is-invalid @enderror" name="site_ids[]" multiple required>
                                    <option value="" disabled>Select SBU first...</option>
                                </select>
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle me-1"></i>You can search and select multiple sites. Use the buttons above for quick selection.
                                </small>
                            </div>
                            @error('site_ids')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Personal Information -->
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="name" class="form-label fw-semibold text-dark">
                                    <i class="bi bi-person me-1 text-info"></i>Full Name
                                </label>
                                <input type="text" class="form-control form-control-lg border-0 shadow-sm @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" placeholder="Enter full name..." 
                                       autocomplete="name" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Contact Information -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-7">
                                <label for="email" class="form-label fw-semibold text-dark">
                                    <i class="bi bi-envelope me-1 text-warning"></i>Email Address
                                </label>
                                <input type="email" class="form-control form-control-lg border-0 shadow-sm @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" 
                                       autocomplete="email" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5">
                                <label for="contact_number" class="form-label fw-semibold text-dark">
                                    <i class="bi bi-telephone me-1 text-danger"></i>Contact Number
                                </label>
                                <input type="tel" class="form-control form-control-lg border-0 shadow-sm @error('contact_number') is-invalid @enderror" 
                                       id="contact_number" name="contact_number" value="{{ old('contact_number') }}" placeholder="09123456789" 
                                       autocomplete="tel" required maxlength="11" pattern="[0-9]{1,11}" 
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                                @error('contact_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Password Fields -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold text-dark">
                                    <i class="bi bi-shield-lock me-1 text-secondary"></i>Password
                                </label>
                                <input type="password" class="form-control form-control-lg border-0 shadow-sm @error('password') is-invalid @enderror" 
                                       id="password" name="password" placeholder="Enter password..." 
                                       autocomplete="new-password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label fw-semibold text-dark">
                                    <i class="bi bi-shield-check me-1 text-secondary"></i>Confirm Password
                                </label>
                                <input type="password" class="form-control form-control-lg border-0 shadow-sm" 
                                       id="password_confirmation" name="password_confirmation" placeholder="Confirm password..." 
                                       autocomplete="new-password" required>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-grid gap-2">
                            <button type="submit" id="submitButton" class="btn btn-primary btn-lg rounded-pill shadow-sm py-3">
                                <i class="bi bi-person-gear me-2"></i>Create Administrator Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Modern Card Styling with Gradient */
    .bg-gradient {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
    }
    
    .card {
        border: none !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
    }

    /* Form Styling */
    .form-control, .form-select {
        border-radius: 12px !important;
        padding: 12px 16px !important;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--accent-color) !important;
        box-shadow: 0 0 0 0.25rem rgba(var(--accent-color-rgb), 0.15) !important;
        transform: translateY(-2px);
    }

    .form-label {
        font-weight: 600 !important;
        color: var(--text-color) !important;
        margin-bottom: 8px !important;
    }

    /* Button Styling */
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
        border: none !important;
        font-weight: 600 !important;
        transition: all 0.3s ease;
        cursor: pointer !important;
        position: relative !important;
        z-index: 999 !important;
        pointer-events: auto !important;
    }

    .btn-primary:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(var(--primary-color-rgb), 0.3) !important;
    }

    .btn-primary:active {
        transform: translateY(0) scale(0.98);
    }

    .btn-primary:focus {
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(var(--primary-color-rgb), 0.25) !important;
    }

    /* Ensure submit button is clickable */
    #submitButton {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        cursor: pointer !important;
        pointer-events: auto !important;
    }

    /* Alert Styling */
    .alert {
        border-radius: 12px !important;
        border: none !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    }

    /* Validation Styling */
    .text-success {
        color: #28a745 !important;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .is-valid {
        border-color: #28a745 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.98-.98-.03-.03L6.6 2.38A.5.5 0 0 1 7.07 3l-3.67 3.68-.03.03-.98.98a.5.5 0 0 1-.71 0l-1.48-1.48a.5.5 0 0 1 .71-.71l1.12 1.12Z'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right calc(0.375em + 0.1875rem) center !important;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
    }

    .is-invalid {
        border-color: #dc3545 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 2.4 2.4M8.2 4.6l-2.4 2.4'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right calc(0.375em + 0.1875rem) center !important;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem) !important;
    }

    /* Loading Animation */
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(var(--primary-color-rgb), 0.3);
        border-radius: 50%;
        border-top-color: var(--primary-color);
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .display-4 {
            font-size: 2.5rem !important;
        }
        
        .card-body {
            padding: 1.5rem !important;
        }
        
        .form-control-lg, .form-select-lg {
            padding: 10px 14px !important;
        }
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

    /* Ensure Select2 dropdown doesn't overflow on smaller screens */
    .select2-dropdown {
        z-index: 9999 !important;
    }
    
    .select2-container {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    
    .select2-container--default .select2-selection--multiple {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        overflow-x: hidden !important;
        word-wrap: break-word !important;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        width: 100% !important;
        overflow-x: hidden !important;
        word-wrap: break-word !important;
        box-sizing: border-box !important;
    }
    
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        max-width: calc(100% - 20px) !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
        box-sizing: border-box !important;
    }

    /* iPad Pro responsive fixes - prevent overflow */
    @media screen and (min-width: 1024px) and (max-width: 1366px) {
        .sites-selection-container {
            padding: 1.25rem;
        }
        
        .sites-selection-container .d-flex.justify-content-between.align-items-center {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start !important;
        }
        
        .sites-selection-container .selection-controls {
            display: flex;
            flex-direction: row;
            gap: 0.5rem;
            width: 100%;
            justify-content: flex-start;
            min-width: auto;
        }
        
        .sites-selection-container .selection-controls .btn {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
            min-width: auto;
            flex: 0 1 auto;
            white-space: nowrap;
        }
        
        .sites-selection-container .text-muted {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            flex: none;
            width: 100%;
        }
        
        .select2-container--default .select2-selection--multiple {
            min-height: 100px;
            max-height: 150px;
            overflow-y: auto;
            width: 100% !important;
            max-width: 100%;
            box-sizing: border-box;
        }
        
        .select2-container {
            width: 100% !important;
            max-width: 100%;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            padding: 3px 6px;
            margin: 2px;
            font-size: 0.85rem;
            max-width: calc(100% - 10px);
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* SBU cards responsive adjustments for iPad Pro */
        .sbu-selection-container {
            padding: 1.25rem;
        }
        
        .sbu-card {
            min-height: 110px;
        }
        
        .sbu-name {
            font-size: 1rem;
        }
        
        .sbu-sites-count {
            font-size: 0.8rem;
        }
    }
    
    /* Standard tablet responsive (768px to 1023px) */
    @media (min-width: 769px) and (max-width: 1023px) {
        .sites-selection-container {
            padding: 1.25rem;
        }
        
        .sites-selection-container .d-flex.justify-content-between.align-items-center {
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start !important;
        }
        
        .sites-selection-container .selection-controls {
            width: 100%;
            gap: 0.5rem;
            min-width: auto;
        }
        
        .sites-selection-container .selection-controls .btn {
            flex: 1;
            font-size: 0.85rem;
        }
        
        .sites-selection-container .text-muted {
            width: 100%;
            flex: none;
        }
    }
    
    /* Mobile responsive improvements */
    @media (max-width: 768px) {
        .sbu-selection-container,
        .sites-selection-container {
            padding: 1rem;
        }
        
        .sites-selection-container .d-flex.justify-content-between.align-items-center {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start !important;
        }
        
        .sites-selection-container .selection-controls {
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
            min-width: auto;
        }
        
        .sites-selection-container .selection-controls .btn {
            width: 100%;
        }
        
        .sites-selection-container .text-muted {
            width: 100%;
            flex: none;
        }
        
        .sbu-card {
            min-height: 100px;
        }
        
        .sbu-name {
            font-size: 1rem;
        }
        
        .sbu-sites-count {
            font-size: 0.8rem;
        }
        
        .sites-selection-container .select2-container--default .select2-selection--multiple {
            min-height: 120px;
            max-height: 200px;
        }
        
        .select2-container {
            width: 100% !important;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            font-size: 0.8rem;
            padding: 4px 8px;
            margin: 3px;
        }
    }
    
    /* iOS specific fixes */
    @supports (-webkit-touch-callout: none) {
        .sites-selection-container {
            -webkit-overflow-scrolling: touch;
        }
        
        .select2-container--default .select2-selection--multiple {
            -webkit-overflow-scrolling: touch;
        }
    }
    
    /* Additional breakpoint for small tablets */
    @media (min-width: 600px) and (max-width: 900px) {
        .sites-selection-container .d-flex.justify-content-between.align-items-center {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 1rem;
        }
        
        .sites-selection-container .text-muted {
            width: 100% !important;
            margin-bottom: 0 !important;
        }
        
        .sites-selection-container .selection-controls {
            width: 100% !important;
            justify-content: flex-start !important;
        }
        
        .sites-selection-container .selection-controls .btn {
            flex: 1 1 auto;
            max-width: 48%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // SBU and Site dropdown relationship
        const sbuCheckboxes = document.querySelectorAll('.sbu-checkbox');
        const sitesSelect = document.getElementById('site_ids');
        const selectAllBtn = document.getElementById('selectAllSites');
        const deselectAllBtn = document.getElementById('deselectAllSites');
        
        // Store all sites data from PHP
        const allSites = @json($sbus->pluck('sites', 'id'));
        
        // Function to update site options based on selected SBUs
        function updateSiteOptions() {
            const selectedSBUs = Array.from(sbuCheckboxes)
                .filter(checkbox => checkbox.checked)
                .map(checkbox => checkbox.value);
            
            // Clear current options
            sitesSelect.innerHTML = '';
            
            // If SBUs are selected, populate with corresponding sites
            if (selectedSBUs.length > 0) {
                const sitesMap = new Map();
                
                selectedSBUs.forEach(sbuId => {
                    if (allSites[sbuId]) {
                        allSites[sbuId].forEach(site => {
                            if (!sitesMap.has(site.id)) {
                                sitesMap.set(site.id, site);
                            }
                        });
                    }
                });
                
                // Sort sites by name and add to select
                const sortedSites = Array.from(sitesMap.values()).sort((a, b) => a.name.localeCompare(b.name));
                sortedSites.forEach(site => {
                    const option = document.createElement('option');
                    option.value = site.id;
                    option.textContent = site.name;
                    
                    // Check if this option should be selected (for form validation redisplay)
                    const oldSiteIds = @json(old('site_ids', []));
                    if (oldSiteIds.includes(site.id.toString())) {
                        option.selected = true;
                    }
                    sitesSelect.appendChild(option);
                });
                
                // Enable control buttons
                selectAllBtn.disabled = false;
                deselectAllBtn.disabled = false;
            } else {
                // Add placeholder option
                const option = document.createElement('option');
                option.value = '';
                option.disabled = true;
                option.textContent = 'Select SBU first';
                sitesSelect.appendChild(option);
                
                // Disable control buttons
                selectAllBtn.disabled = true;
                deselectAllBtn.disabled = true;
            }
            
            // Initialize Select2 after updating options
            if (typeof $ !== 'undefined') {
                $('#site_ids').select2({
                    placeholder: selectedSBUs.length > 0 ? 'Select sites...' : 'Select SBU first',
                    allowClear: true,
                    width: '100%',
                    dropdownParent: $('#site_ids').parent(),
                    adaptContainerCssClass: function(clazz) {
                        return clazz;
                    },
                    adaptDropdownCssClass: function(clazz) {
                        return clazz;
                    }
                });
            }
            
            updateButtonStates();
        }
        
        // Update button states based on selection
        function updateButtonStates() {
            if (!sitesSelect.options.length || sitesSelect.options[0].disabled) {
                return;
            }
            
            const totalCount = sitesSelect.options.length;
            const selectedCount = Array.from(sitesSelect.selectedOptions).length;
            
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
        }
        
        // Select All Sites button handler
        selectAllBtn.addEventListener('click', function() {
            Array.from(sitesSelect.options).forEach(option => {
                if (!option.disabled) option.selected = true;
            });
            
            if (typeof $ !== 'undefined') {
                $('#site_ids').trigger('change');
            }
            updateButtonStates();
        });
        
        // Deselect All Sites button handler
        deselectAllBtn.addEventListener('click', function() {
            Array.from(sitesSelect.options).forEach(option => option.selected = false);
            
            if (typeof $ !== 'undefined') {
                $('#site_ids').val(null).trigger('change');
            }
            updateButtonStates();
        });
        
        // Sites select change handler
        sitesSelect.addEventListener('change', updateButtonStates);
        
        // SBU card click functionality
        document.querySelectorAll('.sbu-card').forEach(card => {
            const checkbox = card.querySelector('.sbu-checkbox');
            
            // Set initial state
            if (checkbox.checked) {
                card.classList.add('selected');
            }
            
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

        // SBU checkboxes change handlers
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
        
        // Initialize site options based on initial checkbox values
        updateSiteOptions();
        
        // SweetAlert2 configuration
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success me-3",
                cancelButton: "btn btn-outline-danger",
                actions: 'gap-2 justify-content-center'
            },
            buttonsStyling: false
        });
        
        console.log('SweetAlert2 initialized:', typeof Swal !== 'undefined');
        console.log('Form found:', document.getElementById('adminForm') !== null);
        
        // Add submit button click handler as fallback
        const submitButton = document.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.addEventListener('click', function(e) {
                console.log('Submit button clicked');
                return confirmSubmit(e);
            });
        }
        
        // Also add form submit handler
        const adminForm = document.getElementById('adminForm');
        if (adminForm) {
            adminForm.addEventListener('submit', function(e) {
                console.log('Form submit event triggered');
                return confirmSubmit(e);
            });
        }
        
        // Debug: Check if button is clickable
        console.log('Submit button found:', submitButton !== null);
        console.log('Submit button enabled:', submitButton ? !submitButton.disabled : false);
        
        // Test button click
        if (submitButton) {
            submitButton.style.cursor = 'pointer';
            submitButton.style.pointerEvents = 'auto';
            
            // Add a simple test click
            console.log('Adding test click handler');
            submitButton.onclick = function(e) {
                console.log('Button onclick triggered');
                e.preventDefault();
                return confirmSubmit(e);
            };
        }
        
        // Emergency fallback - direct form submission if SweetAlert fails
        window.emergencySubmit = function() {
            console.log('Emergency submit triggered');
            const form = document.getElementById('adminForm');
            if (form) {
                // Remove the onsubmit handler temporarily
                form.onsubmit = null;
                form.submit();
            }
        };
        
        // Name field validation
        const nameField = document.getElementById('name');
        let nameIsValid = true;
        
        nameField.addEventListener('blur', function() {
            const name = nameField.value.trim();
            if (name) {
                // Remove any existing feedback
                const existingFeedback = document.getElementById('name-validation-feedback');
                if (existingFeedback) {
                    existingFeedback.remove();
                }
                
                // Check if name exists in admin_users or users table via AJAX
                fetch(`/admin/check-name-availability?name=${encodeURIComponent(name)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            // Name already exists
                            nameIsValid = false;
                            const errorFeedback = document.createElement('div');
                            errorFeedback.className = 'text-danger mt-1';
                            errorFeedback.id = 'name-validation-feedback';
                            errorFeedback.innerHTML = `<small><i class="bi bi-exclamation-triangle-fill me-1"></i>${data.message}</small>`;
                            nameField.parentNode.appendChild(errorFeedback);
                            nameField.classList.add('is-invalid');
                        } else {
                            // Name is available
                            nameIsValid = true;
                            const successFeedback = document.createElement('div');
                            successFeedback.className = 'text-success mt-1';
                            successFeedback.id = 'name-validation-feedback';
                            successFeedback.innerHTML = '<small><i class="bi bi-check-circle-fill me-1"></i>Name is available</small>';
                            nameField.parentNode.appendChild(successFeedback);
                            nameField.classList.remove('is-invalid');
                            nameField.classList.add('is-valid');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking name:', error);
                    });
            }
        });
        
        // Email field validation
        const emailField = document.getElementById('email');
        let emailIsValid = true;
        
        emailField.addEventListener('blur', function() {
            const email = emailField.value.trim();
            if (email) {
                // Remove any existing feedback
                const existingFeedback = document.getElementById('email-validation-feedback');
                if (existingFeedback) {
                    existingFeedback.remove();
                }
                
                // Check if email exists in admin_users or users table via AJAX
                fetch(`/admin/check-email-availability?email=${encodeURIComponent(email)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            // Email already exists
                            emailIsValid = false;
                            const errorFeedback = document.createElement('div');
                            errorFeedback.className = 'text-danger mt-1';
                            errorFeedback.id = 'email-validation-feedback';
                            errorFeedback.innerHTML = `<small><i class="bi bi-exclamation-triangle-fill me-1"></i>${data.message}</small>`;
                            emailField.parentNode.appendChild(errorFeedback);
                            emailField.classList.add('is-invalid');
                        } else {
                            // Email is available
                            emailIsValid = true;
                            const successFeedback = document.createElement('div');
                            successFeedback.className = 'text-success mt-1';
                            successFeedback.id = 'email-validation-feedback';
                            successFeedback.innerHTML = '<small><i class="bi bi-check-circle-fill me-1"></i>Email is available</small>';
                            emailField.parentNode.appendChild(successFeedback);
                            emailField.classList.remove('is-invalid');
                            emailField.classList.add('is-valid');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking email:', error);
                    });
            }
        });
        
        // Contact number field validation
        const contactNumberField = document.getElementById('contact_number');
        let contactNumberIsValid = true;
        
        // Add real-time input validation for contact number
        contactNumberField.addEventListener('input', function() {
            // Remove any non-numeric characters and limit to 11 digits
            let value = this.value.replace(/[^0-9]/g, '');
            if (value.length > 11) {
                value = value.slice(0, 11);
            }
            this.value = value;
            
            // Update validation feedback based on length
            const existingFeedback = document.getElementById('contact-number-validation-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }
            
            if (value.length > 0 && value.length < 10) {
                contactNumberIsValid = false;
                const warningFeedback = document.createElement('div');
                warningFeedback.className = 'text-warning mt-1';
                warningFeedback.id = 'contact-number-validation-feedback';
                warningFeedback.innerHTML = `<small><i class="bi bi-exclamation-triangle me-1"></i>Contact number should be at least 10 digits (${value.length}/11)</small>`;
                contactNumberField.parentNode.appendChild(warningFeedback);
                contactNumberField.classList.remove('is-valid');
                contactNumberField.classList.add('is-invalid');
            } else if (value.length >= 10 && value.length <= 11) {
                contactNumberIsValid = true;
                const successFeedback = document.createElement('div');
                successFeedback.className = 'text-success mt-1';
                successFeedback.id = 'contact-number-validation-feedback';
                successFeedback.innerHTML = `<small><i class="bi bi-check-circle-fill me-1"></i>Valid contact number (${value.length}/11)</small>`;
                contactNumberField.parentNode.appendChild(successFeedback);
                contactNumberField.classList.remove('is-invalid');
                contactNumberField.classList.add('is-valid');
            }
        });
        
        contactNumberField.addEventListener('blur', function() {
            const contactNumber = contactNumberField.value.trim();
            if (contactNumber && contactNumber.length >= 10) {
                // Remove any existing feedback
                const existingFeedback = document.getElementById('contact-number-validation-feedback');
                if (existingFeedback) {
                    existingFeedback.remove();
                }
                
                // Check if contact number exists in admin_users or users table via AJAX
                fetch(`/admin/check-contact-number-availability?contact_number=${encodeURIComponent(contactNumber)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            // Contact number already exists
                            contactNumberIsValid = false;
                            const errorFeedback = document.createElement('div');
                            errorFeedback.className = 'text-danger mt-1';
                            errorFeedback.id = 'contact-number-validation-feedback';
                            errorFeedback.innerHTML = `<small><i class="bi bi-exclamation-triangle-fill me-1"></i>${data.message}</small>`;
                            contactNumberField.parentNode.appendChild(errorFeedback);
                            contactNumberField.classList.add('is-invalid');
                        } else {
                            // Contact number is available
                            contactNumberIsValid = true;
                            const successFeedback = document.createElement('div');
                            successFeedback.className = 'text-success mt-1';
                            successFeedback.id = 'contact-number-validation-feedback';
                            successFeedback.innerHTML = '<small><i class="bi bi-check-circle-fill me-1"></i>Contact number is available</small>';
                            contactNumberField.parentNode.appendChild(successFeedback);
                            contactNumberField.classList.remove('is-invalid');
                            contactNumberField.classList.add('is-valid');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking contact number:', error);
                    });
            }
        });
        
        // Password field validation and real-time matching
        const passwordField = document.getElementById('password');
        const passwordConfirmationField = document.getElementById('password_confirmation');
        let passwordIsValid = true;
        let passwordsMatch = false;
        
        // Function to check password match in real-time
        function checkPasswordMatch() {
            const password = passwordField.value;
            const confirmation = passwordConfirmationField.value;
            
            // Remove existing match feedback
            const existingMatchFeedback = document.getElementById('password-match-feedback');
            if (existingMatchFeedback) {
                existingMatchFeedback.remove();
            }
            
            // Only show feedback if both fields have values
            if (password && confirmation) {
                if (password === confirmation) {
                    // Passwords match
                    passwordsMatch = true;
                    const successFeedback = document.createElement('div');
                    successFeedback.className = 'text-success mt-1';
                    successFeedback.id = 'password-match-feedback';
                    successFeedback.innerHTML = '<small><i class="bi bi-check-circle-fill me-1"></i>Passwords match</small>';
                    passwordConfirmationField.parentNode.appendChild(successFeedback);
                    passwordConfirmationField.classList.remove('is-invalid');
                    passwordConfirmationField.classList.add('is-valid');
                } else {
                    // Passwords don't match
                    passwordsMatch = false;
                    const errorFeedback = document.createElement('div');
                    errorFeedback.className = 'text-danger mt-1';
                    errorFeedback.id = 'password-match-feedback';
                    errorFeedback.innerHTML = '<small><i class="bi bi-exclamation-triangle-fill me-1"></i>Passwords do not match</small>';
                    passwordConfirmationField.parentNode.appendChild(errorFeedback);
                    passwordConfirmationField.classList.remove('is-valid');
                    passwordConfirmationField.classList.add('is-invalid');
                }
            } else if (confirmation && !password) {
                // Confirmation field has value but password field is empty
                passwordsMatch = false;
                const warningFeedback = document.createElement('div');
                warningFeedback.className = 'text-warning mt-1';
                warningFeedback.id = 'password-match-feedback';
                warningFeedback.innerHTML = '<small><i class="bi bi-exclamation-triangle me-1"></i>Please enter password first</small>';
                passwordConfirmationField.parentNode.appendChild(warningFeedback);
                passwordConfirmationField.classList.remove('is-valid');
                passwordConfirmationField.classList.add('is-invalid');
            } else {
                // Reset validation classes if fields are empty
                passwordConfirmationField.classList.remove('is-valid', 'is-invalid');
                passwordsMatch = false;
            }
        }
        
        // Add real-time password matching validation
        passwordField.addEventListener('input', checkPasswordMatch);
        passwordConfirmationField.addEventListener('input', checkPasswordMatch);
        
        passwordField.addEventListener('blur', function() {
            const password = passwordField.value.trim();
            if (password) {
                // Remove any existing feedback
                const existingFeedback = document.getElementById('password-validation-feedback');
                if (existingFeedback) {
                    existingFeedback.remove();
                }
                
                // Check if password exists in admin_users or users table via AJAX
                fetch(`/admin/check-password-availability?password=${encodeURIComponent(password)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            // Password already exists
                            passwordIsValid = false;
                            const errorFeedback = document.createElement('div');
                            errorFeedback.className = 'text-danger mt-1';
                            errorFeedback.id = 'password-validation-feedback';
                            errorFeedback.innerHTML = `<small><i class="bi bi-exclamation-triangle-fill me-1"></i>${data.message}</small>`;
                            passwordField.parentNode.appendChild(errorFeedback);
                            passwordField.classList.add('is-invalid');
                        } else {
                            // Password is available
                            passwordIsValid = true;
                            const successFeedback = document.createElement('div');
                            successFeedback.className = 'text-success mt-1';
                            successFeedback.id = 'password-validation-feedback';
                            successFeedback.innerHTML = '<small><i class="bi bi-check-circle-fill me-1"></i>Password is available</small>';
                            passwordField.parentNode.appendChild(successFeedback);
                            passwordField.classList.remove('is-invalid');
                            passwordField.classList.add('is-valid');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking password:', error);
                    });
            }
        });
    });

    // Function to handle form submission confirmation (global scope)
    window.confirmSubmit = function(event) {
        console.log('confirmSubmit called', event);
        event.preventDefault();
        
        // Check if SweetAlert2 is loaded
        if (typeof Swal === 'undefined') {
            console.error('SweetAlert2 not loaded, submitting form directly');
            document.getElementById('adminForm').submit();
            return false;
        }

        // Validate required fields before showing confirmation
        const form = document.getElementById('adminForm');
        const requiredFields = form.querySelectorAll('[required]');
        let allFieldsValid = true;
        let firstInvalidField = null;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                allFieldsValid = false;
                field.classList.add('is-invalid');
                if (!firstInvalidField) {
                    firstInvalidField = field;
                }
            } else {
                field.classList.remove('is-invalid');
            }
        });

        // Check custom validation flags
        if (typeof nameIsValid !== 'undefined' && !nameIsValid) {
            allFieldsValid = false;
            if (!firstInvalidField) {
                firstInvalidField = document.getElementById('name');
            }
        }

        if (typeof emailIsValid !== 'undefined' && !emailIsValid) {
            allFieldsValid = false;
            if (!firstInvalidField) {
                firstInvalidField = document.getElementById('email');
            }
        }

        if (typeof contactNumberIsValid !== 'undefined' && !contactNumberIsValid) {
            allFieldsValid = false;
            if (!firstInvalidField) {
                firstInvalidField = document.getElementById('contact_number');
            }
        }

        if (typeof passwordIsValid !== 'undefined' && !passwordIsValid) {
            allFieldsValid = false;
            if (!firstInvalidField) {
                firstInvalidField = document.getElementById('password');
            }
        }

        // Check password confirmation match using the real-time validation flag
        if (typeof passwordsMatch !== 'undefined' && !passwordsMatch) {
            const password = document.getElementById('password').value;
            const passwordConfirmation = document.getElementById('password_confirmation').value;
            
            if (password && passwordConfirmation) {
                allFieldsValid = false;
                document.getElementById('password_confirmation').classList.add('is-invalid');
                if (!firstInvalidField) {
                    firstInvalidField = document.getElementById('password_confirmation');
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'Password and confirmation password do not match.',
                    customClass: {
                        confirmButton: "btn btn-primary"
                    },
                    buttonsStyling: false
                });
                return false;
            }
        }

        // Check if at least one SBU is selected
        const sbuCheckboxes = document.querySelectorAll('.sbu-checkbox');
        const selectedSBUs = Array.from(sbuCheckboxes).filter(checkbox => checkbox.checked);
        if (selectedSBUs.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'SBU Selection Required',
                text: 'Please select at least one Strategic Business Unit.',
                customClass: {
                    confirmButton: "btn btn-primary"
                },
                buttonsStyling: false
            });
            // Focus on the first SBU checkbox
            if (sbuCheckboxes.length > 0) {
                sbuCheckboxes[0].focus();
            }
            return false;
        }

        // Check if at least one site is selected
        const sitesSelect = document.getElementById('site_ids');
        const selectedSites = Array.from(sitesSelect.selectedOptions);
        if (selectedSites.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Site Selection Required',
                text: 'Please select at least one site location.',
                customClass: {
                    confirmButton: "btn btn-primary"
                },
                buttonsStyling: false
            });
            sitesSelect.focus();
            return false;
        }

        if (!allFieldsValid) {
            Swal.fire({
                icon: 'warning',
                title: 'Incomplete Form',
                text: 'Please fill in all required fields correctly.',
                customClass: {
                    confirmButton: "btn btn-primary"
                },
                buttonsStyling: false
            }).then(() => {
                if (firstInvalidField) {
                    firstInvalidField.focus();
                }
            });
            return false;
        }

        // Show SweetAlert2 confirmation
        Swal.fire({
            title: 'Create Administrator Account?',
            text: "Please confirm to create a new admin account!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-check-circle me-2"></i>Yes, Create Account',
            cancelButtonText: '<i class="bi bi-x-circle me-2"></i>Cancel',
            reverseButtons: true,
            customClass: {
                confirmButton: "btn btn-success me-3",
                cancelButton: "btn btn-outline-danger",
                actions: "gap-2 justify-content-center",
            },
            buttonsStyling: false,
            focusConfirm: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                const submitButton = document.getElementById('submitButton');
                const originalButtonText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="loading-spinner me-2"></span>Creating Account...';
                
                // Show loading alert
                Swal.fire({
                    title: 'Creating Account...',
                    html: 'Please wait while we create the administrator account.',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                console.log('User confirmed, submitting form');
                
                // Submit the form
                if (form) {
                    // Remove the onsubmit handler temporarily to prevent recursion
                    form.onsubmit = null;
                    form.submit();
                } else {
                    console.error('Form not found');
                    // Restore button state
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                    Swal.close();
                }
            }
        });
        
        return false;
    };

    // Function to handle close button confirmation (global scope)
    window.confirmClose = function() {
        Swal.fire({
            title: "Discard Changes?",
            text: "Any unsaved changes will be lost!",
            icon: "warning",
            showCancelButton: true,
            cancelButtonText: "Stay here",
            confirmButtonText: "Yes, leave page!",
            reverseButtons: true,
            customClass: {
                confirmButton: "btn btn-success me-3",
                cancelButton: "btn btn-outline-danger",
                actions: "gap-2 justify-content-center",
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to dashboard
                window.location.href = "{{ route('admin.dashboard') }}";
            }
        });
    };
</script>
@endsection