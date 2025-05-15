@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <!-- Print-only logo header -->
    <div class="print-only-header d-none">
        <div class="text-center mb-4">
            @if($survey->logo)
                <img src="{{ asset('storage/' . $survey->logo) }}" alt="{{ $survey->title }} Logo" class="print-logo">
            @else
                <img src="{{ asset('img/logo.png') }}" alt="Default Logo" class="print-logo">
            @endif
        </div>
    </div>
    
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
    border-left: 4px solid var(--primary-color);
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

<!-- Include html2canvas and jsPDF libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
function generatePDF() {
    // Show loading indicator
    const loadingToast = document.createElement('div');
    loadingToast.className = 'position-fixed bottom-0 end-0 p-3';
    loadingToast.style.zIndex = '5000';
    loadingToast.innerHTML = `
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Processing PDF</strong>
            </div>
            <div class="toast-body">
                <div class="d-flex align-items-center">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    <span>Creating your PDF file. Please wait...</span>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(loadingToast);

    // Create a temporary container for PDF content including the logo
    const pdfContainer = document.createElement('div');
    pdfContainer.style.width = '800px';
    pdfContainer.style.padding = '20px';
    pdfContainer.style.backgroundColor = 'white';
    
    // Clone the print header with logo for PDF
    const logoHeader = document.querySelector('.print-only-header').cloneNode(true);
    logoHeader.classList.remove('d-none');
    logoHeader.style.display = 'block';
    logoHeader.style.marginBottom = '20px';
    
    // Make the logo smaller in the PDF
    const logoImage = logoHeader.querySelector('img.print-logo');
    if (logoImage) {
        logoImage.style.maxWidth = '200px';
        logoImage.style.height = 'auto';
        logoImage.style.margin = '0 auto';
        logoImage.style.display = 'block';
    }
    
    // Clone the content
    const contentClone = document.querySelector('.card.shadow-sm.border-0').cloneNode(true);
    
    // Remove buttons and unnecessary elements from the clone
    const buttonsToRemove = contentClone.querySelectorAll('.btn, form[action*="toggle-resubmission"]');
    buttonsToRemove.forEach(btn => {
        if (btn && btn.parentNode) {
            btn.parentNode.removeChild(btn);
        }
    });
    
    // Append elements to the PDF container
    pdfContainer.appendChild(logoHeader);
    pdfContainer.appendChild(contentClone);
    
    // Temporarily add to document body but hidden
    pdfContainer.style.position = 'absolute';
    pdfContainer.style.left = '-9999px';
    document.body.appendChild(pdfContainer);
    
    // Use html2canvas to convert the container to an image
    window.html2canvas(pdfContainer, {
        scale: 1.5, // Higher quality rendering
        useCORS: true,
        allowTaint: true,
        scrollY: -window.scrollY // Fix positioning issues
    }).then(canvas => {
        // Clean up - remove temporary container
        document.body.removeChild(pdfContainer);
        
        // Create PDF with proper dimensions
        const imgData = canvas.toDataURL('image/png');
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            orientation: 'portrait',
            unit: 'mm',
            format: 'a4'
        });
        
        // Calculate dimensions to fit the image perfectly on the page
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = pdf.internal.pageSize.getHeight();
        const imgWidth = canvas.width;
        const imgHeight = canvas.height;
        
        // Calculate scaling ratio with adjusted margins
        const marginSide = 10; // Side margins
        const marginTop = 10;   // Top margin
        const marginBottom = 10; // Bottom margin
        const availableWidth = pdfWidth - (marginSide * 2);
        const availableHeight = pdfHeight - (marginTop + marginBottom);
        const ratio = Math.min(availableWidth / imgWidth, availableHeight / imgHeight);
        
        // Calculate centered position
        const imgX = marginSide + (availableWidth - (imgWidth * ratio)) / 2;
        const imgY = marginTop;
        
        // Add image to PDF
        pdf.addImage(imgData, 'PNG', imgX, imgY, imgWidth * ratio, imgHeight * ratio);
        
        // Save PDF
        pdf.save(`${document.title || 'survey-response'}-details.pdf`);
        
        // Remove loading indicator
        document.body.removeChild(loadingToast);
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
.container-fluid {
    width: 100% !important;
    max-width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
}
.btn, .card-header form, .btn-close, .mb-3.d-flex {
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

.print-only-header {
    display: block !important;
}

.print-logo {
    max-width: 200px;
    height: auto;
    margin: 0 auto 15px;
    display: block;
}

/* Make sure the navbar is hidden during print */
nav.navbar, nav.navbar * {
    display: none !important;
}
}
</style>
@endsection