@extends('layouts.app-user')

@section('content')
<div class="container mt-5">
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
            <button onclick="printWithPrintJS()" class="btn btn-outline-secondary">
                <i class="fas fa-print me-2"></i>Print
            </button>
            <button onclick="generatePDF()" class="btn btn-outline-secondary">
                <i class="fas fa-file-pdf me-2"></i>Download PDF
            </button>
        </div>
        <a href="{{ route('surveys.responses.index', $survey) }}" 
            class="btn btn-sm" 
            style="border-color: var(--primary-color); color: var(--primary-color)"
            onmouseover="this.style.backgroundColor='var(--secondary-color)'; this.style.color='white'"
            onmouseout="this.style.borderColor='var(--primary-color)'; this.style.backgroundColor='white'; this.style.color='var(--primary-color)'">
            <i class="fas fa-arrow-left me-2"></i>Back to Responses
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-start">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h4 class="mb-0 fw-bold" style="color: var(--text-color)">{{ strtoupper($survey->title) }} - RESPONSE DETAILS</h4>
                    <div class="d-flex align-items-center gap-2">
                        <form id="resubmissionForm" action="{{ route('surveys.responses.toggle-resubmission', ['survey' => $survey, 'account_name' => $response->account_name]) }}" 
                              method="POST" class="d-inline">
                        @if($response->allow_resubmit)
                            <div id="copyLinkSection" class="d-inline me-2">
                                <button type="button" id="copyLinkBtn" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-link me-2"></i>Copy Link for Customer
                                </button>
                                <span id="copySuccess" class="text-success ms-2 d-none"><i class="fas fa-check-circle"></i> Link copied!</span>
                            </div>
                        @endif
                            @csrf
                            @method('PATCH')
                            <button type="submit" id="resubmissionButton" class="btn {{ $response->allow_resubmit ? 'btn-warning' : 'btn-success' }} btn-sm">
                                <i class="fas {{ $response->allow_resubmit ? 'fa-lock' : 'fa-unlock' }} me-2" id="resubmissionIcon"></i>
                                <span id="resubmissionText">{{ $response->allow_resubmit ? 'Disable Resubmission' : 'Allow Resubmission' }}</span>
                            </button>
                        </form>
                    </div>

                    <!-- SweetAlert2 CSS -->
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
                    <!-- SweetAlert2 JS -->
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    
                    <script>
                    // SweetAlert2 configuration
                    const swalWithBootstrapButtons = Swal.mixin({
                        customClass: {
                            confirmButton: "btn btn-success me-3",
                            cancelButton: "btn btn-outline-danger",
                            actions: 'gap-2 justify-content-center'
                        },
                        buttonsStyling: false
                    });
                    
                    // Copy Link button functionality
                    function setupCopyLinkButton() {
                        const copyLinkBtn = document.getElementById('copyLinkBtn');
                        if (copyLinkBtn) {
                            copyLinkBtn.addEventListener('click', function() {
                                const accountName = encodeURIComponent('{{ $response->account_name }}');
                                const accountType = encodeURIComponent('{{ $response->account_type }}');
                                
                                // Create sharable URL with account details
                                const baseUrl = "{{ url('/survey/' . $survey->id) }}";
                                const shareableUrl = `${baseUrl}?account_name=${accountName}&account_type=${accountType}`;
                                
                                // Create a temporary element to copy the URL
                                const tempInput = document.createElement('input');
                                tempInput.value = shareableUrl;
                                document.body.appendChild(tempInput);
                                tempInput.select();
                                document.execCommand('copy');
                                document.body.removeChild(tempInput);
                                
                                // Show success message
                                const copySuccess = document.getElementById('copySuccess');
                                copySuccess.classList.remove('d-none');
                                setTimeout(() => {
                                    copySuccess.classList.add('d-none');
                                }, 3000);
                            });
                        }
                    }

                    // Initial setup of copy link button
                    setupCopyLinkButton();

                    // Function to confirm resubmission status change
                    function confirmResubmissionChange() {
                        const isCurrentlyAllowed = {{ $response->allow_resubmit ? 'true' : 'false' }};
                        const title = isCurrentlyAllowed ? "Disable Resubmission?" : "Allow Resubmission?";
                        const text = isCurrentlyAllowed 
                            ? "This will prevent the customer from submitting new responses. Are you sure?" 
                            : "This will allow the customer to submit new responses. Are you sure?";
                        const confirmButtonText = isCurrentlyAllowed ? "Yes, disable it" : "Yes, allow it";
                        
                        return swalWithBootstrapButtons.fire({
                            title: title,
                            text: text,
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: confirmButtonText,
                            cancelButtonText: "No, cancel",
                            reverseButtons: true
                        });
                    }

                    document.getElementById('resubmissionForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        confirmResubmissionChange().then((result) => {
                            if (result.isConfirmed) {
                                fetch(this.action, {
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({})
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        const button = document.getElementById('resubmissionButton');
                                        const icon = document.getElementById('resubmissionIcon');
                                        const text = document.getElementById('resubmissionText');
                                        const copyLinkSection = document.getElementById('copyLinkSection');
                                        
                                        if (data.allow_resubmission) {
                                            button.classList.remove('btn-success');
                                            button.classList.add('btn-warning');
                                            icon.classList.remove('fa-unlock');
                                            icon.classList.add('fa-lock');
                                            text.textContent = 'Disable Resubmission';
                                            
                                            // Create and show copy link section
                                            if (!copyLinkSection) {
                                                const newCopyLinkSection = document.createElement('div');
                                                newCopyLinkSection.id = 'copyLinkSection';
                                                newCopyLinkSection.className = 'd-inline me-2';
                                                newCopyLinkSection.innerHTML = `
                                                    <button type="button" id="copyLinkBtn" class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-link me-2"></i>Copy Link for Customer
                                                    </button>
                                                    <span id="copySuccess" class="text-success ms-2 d-none"><i class="fas fa-check-circle"></i> Link copied!</span>
                                                `;
                                                button.parentElement.insertBefore(newCopyLinkSection, button);
                                                setupCopyLinkButton();
                                            }
                                        } else {
                                            button.classList.remove('btn-warning');
                                            button.classList.add('btn-success');
                                            icon.classList.remove('fa-lock');
                                            icon.classList.add('fa-unlock');
                                            text.textContent = 'Allow Resubmission';
                                            
                                            // Remove copy link section if it exists
                                            if (copyLinkSection) {
                                                copyLinkSection.remove();
                                            }
                                        }
                                        
                                        // Show success message with SweetAlert2
                                        swalWithBootstrapButtons.fire({
                                            title: "Success!",
                                            text: data.message,
                                            icon: "success"
                                        });
                                        
                                        // No need for the traditional alert as we're using SweetAlert2
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                });
                            }
                        });
                    });
                    </script>
                </div>
                <div class="card-body p-4">
                    <div class="card user-info-card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                                            <label class="text-muted mb-1">Account Name</label>
                                            <p class="mb-0">{{ $response->account_name }}</p>
                                        </div>
                                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                                            <label class="text-muted mb-1">Account Type</label>
                                            <p class="mb-0"><span class="bg-light text-dark">{{ $response->account_type }}</span></p>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label class="text-muted mb-1">Date</label>
                                            <p class="mb-0">{{ $response->date->format('M d, Y') }}</p>
                                        </div>
                                    </div>

                                </br> 
                                    
                                    <!-- Time Information -->
                                    <div class="row">
                                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                                            <label class="text-muted mb-1">Start Time</label>
                                            <p class="mb-0">{{ $response->start_time ? $response->start_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}</p>
                                        </div>
                                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                                            <label class="text-muted mb-1">End Time</label>
                                            <p class="mb-0">{{ $response->end_time ? $response->end_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}</p>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label class="text-muted mb-1">Duration</label>
                                            <p class="mb-0">
                                                @if($response->start_time && $response->end_time)
                                                    {{ $response->end_time->diffForHumans($response->start_time, ['parts' => 2]) }}
                                                @else
                                                    N/A
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Question Responses -->
                    <div class="responses-list">
                        @foreach($response->details as $detail)
                            <div class="response-item mb-4 shadow-sm hover-lift">
                                <div class="p-4">
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">Question</label>
                                        <h5 class="fw-bold mb-0">{{ $detail->question->text }}</h5>
                                    </div>
                                    <div>
                                        <label class="text-muted small mb-1">Response</label>
                                        @if($detail->question->type === 'radio')
                                            <div class="d-flex align-items-center mt-2">
                                                <div class="radio-display">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" disabled {{ $i == $detail->response ? 'checked' : '' }}>
                                                            <label class="form-check-label">{{ $i }}</label>
                                                        </div>
                                                    @endfor
                                                </div>
                                                <span class="ms-2 fw-bold response-score">{{ $detail->response }} / 5</span>
                                            </div>
                                        @elseif($detail->question->type === 'star')
                                            <div class="d-flex align-items-center mt-2">
                                                <div class="rating-display">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star fs-5 {{ $i <= $detail->response ? 'text-warning' : 'text-muted' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="ms-2 fw-bold response-score">{{ $detail->response }} / 5</span>
                                            </div>
                                        @elseif($detail->question->type === 'text' || $detail->question->type === 'textarea')
                                            <p class="fw-bold mb-0 response-text">{{ $detail->response }}</p>
                                        @else
                                            <p class="fw-bold mb-0 response-text">{{ $detail->response }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Recommendation Score -->
                        <div class="response-item mb-4 shadow-sm hover-lift recommendation-item">
                            <div class="p-4">
                                <div class="mb-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <label class="text-muted small mb-1">Recommendation Score</label>
                                        <h5 class="fw-bold mb-0 d-flex align-items-center">
                                            <div class="recommendation-meter me-3">
                                                <div class="recommendation-fill" style="width: {{ ($response->recommendation / 10) * 100 }}%;"></div>
                                            </div>
                                            <span class="response-score">{{ $response->recommendation }} / 10</span>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comments -->
                        <div class="response-item mb-4 shadow-sm hover-lift">
                            <div class="p-4">
                                <div class="mb-2">
                                    <label class="text-muted small mb-1">Additional Comments</label>
                                    <p class="fw-bold mb-0 response-text">{{ $response->comments ?: 'No additional comments provided.' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Regular styles */
.custom-hover-btn:hover {
    background-color: var(--secondary-color);
    color: white;
}

.card {
    border-radius: 12px;
    border: none;
}

.card-header {
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.user-info-card {
    transition: all 0.3s ease;
}

.response-icon {
    color: #4ECDC4;
    margin-right: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.response-item {
    border-radius: 12px;
    border-left: 4px solid var(--primary-color);
    transition: all 0.3s ease;
    background-color: white;
}

.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.rating-display {
    line-height: 1;
}

.text-warning {
    color: #ffc107 !important;
}

.response-score {
    font-size: 1.1rem;
}

.recommendation-meter {
    height: 10px;
    background-color: #f1f1f1;
    border-radius: 5px;
    overflow: hidden;
    width: 150px;
}

.recommendation-fill {
    height: 100%;
    background-color: #4ECDC4;
    transition: width 0.5s ease;
}

.recommendation-badge .badge {
    font-size: 1.1rem;
    padding: 0.5rem 0.75rem;
    border-radius: 30px;
}

.response-text {
    line-height: 1.6;
}

/* Print styles */
@media print {
    @page {
        size: A4;
        margin: 1cm;
    }

    body {
        padding: 0 !important;
        margin: 0 !important;
    }

    .print-only-header {
        display: block !important;
        page-break-after: avoid;
    }

    .container {
        page-break-before: avoid;
        page-break-after: avoid;
    }

    .container {
        width: 100% !important;
        max-width: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .btn, .navbar, .no-print {
        display: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .print-only-header {
        display: block !important;
        margin-bottom: 20px;
    }

    .print-logo {
        max-width: 170px !important;
        max-height: 80px !important;
        height: auto;
        margin-bottom: 1px !important;
        margin: 0 auto;
        display: block;
    }

    .response-item {
        page-break-inside: avoid;
        border: 1px solid #ddd;
        margin-bottom: 15px;
    }

    .hover-lift {
        transform: none !important;
        box-shadow: none !important;
    }

    .recommendation-meter {
        border: 1px solid #ddd;
    }

    .text-warning {
        color: #000 !important;
    }
}

/* Responsive styles for radio buttons on mobile */
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
    
    /* Fix for resubmission and copy link buttons on mobile */
    .card-header .d-flex.align-items-center.gap-2 {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem !important;
    }
    
    #copyLinkSection {
        display: block !important;
        margin-bottom: 0.5rem;
        width: 100%;
    }
    
    #copyLinkBtn, #resubmissionButton {
        width: 100%;
    }
    
    #resubmissionForm {
        width: 100%;
    }
}

@page {
    size: letter;
    margin: 0in;
}

@media print {
    @page {
        size: auto;
        margin: 3mm 5mm 5mm 5mm; /* Further reduced margins */
    }
    
    body {
        margin: 0;
        padding: 0;
        font-size: 8pt; /* Smaller base font size */
        line-height: 1.1; /* Tighter line height */
        color: #000;
        background-color: #fff;
    }
    
    .container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .btn, 
    .hover-lift:hover,
    .mb-3.d-flex, /* Action Buttons Section */
    #resubmissionForm, /* Allow Resubmission Section */
    #copyLinkSection, /* Copy Link Section */
    #copyLinkBtn, /* Copy Link Button */
    #resubmissionButton, /* Resubmission Button */
    .fa-bars, /* Burger Icon (FontAwesome) */
    .fa-print, /* Print Icon */
    .fa-file-pdf, /* PDF Icon */
    .fa-arrow-left, /* Back Icon */
    .btn-close {
        display: none !important;
    }
    
    .card {
        border: 0.5px solid #ddd !important;
        box-shadow: none !important;
        margin: 0 !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
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
    
    .small, small, label {
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
    
    .alert {
        display: none !important;
    }
    
    .bg-light {
        background-color: #f9f9f9 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    .text-warning, .fa-star.text-warning {
        color: #ffc107 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    .recommendation-meter {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    .recommendation-fill {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .print-only-header {
        display: block !important;
        margin-bottom: 5px !important;
    }

    .print-logo {
        max-width: 100px !important;
        height: auto;
    }

    /* Make sure the navbar is hidden during print */
    nav.navbar, nav.navbar * {
        display: none !important;
    }
    
    /* Show the print-only header during printing */
    .print-only-header .text-center {
        margin-bottom: 0 !important;
    }

    .print-logo {
        max-width: 170px !important;
        height: auto;
        margin-bottom: 5px !important;
    }
    
    /* Reduce space at the top of the page for the logo */
    body {
        padding-top: 5px !important;
    }
}
</style>

<!-- Add Print.js, jsPDF, html2canvas, and html2pdf.js libraries -->
<script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://printjs-4de6.kxcdn.com/print.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<!-- Add FontAwesome for star icons in print -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script>
function printWithPrintJS() {
    // Create print content with proper structure for Print.js
    const printContent = createPrintContent();
    
    printJS({
        printable: printContent,
        type: 'raw-html',
        style: `
            @page {
                size: A4;
                margin: 5mm 15mm 20mm 15mm;
            }
            
            /* Print Header */
            .print-header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                height: 180px;
                background: white;
                border-bottom: 2px solid #ddd;
                padding: 5px 15px 10px 15px;
                z-index: 1000;
            }
            
            .print-logo {
                max-width: 120px;
                max-height: 50px;
                height: auto;
                display: block;
                margin: 0 auto 3px auto;
            }
            
            .survey-title {
                text-align: center;
                font-size: 12pt;
                font-weight: bold;
                margin: 5px 0 10px 0;
                color: #333;
            }
            
            .customer-info {
                font-size: 10pt;
                line-height: 1.3;
            }
            
            .customer-info .row {
                display: flex;
                margin: 0;
            }
            
            .customer-info .col {
                flex: 1;
                padding: 0 5px;
            }
            
            .customer-info label {
                font-weight: bold;
                color: #666;
                font-size: 9pt;
                margin-bottom: 2px;
                display: block;
            }
            
            .customer-info p {
                margin: 0 0 8px 0;
                font-size: 10pt;
            }

            
            /* Print Footer - Only on last page */
            .print-footer {
                background: white;
                border-top: 2px solid #ddd;
                padding: 15px;
                page-break-inside: avoid;
                margin-top: 30px;
            }
            
            /* Last page layout structure */
            .last-page-container {
                min-height: calc(100vh - 300px);
                max-height: calc(100vh - 300px);
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                overflow: visible;
            }
            
            .last-page-content {
                flex: 1;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                max-height: 100%;
            }
            
            .last-page-questions {
                flex: 1;
                margin-bottom: 20px;
                overflow: visible;
            }
            
            .last-page-footer {
                flex-shrink: 0;
                margin-top: 0;
                padding-top: 15px;
            }
            
            /* Compact question items on last page */
            .last-page-questions .question-item {
                margin-bottom: 12px !important;
                padding: 8px !important;
                min-height: 55px !important;
            }
            
            .last-page-questions .question-text {
                font-size: 10pt !important;
                line-height: 1.2 !important;
                margin-bottom: 6px !important;
            }
            
            .last-page-questions .question-label,
            .last-page-questions .response-label {
                font-size: 8pt !important;
                margin-bottom: 2px !important;
            }
            
            /* Compact footer */
            .print-footer {
                background: white;
                border-top: 2px solid #ddd;
                padding: 8px;
                page-break-inside: avoid;
                margin-top: 0;
            }
            
            .recommendation-section, .comments-section {
                margin-bottom: 8px;
            }
            
            .recommendation-section label, .comments-section label {
                font-size: 10pt !important;
                margin-bottom: 3px !important;
            }
            
            .comments-text {
                font-size: 10pt !important;
                line-height: 1.4 !important;
            }
            
            
            .recommendation-meter {
                height: 8px;
                background-color: #f1f1f1;
                border-radius: 4px;
                overflow: hidden;
                width: 120px;
                display: inline-block;
                margin-right: 10px;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .recommendation-fill {
                height: 100%;
                background-color: #4ECDC4;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .response-score {
                font-weight: bold;
                font-size: 11pt;
            }
            
            .comments-text {
                font-size: 11pt;
                line-height: 1.5;
                margin: 0;
            }
            
            /* Print Content */
            .print-content {
                margin-top: 210px;
                margin-bottom: 20px;
                padding: 0 15px;
            }
            
            .page-break {
                page-break-before: always;
                margin-top: 210px;
            }
            
            .question-item {
                margin-bottom: 20px;
                padding: 12px;
                border: 1px solid #eee;
                border-radius: 8px;
                background: #fafafa;
                page-break-inside: avoid;
                min-height: 70px;
            }
            
            .question-label {
                font-weight: bold;
                color: #666;
                font-size: 9pt;
                margin-bottom: 3px;
                display: block;
            }
            
            .question-text {
                font-weight: bold;
                font-size: 11pt;
                margin-bottom: 8px;
                line-height: 1.3;
            }
            
            .response-label {
                font-weight: bold;
                color: #666;
                font-size: 9pt;
                margin-bottom: 3px;
                display: block;
            }
            
            .radio-display, .rating-display {
                margin: 5px 0;
            }
            
            .form-check-inline {
                display: inline-block;
                margin-right: 15px;
            }
            
            .form-check-input {
                margin-right: 5px;
            }
            
            /* Star rating styles */
            .star-rating {
                font-size: 16px;
                margin-right: 2px;
                display: inline-block;
                font-family: serif;
            }
            
            /* FontAwesome star icon support */
            .fas {
                font-family: "Font Awesome 6 Free";
                font-weight: 900;
                font-style: normal;
                font-variant: normal;
                text-rendering: auto;
                line-height: 1;
            }
            
            .fa-star:before {
                content: "\\f005";
            }
            
            .fa-star {
                font-size: 16px;
                margin-right: 2px;
                display: inline-block;
            }
            
            .text-warning {
                color: #ffc107 !important;
            }
            
            .text-muted {
                color: #6c757d !important;
            }
            
            .response-text {
                font-size: 10pt;
                line-height: 1.4;
                margin: 0;
            }
            
            .survey-title {
                text-align: center;
                font-size: 14pt;
                font-weight: bold;
                margin-bottom: 20px;
                color: #333;
            }
        `,
        scanStyles: false
    });
}

function createPrintContent() {
    // Get survey data
    const surveyTitle = "{{ $survey->title }}";
    const accountName = "{{ $response->account_name }}";
    const accountType = "{{ $response->account_type }}";
    const responseDate = "{{ $response->date->format('M d, Y') }}";
    const startTime = "{{ $response->start_time ? $response->start_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}";
    const endTime = "{{ $response->end_time ? $response->end_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}";
    const duration = "@if($response->start_time && $response->end_time){{ $response->end_time->diffForHumans($response->start_time, ['parts' => 2]) }}@else N/A @endif";
    const recommendation = "{{ $response->recommendation }}";
    const comments = "{{ $response->comments ?: 'No additional comments provided.' }}";
    
    // Get logo
    const logoSrc = @if($survey->logo)"{{ asset('storage/' . $survey->logo) }}"@else"{{ asset('img/logo.png') }}"@endif;
    
    // Get questions
    const questions = [
        @foreach($response->details as $detail)
        {
            text: `{{ addslashes($detail->question->text) }}`,
            type: '{{ $detail->question->type }}',
            response: `{{ addslashes($detail->response) }}`
        },
        @endforeach
    ];
    
    let html = `
        <!-- Print Header -->
        <div class="print-header">
            <img src="${logoSrc}" alt="Logo" class="print-logo">
            <div class="survey-title">${surveyTitle.toUpperCase()} - RESPONSE DETAILS</div>
            <div class="customer-info">
                <div class="row">
                    <div class="col">
                        <label>Account Name</label>
                        <p>${accountName}</p>
                    </div>
                    <div class="col">
                        <label>Account Type</label>
                        <p>${accountType}</p>
                    </div>
                    <div class="col">
                        <label>Date</label>
                        <p>${responseDate}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label>Start Time</label>
                        <p>${startTime}</p>
                    </div>
                    <div class="col">
                        <label>End Time</label>
                        <p>${endTime}</p>
                    </div>
                    <div class="col">
                        <label>Duration</label>
                        <p>${duration}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Print Content -->
        <div class="print-content">
    `;
    
    // Add questions (5 per page)
    let pageCount = 0;
    let questionsOnCurrentPage = 0;
    const totalQuestions = questions.length;
    const questionsPerPage = 5;
    const totalPages = Math.ceil(totalQuestions / questionsPerPage);
    
    questions.forEach((question, index) => {
        if (index > 0 && index % questionsPerPage === 0) {
            html += '</div>'; // Close current page content
            pageCount++;
            questionsOnCurrentPage = 0;
            html += '<div class="page-break"></div>';
            html += '<div class="print-content">';
        }
        
        questionsOnCurrentPage++;
        const isLastQuestion = index === totalQuestions - 1;
        const isStartOfLastPage = Math.floor(index / questionsPerPage) === totalPages - 1 && index % questionsPerPage === 0;
        
        // Start last page container if this is the beginning of the last page
        if (isStartOfLastPage) {
            html += '<div class="last-page-container"><div class="last-page-content"><div class="last-page-questions">';
        }
        
        html += `
            <div class="question-item">
                <label class="question-label">Question</label>
                <div class="question-text">${question.text}</div>
                <label class="response-label">Response</label>
        `;
        
        if (question.type === 'radio') {
            html += '<div class="radio-display">';
            for (let i = 1; i <= 5; i++) {
                const checked = i == question.response ? 'checked' : '';
                html += `
                    <div class="form-check-inline">
                        <input class="form-check-input" type="radio" ${checked} disabled>
                        <label class="form-check-label">${i}</label>
                    </div>
                `;
            }
            html += `</div><span class="response-score">${question.response} / 5</span>`;
        } else if (question.type === 'star') {
            html += '<div class="rating-display">';
            for (let i = 1; i <= 5; i++) {
                const starClass = i <= question.response ? 'text-warning' : 'text-muted';
                // Use Unicode star character as fallback for better print compatibility
                html += `<span class="star-rating ${starClass}">★</span>`;
            }
            html += `</div><span class="response-score">${question.response} / 5</span>`;
        } else {
            html += `<p class="response-text">${question.response}</p>`;
        }
        
        html += '</div>';
        
        // Add footer only after the last question
        if (isLastQuestion) {
            html += '</div></div>'; // Close last-page-questions and last-page-content
            html += `
                <div class="last-page-footer">
                    <div class="print-footer">
                        <div class="recommendation-section">
                            <label>Recommendation Score</label>
                            <div style="display: flex; align-items: center;">
                                <div class="recommendation-meter" style="border: 1px solid #ccc;">
                                    <div class="recommendation-fill" style="width: ${(recommendation / 10) * 100}%; background-color: #4ECDC4 !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important;"></div>
                                </div>
                                <span class="response-score">${recommendation} / 10</span>
                            </div>
                        </div>
                        <div class="comments-section">
                            <label>Additional Comments</label>
                            <p class="comments-text">${comments}</p>
                        </div>
                    </div>
                </div>
            `;
            html += '</div>'; // Close last-page-container
        }
    });
    
    // Close the final page content
    html += '</div>';
    
    return html;
}

function generatePDF() {
    // Get survey title and account name for filename
    const surveyTitle = "{{ $survey->title }}".replace(/[^a-z0-9]/gi, '_');
    const accountName = "{{ $response->account_name }}".replace(/[^a-z0-9]/gi, '_');
    
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
                <i class="fas fa-spinner fa-spin me-2"></i>
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
    pdfContainer.style.lineHeight = '1.4'; // Tighter line height

    // Clone the print header with logo for PDF
    const logoHeader = document.querySelector('.print-only-header').cloneNode(true);
    logoHeader.classList.remove('d-none');
    logoHeader.style.display = 'block';
    logoHeader.style.marginBottom = '5px';
    
    const logoImage = logoHeader.querySelector('img.print-logo');
    if (logoImage) {
        logoImage.style.maxWidth = '170px';
        logoImage.style.maxHeight = '80px';
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
    
    // Adjust user info card
    const userInfoCard = contentClone.querySelector('.user-info-card');
    if (userInfoCard) {
        userInfoCard.style.marginBottom = '0.25rem';
        userInfoCard.style.padding = '0.15rem';
    }

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
        .recommendation-item { page-break-inside: avoid; }
        .user-info-card { page-break-inside: avoid; }
        .card-header { page-break-after: avoid; }
        .card-body { page-break-inside: auto; }
        .responses-list { page-break-inside: auto; }
        @media print {
            .response-item, .recommendation-item, .user-info-card { page-break-inside: avoid; }
        }
    `;
    pdfContainer.appendChild(style);

    document.body.appendChild(pdfContainer);

    // Use html2pdf to generate the PDF with proper page breaks
    const opt = {
        margin:       0.15, // Further reduced margins
        filename:     `${surveyTitle}_Response_${accountName}.pdf`,
        image:        { type: 'jpeg', quality: 0.95 }, // Slightly reduced quality for smaller file size
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' },
    };
    html2pdf().set(opt).from(pdfContainer).save().then(() => {
        document.body.removeChild(pdfContainer);
        if (document.getElementById('pdfLoadingToast')) {
            document.getElementById('pdfLoadingToast').remove();
        }
    });
}
</script>
@endsection