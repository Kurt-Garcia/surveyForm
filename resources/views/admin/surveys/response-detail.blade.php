@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Action Buttons Section -->
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary me-2">
                <i class="bi bi-printer me-2"></i>Print
            </button>
            <button onclick="generatePDF()" class="btn btn-outline-secondary">
                <i class="bi bi-file-pdf me-2"></i>Download PDF
            </button>
        </div>
        <a href="{{ route('admin.surveys.responses.index', $survey) }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i>Back to Responses
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h4 class="mb-0 fw-bold">{{ $survey->title }} - Response Details</h4>
                    <form action="{{ route('admin.surveys.responses.toggle-resubmission', ['survey' => $survey, 'account_name' => $header->account_name]) }}" 
                          method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn {{ $header->allow_resubmit ? 'btn-warning' : 'btn-success' }} btn-sm">
                            <i class="bi {{ $header->allow_resubmit ? 'bi-lock' : 'bi-unlock' }} me-2"></i>
                            {{ $header->allow_resubmit ? 'Disable Resubmission' : 'Allow Resubmission' }}
                        </button>
                    </form>
                </div>

                <div class="card-body p-4">
                    <!-- Response Meta Information -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted mb-1">Account Name</label>
                                <h5>{{ $header->account_name }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted mb-1">Account Type</label>
                                <h5>{{ $header->account_type }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted mb-1">Date</label>
                                <h5>{{ $header->date->format('M d, Y') }}</h5>
                            </div>
                        </div>
                    </div>

                    <!-- Time Information -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted mb-1">Start Time</label>
                                <h5>{{ $header->start_time ? $header->start_time->format('h:i:s A') : 'N/A' }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted mb-1">End Time</label>
                                <h5>{{ $header->end_time ? $header->end_time->format('h:i:s A') : 'N/A' }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted mb-1">Duration</label>
                                <h5>
                                    @if($header->start_time && $header->end_time)
                                        {{ $header->end_time->diffForHumans($header->start_time, ['parts' => 2]) }}
                                    @else
                                        N/A
                                    @endif
                                </h5>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Question Responses -->
                    <div class="responses-list">
                        @foreach($responses as $response)
                            <div class="response-item mb-4 p-3 bg-light rounded">
                                <div class="mb-2">
                                    <label class="text-muted">Question</label>
                                    <h5 class="fw-bold">{{ $response->question->text }}</h5>
                                </div>
                                <div>
                                    <label class="text-muted">Response</label>
                                    @if($response->question->type === 'radio' || $response->question->type === 'star')
                                        <div class="d-flex align-items-center mt-2">
                                            <div class="rating-display">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi bi-star-fill fs-5 {{ $i <= $response->response ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="ms-2 fw-bold">{{ $response->response }} / 5</span>
                                        </div>
                                    @else
                                        <p class="fw-bold mb-0">{{ $response->response }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <!-- Recommendation Score -->
                        <div class="response-item mb-4 p-3 bg-light rounded">
                            <div class="mb-2">
                                <label class="text-muted">Recommendation Score</label>
                                <h5 class="fw-bold">{{ $header->recommendation }} / 10</h5>
                            </div>
                        </div>

                        <!-- Comments -->
                        <div class="response-item mb-4 p-3 bg-light rounded">
                            <div class="mb-2">
                                <label class="text-muted">Additional Comments</label>
                                <p class="fw-bold mb-0">{{ $header->comments }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.response-item {
    transition: transform 0.2s;
    border: 1px solid rgba(0,0,0,0.05);
}

.response-item:hover {
    transform: translateY(-2px);
}

.rating-display {
    line-height: 1;
}

.text-warning {
    color: #ffc107 !important;
}
</style>

<!-- Include html2pdf library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function generatePDF() {
    const element = document.querySelector('.card');
    const opt = {
        margin: 1,
        filename: 'survey-response.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save();
}
</script>

<!-- Print Styles -->
<style media="print">
@page {
    size: auto;
    margin: 10mm;
}
.btn, .card-header a {
    display: none !important;
}
.card {
    border: none !important;
    box-shadow: none !important;
}
.card-body {
    padding: 10px !important;
}
.response-item {
    break-inside: avoid;
    border: 1px solid #ddd !important;
    margin-bottom: 10px !important;
    padding: 10px !important;
}
body {
    padding: 0;
    margin: 0;
    font-size: 12px;
}
.container {
    max-width: 100% !important;
    padding: 0 !important;
}
h2, h4, h5 {
    margin: 5px 0 !important;
    font-size: 1.1em !important;
}
.mb-4 {
    margin-bottom: 10px !important;
}
.p-4 {
    padding: 10px !important;
}
.py-4 {
    padding-top: 10px !important;
    padding-bottom: 10px !important;
}
</style>
@endsection