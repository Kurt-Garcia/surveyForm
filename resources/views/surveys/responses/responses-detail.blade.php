@extends('layouts.app-user')

@section('content')
<div class="container mt-5">
    <!-- Action Buttons Section -->
    <div class="mb-3 d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center gap-2 gap-sm-0">
        <div class="d-flex flex-column flex-sm-row gap-2 mb-2 mb-sm-0">
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="fas fa-print me-2"></i>Print
            </button>
            <button onclick="generatePDF()" class="btn btn-outline-secondary">
                <i class="fas fa-file-pdf me-2"></i>Download PDF
            </button>
        </div>
        <a href="{{ route('surveys.responses.index', $survey) }}" class="btn btn-outline-light custom-hover-btn" style="color: var(--primary-color); border-color: var(--primary-color)">
            <i class="fas fa-arrow-left me-1"></i> Back to Survey
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
                    <h4 class="mb-0 fw-bold">{{ strtoupper($survey->title) }} - RESPONSE DETAILS</h4>
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

                    <script>
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

                    document.getElementById('resubmissionForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        
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
                                
                                // Show success message
                                const alertDiv = document.createElement('div');
                                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                                alertDiv.innerHTML = `
                                    <i class="fas fa-check-circle me-2"></i>${data.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                `;
                                
                                // Insert alert at the top of the card
                                const cardHeader = document.querySelector('.card-header');
                                cardHeader.parentNode.insertBefore(alertDiv, cardHeader);
                                
                                // Auto dismiss after 3 seconds
                                setTimeout(() => {
                                    alertDiv.remove();
                                }, 3000);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                    });
                    </script>
                </div>
                <div class="card-body p-4">
                    <div class="card user-info-card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-auto mb-3 mb-md-0">
                                    <div class="response-icon">
                                        <i class="fas fa-user-circle fa-3x"></i>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row mb-3">
                                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                                            <label class="text-muted small mb-1">Account Name</label>
                                            <h5 class="mb-0">{{ $response->account_name }}</h5>
                                        </div>
                                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                                            <label class="text-muted small mb-1">Account Type</label>
                                            <h5 class="mb-0"><span class="badge bg-light text-dark">{{ $response->account_type }}</span></h5>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label class="text-muted small mb-1">Date</label>
                                            <h5 class="mb-0"><i class="fas fa-calendar-alt me-1 small"></i> {{ $response->date->format('M d, Y') }}</h5>
                                        </div>
                                    </div>
                                    
                                    <!-- Time Information -->
                                    <div class="row">
                                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                                            <label class="text-muted small mb-1">Start Time</label>
                                            <h5 class="mb-0"><i class="fas fa-clock me-1 small"></i> {{ $response->start_time ? $response->start_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}</h5>
                                        </div>
                                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                                            <label class="text-muted small mb-1">End Time</label>
                                            <h5 class="mb-0"><i class="fas fa-clock me-1 small"></i> {{ $response->end_time ? $response->end_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}</h5>
                                        </div>
                                        <div class="col-12 col-md-4">
                                            <label class="text-muted small mb-1">Duration</label>
                                            <h5 class="mb-0"><i class="fas fa-stopwatch me-1 small"></i> 
                                                @if($response->start_time && $response->end_time)
                                                    {{ $response->end_time->diffForHumans($response->start_time, ['parts' => 2]) }}
                                                @else
                                                    N/A
                                                @endif
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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

.custom-hover-btn:hover {
    background-color: var(--secondary-color);
    color: white; /* Optional: change text color on hover */
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
    border-left: 4px solid #4ECDC4;
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
        margin: 5mm 10mm 10mm 10mm; /* Reduced top margin */
    }
    
    body {
        margin: 0;
        padding: 0;
        font-size: 10pt;
        line-height: 1.2;
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
        border: 1px solid #ddd !important;
        box-shadow: none !important;
        margin: 0 !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }
    
    .card-header {
        padding: 0.5rem 0 !important;
        border-bottom: 1px solid #ddd !important;
        margin-bottom: 0.5rem !important;
        background-color: #f8f9fa !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    .card-body {
        padding: 0 !important;
    }
    
    /* Font size adjustments */
    h1, h2, h3 {
        font-size: 16pt !important;
    }
    
    h4 {
        font-size: 16pt !important;
        margin: 0.5rem 0 !important;
    }
    
    h5 {
        font-size: 12pt !important;
        margin: 0.25rem 0 !important;
    }
    
    p, span, div {
        font-size: 12pt !important;
    }
    
    .small, small {
        font-size: 10pt !important;
    }
    
    /* Spacing adjustments */
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
    
    /* Layout adjustments */
    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 !important;
    }
    
    .col-md-4 {
        width: 33% !important;
        padding: 0 0.25rem !important;
    }
    
    .response-item {
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        border: 1px solid #eee !important;
        margin-bottom: 0.5rem !important;
        padding: 0.5rem !important;
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
}
</style>

<!-- Add jsPDF library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colors = [
        '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEEAD',
        '#D4A5A5', '#9B59B6', '#3498DB', '#E67E22', '#2ECC71',
        '#FF9F43', '#00B894', '#74B9FF', '#A8E6CF', '#FFD93D',
        '#FF6B81', '#6C5CE7', '#00CEC9', '#FD79A8', '#81ECEC'
    ];

    // Set icon color
    const responseIcon = document.querySelector('.response-icon i');
    const iconColor = colors[Math.floor(Math.random() * colors.length)];
    if (responseIcon) {
        responseIcon.style.color = iconColor;
    }

    // Set recommendation badge color
    const recommendationBadge = document.querySelector('.recommendation-badge .badge');
    const recommendationColor = colors[Math.floor(Math.random() * colors.length)];
    if (recommendationBadge) {
        recommendationBadge.style.backgroundColor = recommendationColor;
    }
    
    const recommendationFill = document.querySelector('.recommendation-fill');
    if (recommendationFill) {
        recommendationFill.style.backgroundColor = recommendationColor;
    }

    // Set different colors for each response item
    document.querySelectorAll('.response-item').forEach(item => {
        const randomColor = colors[Math.floor(Math.random() * colors.length)];
        item.style.borderLeftColor = randomColor;
        
        const ratedStars = item.querySelectorAll('.rated');
        ratedStars.forEach(star => {
            star.style.color = randomColor;
        });
    });
});

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

    // Get content to convert
    const contentElement = document.querySelector('.card.shadow-sm.border-0');
    
    // Hide buttons for PDF and prepare content
    const actionButtons = document.querySelectorAll('.btn');
    actionButtons.forEach(btn => btn.style.display = 'none');
    
    // Set max width for better fitting
    const originalWidth = contentElement.style.width;
    contentElement.style.width = '800px';
    
    // Use html2canvas to convert the card to an image
    window.html2canvas(contentElement, {
        scale: 1.5, // Higher quality rendering
        useCORS: true,
        allowTaint: true,
        scrollY: -window.scrollY // Fix positioning issues
    }).then(canvas => {
        // Restore original styles
        contentElement.style.width = originalWidth;
        actionButtons.forEach(btn => btn.style.display = '');
        
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
        
        // Calculate scaling ratio with adjusted margins (reduced top margin)
        const marginSide = 10; // Side margins
        const marginTop = 5;   // Reduced top margin
        const marginBottom = 10; // Bottom margin
        const availableWidth = pdfWidth - (marginSide * 2);
        const availableHeight = pdfHeight - (marginTop + marginBottom);
        const ratio = Math.min(availableWidth / imgWidth, availableHeight / imgHeight);
        
        // Calculate centered position with reduced top margin
        const imgX = marginSide + (availableWidth - (imgWidth * ratio)) / 2;
        const imgY = marginTop; // Place content closer to the top
        
        // Add image to PDF
        pdf.addImage(imgData, 'PNG', imgX, imgY, imgWidth * ratio, imgHeight * ratio);
        
        // Save PDF
        pdf.save(`${document.title || 'survey-response'}-details.pdf`);
        
        // Remove loading indicator
        document.body.removeChild(loadingToast);
    });
}
</script>
@endsection