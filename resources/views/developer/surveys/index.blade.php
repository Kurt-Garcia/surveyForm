<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Survey Management - Developer Portal</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}
.developer-dashboard {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    min-height: 100vh;
    color: white;
}

.dev-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
}

.survey-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.survey-card:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.status-active {
    background: linear-gradient(135deg, #27ae60, #229954);
    color: white;
}

.status-inactive {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
}

.btn-dev-danger {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    border: none;
    color: white;
}

.btn-dev-success {
    background: linear-gradient(135deg, #27ae60, #229954);
    border: none;
    color: white;
}

.btn-warning {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    border: none;
    color: white;
}

/* Custom Pagination Styles */
.custom-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
}

.custom-pagination .pagination {
    margin: 0;
    padding: 0;
    list-style: none;
    display: flex;
    align-items: center;
    gap: 8px;
}

.custom-pagination .page-item {
    border: none;
}

.custom-pagination .page-link {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    padding: 8px 12px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    height: 40px;
}

.custom-pagination .page-link:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.4);
    color: white;
    transform: translateY(-2px);
}

.custom-pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #27ae60, #229954);
    border-color: #27ae60;
    color: white;
    box-shadow: 0 4px 8px rgba(39, 174, 96, 0.3);
}

.custom-pagination .page-item.disabled .page-link {
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.4);
    cursor: not-allowed;
}

.custom-pagination .page-item.disabled .page-link:hover {
    transform: none;
    background: rgba(255, 255, 255, 0.05);
    border-color: rgba(255, 255, 255, 0.1);
}

.custom-pagination .page-link span {
    font-size: 14px;
}
</style>
</head>
<body>

<div class="developer-dashboard">
    <div class="container-fluid px-4 py-5">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="display-5 fw-bold text-success">
                    <i class="bi bi-clipboard-data"></i> Survey Management
                    @if($sbuName)
                        <span class="badge bg-success ms-2">{{ $sbuName }}</span>
                    @endif
                </h1>
                <p class="text-light">
                    Total Surveys: {{ $surveys->total() }}
                    @if($sbuName)
                        <span class="text-success">| Filtered by SBU: {{ $sbuName === 'FDC' ? 'Fast Distribution' : 'Fast Unimerchant' }}</span>
                    @endif
                </p>
            </div>
            <div class="col-md-6">
                <!-- Navigation Actions -->
                <div class="d-flex flex-wrap gap-2 justify-content-end align-items-center mb-3">
                    <!-- Back to Dashboard -->
                    <a href="{{ route('developer.dashboard') }}" class="btn btn-outline-light">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('developer.logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light">
                            <i class="bi bi-power"></i> Logout
                        </button>
                    </form>
                </div>

                <!-- Search and Filter Actions -->
                <div class="d-flex flex-wrap gap-2 justify-content-end align-items-center">
                    <!-- Search Input -->
                    <form method="GET" action="{{ route('developer.surveys') }}" class="d-inline">
                        @if($sbuName)
                            <input type="hidden" name="sbu" value="{{ $sbuName }}">
                        @endif
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" name="search" class="form-control" placeholder="Search by title or admin..." 
                                   value="{{ request('search') }}" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: white;">
                            <button class="btn btn-outline-success" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Filter Dropdown -->
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('developer.surveys', array_merge(request()->query(), [])) }}">
                                <i class="bi bi-list"></i> All Surveys
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('developer.surveys', array_merge(request()->query(), ['sbu' => 'FDC'])) }}">
                                <i class="bi bi-building"></i> FDC Only
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('developer.surveys', array_merge(request()->query(), ['sbu' => 'FUI'])) }}">
                                <i class="bi bi-building"></i> FUI Only
                            </a></li>
                        </ul>
                    </div>

                    <!-- Clear Filter -->
                    @if($sbuName || request('search'))
                        <a href="{{ route('developer.surveys') }}" class="btn btn-outline-success">
                            <i class="bi bi-x"></i> Clear
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Surveys List -->
        <div class="dev-card p-4">
            <div id="surveysContainer" class="row g-4">
                @forelse($surveys as $survey)
                    <div class="col-md-6 col-lg-4 survey-item">
                        <div class="survey-card p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="text-white mb-1">{{ $survey->title }}</h5>
                                    <p class="text-light mb-1">Created by: {{ $survey->admin->name ?? 'Unknown' }}</p>
                                </div>
                                <span class="badge {{ $survey->is_active ? 'status-active' : 'status-inactive' }} px-3 py-2">
                                    {{ $survey->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="text-white mb-3">
                                <small>
                                    <i class="bi bi-calendar"></i> Created: {{ $survey->created_at->format('M d, Y') }}<br>
                                    <i class="bi bi-chat-dots"></i> Questions: {{ $survey->questions->count() }}<br>
                                    <i class="bi bi-graph-up"></i> Responses: {{ $survey->responses->count() }}<br>
                                    @if($survey->sbus->count() > 0)
                                        <i class="bi bi-building"></i> SBU: 
                                        @foreach($survey->sbus as $sbu)
                                            <span class="badge bg-primary">{{ $sbu->name }}</span>
                                        @endforeach
                                    @endif
                                </small>
                            </div>

                            @if($survey->start_date && $survey->end_date)
                                <div class="text-white mb-3">
                                    <small>
                                        <i class="bi bi-clock"></i> 
                                        {{ $survey->start_date->format('M d') }} - {{ $survey->end_date->format('M d, Y') }}
                                    </small>
                                </div>
                            @endif

                            <div class="d-grid gap-2">
                                <!-- Enable/Disable Survey -->
                                @if($survey->is_active)
                                    <button type="button" class="btn btn-warning btn-sm w-100 disable-survey-btn" 
                                            data-survey-id="{{ $survey->id }}" 
                                            data-survey-title="{{ $survey->title }}"
                                            data-action-url="{{ route('developer.surveys.disable', $survey->id) }}">
                                        <i class="bi bi-pause-circle"></i> Disable Survey
                                    </button>
                                @else
                                    <button type="button" class="btn btn-dev-success btn-sm w-100 enable-survey-btn" 
                                            data-survey-id="{{ $survey->id }}" 
                                            data-survey-title="{{ $survey->title }}"
                                            data-action-url="{{ route('developer.surveys.enable', $survey->id) }}">
                                        <i class="bi bi-play-circle"></i> Enable Survey
                                    </button>
                                @endif

                                <!-- Delete Survey -->
                                <button type="button" class="btn btn-dev-danger btn-sm w-100 delete-survey-btn" 
                                        data-survey-id="{{ $survey->id }}" 
                                        data-survey-title="{{ $survey->title }}"
                                        data-survey-responses="{{ $survey->responses->count() }}"
                                        data-action-url="{{ route('developer.surveys.delete', $survey->id) }}">
                                    <i class="bi bi-trash"></i> Delete Survey
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12" id="noSurveysMessage">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-clipboard-x display-4 mb-3"></i>
                            <h4>No Surveys Found</h4>
                            <p>There are currently no surveys in the system.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($surveys->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                <div class="custom-pagination">
                    {{ $surveys->links() }}
                </div>
            </div>
        @endif

        <!-- Warning Notice -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Danger Zone:</strong> Deleting surveys will permanently remove all associated responses and data. This action cannot be undone.
                </div>
            </div>        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Enhanced form validation and SweetAlert confirmations for Survey Management
document.addEventListener('DOMContentLoaded', function() {
    // Enable Survey - SweetAlert Confirmation
    document.querySelectorAll('.enable-survey-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const surveyTitle = this.dataset.surveyTitle;
            const actionUrl = this.dataset.actionUrl;
            
            Swal.fire({
                title: 'Enable Survey?',
                html: `Are you sure you want to <strong class="text-success">enable</strong> the survey <span class="text-primary">"${surveyTitle}"</span>?<br><br><small class="text-muted">Users will be able to access and respond to this survey.</small>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-play-circle"></i> Yes, Enable Survey',
                cancelButtonText: '<i class="bi bi-x-circle"></i> Cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Enabling Survey...',
                        text: 'Please wait while we enable the survey.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Create and submit form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = actionUrl;
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PATCH';
                    
                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    // Disable Survey - SweetAlert Confirmation
    document.querySelectorAll('.disable-survey-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const surveyTitle = this.dataset.surveyTitle;
            const actionUrl = this.dataset.actionUrl;
            
            Swal.fire({
                title: 'Disable Survey?',
                html: `Are you sure you want to <strong class="text-warning">disable</strong> the survey <span class="text-primary">"${surveyTitle}"</span>?<br><br><div class="alert alert-warning mt-3"><i class="bi bi-exclamation-triangle"></i> <strong>Note:</strong> Users will no longer be able to access this survey.</div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f39c12',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-pause-circle"></i> Yes, Disable Survey',
                cancelButtonText: '<i class="bi bi-x-circle"></i> Cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-warning',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Disabling Survey...',
                        text: 'Please wait while we disable the survey.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Create and submit form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = actionUrl;
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PATCH';
                    
                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    // Delete Survey - SweetAlert Confirmation
    document.querySelectorAll('.delete-survey-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const surveyTitle = this.dataset.surveyTitle;
            const surveyResponses = this.dataset.surveyResponses;
            const actionUrl = this.dataset.actionUrl;
            
            const responseText = parseInt(surveyResponses) > 0 
                ? `<strong class="text-info">${surveyResponses}</strong> response${parseInt(surveyResponses) !== 1 ? 's' : ''}` 
                : 'no responses';
            
            Swal.fire({
                title: 'Delete Survey?',
                html: `
                    <div class="text-center">
                        <p>Are you sure you want to <strong class="text-danger">permanently delete</strong> the survey <span class="text-primary">"${surveyTitle}"</span>?</p>
                        <p>This survey currently has <strong>${responseText}</strong>.</p>
                        <div class="alert alert-danger mt-3">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Warning:</strong> This action will permanently delete the survey and all its data. This cannot be undone!
                        </div>
                    </div>
                `,
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash"></i> Yes, Delete Permanently',
                cancelButtonText: '<i class="bi bi-x-circle"></i> Cancel',
                reverseButtons: true,
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Deleting Survey...',
                        text: 'Please wait while we delete the survey and all its data.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        willOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Create and submit form
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = actionUrl;
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    
                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });

    // Style search input placeholder for better UX
    const searchInputs = document.querySelectorAll('input[name="search"]');
    searchInputs.forEach(function(searchInput) {
        searchInput.addEventListener('focus', function() {
            this.style.background = 'rgba(255,255,255,0.2)';
        });
        
        searchInput.addEventListener('blur', function() {
            this.style.background = 'rgba(255,255,255,0.1)';
        });
        
        // Submit form on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('form').submit();
            }
        });
    });
});
</script>

</body>
</html>
