@extends('layouts.app-welcome')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-gradient text-white text-center py-4 position-relative">
                    <div class="d-flex flex-column align-items-center">
                        <div class="profile-avatar mb-3">
                            <i class="fa fa-user-circle fa-4x text-secondary"></i>
                        </div>
                        <h3 class="mb-0 text-white">My Profile</h3>
                        <p class="mb-0 text-light small">View and update your account information</p>
                    </div>
                </div>
                <div class="card-body p-5">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('profile.update') }}" id="profileForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">Full Name</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', auth()->user()->name) }}" required autocomplete="name" maxlength="100">
                            @error('name')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', auth()->user()->email) }}" required autocomplete="email" maxlength="100">
                            @error('email')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="contact_number" class="form-label fw-semibold">Contact Number</label>
                            <input id="contact_number" type="tel" class="form-control @error('contact_number') is-invalid @enderror" name="contact_number" value="{{ old('contact_number', auth()->user()->contact_number) }}" required autocomplete="tel" maxlength="20">
                            @error('contact_number')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="d-grid gap-2 mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
                        </div>
                    </form>
                    <hr class="my-5">
                    <h5 class="mb-4">Change Password</h5>
                    <form method="POST" action="{{ route('profile.password') }}" id="passwordForm" onsubmit="return confirmPasswordChange(event)">
                        @csrf
                        <div class="mb-4">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required autocomplete="current-password" maxlength="100">
                            @error('current_password')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label">New Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" maxlength="100">
                            @error('password')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" maxlength="100">
                            <div id="password-match-message"></div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-outline-primary btn-lg">Change Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmPasswordChange(event) {
    event.preventDefault();
    Swal.fire({
        title: "Confirm Password Change",
        text: "Are you sure you want to change your password?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, change it!",
        cancelButtonText: "No, cancel!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Enable the submit button before submitting
            const submitButton = document.querySelector('#passwordForm button[type="submit"]');
            submitButton.disabled = false;
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
    if (confirmPassword === '') {
        messageElement.innerHTML = '';
        messageElement.className = '';
        return;
    }
    if (password === confirmPassword) {
        messageElement.innerHTML = '<i class="bi bi-check-circle me-2"></i>Passwords match';
        messageElement.className = 'text-success small mt-2';
        submitButton.disabled = false;
    } else {
        messageElement.innerHTML = '<i class="bi bi-x-circle me-2"></i>Passwords do not match';
        messageElement.className = 'text-danger small mt-2';
        submitButton.disabled = true;
    }
}

function checkCurrentPassword() {
    const currentPassword = document.getElementById('current_password').value;
    const currentPasswordInput = document.getElementById('current_password');
    const submitButton = document.querySelector('#passwordForm button[type="submit"]');
    let messageElem = document.getElementById('current-password-message');
    if (!messageElem) {
        messageElem = document.createElement('div');
        messageElem.id = 'current-password-message';
        currentPasswordInput.parentNode.appendChild(messageElem);
    }
    if (currentPassword.length === 0) {
        messageElem.innerHTML = '';
        messageElem.className = '';
        submitButton.disabled = false;
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
    .then(response => response.json())
    .then(data => {
        if (data.valid) {
            messageElem.innerHTML = '<i class="bi bi-check-circle me-2"></i>Current password is correct';
            messageElem.className = 'text-success small mt-2';
            submitButton.disabled = false;
        } else {
            messageElem.innerHTML = '<i class="bi bi-x-circle me-2"></i>Current password is incorrect';
            messageElem.className = 'text-danger small mt-2';
            submitButton.disabled = true;
        }
    })
    .catch(() => {
        messageElem.innerHTML = '<i class="bi bi-x-circle me-2"></i>Error checking password';
        messageElem.className = 'text-danger small mt-2';
        submitButton.disabled = true;
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const currentPasswordInput = document.getElementById('current_password');
    passwordInput.addEventListener('input', checkPasswordMatch);
    confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    currentPasswordInput.addEventListener('input', checkCurrentPassword);
});
</script>

<style>
.profile-avatar {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: #f1f1f1;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}
.card-header.bg-gradient {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
}
.card {
    backdrop-filter: blur(10px);
    background-color: rgba(255, 255, 255, 0.95);
}
.btn-lg {
    font-size: 1.1rem;
    padding: 0.75rem 1.5rem;
}
#password-match-message {
    transition: all 0.3s ease;
    font-weight: 500;
}
#password-match-message i {
    font-size: 0.9em;
}
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
@endsection
