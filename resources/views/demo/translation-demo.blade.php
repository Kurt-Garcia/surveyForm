@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Database-Driven Translation Demo</h3>
                </div>
                <div class="card-body">
                    <h4>Original File-Based Translations:</h4>
                    <ul>
                        <li>Account Name: {{ __db('account_name') }}</li>
                        <li>Account Type: {{ __db('account_type') }}</li>
                        <li>Date: {{ __db('date') }}</li>
                        <li>Required: {{ __db('required') }}</li>
                        <li>Optional: {{ __db('optional') }}</li>
                    </ul>

                    <h4>New Database-Driven Translations:</h4>
                    <ul>
                        <li>Account Name: {{ __db('account_name') }}</li>
                        <li>Account Type: {{ __db('account_type') }}</li>
                        <li>Date: {{ __db('date') }}</li>
                        <li>Required: {{ __db('required') }}</li>
                        <li>Optional: {{ __db('optional') }}</li>
                    </ul>

                    <h4>Language Switching:</h4>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="switchLanguage('en')">
                            English
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="switchLanguage('tl')">
                            Filipino
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="switchLanguage('ceb')">
                            Cebuano
                        </button>
                    </div>

                    <h4>Available Locales:</h4>
                    <ul>
                        @foreach(get_available_locales() as $locale)
                            <li><code>{{ $locale }}</code></li>
                        @endforeach
                    </ul>

                    <h4>Check Translation Exists:</h4>
                    <p>Translation 'account_name' exists: {{ has_translation('account_name') ? 'Yes' : 'No' }}</p>
                    <p>Translation 'non_existent' exists: {{ has_translation('non_existent') ? 'Yes' : 'No' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function switchLanguage(locale) {
    // You would normally make an AJAX call to update the session locale
    // For demo purposes, we'll just show an alert
    alert('Language switched to: ' + locale);
    // In a real implementation:
    // window.location.href = '/set-language?locale=' + locale;
}
</script>
@endsection
