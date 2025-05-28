@extends('layouts.app')

@section('content')
<script src="{{ asset('js/lib/smooth-pagination.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="container-fluid py-4 px-4" style="background-color: var(--background-color)">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="h3 mb-0 text-gray-800">{{ __('My Surveys') }}</h2>
                    <p class="text-muted small mb-0 mt-2">Total Surveys: {{ $totalSurveys }}</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <form method="GET" action="{{ route('admin.surveys.index') }}" class="search-form me-2">                
                <div class="search-container">
                    <input type="text" name="search" id="survey-search" class="search-input" placeholder="Search surveys..." value="{{ request('search') }}">
                    <button type="button" class="search-button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                    </form>
                    <a href="{{ route('admin.surveys.create') }}" class="btn btn-custom" >
                        <i class="bi bi-plus-lg me-1"></i>{{ __('Create Survey') }}
                    </a>
                </div>
            </div>

            <!-- Alert Messages -->
            @if (session('success'))
                <div class="MuiAlert-root MuiAlert-standardSuccess MuiAlert-filled" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="MuiButtonBase-root MuiIconButton-root MuiIconButton-sizeMedium" aria-label="Close"><svg class="MuiSvgIcon-root" focusable="false" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path></svg></button>
                </div>
            @endif

            <!-- Surveys Grid -->
            <div class="surveys-content">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @forelse ($surveys as $survey)
                    <div class="col">
                        <div class="card h-100 survey-card shadow-sm hover-lift" style="border-radius: 16px; background-color: white;">
                            <div class="card-body">
                                <div class="survey-logo-wrapper text-center mb-3">
                                    @if($survey->logo)
                                        <img src="{{ asset('storage/' . $survey->logo) }}" alt="{{ $survey->title }} Logo" class="survey-logo-large">
                                    @else
                                        <i class="bi bi-bar-chart-fill" style="font-size:2.5rem;"></i>
                                    @endif
                                </div>
                                <h4 class="card-title text-center">{{ strtoupper($survey->title) }}</h4>
                                <div class="d-flex justify-content-between mt-3 mb-3">
                                    <div class="survey-info">
                                        <div class=" small text-muted mb-1">
                                            <i class="fas fa-question-circle me-1"></i>
                                            {{ $survey->questions->count() }} questions
                                        </div>
                                        <div class="small text-muted">
                                            <i class="bi bi-person me-1"></i>
                                            {{ $survey->admin->name }}
                                        </div>
                                    </div>
                                    <div class="responded-badge">
                                        <span class="badge {{ $survey->is_active ? 'bg-success' : 'bg-danger' }}">
                                            <i class="fas fa-circle me-1"></i>
                                            {{ $survey->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column mb-3">
                                    <div class="small text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        Created {{ $survey->created_at->format('M d, Y') }}
                                    </div>
                                    <div class="small text-muted mt-1">
                                        <i class="bi bi-bar-chart-fill me-1"></i>
                                        {{ $survey->responseHeaders->count() }} responses
                                    </div>
                                </div>
                                <div class="d-flex gap-2 mt-auto">
                                    <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-custom flex-grow-1"> {{--add a (btn-primary if you want to turn --primary-color)--}}
                                        <i class="fas fa-eye me-1"></i> View Details
                                    </a>
                                    <a href="{{ route('admin.surveys.responses.index', $survey) }}" class="btn btn-outline-secondary btn-view-responses">
                                        <i class="fas fa-chart-bar me-1"></i> Responses
                                    </a>
                                    <button type="button" class="btn btn-outline-primary broadcast-btn" 
                                            data-survey-id="{{ $survey->id }}" 
                                            data-survey-title="{{ $survey->title }}">
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
                                <h4 class="mb-2">No Surveys Found</h4>
                                <p class="text-muted">Start by creating your first survey</p>
                            </div>
                        </div>
                    </div>
                @endforelse
                </div>

                <div class="pagination-container mt-4">
                    <div class="row align-items-center">
                        <div class="col-md-6 text-muted small text-start mb-2 mb-md-0">
                            Showing {{ $surveys->firstItem() }} to {{ $surveys->lastItem() }} of {{ $surveys->total() }} entries
                        </div>
                        <div class="d-flex justify-content-center">
                            {{ $surveys->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.btn-custom{
    background-color: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 500;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    position: relative;
    z-index: 10;
}

.btn-custom:hover{
    background-color: var(--secondary-color);
    color: white;
}

.alert {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    min-width: 300px;
}

.search-form {
    min-width: 280px;
    width: 100%;
}

.search-container {
    position: relative;
    display: flex;
    align-items: center;
}

.search-input {
    width: 100%;
    padding: 10px 15px 10px 40px;
    border-radius: 30px;
    border: none;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    font-size: 14px;
}

@media (max-width: 768px) {
    .search-form {
        min-width: auto;
        margin-bottom: 10px;
    }
    
    .d-flex.gap-2.align-items-center {
        flex-direction: column;
        align-items: stretch;
    }
}

.search-input:focus {
    outline: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    background: linear-gradient(135deg, #ffffff 0%, #f0f4f7 100%);
}

.search-button {
    position: absolute;
    left: 10px;
    background: transparent;
    border: none;
    color: #6c757d;
    cursor: pointer;
    transition: all 0.3s ease;
}

.search-button:hover {
    color: var(--primary-color);
    transform: scale(1.1);
}

.pagination {
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

.pagination .page-item .page-link:hover {
    background-color: var(--primary-color);
    color: white;
}

.pagination-container {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    margin-top: 2rem;
}
.pagination-container .row {
    width: 100%;
}
.pagination-container .pagination {
    justify-content: center;
    margin: 0;
}
.surveys-content {
    min-height: 300px;
}

.pagination .page-link {
    color: #333;
    transition: all 0.2s ease;
}
.pagination .page-link:hover {
    background-color: #eee;
}
.survey-card {
    border-radius: 12px;
    border: none;
    border-left: 4px solid var(--primary-color);
    transition: all 0.3s ease;
    height: 100%;
}
.survey-card:hover {
    border-left-color: var(--secondary-color);
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}
.card-icon {
    display: inline-block;
    background-color: rgba(78, 205, 196, 0.1);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.survey-logo {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 50%;
    padding: 5px;
}
.survey-logo-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
}
.survey-logo-large {
    min-width: 80px;
    min-height: 64px;
    max-width: 96px;
    max-height: 64px;
    width: auto;
    height: auto;
    object-fit: contain;
    /* Removed border-radius, background, box-shadow, and padding for a cleaner logo */
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
    text-align: center;
}
.survey-info {
    font-size: 0.9rem;
}
.responded-badge {
    align-self: flex-start;
}
.badge {
    font-weight: 500;
    padding: 8px 12px;
    border-radius: 30px;
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
.survey-card .btn {
    cursor: pointer !important;
    pointer-events: auto !important;
}
.d-flex.gap-2.mt-auto {
    position: relative;
    z-index: 10;
}
.btn-view-responses {
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 500;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    position: relative;
    z-index: 10;
}

/* Broadcast button */
.broadcast-btn {
    width: 40px;
    height: 38px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    position: relative;
    z-index: 10;
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
                // Apply styling to survey cards after loading
                applyStylingToCards();
            }
        });
        
        // Function to apply styling to survey cards
        function applyStylingToCards() {
            document.querySelectorAll('.survey-card').forEach(card => {
                const btnStart = card.querySelector('.btn-start');
                if (btnStart) {
                    btnStart.style.pointerEvents = 'auto';
                    btnStart.style.position = 'relative';
                    btnStart.style.zIndex = '2';
                }
                const buttons = card.querySelectorAll('a.btn');
                buttons.forEach(btn => {
                    btn.style.pointerEvents = 'auto';
                    btn.style.position = 'relative';
                    btn.style.zIndex = '2';
                });
            });
        }
        
        // Handle search functionality
        const searchInput = document.getElementById('survey-search');
        const searchForm = document.querySelector('.search-form');
        const searchButton = document.querySelector('.search-button');
        
        if (searchInput && searchForm) {
            let searchTimeout;
            
            // Handle input typing
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const query = this.value;
                    const url = new URL(window.location.href);
                    url.searchParams.set('search', query);
                    smoothPagination.loadPage(url.toString());
                }, 300); // Debounce for 300ms
            });
            
            // Handle search button click
            if (searchButton) {
                searchButton.addEventListener('click', function() {
                    const query = searchInput.value;
                    const url = new URL(window.location.href);
                    url.searchParams.set('search', query);
                    smoothPagination.loadPage(url.toString());
                });
            }
            
            // Prevent form submission and use SmoothPagination instead
            searchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const query = searchInput.value;
                const url = new URL(this.action);
                url.searchParams.set('search', query);
                smoothPagination.loadPage(url.toString());
            });
        }
        
        // Apply initial styling
        applyStylingToCards();
        
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
                    Swal.fire({
                        title: "No customers selected",
                        text: "Please select at least one customer",
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
                        title: "Send Survey Invitations?",
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
                            // Show progress dialog
                            let timerInterval;
                            const totalCustomers = selectedCustomers.length;
                        let currentProgress = 0;

                        const progressSwal = Swal.fire({
                            title: 'Sending Invitations',
                            html: `
                                <div class="text-center mb-3">
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                             role="progressbar" 
                                             style="width: 0%" 
                                             aria-valuenow="0" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <span class="sent-count">0</span> of <span class="total-count">${totalCustomers}</span> invitations sent
                                    </div>
                                </div>
                            `,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: () => {
                                const progressBar = Swal.getHtmlContainer().querySelector('.progress-bar');
                                const sentCount = Swal.getHtmlContainer().querySelector('.sent-count');
                                
                                // Send broadcast request
                                fetch(`/admin/surveys/${surveyId}/broadcast`, {
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
                                    // Simulate progress (since we don't have real-time progress)
                                    const interval = setInterval(() => {
                                        currentProgress += 5;
                                        if (currentProgress <= 100) {
                                            progressBar.style.width = `${currentProgress}%`;
                                            progressBar.setAttribute('aria-valuenow', currentProgress);
                                            sentCount.textContent = Math.floor((currentProgress / 100) * totalCustomers);
                                        } else {
                                            clearInterval(interval);
                                            // Hide modal and show success
                                            bootstrap.Modal.getInstance(document.getElementById('broadcastModal')).hide();
                                            Swal.fire({
                                                title: 'Success!',
                                                text: data.message,
                                                icon: 'success'
                                            });
                                        }
                                    }, 100);
                                })
                                .catch(error => {
                                    console.error('Error sending broadcast:', error);
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Failed to send broadcast. Please try again.',
                                        icon: 'error'
                                    });
                                });
                            }
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
            fetch(`/admin/surveys/${surveyId}/customers`)
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
        
        // Add event listeners for cancel and close buttons
        document.getElementById('cancelBroadcastBtn').addEventListener('click', function() {
            // Remove focus and hide modal before showing SweetAlert2
            this.blur();
            const broadcastModal = bootstrap.Modal.getInstance(document.getElementById('broadcastModal'));
            broadcastModal.hide();
            
            // Show SweetAlert2 after modal is hidden
            setTimeout(() => {
                swalWithBootstrapButtons.fire({
                    title: "Cancel broadcast?",
                    text: "Are you sure you want to cancel this broadcast?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, cancel it!",
                    cancelButtonText: "No, continue!",
                    reverseButtons: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        swalWithBootstrapButtons.fire({
                            title: "Cancelled",
                            text: "Broadcast has been cancelled.",
                            icon: "info",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        // If user cancels, show the modal again
                        broadcastModal.show();
                    }
                });
            }, 300);
        });
        
        document.getElementById('closeBroadcastBtn').addEventListener('click', function() {
            // Remove focus and hide modal before showing SweetAlert2
            this.blur();
            const broadcastModal = bootstrap.Modal.getInstance(document.getElementById('broadcastModal'));
            broadcastModal.hide();
            
            // Show SweetAlert2 after modal is hidden
            setTimeout(() => {
                swalWithBootstrapButtons.fire({
                    title: "Close broadcast?",
                    text: "Are you sure you want to close this broadcast window?",
                    icon: "warning",
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
    });
</script>

<!-- Broadcast Modal -->
<div class="modal fade" id="broadcastModal" tabindex="-1" aria-labelledby="broadcastModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="broadcastModalLabel">Broadcast Survey</h5>
                <button type="button" class="btn-close" id="closeBroadcastBtn" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="customerSearch" class="form-label">Search Customers</label>
                    <input type="text" class="form-control" id="customerSearch" placeholder="Type to search...">
                </div>


                <div class="customer-list-container">
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelBroadcastBtn">Cancel</button>
                <button type="button" class="btn btn-primary send-broadcast-btn" disabled>
                    <span class="spinner-border spinner-border-sm d-none me-2" role="status" aria-hidden="true"></span>
                    <i class="fas fa-paper-plane me-1"></i> Send Invitations
                </button>
            </div>
        </div>
    </div>
</div>
@endsection