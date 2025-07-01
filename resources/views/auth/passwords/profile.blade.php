@extends('layouts.app')

@section('content')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    body {
        background-color: var(--background-color, #f3f4f6);
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .profile-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
        min-height: 100vh;
    }

    .profile-header {
        text-align: center;
        margin-bottom: 3rem;
    }

    .profile-header h1 {
        color: var(--text-color, rgb(0, 0, 0));
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .profile-header p {
        color: var(--text-color, rgb(0, 0, 0));
        font-size: 1.1rem;
        font-weight: 300;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        grid-template-rows: repeat(6, 1fr);
        gap: 1.5rem;
        min-height: 80vh;
    }

    .profile-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        overflow: hidden;
        transition: all 0.3s ease;
        
    }

    .profile-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 30px 50px rgba(0,0,0,0.15);
    }

    .hero-section {
        grid-row: span 4 / span 4;
        background-color: var(--background-color, #667eea);
        color: white;
        padding: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="2"/></g></svg>');
        opacity: 0.3;
    }

    .avatar {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        border: 4px solid rgba(255,255,255,0.3);
        position: relative;
        z-index: 1;
    }

    .hero-name {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        position: relative;
        z-index: 1;
        color: var(--text-color, white);
    }

    .hero-details {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        font-size: 0.9rem;
        opacity: 0.9;
        position: relative;
        z-index: 1;
        color: var(--text-color, white);
    }

    .status-badge {
        background: rgba(16, 185, 129, 0.9);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-top: 1rem;
        position: relative;
        z-index: 1;
    }

    .edit-profile-section {
        grid-column-start: 2;
        grid-row-start: 1;
        grid-column: span 4;
        grid-row: span 2;
        padding: 1.5rem;
    }

    .change-password-section {
        grid-column-start: 2;
        grid-row-start: 3;
        grid-column: span 4;
        grid-row: span 2;
        padding: 1.5rem;
    }

    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f3f4f6;
    }

    .section-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-right: 1rem;
        font-size: 1rem;
    }

    .section-title {
        color: #1f2937;
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: block;
        color: #374151;
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #f9fafb;
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        transform: translateY(-1px);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        width: 100%;
        margin-top: 0.75rem;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.8rem;
        margin-top: 0.5rem;
        padding: 0.5rem;
        background: rgba(239, 68, 68, 0.1);
        border-radius: 8px;
        border-left: 3px solid #ef4444;
    }

    .validation-message {
        font-size: 0.8rem;
        margin-top: 0.5rem;
        padding: 0.5rem;
        border-radius: 8px;
        font-weight: 500;
    }

    @media (max-width: 1200px) {
        .profile-grid {
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(5, 1fr);
        }
        
        .hero-section {
            grid-column: span 3;
            grid-row: span 1;
        }
        
        .edit-profile-section {
            grid-column: span 3;
            grid-row: span 2;
        }
        
        .change-password-section {
            grid-column: span 3;
            grid-row: span 2;
        }
    }

    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr;
            grid-template-rows: auto;
            gap: 1rem;
        }
        
        .profile-grid > div {
            grid-column: 1 !important;
            grid-row: auto !important;
        }
        
        .hero-section {
            padding: 1.5rem;
        }
        
        .avatar {
            width: 80px;
            height: 80px;
            font-size: 2rem;
        }
        
        .hero-name {
            font-size: 1.5rem;
        }
    }
</style>

@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#667eea',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    </script>
@endif

<div class="profile-container">
    <div class="profile-header">
        <h1><i class="fas fa-user-cog"></i> Account Settings</h1>
        <p>Manage your profile information and security settings with ease</p>
    </div>

    <div class="profile-grid">
        <!-- Hero Profile Section -->
        <div class="profile-card hero-section">
            <div class="avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <h2 class="hero-name">{{ auth()->user()->name }}</h2>
            <div class="hero-details">
                <div><i class="fas fa-envelope"></i> {{ auth()->user()->email }}</div>
                <div><i class="fas fa-phone"></i> {{ auth()->user()->contact_number }}</div>
                <div><i class="fas fa-calendar-alt"></i> Joined {{ auth()->user()->created_at ? auth()->user()->created_at->format('F d, Y') : '' }}</div>
                <div><i class="fas fa-building"></i> SBU: 
                    @if(isset($user) && $user->sbus->count() > 0)
                        {{ $user->sbus->pluck('name')->join(', ') }}
                    @else
                        Not Assigned
                    @endif
                </div>
                <div><i class="fas fa-map-marker-alt"></i> Site: 
                    @if(isset($user) && $user->sites->count() > 0)
                        @if($user->sites->count() == 1)
                            {{ $user->sites->first()->name }}
                        @else
                            <span title="{{ $user->sites->pluck('name')->join(', ') }}">
                                {{ $user->sites->first()->name }} +{{ $user->sites->count() - 1 }} More...
                            </span>
                        @endif
                    @else
                        Not Assigned
                    @endif
                </div>
            </div>
            <div class="status-badge">
                <i class="fas fa-check-circle"></i> Active Account
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="profile-card edit-profile-section">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <h2 class="section-title">Edit Profile</h2>
            </div>
            <form method="POST" action="{{ route('profile.update') }}" id="profileForm" onsubmit="return confirmProfileUpdate(event)">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required class="form-input">
                    @error('name') <div class="error-message">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required class="form-input">
                    @error('email') <div class="error-message">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="contact_number" class="form-label">Contact Number</label>
                    <input id="contact_number" type="text" name="contact_number" value="{{ old('contact_number', auth()->user()->contact_number) }}" required class="form-input">
                    @error('contact_number') <div class="error-message">{{ $message }}</div> @enderror
                </div>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>

        <!-- Change Password Form -->
        <div class="profile-card change-password-section">
            <div class="section-header">
                <div class="section-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h2 class="section-title">Security</h2>
            </div>
            <form method="POST" action="{{ route('profile.password') }}" id="passwordForm" onsubmit="return confirmPasswordChange(event)">
                @csrf
                <div class="form-group">
                    <label for="current_password" class="form-label">Current Password</label>
                    <input id="current_password" type="password" name="current_password" required class="form-input">
                    <div id="current-password-message" class="validation-message"></div>
                    @error('current_password') <div class="error-message">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input id="password" type="password" name="password" required class="form-input">
                    <div id="password-strength-indicator"></div>
                    @error('password') <div class="error-message">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required class="form-input">
                    <div id="password-match-message" class="validation-message"></div>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-shield-alt"></i> Update Password
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmProfileUpdate(event) {
    event.preventDefault();
    Swal.fire({
        title: 'Confirm Profile Update',
        text: 'Are you sure you want to save these changes?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, save changes',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        backdrop: true,
        allowOutsideClick: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Updating...',
                text: 'Saving your changes',
                icon: 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            event.target.submit();
        }
    });
    return false;
}

function confirmPasswordChange(event) {
    event.preventDefault();
    Swal.fire({
        title: 'Confirm Password Change',
        text: 'Are you sure you want to change your password?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, change password',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        backdrop: true,
        allowOutsideClick: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Updating...',
                text: 'Changing your password',
                icon: 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            event.target.submit();
        }
    });
    return false;
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    const messageElement = document.getElementById('password-match-message');
    const submitButton = document.querySelector('#passwordForm button[type="submit"]');
    if (!confirmPassword) {
        messageElement.innerHTML = '';
        return;
    }
    if (password === confirmPassword) {
        messageElement.innerHTML = '<span style="color: #10b981;">✓ Passwords match</span>';
        submitButton.disabled = false;
    } else {
        messageElement.innerHTML = '<span style="color: #ef4444;">✗ Passwords do not match</span>';
        submitButton.disabled = true;
    }
}

function checkCurrentPassword() {
    const currentPassword = document.getElementById('current_password').value;
    const messageElem = document.getElementById('current-password-message');
    const submitButton = document.querySelector('#passwordForm button[type="submit"]');
    if (!currentPassword) {
        messageElem.innerHTML = '';
        return;
    }
    fetch("{{ route('profile.checkCurrentPassword') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({ current_password: currentPassword })
    })
    .then(res => res.json())
    .then(data => {
        if (data.valid) {
            messageElem.innerHTML = '<span style="color: #10b981;">✓ Current password is correct</span>';
            submitButton.disabled = false;
        } else {
            messageElem.innerHTML = '<span style="color: #ef4444;">✗ Current password is incorrect</span>';
            submitButton.disabled = true;
        }
    })
    .catch(() => {
        messageElem.innerHTML = '<span style="color: #ef4444;">✗ Error checking password</span>';
        submitButton.disabled = true;
        Swal.fire({
            icon: 'error',
            title: 'Connection Error',
            text: 'Unable to verify current password. Please check your connection and try again.',
            confirmButtonText: 'OK',
            confirmButtonColor: '#ef4444',
            toast: true,
            position: 'top-end',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    });
}

function checkPasswordStrength(password) {
    const strengthIndicator = document.getElementById('password-strength-indicator');
    if (!strengthIndicator) return;
    let strength = 0;
    const checks = [
        password.length >= 8,
        /[A-Z]/.test(password),
        /[a-z]/.test(password),
        /[0-9]/.test(password),
        /[^A-Za-z0-9]/.test(password)
    ];
    strength = checks.filter(Boolean).length;
    const strengthLevels = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
    const strengthColors = ['#ef4444', '#f97316', '#eab308', '#22c55e', '#16a34a'];
    strengthIndicator.innerHTML = `
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
            <div style="flex: 1; height: 4px; background: #e5e7eb; border-radius: 2px;">
                <div style="height: 100%; width: ${(strength / 5) * 100}%; background: ${strengthColors[strength - 1] || '#e5e7eb'}; border-radius: 2px; transition: all 0.3s ease;"></div>
            </div>
            <span style="font-size: 0.75rem; color: ${strengthColors[strength - 1] || '#6b7280'}; font-weight: 500;">
                ${strengthLevels[strength - 1] || 'Too short'}
            </span>
        </div>
    `;
}

document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const currentPasswordInput = document.getElementById('current_password');
    // Add password strength indicator
    const passwordGroup = passwordInput.closest('div');
    const strengthDiv = document.createElement('div');
    strengthDiv.id = 'password-strength-indicator';
    passwordGroup.appendChild(strengthDiv);
    passwordInput.addEventListener('input', (e) => {
        checkPasswordStrength(e.target.value);
        checkPasswordMatch();
    });
    confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    currentPasswordInput.addEventListener('input', checkCurrentPassword);
});
</script>
@endsection
