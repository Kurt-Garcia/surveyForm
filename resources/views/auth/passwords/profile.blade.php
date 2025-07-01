@extends('layouts.app')

@section('content')
@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            alert(@json(session('success')));
        });
    </script>
@endif

<h1>Account Settings</h1>

<!-- Profile Info -->
<h2>Profile</h2>
<p>Name: {{ auth()->user()->name }}</p>
<p>Email: {{ auth()->user()->email }}</p>
<p>Status: Active</p>
<p>Member Since: {{ auth()->user()->created_at->format('M Y') }}</p>
<p>Account Type: Standard User</p>

<!-- Edit Profile Form -->
<h2>Edit Profile</h2>
<form method="POST" action="{{ route('profile.update') }}" id="profileForm" onsubmit="return confirmProfileUpdate(event)">
    @csrf
    @method('PUT')

    <label for="name">Full Name</label><br>
    <input id="name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required><br>
    @error('name') <span>{{ $message }}</span><br> @enderror

    <label for="email">Email Address</label><br>
    <input id="email" type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required><br>
    @error('email') <span>{{ $message }}</span><br> @enderror

    <label for="contact_number">Contact Number</label><br>
    <input id="contact_number" type="text" name="contact_number" value="{{ old('contact_number', auth()->user()->contact_number) }}" required><br>
    @error('contact_number') <span>{{ $message }}</span><br> @enderror

    <button type="submit">Save Changes</button>
</form>

<!-- Change Password Form -->
<h2>Change Password</h2>
<form method="POST" action="{{ route('profile.password') }}" id="passwordForm" onsubmit="return confirmPasswordChange(event)">
    @csrf

    <label for="current_password">Current Password</label><br>
    <input id="current_password" type="password" name="current_password" required><br>
    <span id="current-password-message"></span><br>
    @error('current_password') <span>{{ $message }}</span><br> @enderror

    <label for="password">New Password</label><br>
    <input id="password" type="password" name="password" required><br>
    @error('password') <span>{{ $message }}</span><br> @enderror

    <label for="password_confirmation">Confirm Password</label><br>
    <input id="password_confirmation" type="password" name="password_confirmation" required><br>
    <span id="password-match-message"></span><br>

    <button type="submit">Update Password</button>
</form>

<script>
function confirmProfileUpdate(event) {
    event.preventDefault();
    if (confirm("Are you sure you want to save these changes?")) {
        event.target.submit();
    }
    return false;
}

function confirmPasswordChange(event) {
    event.preventDefault();
    if (confirm("Are you sure you want to change your password?")) {
        event.target.submit();
    }
    return false;
}

function checkPasswordMatch() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    const messageElement = document.getElementById('password-match-message');
    const submitButton = document.querySelector('#passwordForm button[type="submit"]');
    if (!confirmPassword) return messageElement.innerHTML = '';

    if (password === confirmPassword) {
        messageElement.innerHTML = 'Passwords match';
        submitButton.disabled = false;
    } else {
        messageElement.innerHTML = 'Passwords do not match';
        submitButton.disabled = true;
    }
}

function checkCurrentPassword() {
    const currentPassword = document.getElementById('current_password').value;
    const messageElem = document.getElementById('current-password-message');
    const submitButton = document.querySelector('#passwordForm button[type="submit"]');

    if (!currentPassword) return messageElem.innerHTML = '';

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
            messageElem.innerHTML = 'Current password is correct';
            submitButton.disabled = false;
        } else {
            messageElem.innerHTML = 'Current password is incorrect';
            submitButton.disabled = true;
        }
    })
    .catch(() => {
        messageElem.innerHTML = 'Error checking password';
        submitButton.disabled = true;
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('password').addEventListener('input', checkPasswordMatch);
    document.getElementById('password_confirmation').addEventListener('input', checkPasswordMatch);
    document.getElementById('current_password').addEventListener('input', checkCurrentPassword);
});
</script>
@endsection
