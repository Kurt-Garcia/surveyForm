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
        <a href="{{ route('admin.surveys.responses.index', $survey) }}" class="btn btn-outline-primary align-self-start align-self-sm-center">
            <i class="bi bi-arrow-left me-2"></i>Back to Responses
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
                    <h4 class="mb-0 fw-bold">{{ strtoupper($survey->title) }} - RESPONSE DETAILS</h4>
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
                                <h5>{{ $header->start_time ? $header->start_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}</h5>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="text-muted mb-1">End Time</label>
                                <h5>{{ $header->end_time ? $header->end_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}</h5>
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
function generatePDF() {
    // Get survey title and account name for filename
    const surveyTitle = "{{ $survey->title }}".replace(/[^a-z0-9]/gi, '_');
    const accountName = "{{ $header->account_name }}".replace(/[^a-z0-9]/gi, '_');
    
    // Show loading toast
    const loadingToast = document.createElement('div');
    loadingToast.id = 'pdfLoadingToast';
    loadingToast.style.position = 'fixed';
    loadingToast.style.top = '20px';
    loadingToast.style.right = '20px';
    loadingToast.style.zIndex = '9999';
    loadingToast.innerHTML = `
        <div class="toast show align-items-center text-white bg-primary border-0" role="alert">
            <div class="d-flex align-items-center p-2">
                <i class="bi bi-arrow-repeat spin me-2"></i>
                <span>Creating your PDF file. Please wait...</span>
            </div>
        </div>
    `;
    document.body.appendChild(loadingToast);

    // Prepare PDF content
    const pdfContainer = document.createElement('div');
    pdfContainer.style.width = '800px'; // Reduced width for more compact output
    pdfContainer.style.maxWidth = '100%';
    pdfContainer.style.backgroundColor = 'white';
    pdfContainer.style.fontFamily = 'Arial, sans-serif';
    pdfContainer.style.color = '#222';
    pdfContainer.style.fontSize = '8pt'; // Smaller base font size
    pdfContainer.style.lineHeight = '1.1'; // Tighter line height

    // Clone the print header with logo for PDF
    const logoHeader = document.querySelector('.print-only-header').cloneNode(true);
    logoHeader.classList.remove('d-none');
    logoHeader.style.display = 'block';
    logoHeader.style.marginBottom = '5px';
    
    const logoImage = logoHeader.querySelector('img.print-logo');
    if (logoImage) {
        logoImage.style.maxWidth = '140px'; // Even smaller logo for PDF
        logoImage.style.height = 'auto';
        logoImage.style.margin = '0 auto';
        logoImage.style.display = 'block';
    }

    // Clone the main card content
    const contentClone = document.querySelector('.card.shadow-sm.border-0').cloneNode(true);
    // Remove buttons and unnecessary elements from the clone
    const buttonsToRemove = contentClone.querySelectorAll('.btn, form[action*="toggle-resubmission"]');
    buttonsToRemove.forEach(btn => {
        if (btn && btn.parentNode) {
            btn.parentNode.removeChild(btn);
        }
    });
    
    // Make the PDF content more compact
    const cardHeader = contentClone.querySelector('.card-header');
    if (cardHeader) {
        cardHeader.style.padding = '0.25rem';
        const headerTitle = cardHeader.querySelector('h4');
        if (headerTitle) {
            headerTitle.style.fontSize = '11pt';
            headerTitle.style.margin = '0';
        }
    }
    
    const cardBody = contentClone.querySelector('.card-body');
    if (cardBody) {
        cardBody.style.padding = '0.25rem';
    }
    
    // Make response items more compact
    const responseItems = contentClone.querySelectorAll('.response-item');
    responseItems.forEach(item => {
        item.style.padding = '0.25rem';
        item.style.marginBottom = '0.25rem';
        item.style.border = '0.5px solid #eee';
        
        // Adjust question and response text
        const questionText = item.querySelector('h5');
        if (questionText) {
            questionText.style.fontSize = '9pt';
            questionText.style.margin = '0';
        }
        
        const labels = item.querySelectorAll('label');
        labels.forEach(label => {
            label.style.fontSize = '7pt';
            label.style.marginBottom = '0';
        });
    });

    pdfContainer.appendChild(logoHeader);
    pdfContainer.appendChild(contentClone);

    // Add page-break CSS for html2pdf
    const style = document.createElement('style');
    style.innerHTML = `
        .response-item { page-break-inside: avoid; }
        h1, h2, h3 { font-size: 12pt !important; margin: 0.25rem 0 !important; }
        h4 { font-size: 11pt !important; margin: 0.25rem 0 !important; }
        h5 { font-size: 9pt !important; margin: 0.15rem 0 !important; }
        p, span, div { font-size: 8pt !important; margin-bottom: 0.15rem !important; }
        .small, small, label { font-size: 7pt !important; margin-bottom: 0 !important; }
        .mb-4, .my-4 { margin-bottom: 0.25rem !important; }
        .mb-3, .my-3 { margin-bottom: 0.15rem !important; }
        .p-4, .py-4, .px-4 { padding: 0.25rem !important; }
        .p-3, .py-3, .px-3 { padding: 0.15rem !important; }
        .card-header { page-break-after: avoid; }
        .card-body { page-break-inside: auto; }
        .responses-list { page-break-inside: auto; }
        @media print {
            .response-item { page-break-inside: avoid; }
        }
    `;
    pdfContainer.appendChild(style);

    document.body.appendChild(pdfContainer);

    // Use html2pdf to generate the PDF with proper page breaks
    const opt = {
        margin: 0.15, // Reduced margins
        filename: `${surveyTitle}_Response_${accountName}.pdf`,
        image: { type: 'jpeg', quality: 0.95 },
        html2canvas: { scale: 2, useCORS: true },
        jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' },
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
    };
    html2pdf().set(opt).from(pdfContainer).save().then(() => {
        document.body.removeChild(pdfContainer);
        if (document.getElementById('pdfLoadingToast')) {
            document.getElementById('pdfLoadingToast').remove();
        }
    });
}
</script>

<!-- Print Styles -->
<style media="print">
@page {
    size: auto;
    margin: 3mm 5mm 5mm 5mm; /* Reduced margins */
}
body {
    margin: 0;
    padding: 0;
    font-size: 8pt; /* Smaller base font size */
    line-height: 1.1; /* Tighter line height */
    color: #000;
    background-color: #fff;
}
.container-fluid {
    width: 100% !important;
    max-width: 100% !important;
    padding: 0 !important;
    margin: 0 !important;
}
.btn, 
.card-header form, 
.btn-close,
.mb-3.d-flex, /* Action Buttons Section */
form[action*="toggle-resubmission"] /* Allow Resubmission Section */ {
    display: none !important;
}

.card-header {
    padding: 0.25rem 0 !important;
    border-bottom: 0.5px solid #ddd !important;
    margin-bottom: 0.25rem !important;
    background-color: #f8f9fa !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}
.card-body {
    padding: 0 !important;
}
/* Font size adjustments */
h1, h2, h3 {
    font-size: 12pt !important;
    margin: 0.25rem 0 !important;
}
h4 {
    font-size: 11pt !important;
    margin: 0.25rem 0 !important;
}
h5 {
    font-size: 10pt !important;
    margin: 0.15rem 0 !important;
}
p, span, div {
    font-size: 8pt !important;
    margin-bottom: 0.15rem !important;
}
.text-muted, small, label {
    font-size: 7pt !important;
    margin-bottom: 0 !important;
}
/* Spacing adjustments */
.mb-4, .my-4 {
    margin-bottom: 0.25rem !important;
}
.mb-3, .my-3 {
    margin-bottom: 0.15rem !important;
}
.p-4, .py-4, .px-4 {
    padding: 0.25rem !important;
}
.p-3, .py-3, .px-3 {
    padding: 0.15rem !important;
}
/* Layout adjustments */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 !important;
    gap: 0 !important;
}
.col-md-4 {
    width: 33% !important;
    padding: 0 0.15rem !important;
}
.response-item {
    page-break-inside: avoid !important;
    break-inside: avoid !important;
    border: 0.5px solid #eee !important;
    margin-bottom: 0.25rem !important;
    padding: 0.25rem !important;
    background-color: #f9f9f9 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}
hr {
    margin: 0.25rem 0 !important;
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
    margin-bottom: 5px !important;
}
.print-logo {
    max-width: 150px !important;
    height: auto;
    margin: 0 auto;
    display: block;
}
/* Make sure the navbar is hidden during print */
nav, header, footer {
    display: none !important;
}
/* Responsive styles for radio display */
.radio-display {
    display: flex;
    flex-wrap: wrap;
}
.form-check-inline {
    margin-right: 3px !important;
}
.rating-display .bi-star-fill {
    font-size: 8pt !important;
}
</style>
@endsection