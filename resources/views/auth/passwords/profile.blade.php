@extends(session('is_admin') ? 'layouts.app' : 'layouts.app-user')

@section('content')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --glass-bg: rgba(255, 255, 255, 0.25);
        --glass-border: rgba(255, 255, 255, 0.18);
        --shadow-light: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        --shadow-hover: 0 15px 35px 0 rgba(31, 38, 135, 0.4);
        --text-primary: #2d3748;
        --text-secondary: #4a5568;
        --border-radius: 20px;
    }

    body {
        background: white;
        background-attachment: fixed;
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        overflow-x: hidden;
    }

    .profile-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
        min-height: 100vh;
        position: relative;
    }

    .profile-container::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.05"><circle cx="30" cy="30" r="1.5"/></g></svg>');
        pointer-events: none;
        z-index: 0;
    }

    .profile-header {
        text-align: center;
        margin-bottom: 3rem;
        position: relative;
        z-index: 1;
    }

    .profile-header h1 {
        color: var(--text-primary);
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }



    .profile-header p {
        color: var(--text-secondary);
        font-size: 1.2rem;
        font-weight: 400;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        grid-template-rows: repeat(6, 1fr);
        gap: 2rem;
        min-height: 80vh;
        position: relative;
        z-index: 1;
    }

    .profile-card {
        background: var(--glass-bg);
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        border: 1px solid var(--glass-border);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
    }

    .profile-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .profile-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: var(--shadow-hover);
        border-color: rgba(255,255,255,0.3);
    }

    .profile-card:hover::before {
        opacity: 1;
    }

    .hero-section {
        grid-row: span 4 / span 4;
        background: linear-gradient(135deg, var(--primary-color, #667eea) 0%, var(--secondary-color, #764ba2) 100%);
        color: white;
        padding: 2.5rem;
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
        background: url('data:image/svg+xml,<svg width="80" height="80" viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.08"><circle cx="40" cy="40" r="2"/><circle cx="20" cy="20" r="1"/><circle cx="60" cy="60" r="1.5"/></g></svg>');
        opacity: 0.4;

    }



    .hero-section::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);

        pointer-events: none;
    }



    .avatar-container {
        position: relative;
        margin-bottom: 1.5rem;
        cursor: pointer;
    }

    .avatar {
        width: 140px;
        height: 140px;
        background: #6b7280;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        font-weight: 800;
        border: 5px solid rgba(255,255,255,0.4);
        position: relative;
        z-index: 2;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2), inset 0 2px 10px rgba(255,255,255,0.3);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .avatar-container:hover .avatar {
        transform: scale(1.05);
        box-shadow: 0 20px 40px rgba(0,0,0,0.3), inset 0 2px 15px rgba(255,255,255,0.4);
    }



    .avatar-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .avatar-upload-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 140px;
        height: 140px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transition: all 0.3s ease;
        z-index: 3;
        cursor: pointer;
    }

    .avatar-container:hover .avatar-upload-overlay {
        opacity: 1;
    }

    .avatar-upload-overlay i {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .avatar-upload-overlay span {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }



    .hero-name {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 1.2rem;
        position: relative;
        z-index: 2;
        color: white;
        text-shadow: 0 4px 15px rgba(0,0,0,0.3);

    }



    .hero-details {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
        font-size: 0.95rem;
        opacity: 0.95;
        position: relative;
        z-index: 2;
        color: white;
        text-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }

    .hero-details > div {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.3rem 0;
        transition: all 0.3s ease;
    }

    .hero-details > div:hover {
        transform: translateX(5px);
        opacity: 1;
    }

    .hero-details i {
        width: 16px;
        text-align: center;
        opacity: 0.8;
    }

    .status-badge {
        background: linear-gradient(135deg, var(--secondary-color, #764ba2) 0%, var(--primary-color, #667eea) 100%);
        color: white;
        padding: 0.8rem 1.5rem;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 700;
        margin-top: 1.5rem;
        position: relative;
        z-index: 2;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        border: 2px solid rgba(255,255,255,0.3);
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.3);
        border-color: rgba(255,255,255,0.5);
    }

    .status-badge i {
        margin-right: 0.5rem;

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
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid rgba(255,255,255,0.2);
        position: relative;
    }

    .section-header::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 60px;
        height: 2px;
        background: linear-gradient(135deg, var(--primary-color, #667eea) 0%, var(--secondary-color, #764ba2) 100%);
        border-radius: 1px;
    }

    .section-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary-color, #667eea) 0%, var(--secondary-color, #764ba2) 100%);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-right: 1.2rem;
        font-size: 1.2rem;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
    }

    .section-icon:hover {
        transform: translateY(-2px) rotate(5deg);
        box-shadow: 0 12px 25px rgba(102, 126, 234, 0.4);
    }

    .section-title {
        color: var(--text-primary);
        font-size: 1.4rem;
        font-weight: 800;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .form-group {
        margin-bottom: 1.5rem;
        position: relative;
    }

    .form-label {
        display: block;
        color: var(--text-primary);
        font-weight: 700;
        margin-bottom: 0.8rem;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        position: relative;
    }

    .form-label::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 30px;
        height: 2px;
        background: linear-gradient(135deg, var(--primary-color, #667eea) 0%, var(--secondary-color, #764ba2) 100%);
        border-radius: 1px;
    }

    .form-input {
        width: 100%;
        padding: 1rem 1.2rem;
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        font-size: 1rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        font-weight: 500;
        color: var(--text-primary);
    }

    .form-input::placeholder {
        color: var(--text-secondary);
        opacity: 0.7;
    }

    .form-input:focus {
        outline: none;
        border-color: rgba(102, 126, 234, 0.6);
        background: rgba(255,255,255,0.95);
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1), 0 8px 25px rgba(102, 126, 234, 0.15);
        transform: translateY(-2px);
    }

    .form-input:hover {
        border-color: rgba(102, 126, 234, 0.4);
        transform: translateY(-1px);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color, #667eea) 0%, var(--secondary-color, #764ba2) 100%);
        color: white;
        padding: 1rem 2rem;
        border: none;
        border-radius: 15px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        text-transform: uppercase;
        letter-spacing: 1px;
        width: 100%;
        margin-top: 1rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        border: 2px solid rgba(255,255,255,0.2);
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
        border-color: rgba(255,255,255,0.4);
    }

    .btn-primary:active {
        transform: translateY(-1px) scale(0.98);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
    }

    .btn-primary:disabled:hover {
        transform: none;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);
    }

    .btn-primary i {
        margin-right: 0.5rem;
        transition: transform 0.3s ease;
    }

    .btn-primary:hover i {
        transform: translateX(2px);
    }

    .error-message {
        color: #ef4444;
        font-size: 0.85rem;
        margin-top: 0.8rem;
        padding: 0.8rem 1rem;
        background: rgba(239, 68, 68, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        border: 1px solid rgba(239, 68, 68, 0.2);
        border-left: 4px solid #ef4444;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;

    }

    .error-message::before {
        content: '⚠️';
        font-size: 1rem;
    }



    .validation-message {
        font-size: 0.85rem;
        margin-top: 0.8rem;
        padding: 0.8rem 1rem;
        border-radius: 12px;
        font-weight: 600;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        gap: 0.5rem;

    }



    @media (max-width: 1200px) {
        .profile-grid {
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(5, 1fr);
            gap: 1.5rem;
        }
        
        .hero-section {
            grid-column: span 3;
            grid-row: span 1;
            padding: 2rem;
        }
        
        .edit-profile-section {
            grid-column: span 3;
            grid-row: span 2;
        }
        
        .change-password-section {
            grid-column: span 3;
            grid-row: span 2;
        }
        
        .avatar {
            width: 120px;
            height: 120px;
            font-size: 3rem;
        }
    }

    @media (max-width: 768px) {
        .profile-container {
            padding: 1rem;
        }
        
        .profile-header h1 {
            font-size: 2.2rem;
        }
        
        .profile-header p {
            font-size: 1rem;
        }
        
        .profile-grid {
            grid-template-columns: 1fr;
            grid-template-rows: auto;
            gap: 1.5rem;
        }
        
        .profile-grid > div {
            grid-column: 1 !important;
            grid-row: auto !important;
        }
        
        .hero-section {
            padding: 2rem 1.5rem;
        }
        
        .avatar {
            width: 100px;
            height: 100px;
            font-size: 2.5rem;
        }
        
        .hero-name {
            font-size: 1.8rem;
        }
        
        .hero-details {
            font-size: 0.9rem;
        }
        
        .section-icon {
            width: 45px;
            height: 45px;
            font-size: 1.1rem;
        }
        
        .section-title {
            font-size: 1.2rem;
        }
        
        .form-input {
            padding: 0.9rem 1rem;
            font-size: 0.95rem;
        }
        
        .btn-primary {
            padding: 0.9rem 1.5rem;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .profile-header h1 {
            font-size: 1.8rem;
        }
        
        .hero-section {
            padding: 1.5rem 1rem;
        }
        
        .avatar {
            width: 80px;
            height: 80px;
            font-size: 2rem;
        }
        
        .hero-name {
            font-size: 1.5rem;
        }
        
        .hero-details {
            font-size: 0.85rem;
        }
        
        .status-badge {
            padding: 0.6rem 1rem;
            font-size: 0.75rem;
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
            <div class="avatar-container">
                <div class="avatar" id="avatarDisplay">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="Profile Picture" class="avatar-image">
                    @else
                        <i class="bi bi-person-fill"></i>
                    @endif
                </div>
                <div class="avatar-upload-overlay" onclick="document.getElementById('avatarInput').click()">
                    <i class="fas fa-camera"></i>
                    <span>Upload Photo</span>
                </div>
                <input type="file" id="avatarInput" accept="image/*" style="display: none;" onchange="handleAvatarUpload(event)">
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

function handleAvatarUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    // Validate file type
    if (!file.type.startsWith('image/')) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid File Type',
            text: 'Please select a valid image file (JPG, PNG, GIF, etc.)',
            confirmButtonColor: '#ef4444'
        });
        return;
    }

    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        Swal.fire({
            icon: 'error',
            title: 'File Too Large',
            text: 'Please select an image smaller than 5MB',
            confirmButtonColor: '#ef4444'
        });
        return;
    }

    // Show loading
    Swal.fire({
        title: 'Uploading...',
        text: 'Please wait while we upload your profile picture',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Create FormData for file upload
    const formData = new FormData();
    formData.append('avatar', file);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    // Upload the file
    fetch('{{ route("profile.avatar.upload") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update avatar display
            const avatarDisplay = document.getElementById('avatarDisplay');
            avatarDisplay.innerHTML = `<img src="${data.avatar_url}" alt="Profile Picture" class="avatar-image">`;
            
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Your profile picture has been updated successfully',
                confirmButtonColor: '#667eea',
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            throw new Error(data.message || 'Upload failed');
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Upload Failed',
            text: error.message || 'There was an error uploading your profile picture. Please try again.',
            confirmButtonColor: '#ef4444'
        });
    });

    // Reset file input
    event.target.value = '';
}
</script>
@endsection
