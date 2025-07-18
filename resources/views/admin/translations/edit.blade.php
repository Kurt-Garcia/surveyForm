@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Translation</h5>
                    <a href="{{ route('admin.translations.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Translations
                    </a>
                </div>

                <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.translations.update', $translation) }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="key" class="form-label">Translation Key <span class="text-danger">*</span></label>
                                    <input type="text" name="key" id="key" class="form-control @error('key') is-invalid @enderror" 
                                           value="{{ old('key', $translation->key) }}" required>
                                    <small class="form-text text-muted">Use dot notation for nested keys (e.g., survey.account_name)</small>
                                    @error('key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="locale" class="form-label">Language <span class="text-danger">*</span></label>
                                    <select name="locale" id="locale" class="form-select @error('locale') is-invalid @enderror" required>
                                        <option value="">Select Language</option>
                                        @foreach($locales as $localeCode => $localeName)
                                            <option value="{{ $localeCode }}" {{ old('locale', $translation->translationHeader->locale) == $localeCode ? 'selected' : '' }}>
                                                {{ $localeName }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('locale')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="value" class="form-label">Translation Value <span class="text-danger">*</span></label>
                                    <textarea name="value" id="value" class="form-control @error('value') is-invalid @enderror" 
                                              rows="4" required>{{ old('value', $translation->value) }}</textarea>
                                    <small class="form-text text-muted">The translated text for this key</small>
                                    @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-circle"></i> Update Translation
                                    </button>
                                    <a href="{{ route('admin.translations.index') }}" class="btn btn-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
