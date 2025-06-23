<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Management - Developer Portal</title>
    
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

.user-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.user-card:hover {
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

.btn-dev-info {
    background: linear-gradient(135deg, #3498db, #2980b9);
    border: none;
    color: white;
}
</style>
</head>
<body>

<div class="developer-dashboard">
    <div class="container-fluid px-4 py-5">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h1 class="display-5 fw-bold text-info">
                    <i class="bi bi-people"></i> User Management
                </h1>
                <p class="text-light">Total Users: {{ $users->total() }}</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('developer.dashboard') }}" class="btn btn-outline-light me-2">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
                <form method="POST" action="{{ route('developer.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">
                        <i class="bi bi-power"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Users List -->
        <div class="dev-card p-4">
            <div class="row g-4">
                @forelse($users as $user)
                    <div class="col-md-6 col-lg-4">
                        <div class="user-card p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="text-white mb-1">{{ $user->name }}</h5>
                                    <p class="text-light mb-1">{{ $user->email }}</p>
                                    @if($user->contact_number)
                                        <small class="text-muted">{{ $user->contact_number }}</small>
                                    @endif
                                </div>
                                <span class="badge {{ $user->status ? 'status-active' : 'status-disabled' }} px-3 py-2">
                                    {{ $user->status ? 'Active' : 'Disabled' }}
                                </span>
                            </div>

                            <div class="text-muted mb-3">
                                <small>
                                    <i class="bi bi-calendar"></i> Created: {{ $user->created_at->format('M d, Y') }}
                                </small>
                            </div>

                            <div class="d-grid gap-2">
                                <!-- Toggle Status -->
                                @if($user->status)
                                    <!-- Disable Button - Opens Modal -->
                                    <button type="button" class="btn btn-dev-info btn-sm w-100" data-bs-toggle="modal" data-bs-target="#disableUserModal{{ $user->id }}">
                                        <i class="bi bi-pause-circle"></i>
                                        Disable Account
                                    </button>
                                @else
                                    <!-- Enable Button - Direct Form -->
                                    <form method="POST" action="{{ route('developer.users.toggle-status', $user->id) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn status-active btn-sm w-100">
                                            <i class="bi bi-play-circle"></i>
                                            Enable Account
                                        </button>
                                    </form>
                                @endif

                                <!-- Delete User -->
                                <form method="POST" action="{{ route('developer.users.delete', $user->id) }}" 
                                      onsubmit="return confirm('Are you sure you want to DELETE this user? This action cannot be undone!')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-dev-danger btn-sm w-100">
                                        <i class="bi bi-trash"></i> Delete User
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-people-fill display-4 mb-3"></i>
                            <h4>No Users Found</h4>
                            <p>There are currently no user accounts in the system.</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @endif

        <!-- Warning Notice -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle"></i>
                    <strong>User Management:</strong> You can disable or delete any user account. Disabled users cannot log in to the system.
                </div>
            </div>        </div>
    </div>
</div>

<!-- Disable User Modals -->
@foreach($users as $user)
    @if($user->status)
    <div class="modal fade" id="disableUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="disableUserModalLabel{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="disableUserModalLabel{{ $user->id }}">
                        <i class="bi bi-exclamation-triangle me-2"></i>Disable User Account
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('developer.users.toggle-status', $user->id) }}">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <div class="alert alert-warning" role="alert">
                            <strong>Warning:</strong> You are about to disable <strong>{{ $user->name }}</strong>'s user account.
                        </div>
                        
                        <div class="mb-3">
                            <label for="disabled_reason{{ $user->id }}" class="form-label">
                                <strong>Reason for Disabling Account</strong> <span class="text-danger">*</span>
                            </label>
                            <textarea 
                                class="form-control" 
                                id="disabled_reason{{ $user->id }}" 
                                name="disabled_reason" 
                                rows="4" 
                                placeholder="Example: Account disabled due to security policy violation, suspicious activity, or administrative decision. Please provide a clear explanation..."
                                required
                                minlength="10"
                                maxlength="500"
                            ></textarea>
                            <div class="form-text">
                                This reason will be displayed to the user when they attempt to log in. Minimum 10 characters, maximum 500 characters.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-info">
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
// Add form validation for disable reason modals
document.addEventListener('DOMContentLoaded', function() {
    // Get all disable user forms
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
});
</script>

</body>
</html>
