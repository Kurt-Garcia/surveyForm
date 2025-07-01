@extends('layouts.app')

@section('content')
<style>
    .profile-container {
        max-width: 1600px;
        margin: 0 auto;
        padding: 1.5rem;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .profile-header {
        text-align: center;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
    }

    .profile-title {
        font-size: 3rem;
        font-weight: 800;
        color: var(--primary-color, #222);
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.75rem;
        line-height: 1.2;
        text-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .profile-subtitle {
        color: #6b7280;
        font-size: 1.125rem;
        font-weight: 400;
        max-width: 550px;
        margin: 0 auto;
        line-height: 1.6;
    }

    .main-layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        flex: 1;
    }

    .hero-profile-section {
        width: 100%;
        margin-bottom: 1rem;
    }

    .profile-info-card {
        background: var(--background-color, linear-gradient(135deg, #667eea 0%, #764ba2 100%));
        border-radius: 24px;
        padding: 2rem 1.5rem;
        color: var(--text-color, white);
        text-align: center;
        position: relative;
        overflow: hidden;
        border: none;
        box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        width: 100%;
        background-size: 120% 120%;
        background-position: center;
        animation: gradientShift 8s ease-in-out infinite;
    }

    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .profile-info-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        animation: rotate 20s linear infinite;
        pointer-events: none;
    }

    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .profile-info-card:hover {
        transform: translateY(-4px) scale(1.01);
        box-shadow: 0 20px 50px rgba(102, 126, 234, 0.5);
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(20px);
        border: 3px solid rgba(255, 255, 255, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-color, white);
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0 auto 1.5rem;
        position: relative;
        z-index: 2;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        transition: transform 0.3s ease;
    }

    .profile-avatar:hover {
        transform: scale(1.05);
    }

    .profile-name {
        font-size: 1.75rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 2;
        color: var(--text-color, white);
        line-height: 1.2;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .profile-details {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        margin-bottom: 1.25rem;
        position: relative;
        z-index: 2;
    }

    .profile-email {
        font-size: 0.9rem;
        opacity: 0.95;
        color: var(--text-color, white);
        background: rgba(255, 255, 255, 0.15);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        word-break: break-word;
        transition: transform 0.3s ease;
    }

    .profile-email:hover {
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.2);
    }

    .profile-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(16, 185, 129, 0.25);
        color: #10b981;
        padding: 0.6rem 1.25rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        border: 2px solid rgba(16, 185, 129, 0.4);
        backdrop-filter: blur(15px);
        margin: 1.25rem auto 0;
        position: relative;
        z-index: 2;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        transition: all 0.3s ease;
    }

    .profile-badge:hover {
        transform: scale(1.05);
        background: rgba(16, 185, 129, 0.3);
    }

    .profile-badge::before {
        content: '✓';
        width: 18px;
        height: 18px;
        background: #10b981;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 800;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.5);
    }

    .content-area {
        display: flex;
        flex-direction: column;
        gap: 2rem;
        min-height: 0;
    }

    .forms-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
        gap: 2rem;
    }

    .form-card {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid #f3f4f6;
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        position: relative;
        overflow: hidden;
    }

    .form-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, var(--primary-color, #667eea) 0%, var(--secondary-color, #764ba2) 100%);
        background-size: 200% 200%;
        animation: gradientMove 3s ease-in-out infinite;
    }

    @keyframes gradientMove {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .form-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .form-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border-bottom: 2px solid #f3f4f6;
        padding-bottom: 1rem;
    }

    .form-icon {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, var(--primary-color, #667eea) 0%, var(--secondary-color, #764ba2) 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.1rem;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        transition: transform 0.3s ease;
    }

    .form-icon:hover {
        transform: scale(1.1);
    }

    .form-group {
        margin-bottom: 2rem;
        position: relative;
    }

    .form-label {
        display: block;
        font-weight: 700;
        color: #374151;
        margin-bottom: 0.75rem;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    .form-input {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid #e5e7eb;
        border-radius: 14px;
        font-size: 1rem;
        transition: all 0.4s ease;
        background: #fafafa;
        font-family: inherit;
        font-weight: 500;
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
        transform: translateY(-1px);
    }

    .form-button {
        width: 100%;
        padding: 1rem 1.75rem;
        background: linear-gradient(135deg, var(--primary-color, #667eea) 0%, var(--secondary-color, #764ba2) 100%);
        color: white;
        border: none;
        border-radius: 14px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.4s ease;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-top: 1rem;
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .form-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.6s;
    }

    .form-button:hover::before {
        left: 100%;
    }

    .form-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
    }

    .form-button:disabled {
        background: #d1d5db;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .error-message {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        font-weight: 500;
    }

    .success-message {
        color: #10b981;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        font-weight: 500;
    }

    .password-strength {
        margin-top: 0.5rem;
        font-size: 0.875rem;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @media (max-width: 1200px) {
        .forms-container {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .profile-container {
            padding: 1rem;
        }
        
        .profile-info-card {
            padding: 2rem 1.5rem;
        }
        
        .profile-avatar {
            width: 110px;
            height: 110px;
            font-size: 2.75rem;
        }
    }

    @media (max-width: 768px) {
        .profile-title {
            font-size: 2.5rem;
        }
        
        .profile-header {
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .profile-info-card {
            padding: 1.75rem 1.25rem;
        }
        
        .profile-avatar {
            width: 90px;
            height: 90px;
            font-size: 2.25rem;
        }
        
        .profile-name {
            font-size: 1.5rem;
        }
        
        .profile-email {
            font-size: 0.85rem;
        }
        
        .form-card {
            padding: 2rem;
        }
        
        .form-title {
            font-size: 1.75rem;
        }
        
        .forms-container {
            gap: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .profile-container {
            padding: 0.75rem;
        }
        
        .profile-title {
            font-size: 2rem;
        }
        
        .profile-subtitle {
            font-size: 1rem;
        }
        
        .form-card {
            padding: 1.5rem;
        }
        
        .profile-info-card {
            padding: 1.5rem 1rem;
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
            font-size: 2rem;
        }
        
        .profile-name {
            font-size: 1.4rem;
        }
        
        .form-title {
            font-size: 1.5rem;
        }
        
        .form-input {
            padding: 1rem;
        }
        
        .form-button {
            padding: 1rem 1.5rem;
        }
    }
</style>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        <h1 class="profile-title">Account Settings</h1>
        <p class="profile-subtitle">Manage your profile information and security settings with our intuitive dashboard designed for modern users</p>
    </div>

    <div class="main-layout">
        <!-- Hero Profile Section -->
        <div class="hero-profile-section">
            <div class="profile-info-card">
                <div class="profile-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                
                <h2 class="profile-name">{{ auth()->user()->name }}</h2>
                
                <div class="profile-details">
                    <div class="profile-email">{{ auth()->user()->email }}</div>
                    <div class="profile-email">{{ auth()->user()->contact_number }}</div>
                </div>
                
                <div class="profile-badge">
                    Active Account
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <div class="forms-container">
                <!-- Edit Profile Form -->
                <div class="form-card">
                    <h2 class="form-title">
                        <span class="form-icon"><i class="bi bi-person-fill"></i></span>
                        Edit Profile
                    </h2>
                    
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

                        <button type="submit" class="form-button">Save Changes</button>
                    </form>
                </div>

                <!-- Change Password Form -->
                <div class="form-card">
                    <h2 class="form-title">
                        <span class="form-icon"><i class="bi bi-lock-fill"></i></span>
                        Change Password
                    </h2>
                    
                    <form method="POST" action="{{ route('profile.password') }}" id="passwordForm" onsubmit="return confirmPasswordChange(event)">
                        @csrf

                        <div class="form-group">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input id="current_password" type="password" name="current_password" required class="form-input">
                            <div id="current-password-message" class="password-strength"></div>
                            @error('current_password') <div class="error-message">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label">New Password</label>
                            <input id="password" type="password" name="password" required class="form-input">
                            @error('password') <div class="error-message">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required class="form-input">
                            <div id="password-match-message" class="password-strength"></div>
                        </div>

                        <button type="submit" class="form-button">Update Password</button>
                    </form>
                </div>
            </div>
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
            // Show loading toast
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
            // Show loading toast
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
        messageElement.innerHTML = '<span class="success-message">✓ Passwords match</span>';
        messageElement.className = 'password-strength';
        submitButton.disabled = false;
    } else {
        messageElement.innerHTML = '<span class="error-message">✗ Passwords do not match</span>';
        messageElement.className = 'password-strength';
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
            messageElem.innerHTML = '<span class="success-message">✓ Current password is correct</span>';
            submitButton.disabled = false;
        } else {
            messageElem.innerHTML = '<span class="error-message">✗ Current password is incorrect</span>';
            submitButton.disabled = true;
        }
    })
    .catch(() => {
        messageElem.innerHTML = '<span class="error-message">✗ Error checking password</span>';
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
    const passwordGroup = passwordInput.closest('.form-group');
    const strengthDiv = document.createElement('div');
    strengthDiv.id = 'password-strength-indicator';
    passwordGroup.appendChild(strengthDiv);

    passwordInput.addEventListener('input', (e) => {
        checkPasswordStrength(e.target.value);
        checkPasswordMatch();
    });

    confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    currentPasswordInput.addEventListener('input', checkCurrentPassword);

    // Add smooth focus animations
    document.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('focus', function() {
            this.style.transform = 'scale(1.02)';
        });
        
        input.addEventListener('blur', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>
@endsection
