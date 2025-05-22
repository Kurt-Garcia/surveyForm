@extends('layouts.app-welcome')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: "btn btn-success me-3",
        cancelButton: "btn btn-outline-danger",
        actions: 'gap-2 justify-content-center'
    },
    buttonsStyling: false
});

function confirmClose(event) {
    event.preventDefault();
    swalWithBootstrapButtons.fire({
        title: "Are you sure?",
        text: "Do you want to leave this page?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, leave page!",
        cancelButtonText: "No, stay here!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "{{ route('index') }}";
        }
    });
}

function confirmPasswordChange(event) {
    event.preventDefault();
    swalWithBootstrapButtons.fire({
        title: "Confirm Password Change",
        text: "Are you sure you want to change your password?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, change it!",
        cancelButtonText: "No, cancel!",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            event.target.closest('form').submit();
        }
    });
}
</script>
<div class="container mt-5 mb-5">
    <div class="row justify-content-center align-items-center min-vh-75">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header text-white text-center py-3 position-relative">
                    <h3 class="mb-0 text-white">{{ __('Change Password') }}</h3>
                    <a href="{{ route('index') }}" onclick="confirmClose(event)" class="position-absolute top-0 end-0 p-3 text-white">
                        <i class="bi bi-x fs-4"></i>
                    </a>
                </div>

                <div class="card-body p-5">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.change') }}" onsubmit="confirmPasswordChange(event)">
                        @csrf

                        <div class="mb-4">
                            <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input id="current_password" type="password" 
                                    class="form-control @error('current_password') is-invalid @enderror" 
                                    name="current_password" required autocomplete="current-password">
                            </div>
                            @error('current_password')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">{{ __('New Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input id="password" type="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    name="password" required autocomplete="new-password">
                            </div>
                            @error('password')
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password-confirm" class="form-label">{{ __('Confirm New Password') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input id="password-confirm" type="password" class="form-control"
                                    name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                {{ __('Change Password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.9);
    }
    .card-header {
        background-color: var(--primary-color) !important;
    }
    .input-group-text {
        background-color: transparent;
        border-right: none;
    }
    .input-group input {
        border-left: none;
    }
    .input-group input:focus {
        box-shadow: none;
        border-color: var(--accent-color);
    }
</style>
@endsection