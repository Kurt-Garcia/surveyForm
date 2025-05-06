@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <!-- Action Buttons Section -->
    <div class="mb-3 d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2 gap-sm-0">
        <div class="d-flex flex-column flex-sm-row gap-2 mb-2 mb-sm-0">
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-2"></i>Print
            </button>
            <button onclick="generatePDF()" class="btn btn-outline-secondary">
                <i class="bi bi-file-pdf me-2"></i>Download PDF
            </button>
        </div>
        <a href="{{ route('admin.surveys.responses.index', $survey) }}" 
            class="btn btn-sm" 
            style="border-color: var(--primary-color); color: var(--primary-color)"
            onmouseover="this.style.backgroundColor='var(--secondary-color)'; this.style.color='white'"
            onmouseout="this.style.borderColor='var(--primary-color)'; this.style.backgroundColor='white'; this.style.color='var(--primary-color)'">
            <i class="fas fa-arrow-left me-2"></i>Back to Responses
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-start">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h4 class="mb-0 fw-bold" style="color: var(--text-color)">{{ strtoupper($survey->title) }} - RESPONSE DETAILS</h4>
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
                                <p>{{ $header->account_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted mb-1">Account Type</label>
                                <p>{{ $header->account_type }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted mb-1">Date</label>
                                <p>{{ $header->date->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Time Information -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted mb-1">Start Time</label>
                                <p>{{ $header->start_time ? $header->start_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted mb-1">End Time</label>
                                <p>{{ $header->end_time ? $header->end_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted mb-1">Duration</label>
                                <p>
                                    @if($header->start_time && $header->end_time)
                                        {{ $header->end_time->diffForHumans($header->start_time, ['parts' => 2]) }}
                                    @else
                                        N/A
                                    @endif
                                </p>
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
                                    @if($response->question->type === 'radio')
                                        <div class="d-flex align-items-center mt-2">
                                            <div class="radio-display">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" disabled {{ $i == $response->response ? 'checked' : '' }}>
                                                        <label class="form-check-label">{{ $i }}</label>
                                                    </div>
                                                @endfor
                                            </div>
                                            <span class="ms-2 fw-bold">{{ $response->response }} / 5</span>
                                        </div>
                                    @elseif($response->question->type === 'star')
                                        <div class="d-flex align-items-center mt-2">
                                            <div class="rating-display">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi bi-star-fill fs-5 {{ $i <= $response->response ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="ms-2 fw-bold">{{ $response->response }} / 5</span>
                                        </div>
                                    @elseif($response->question->type === 'text' || $response->question->type === 'textarea')
                                        <p class="fw-bold mb-0">{{ $response->response }}</p>
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
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress" style="height: 11px; width: 170px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $header->recommendation * 10 }}%;" aria-valuenow="{{ $header->recommendation }}" aria-valuemin="0" aria-valuemax="10"></div>
                                    </div>
                                    <span class="fw-bold">{{ $header->recommendation }} / 10</span>
                                </div>
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
    border-left: 4px solid transparent;
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

/* Responsive styles for radio display */
@media (max-width: 576px) {
    .radio-display {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }
    
    .form-check-inline {
        margin-right: 5px;
        margin-bottom: 5px;
    }
    
    .d-flex.align-items-center.mt-2 {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .d-flex.align-items-center.mt-2 .ms-2 {
        margin-left: 0 !important;
        margin-top: 8px;
    }
}
</style>

<!-- Include html2pdf library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function getRandomColor() {
    // Generate pastel colors by limiting RGB values between 150 and 255
    const r = Math.floor(Math.random() * 105) + 150;
    const g = Math.floor(Math.random() * 105) + 150;
    const b = Math.floor(Math.random() * 105) + 150;
    return `rgb(${r}, ${g}, ${b})`;
}

function applyRandomColors() {
    const items = document.querySelectorAll('.response-item');
    items.forEach(item => {
        item.style.borderLeftColor = getRandomColor();
    });
}

document.addEventListener('DOMContentLoaded', applyRandomColors);

function generatePDF() {
    const element = document.querySelector('.card');
    const opt = {
        margin: [0.5, 0.5, 0.5, 0.5],
        filename: 'survey-response.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2,
            useCORS: true,
            logging: false
        },
        jsPDF: { 
            unit: 'in', 
            format: 'letter',
            orientation: 'portrait',
            compress: true
        },
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
    };

    // Add custom CSS for PDF generation
    const style = document.createElement('style');
    style.textContent = `
        .response-item {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        body {
            font-size: 10pt !important;
            line-height: 1.2 !important;
        }
        .card-body {
            padding: 0.25in !important;
        }
        form[action*="toggle-resubmission"] {
            display: none !important;
        }
    `;
    document.head.appendChild(style);

    html2pdf().set(opt).from(element).save().then(() => {
        document.head.removeChild(style);
    });
}
</script>

<!-- Print Styles -->
<style media="print">
@page {
    size: letter;
    margin: 0.5in;
}
body {
    margin: 0;
    padding: 0;
    font-size: 10pt; /* Slightly smaller font for better fit */
    line-height: 1.2;
}
.container {
    width: 100% !important;
    max-width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
}
.btn, .card-header form, .btn-close {
    display: none !important;
}
.card {
    border: none !important;
    box-shadow: none !important;
    margin: 0 !important;
}
.card-header {
    padding: 0.5rem 0 !important;
    border-bottom: 1px solid #ddd !important;
    margin-bottom: 0.5rem !important;
}
.card-body {
    padding: 0 !important;
}
.response-item {
    break-inside: avoid;
    page-break-inside: avoid; /* For older browsers */
    border: 1px solid #eee !important;
    margin-bottom: 0.5rem !important;
    padding: 0.5rem !important;
    background-color: #f9f9f9 !important;
}
h4 {
    font-size: 16pt !important;
    margin: 0.5rem 0 !important;
}
h5 {
    font-size: 12pt !important;
    margin: 0.25rem 0 !important;
}
.mb-4, .my-4 {
    margin-bottom: 0.5rem !important;
}
.mb-3, .my-3 {
    margin-bottom: 0.25rem !important;
}
.p-4, .py-4, .px-4 {
    padding: 0.5rem !important;
}
.p-3, .py-3, .px-3 {
    padding: 0.25rem !important;
}
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 !important;
}
.col-md-4 {
    width: 33% !important;
    padding: 0 0.25rem !important;
}
hr {
    margin: 0.5rem 0 !important;
    border-color: #ddd !important;
}
.alert {
    display: none !important;
}
.bg-light {
    background-color: #f9f9f9 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}
.text-warning {
    color: #ffc107 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}
}
</style>
@endsection