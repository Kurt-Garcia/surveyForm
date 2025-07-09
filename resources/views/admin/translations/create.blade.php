<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Translation - Developer Portal</title>
    
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
    position: relative;
}

.bg-particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 1;
    pointer-events: none;
}

.particle {
    position: absolute;
    width: 2px;
    height: 2px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); opacity: 1; }
    50% { transform: translateY(-20px) rotate(180deg); opacity: 0.5; }
}

.dev-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 10;
}

.dev-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.form-control, .form-select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: white;
    backdrop-filter: blur(10px);
}

.form-control:focus, .form-select:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    color: white;
}

.form-control::placeholder {
    color: rgba(255, 255, 255, 0.6);
}

.form-select option {
    background: #1a1a2e;
    color: white;
}

.form-text {
    color: rgba(255, 255, 255, 0.7);
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
}

.btn-outline-secondary {
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    background: transparent;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    transform: translateY(-2px);
}

.alert-danger {
    background: rgba(220, 53, 69, 0.2);
    border: 1px solid rgba(220, 53, 69, 0.4);
    color: #ff6b7d;
    backdrop-filter: blur(10px);
}

.form-label {
    color: white;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.text-danger {
    color: #ff6b7d !important;
}

.invalid-feedback {
    color: #ff6b7d;
}

.is-invalid {
    border-color: #dc3545;
}

.header-section {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
}

.header-section h1 {
    background: linear-gradient(135deg, #00d4ff, #007bff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.header-section p {
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 0;
}
</style>
</head>
<body>

<div class="bg-particles" id="particles"></div>

<div class="developer-dashboard">
    <div class="container-fluid px-4 py-5" style="position: relative; z-index: 10;">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="header-section">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="display-6 fw-bold mb-2">Add Translation</h1>
                            <p class="text-muted">Create a new translation entry</p>
                        </div>
                        <a href="{{ route('developer.translations.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Translations
                        </a>
                    </div>
                </div>

                <div class="dev-card">
                    <div class="card-body p-4">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('developer.translations.store') }}">
                            @csrf
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="key" class="form-label">Translation Key <span class="text-danger">*</span></label>
                                    <input type="text" name="key" id="key" class="form-control @error('key') is-invalid @enderror" 
                                           value="{{ old('key') }}" required>
                                    <div class="form-text">Use dot notation for nested keys (e.g., survey.account_name)</div>
                                    @error('key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label for="locale" class="form-label">Language <span class="text-danger">*</span></label>
                                    <select name="locale" id="locale" class="form-select @error('locale') is-invalid @enderror" required>
                                        <option value="">Select Language</option>
                                        @foreach($locales as $localeOption)
                                            <option value="{{ $localeOption }}" {{ old('locale') == $localeOption ? 'selected' : '' }}>
                                                {{ strtoupper($localeOption) }}
                                            </option>
                                        @endforeach
                                        <option value="en" {{ old('locale') == 'en' ? 'selected' : '' }}>EN</option>
                                        <option value="tl" {{ old('locale') == 'tl' ? 'selected' : '' }}>TL</option>
                                        <option value="ceb" {{ old('locale') == 'ceb' ? 'selected' : '' }}>CEB</option>
                                    </select>
                                    @error('locale')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label for="group" class="form-label">Group</label>
                                    <select name="group" id="group" class="form-select @error('group') is-invalid @enderror">
                                        <option value="">Select Group</option>
                                        @foreach($groups as $groupOption)
                                            <option value="{{ $groupOption }}" {{ old('group') == $groupOption ? 'selected' : '' }}>
                                                {{ $groupOption }}
                                            </option>
                                        @endforeach
                                        <option value="survey" {{ old('group') == 'survey' ? 'selected' : '' }}>survey</option>
                                        <option value="auth" {{ old('group') == 'auth' ? 'selected' : '' }}>auth</option>
                                        <option value="validation" {{ old('group') == 'validation' ? 'selected' : '' }}>validation</option>
                                    </select>
                                    <div class="form-text">Optional grouping for organization</div>
                                    @error('group')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="value" class="form-label">Translation Value <span class="text-danger">*</span></label>
                                    <textarea name="value" id="value" class="form-control @error('value') is-invalid @enderror" 
                                              rows="4" required>{{ old('value') }}</textarea>
                                    <div class="form-text">The translated text for this key</div>
                                    @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Create Translation
                                </button>
                                <a href="{{ route('developer.translations.index') }}" class="btn btn-outline-secondary">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Create floating particles
function createParticles() {
    const particles = document.getElementById('particles');
    const particleCount = 50;
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 6 + 's';
        particle.style.animationDuration = (Math.random() * 3 + 3) + 's';
        particles.appendChild(particle);
    }
}

document.addEventListener('DOMContentLoaded', createParticles);
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
