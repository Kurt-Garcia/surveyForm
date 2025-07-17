@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold mb-0">Add Translation</h2>
                <a href="{{ route('admin.translations.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Translations
                </a>
            </div>

            <!-- Main Content Card -->
            <div class="card shadow-sm">
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

                    <form method="POST" action="{{ route('admin.translations.store') }}">
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

                            <div class="col-md-6">
                                <label for="locale" class="form-label">Language <span class="text-danger">*</span></label>
                                <select name="locale" id="locale" class="form-select @error('locale') is-invalid @enderror" required>
                                    <option value="">Select Language</option>
                                    @foreach($locales as $localeCode => $localeName)
                                        <option value="{{ $localeCode }}" {{ old('locale') == $localeCode ? 'selected' : '' }}>
                                            {{ $localeName }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('locale')
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
                                <i class="bi bi-check-circle me-2"></i>Create Translation
                            </button>
                            <a href="{{ route('admin.translations.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
