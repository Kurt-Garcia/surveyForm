@extends('layouts.app-user')

@section('title', 'Welcome to Survey Form')

@section('content')
    <link href="{{ asset('css/index.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="{{ asset('js/lib/smooth-pagination.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="hero-section">
        <div class="pattern-overlay"></div>
        <div class="container position-relative">
            <header class="welcome-header text-center">
                <h1 class="display-4 fw-bold animate-text">Share Your Thoughts</h1>
                <p class="lead animate-text-delay" style="font-family: var(--body-font)">Your opinion matters! Select a survey below to get started.</p>
            </header>
        </div>
    </div>

    <div class="container survey-container mt-4">
        <div class="search-container mb-4 d-flex justify-content-end">
            <form id="search-form" action="{{ route('index') }}" method="GET" class="w-100 d-flex justify-content-end">
                <div class="input-group search-modern" style="max-width: 400px;">
                    <input type="text" id="survey-search" name="search" class="form-control search-input-modern" placeholder="Search surveys..." value="{{ request('search') }}">
                    <button class="btn btn-search-modern" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="surveys-content">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="surveys-grid">
            @forelse($surveys as $survey)
                @php
                    $hasResponded = session('account_name') ? App\Models\SurveyResponseHeader::hasResponded($survey->id, session('account_name')) : false;
                    $responseCount = App\Models\SurveyResponseHeader::where('survey_id', $survey->id)->count();
                @endphp
                <div class="col">
                    <div class="card h-100 survey-card shadow-sm hover-lift">
                        <div class="card-body">
                            <div class="survey-logo-wrapper text-center mb-3">
                                @if($survey->logo)
                                    <img src="{{ asset('storage/' . $survey->logo) }}" alt="{{ $survey->title }} Logo" class="survey-logo-large">
                                @else
                                    <i class="fas fa-poll fa-2x"></i>
                                @endif
                            </div>
                            <h4 class="card-title">{{ strtoupper($survey->title) }}</h4>
                            <div class="survey-meta mb-2">
                                @if($survey->sbus->count() > 0)
                                    @foreach($survey->sbus as $sbu)
                                        <span class="badge bg-primary me-1">{{ $sbu->name }}</span>
                                    @endforeach
                                @endif
                                <small class="text-muted">
                                    @if($survey->sites->count() > 0)
                                        <i class="fas fa-map-marker-alt me-1"></i> 
                                        @formatSitesList($survey->sites)
                                    @endif
                                </small>
                            </div>
                            <div class="d-flex justify-content-between mt-3 mb-3">
                                <div class="survey-info">
                                    <div class="text-muted mb-2">
                                        <i class="fas fa-question-circle me-1"></i>
                                        {{ $survey->questions->count() }} questions
                                    </div>
                                    <div class="text-muted">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        {{ $responseCount }} responses
                                    </div>
                                </div>
                                @if($hasResponded)
                                <div class="responded-badge">
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Completed</span>
                                </div>
                                @endif
                            </div>
                            <div class="d-flex gap-2 mt-auto">
                                <a href="{{ route('surveys.show', $survey) }}" class="btn btn-start btn-primary flex-grow-1" style="font-family: var(--body-font)">
                                    <i class="fas fa-eye me-1"></i> View Survey
                                </a>
                                <a href="{{ route('surveys.responses.index', $survey) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                                <button type="button" class="btn btn-outline-primary broadcast-btn" data-survey-id="{{ $survey->id }}" data-survey-title="{{ $survey->title }}">
                                    <i class="fas fa-bullhorn"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card empty-state py-5 text-center">
                        <div class="card-body">
                            <i class="fas fa-clipboard-question fs-1 mb-3 text-muted"></i>
                            <h4 class="mb-2">No Surveys Available</h4>
                            <p class="text-muted">Check back soon for new surveys!</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
        
        <div class="pagination-container mt-4 d-flex flex-column flex-md-row justify-content-md-between align-items-md-center gap-3 gap-md-0">
            @if($surveys->hasPages())
            <div class="pagination-info text-center text-md-start mb-2 mb-md-0">
                Showing {{ $surveys->firstItem() }} to {{ $surveys->lastItem() }} of {{ $surveys->total() }} surveys
            </div>
            @endif
            <div class="d-flex justify-content-center">
                {{ $surveys->links() }}
            </div>
        </div>
        </div>
    </div>

    <style>
    .alert {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
        min-width: 300px;
    }
    
    /* Modern Search Styles */
    .search-modern {
        border-radius: 30px;
        overflow: hidden;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .search-modern:hover {
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }
    
    .search-input-modern {
        border: none;
        padding: 12px 20px;
        background: #f8f9fa;
        font-family: var(--body-font);
    }
    
    .search-input-modern:focus {
        background: white;
        box-shadow: none;
    }
    
    .btn-search-modern {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 0 20px;
        transition: all 0.3s ease;
    }
    
    .btn-search-modern:hover {
        background: var(--primary-color);
        opacity: 0.9;
        transform: scale(1.05);
    }
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
    }
    .pagination-info {
        font-size: 0.9rem;
        color: #666;
    }
    .pagination-container .pagination {
        margin: 0;
        justify-content: center;
    }
    .pagination .page-item .page-link {
        color: var(--primary-color);
        border: 1px solid var(--primary-color);
        margin: 0 5px;
        border-radius: 4px;
        transition: all 0.3s ease;
    }
    .pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }
    .pagination .page-link:hover {
        background-color: var(--primary-color);
        color: white;
    }
    
    /* Card Styles */
    .survey-card {
        border-radius: 12px;
        border: none;
        border-left: 4px solid var(--primary-color);
        transition: all 0.3s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .survey-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        border-left: 4px solid var(--secondary-color);
    }
    .card-icon {
        display: inline-block;
        background-color: rgba(var(--primary-color-rgb), 0.1);
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .survey-info {
        font-size: 0.9rem;
        font-family: var(--body-font);
    }
    .responded-badge {
        align-self: flex-start;
    }
    .badge {
        font-weight: 500;
        padding: 8px 12px;
        border-radius: 30px;
        font-family: var(--body-font);
    }
    .btn-start {
        border-radius: 8px;
        font-weight: 500;
        position: relative;
        z-index: 10;
    }
    .btn-outline-secondary {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        position: relative;
        z-index: 10;
    }
    .empty-state {
        border-radius: 12px;
        border: 2px dashed #e9ecef;
    }
    .card-body {
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
    
    /* Smooth Pagination Styles */
    .surveys-content {
        min-height: 300px;
    }
    
    /* Fix for button clickability */
    .survey-card .btn {
        cursor: pointer !important;
        pointer-events: auto !important;
    }
    .d-flex.gap-2.mt-auto {
        position: relative;
        z-index: 10;
    }
    .survey-logo-large {
        min-width: 80px;
        min-height: 80px;
        max-width: 100px;
        max-height: 80px;
        object-fit: contain;
        margin: 0 auto;
    }
    
    /* Broadcast Modal Styles */
    .customer-list {
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #eee;
        border-radius: 6px;
        padding: 0.5rem;
    }
    
    .customer-item {
        padding: 10px 15px;
        border-bottom: 1px solid #f5f5f5;
        display: flex;
        align-items: center;
        transition: background-color 0.2s;
    }
    
    .customer-item:last-child {
        border-bottom: none;
    }
    
    .customer-item:hover {
        background-color: #f8f9fa;
    }
    
    .customer-item label {
        margin-bottom: 0;
        cursor: pointer;
        flex: 1;
        display: flex;
        align-items: center;
    }
    
    .customer-item input[type="checkbox"] {
        margin-right: 12px;
    }
    
    .customer-details {
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    
    .customer-name {
        font-weight: 500;
    }
    
    .customer-email {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    .customer-code {
        color: #6c757d;
        font-size: 0.8rem;
        background: #f0f0f0;
        padding: 2px 8px;
        border-radius: 12px;
        margin-left: auto;
    }
    
    .selected-count {
        font-weight: 500;
        color: var(--primary-color);
    }
    
    /* Broadcast button */
    .broadcast-btn {
        width: 40px;
        height: 38px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Success Animation */
    .broadcast-success {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1051;
        background: #28a745;
        color: white;
        padding: 15px 20px;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        opacity: 0;
        transform: translateY(-20px);
        transition: all 0.3s ease;
    }
    
    .broadcast-success.show {
        opacity: 1;
        transform: translateY(0);
    }
    
    .broadcast-success i {
        margin-right: 10px;
        font-size: 20px;
    }

    .font-theme{
         font-family: var(--body-font);
    }
    
    /* iPad Specific Styles */
    @media screen and (min-width: 768px) and (max-width: 1024px) {
        /* Button container improvements for iPad */
        .survey-card .d-flex.gap-2.mt-auto {
            gap: 0.5rem !important;
            margin-top: 1.25rem !important;
            flex-wrap: nowrap !important;
            align-items: stretch !important;
        }
        
        /* View Survey Button (Primary) */
        .survey-card .btn-start.btn-primary {
            padding: 0.75rem 0.75rem !important;
            font-size: 0.85rem !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            min-height: 42px !important;
            flex: 1 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            white-space: nowrap !important;
            text-decoration: none !important;
            line-height: 1.2 !important;
        }
        
        /* Responses/Chart Button (Secondary) */
        .survey-card .btn-outline-secondary {
            padding: 0.75rem 0.75rem !important;
            font-size: 1rem !important;
            border-radius: 8px !important;
            min-height: 42px !important;
            min-width: 42px !important;
            max-width: 42px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-width: 1.5px !important;
            flex-shrink: 0 !important;
            text-decoration: none !important;
        }
        
        /* Broadcast Button (Primary Outline) */
        .survey-card .btn-outline-primary.broadcast-btn {
            width: 42px !important;
            height: 42px !important;
            min-width: 42px !important;
            padding: 0 !important;
            border-radius: 8px !important;
            border-width: 1.5px !important;
            font-size: 1rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-shrink: 0 !important;
        }
        
        /* Icon adjustments for better visibility */
        .survey-card .btn-start i {
            font-size: 0.85rem !important;
            margin-right: 0.4rem !important;
        }
        
        .survey-card .btn-outline-secondary i {
            font-size: 1rem !important;
            margin: 0 !important;
        }
        
        .survey-card .broadcast-btn i {
            font-size: 0.95rem !important;
            margin: 0 !important;
        }
        
        /* Hover effects optimized for iPad */
        .survey-card .btn-start:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 8px rgba(var(--bs-primary-rgb), 0.25) !important;
        }
        
        .survey-card .btn-outline-secondary:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 3px 6px rgba(0,0,0,0.15) !important;
            background-color: var(--bs-secondary) !important;
            color: white !important;
        }
        
        .survey-card .btn-outline-primary.broadcast-btn:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 3px 6px rgba(var(--bs-primary-rgb), 0.2) !important;
            background-color: var(--bs-primary) !important;
            color: white !important;
        }
        
        /* Card body adjustments for better spacing */
        .survey-card .card-body {
            padding: 1.25rem !important;
            display: flex !important;
            flex-direction: column !important;
        }
        
        /* Better touch targets and accessibility */
        .survey-card .btn {
            transition: all 0.2s ease !important;
            user-select: none !important;
            -webkit-tap-highlight-color: transparent !important;
            cursor: pointer !important;
            pointer-events: auto !important;
            position: relative !important;
            z-index: 10 !important;
        }
        
        /* Ensure buttons don't break on smaller iPad screens */
        .survey-card .btn-start {
            min-width: 0 !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }
        
        /* Fix for flex-grow-1 on iPad */
        .survey-card .flex-grow-1 {
            flex-grow: 1 !important;
            flex-shrink: 1 !important;
            flex-basis: 0 !important;
        }
    }
    
    /* iPad Pro Specific (larger iPad screens) */
    @media screen and (min-width: 1025px) and (max-width: 1366px) {
        /* Button container for iPad Pro */
        .survey-card .d-flex.gap-2.mt-auto {
            gap: 0.6rem !important;
            margin-top: 1.5rem !important;
        }
        
        /* View Survey Button for iPad Pro - Fixed text cutoff */
        .survey-card .btn-start.btn-primary {
            padding: 0.875rem 0.75rem !important;
            font-size: 0.85rem !important;
            min-height: 46px !important;
            border-radius: 9px !important;
            min-width: 120px !important;
            max-width: none !important;
            flex: 1 1 auto !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        /* Responses Button for iPad Pro */
        .survey-card .btn-outline-secondary {
            padding: 0.875rem 0.5rem !important;
            min-width: 46px !important;
            min-height: 46px !important;
            max-width: 46px !important;
            border-radius: 9px !important;
            flex-shrink: 0 !important;
        }
        
        /* Broadcast Button for iPad Pro */
        .survey-card .btn-outline-primary.broadcast-btn {
            width: 46px !important;
            height: 46px !important;
            min-width: 46px !important;
            border-radius: 9px !important;
            flex-shrink: 0 !important;
        }
        
        /* Icon sizes for iPad Pro - Reduced to save space */
        .survey-card .btn-start i {
            font-size: 0.8rem !important;
            margin-right: 0.4rem !important;
        }
        
        .survey-card .btn-outline-secondary i,
        .survey-card .broadcast-btn i {
            font-size: 1.1rem !important;
        }
        
        /* Card body padding for iPad Pro */
        .survey-card .card-body {
            padding: 1.5rem !important;
        }
        
        /* Ensure proper flex behavior */
        .survey-card .flex-grow-1 {
            flex-grow: 1 !important;
            flex-shrink: 1 !important;
            flex-basis: 0 !important;
            min-width: 0 !important;
        }
    }
    
    /* iPad Pro 12.9" and similar large tablet screens */
    @media screen and (min-width: 1200px) and (max-width: 1366px) {
        .survey-card .btn-start.btn-primary {
            font-size: 0.9rem !important;
            padding: 0.875rem 1rem !important;
            min-width: 140px !important;
        }
        
        .survey-card .btn-start i {
            font-size: 0.85rem !important;
            margin-right: 0.5rem !important;
        }
        
        .survey-card .d-flex.gap-2.mt-auto {
            gap: 0.75rem !important;
        }
    }
    
    /* iPad Portrait Orientation Specific */
    @media screen and (min-width: 768px) and (max-width: 834px) and (orientation: portrait) {
        .survey-card .btn-start.btn-primary {
            font-size: 0.8rem !important;
            padding: 0.7rem 0.6rem !important;
        }
        
        .survey-card .btn-start i {
            font-size: 0.8rem !important;
            margin-right: 0.3rem !important;
        }
        
        .survey-card .d-flex.gap-2.mt-auto {
            gap: 0.4rem !important;
        }
        
        .survey-card .btn-outline-secondary,
        .survey-card .broadcast-btn {
            min-width: 40px !important;
            width: 40px !important;
            height: 40px !important;
        }
    }
    
    /* Fix for any remaining button issues */
    @media screen and (min-width: 768px) and (max-width: 1366px) {
        .survey-card .btn:focus {
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(var(--bs-primary-rgb), 0.25) !important;
        }
        
        .survey-card .btn:active {
            transform: translateY(0) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        }
        
        /* Ensure proper link behavior */
        .survey-card .btn[href] {
            text-decoration: none !important;
            color: inherit !important;
        }
        
        .survey-card .btn[href]:hover {
            text-decoration: none !important;
        }
        
        /* Fix for button text wrapping */
        .survey-card .btn-start {
            word-break: keep-all !important;
            hyphens: none !important;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Smooth Pagination
        const smoothPagination = new SmoothPagination({
            contentSelector: '.surveys-content',
            paginationSelector: '.pagination',
            scrollToTop: false,
            loadingIndicator: false,
            onAfterLoad: function() {
                // Re-attach event handlers after content is loaded
                attachBroadcastHandlers();
            }
        });
        
        // AJAX Instant Search functionality with SmoothPagination
        const searchForm = document.getElementById('search-form');
        const searchInput = document.getElementById('survey-search');
        let searchTimeout = null;
        
        if (searchInput && searchForm) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value;
                searchTimeout = setTimeout(() => {
                    const url = searchForm.action + '?search=' + encodeURIComponent(query);
                    smoothPagination.loadPage(url);
                }, 300); // Debounce for 300ms
            });
            
            // Prevent form submission and use SmoothPagination instead
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const query = searchInput.value;
                const url = this.action + '?search=' + encodeURIComponent(query);
                smoothPagination.loadPage(url);
            });
        }
        
        // Broadcast functionality
        function attachBroadcastHandlers() {
            const broadcastBtns = document.querySelectorAll('.broadcast-btn');
            broadcastBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const surveyId = this.getAttribute('data-survey-id');
                    const surveyTitle = this.getAttribute('data-survey-title');
                    
                    // Set modal title
                    document.getElementById('broadcastModalLabel').textContent = 'Broadcast: ' + surveyTitle;
                    
                    // Show modal
                    const broadcastModal = new bootstrap.Modal(document.getElementById('broadcastModal'));
                    broadcastModal.show();
                    
                    // Load customers
                    loadCustomers(surveyId);
                });
            });
        }
        
        // Initial attachment of broadcast handlers
        attachBroadcastHandlers();
        
        // Customer search functionality
        const customerSearch = document.getElementById('customerSearch');
        if (customerSearch) {
            customerSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const customerItems = document.querySelectorAll('.customer-item');
                
                customerItems.forEach(item => {
                    const customerName = item.querySelector('.customer-name').textContent.toLowerCase();
                    const customerEmail = item.querySelector('.customer-email').textContent.toLowerCase();
                    const customerCode = item.querySelector('.customer-code').textContent.toLowerCase();
                    
                    if (customerName.includes(searchTerm) || 
                        customerEmail.includes(searchTerm) || 
                        customerCode.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
        
        // Select/Deselect All functionality
        const selectAllBtn = document.querySelector('.select-all-btn');
        const deselectAllBtn = document.querySelector('.deselect-all-btn');
        
        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.customer-checkbox:not([disabled])');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = true;
                });
                updateSelectedCount();
            });
        }
        
        if (deselectAllBtn) {
            deselectAllBtn.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.customer-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateSelectedCount();
            });
        }
        
        // SweetAlert2 configuration
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success me-3",
                cancelButton: "btn btn-outline-danger",
                actions: 'gap-2 justify-content-center'
            },
            buttonsStyling: false
        });
        
        // Send broadcast functionality
        const sendBroadcastBtn = document.querySelector('.send-broadcast-btn');
        if (sendBroadcastBtn) {
            sendBroadcastBtn.addEventListener('click', function() {
                const surveyId = document.querySelector('.broadcast-btn').getAttribute('data-survey-id');
                const selectedCustomers = Array.from(document.querySelectorAll('.customer-checkbox:checked'))
                    .map(checkbox => checkbox.value);
                
                if (selectedCustomers.length === 0) {
                    // Remove focus before showing SweetAlert2
                    this.blur();
                    swalWithBootstrapButtons.fire({
                        title: "No customers selected",
                        text: "Please select at least one customer to send invitations.",
                        icon: "warning",
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                    return;
                }
                
                // Remove focus and hide modal before showing confirmation
                this.blur();
                const broadcastModal = bootstrap.Modal.getInstance(document.getElementById('broadcastModal'));
                broadcastModal.hide();
                
                // Show confirmation dialog after modal is hidden
                setTimeout(() => {
                    swalWithBootstrapButtons.fire({
                        title: "Send Invitations?",
                        text: `You are about to send invitations to ${selectedCustomers.length} customers. Continue?`,
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonText: "Yes, send invitations!",
                        cancelButtonText: "No, cancel!",
                        reverseButtons: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                        // Create and show progress modal
                        Swal.fire({
                            title: 'Sending Invitations',
                            html: `
                                <div class="progress mb-3" style="height: 20px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                         role="progressbar" 
                                         style="width: 0%" 
                                         aria-valuenow="0" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        0%
                                    </div>
                                </div>
                                <div class="text-muted">
                                    <span class="sent-count">0</span> of ${selectedCustomers.length} invitations sent
                                </div>
                            `,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false
                        });

                        // Send broadcast request
                        fetch(`/surveys/${surveyId}/broadcast`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                customer_ids: selectedCustomers
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Calculate progress per customer
                            const progressPerCustomer = 100 / selectedCustomers.length;
                            let currentProgress = 0;
                            let sentCount = 0;

                            // Simulate progress for each customer
                            const progressInterval = setInterval(() => {
                                if (sentCount < selectedCustomers.length) {
                                    sentCount++;
                                    currentProgress = Math.min(progressPerCustomer * sentCount, 100);

                                    // Update progress bar and count
                                    const progressBar = document.querySelector('.progress-bar');
                                    const sentCountElement = document.querySelector('.sent-count');
                                    
                                    if (progressBar && sentCountElement) {
                                        progressBar.style.width = `${currentProgress}%`;
                                        progressBar.setAttribute('aria-valuenow', currentProgress);
                                        progressBar.textContent = `${Math.round(currentProgress)}%`;
                                        sentCountElement.textContent = sentCount;
                                    }

                                    if (sentCount === selectedCustomers.length) {
                                        clearInterval(progressInterval);
                                        
                                        // Close modals and show success message
                                        setTimeout(() => {
                                            Swal.close();
                                            bootstrap.Modal.getInstance(document.getElementById('broadcastModal')).hide();
                                            
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success!',
                                                text: 'All invitations have been sent successfully!',
                                                timer: 3000,
                                                showConfirmButton: false
                                            });
                                        }, 500);
                                    }
                                }
                            }, 100); // Update every 100ms
                        })
                        .catch(error => {
                            console.error('Error sending broadcast:', error);
                            
                            swalWithBootstrapButtons.fire({
                                title: "Error!",
                                text: "Failed to send invitations. Please try again.",
                                icon: "error"
                            });
                            
                            // Reset button state
                            sendBroadcastBtn.disabled = false;
                            sendBroadcastBtn.querySelector('.spinner-border').classList.add('d-none');
                        });
                    } else if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                        // If user cancels, show the broadcast modal again
                        broadcastModal.show();
                    }
                });
                }, 300);
            });
        }
        
        function loadCustomers(surveyId) {
            const customerList = document.querySelector('.customer-list');
            if (!customerList) return;
            
            // Show loading state
            customerList.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading customers...</p>
                </div>
            `;
            
            // Fetch customers
            fetch(`/surveys/${surveyId}/customers`)
                .then(response => response.json())
                .then(data => {
                    if (data.customers && data.customers.length > 0) {
                        let customersHtml = '';
                        
                        data.customers.forEach(customer => {
                            customersHtml += `
                                <div class="customer-item">
                                    <label>
                                        <input type="checkbox" class="customer-checkbox" value="${customer.id}" 
                                            ${!customer.EMAIL ? 'disabled' : ''}>
                                        <div class="customer-details">
                                            <div class="customer-name">${customer.CUSTNAME}</div>
                                            <div class="customer-email">${customer.EMAIL || 'No email available'}</div>
                                        </div>
                                        <div class="customer-code">${customer.CUSTCODE}</div>
                                    </label>
                                </div>
                            `;
                        });
                        
                        customerList.innerHTML = customersHtml;
                        
                        // Attach change event to checkboxes
                        const checkboxes = document.querySelectorAll('.customer-checkbox');
                        checkboxes.forEach(checkbox => {
                            checkbox.addEventListener('change', updateSelectedCount);
                        });
                        
                        updateSelectedCount();
                    } else {
                        customerList.innerHTML = `
                            <div class="text-center py-4">
                                <i class="fas fa-users-slash fs-1 text-muted mb-3"></i>
                                <p>No customers with email addresses found.</p>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading customers:', error);
                    customerList.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle fs-1 text-danger mb-3"></i>
                            <p>Failed to load customers. Please try again.</p>
                        </div>
                    `;
                });
        }
        
        function updateSelectedCount() {
            const selectedCount = document.querySelectorAll('.customer-checkbox:checked').length;
            const selectedCountText = document.querySelector('.selected-count');
            if (selectedCountText) {
                selectedCountText.textContent = `${selectedCount} selected`;
            }
            
            // Enable/disable send button
            const sendButton = document.querySelector('.send-broadcast-btn');
            if (sendButton) {
                sendButton.disabled = selectedCount === 0;
            }
        }
        
        // Add event listeners for modal close and cancel buttons
        const modalCloseBtn = document.getElementById('modalCloseBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        
        if (modalCloseBtn) {
            modalCloseBtn.addEventListener('click', function() {
                // Remove focus and hide modal before showing SweetAlert2
                this.blur();
                const broadcastModal = bootstrap.Modal.getInstance(document.getElementById('broadcastModal'));
                broadcastModal.hide();
                
                // Show SweetAlert2 after modal is hidden
                setTimeout(() => {
                    swalWithBootstrapButtons.fire({
                        title: "Close broadcast?",
                        text: "Are you sure you want to close without sending invitations?",
                        icon: "question",
                        showCancelButton: true,
                        confirmButtonText: "Yes, close it!",
                        cancelButtonText: "No, stay here!",
                        reverseButtons: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then((result) => {
                        if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                            // If user cancels, show the modal again
                            broadcastModal.show();
                        }
                    });
                }, 300);
            });
        }
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                // Remove focus and hide modal before showing SweetAlert2
                this.blur();
                const broadcastModal = bootstrap.Modal.getInstance(document.getElementById('broadcastModal'));
                broadcastModal.hide();
                
                // Show SweetAlert2 after modal is hidden
                setTimeout(() => {
                    swalWithBootstrapButtons.fire({
                        title: "Cancel broadcast?",
                        text: "Are you sure you want to cancel sending invitations?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, cancel it!",
                        cancelButtonText: "No, continue!",
                        reverseButtons: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then((result) => {
                        if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                            // If user cancels, show the modal again
                            broadcastModal.show();
                        }
                    });
                }, 300);
            });
        }
        
        function showBroadcastSuccess(message) {
            // Create success notification
            const notification = document.createElement('div');
            notification.className = 'broadcast-success';
            notification.innerHTML = `
                <i class="fas fa-check-circle"></i>
                <span>${message}</span>
            `;
            
            // Add to DOM
            document.body.appendChild(notification);
            
            // Show with animation
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);
            
            // Remove after delay
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 5000);
        }
    });
    </script>

    <!-- Broadcast Modal -->
    <div class="modal fade" id="broadcastModal" tabindex="-1" aria-labelledby="broadcastModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="broadcastModalLabel">Broadcast Survey</h5>
                    <button type="button" class="btn-close" id="modalCloseBtn" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="customerSearch" class="form-label font-theme">Search Customers</label>
                        <input type="text" class="form-control font-theme" id="customerSearch" placeholder="Type to search...">
                    </div>
                

                    <div class="customer-list-container font-theme">
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-secondary select-all-btn">Select All</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary deselect-all-btn ms-2">Deselect All</button>
                            </div>
                            <div class="selected-count">0 selected</div>
                        </div>
                        <div class="customer-list">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading customers...</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer font-theme">
                    <button type="button" class="btn btn-secondary" id="cancelBtn">Cancel</button>
                    <button type="button" class="btn btn-primary send-broadcast-btn" disabled>
                        <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                        <i class="fas fa-paper-plane me-1"></i> Send Invitations
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
