<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Management - Developer Portal</title>
    
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

.admin-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.admin-card:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

.status-active {
    background: linear-gradient(135deg, #27ae60, #229954);
    color: white;
}

.status-disabled {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    color: white;
}

.btn-dev-danger {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    border: none;
    color: white;
}

.btn-dev-warning {
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
    background: linear-gradient(135deg, #f39c12, #e67e22);
    border-color: #f39c12;
    color: white;
    box-shadow: 0 4px 8px rgba(243, 156, 18, 0.3);
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
                <h1 class="display-5 fw-bold text-warning">
                    <i class="bi bi-shield-lock"></i> Admin Management
                    @if($sbuName)
                        <span class="badge bg-warning text-dark ms-2">{{ $sbuName }}</span>
                    @endif
                </h1>
                <p class="text-light">
                    Total Admins: {{ $admins->total() }}
                    @if($sbuName)
                        <span class="text-warning">| Filtered by SBU: {{ $sbuName === 'FDC' ? 'Fast Distribution' : 'Fast Unimerchant' }}</span>
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
                    <form method="GET" action="{{ route('developer.admins') }}" class="d-inline">
                        @if($sbuName)
                            <input type="hidden" name="sbu" value="{{ $sbuName }}">
                        @endif
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" name="search" class="form-control" placeholder="Search admins..." 
                                   value="{{ request('search') }}" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: white;">
                            <button class="btn btn-outline-warning" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Filter Dropdown -->
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-warning dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('developer.admins', array_merge(request()->query(), [])) }}">
                                <i class="bi bi-list"></i> All Admins
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('developer.admins', array_merge(request()->query(), ['sbu' => 'FDC'])) }}">
                                <i class="bi bi-building"></i> FDC Only
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('developer.admins', array_merge(request()->query(), ['sbu' => 'FUI'])) }}">
                                <i class="bi bi-building"></i> FUI Only
                            </a></li>
                        </ul>
                    </div>

                    <!-- Clear Filter -->
                    @if($sbuName || request('search'))
                        <a href="{{ route('developer.admins') }}" class="btn btn-outline-warning">
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

        <!-- Admins List -->
        <div class="dev-card p-4">
            <div id="adminsContainer" class="row g-4">
                @forelse($admins as $admin)
                    <div class="col-md-6 col-lg-4 admin-item">
                        <div class="admin-card p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="text-white mb-1">{{ $admin->name }}</h5>
                                    <p class="text-light mb-1">{{ $admin->email }}</p>
                                    @if($admin->contact_number)
                                        <small class="text-white">{{ $admin->contact_number }}</small>
                                    @endif
                                </div>
                                <span class="badge {{ $admin->status ? 'status-active' : 'status-disabled' }} px-3 py-2">
                                    {{ $admin->status ? 'Active' : 'Disabled' }}
                                </span>
                            </div>

                            <div class="text-white mb-3">
                                <small>
                                    <i class="bi bi-calendar"></i> Created: {{ $admin->created_at->format('M d, Y') }}<br>
                                    @if($admin->sbus->count() > 0)
                                        <i class="bi bi-building"></i> SBU: 
                                        @foreach($admin->sbus as $sbu)
                                            <span class="badge bg-warning text-dark">{{ $sbu->name }}</span>
                                        @endforeach
                                    @else
                                        <i class="bi bi-building"></i> SBU: <span class="text-white">None assigned</span>
                                    @endif
                                </small>
                            </div>

                            <div class="d-grid gap-2">
                                <!-- Toggle Status -->
                                @if($admin->status)
                                    <!-- Disable Button - Opens Modal -->
                                    <button type="button" class="btn btn-dev-warning btn-sm w-100" data-bs-toggle="modal" data-bs-target="#disableAdminModal{{ $admin->id }}">
                                        <i class="bi bi-pause-circle"></i>
                                        Disable Account
                                    </button>
                                @else
                                    <!-- Enable Button - Direct Form -->
                                    <form method="POST" action="{{ route('developer.admins.toggle-status', $admin->id) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn status-active btn-sm w-100">
                                            <i class="bi bi-play-circle"></i>
                                            Enable Account
                                        </button>
                                    </form>
                                @endif

                                <!-- Delete Admin -->
                                <form method="POST" action="{{ route('developer.admins.delete', $admin->id) }}" 
                                      onsubmit="return confirm('Are you sure you want to DELETE this admin? This action cannot be undone!')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-dev-danger btn-sm w-100">
                                        <i class="bi bi-trash"></i> Delete Admin
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12" id="noAdminsMessage">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-shield-x display-4 mb-3"></i>
                            <h4>No Admins Found</h4>
                            <p>There are currently no admin accounts in the system.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($admins->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                <div class="custom-pagination">
                    {{ $admins->links() }}
                </div>
            </div>
        @endif

        <!-- Warning Notice -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="alert alert-warning" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Warning:</strong> You can disable or delete any admin account. Use extreme caution as this can affect system administration.
                </div>
            </div>        </div>
    </div>
</div>

<!-- Disable Admin Modals -->
@foreach($admins as $admin)
    @if($admin->status)
    <div class="modal fade" id="disableAdminModal{{ $admin->id }}" tabindex="-1" aria-labelledby="disableAdminModalLabel{{ $admin->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="disableAdminModalLabel{{ $admin->id }}">
                        <i class="bi bi-exclamation-triangle me-2"></i>Disable Admin Account
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('developer.admins.toggle-status', $admin->id) }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="alert alert-warning" role="alert">
                            <strong>Warning:</strong> You are about to disable <strong>{{ $admin->name }}</strong>'s admin account.
                        </div>
                        
                        <div class="mb-3">
                            <label for="disabled_reason{{ $admin->id }}" class="form-label">
                                <strong>Reason for Disabling Account</strong> <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                class="form-control" 
                                id="disabled_reason{{ $admin->id }}" 
                                name="disabled_reason" 
                                rows="4" 
                                placeholder="Example: Account disabled due to security policy violation, suspicious activity, or administrative decision. Please provide a clear explanation..."
                                required
                                minlength="10"
                                maxlength="500"
                            ></textarea>
                            <div class="form-text">
                                This reason will be displayed to the admin when they attempt to log in. Minimum 10 characters, maximum 500 characters.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pause-circle me-1"></i>Disable Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Form validation for disable admin forms
document.addEventListener('DOMContentLoaded', function() {
    // Get all disable admin forms
    const disableForms = document.querySelectorAll('form[action*="toggle-status"]');
    
    disableForms.forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const textarea = form.querySelector('textarea[name="disabled_reason"]');
            if (textarea) {
                const reason = textarea.value.trim();
                if (reason.length === 0) {
                    e.preventDefault();
                    alert('Please provide a reason for disabling this account.');
                    textarea.focus();
                    return false;
                }
                if (reason.length < 10) {
                    e.preventDefault();
                    alert('Please provide a more detailed reason (at least 10 characters).');
                    textarea.focus();
                    return false;
                }
            }
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
