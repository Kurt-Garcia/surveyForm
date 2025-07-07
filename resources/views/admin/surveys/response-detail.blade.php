@extends(Auth::guard('admin')->check() ? 'layouts.app' : 'layouts.app-user')

@php
use Illuminate\Support\Facades\DB;
@endphp

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
            <button onclick="printWithPrintJS()" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-2"></i>Print
            </button>
            <button onclick="generatePDF()" class="btn btn-outline-secondary">
                <i class="bi bi-file-pdf me-2"></i>Download PDF
            </button>
        </div>
        <a href="{{ Auth::guard('admin')->check() ? route('admin.surveys.responses.index', $survey) : route('surveys.responses.index', $survey) }}" class="btn btn-outline-primary align-self-start align-self-sm-center">
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
                    
                    <!-- SweetAlert2 CSS -->
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
                    <!-- SweetAlert2 JS -->
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                    <form id="resubmissionForm" action="{{ Auth::guard('admin')->check() ? route('admin.surveys.responses.toggle-resubmission', ['survey' => $survey, 'account_name' => $header->account_name]) : route('surveys.responses.toggle-resubmission', ['survey' => $survey, 'account_name' => $header->account_name]) }}" 
                          method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="button" onclick="confirmResubmission()" class="btn {{ $header->allow_resubmit ? 'btn-warning' : 'btn-success' }} btn-sm">
                            <i class="bi {{ $header->allow_resubmit ? 'bi-lock' : 'bi-unlock' }} me-2"></i>
                            {{ $header->allow_resubmit ? 'Disable Resubmission' : 'Allow Resubmission' }}
                        </button>
                    </form>

<script>
const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
        confirmButton: 'btn btn-success ms-2',
        cancelButton: 'btn btn-danger'
    },
    buttonsStyling: false
});

function confirmResubmission() {
    const isCurrentlyAllowed = {{ $header->allow_resubmit ? 'true' : 'false' }};
    const actionText = isCurrentlyAllowed ? 'disable' : 'enable';
    const actionTitle = `Are you sure you want to ${actionText} resubmission?`;
    const actionDesc = isCurrentlyAllowed ? 
        'This will prevent the user from submitting another response.' : 
        'This will allow the user to submit another response.';

    swalWithBootstrapButtons.fire({
        title: actionTitle,
        text: actionDesc,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: `Yes, ${actionText} it!`,
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            @if(Auth::guard('admin')->check())
                // Admin context - use traditional form submission
                document.getElementById('resubmissionForm').submit();
            @else
                // User context - use fetch for JSON response
                const form = document.getElementById('resubmissionForm');
                fetch(form.action, {
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
                        // Show success message
                        swalWithBootstrapButtons.fire({
                            title: "Success!",
                            text: data.message,
                            icon: "success"
                        }).then(() => {
                            // Reload the page to reflect changes
                            window.location.reload();
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    swalWithBootstrapButtons.fire({
                        title: "Error!",
                        text: "An error occurred while updating resubmission status.",
                        icon: "error"
                    });
                });
            @endif
        }
    });
}
</script>
                </div>

                <div class="card-body p-4">
                    <!-- Response Meta Information -->
                    <div class="customer-info-section border p-3 mb-4 bg-light rounded">
                        <!-- First row: Account Name and Account Type -->
                        <div class="row mb-3">
                            <div class="col-md-9">
                                <div class="mb-3">
                                    <label class="text-muted mb-1">Account Name</label>
                                    <h5>
                                        @php
                                            $customer = DB::table('TBLCUSTOMER')->where('CUSTNAME', $header->account_name)->first();
                                            $custcode = $customer ? $customer->CUSTCODE : '';
                                        @endphp
                                        {{ $custcode ? $custcode . ' - ' . $header->account_name : $header->account_name }}
                                    </h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="text-muted mb-1">Account Type</label>
                                    <h5>{{ $header->account_type }}</h5>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Second row: Date, Start Time, End Time, Duration -->
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="text-muted mb-1">Date</label>
                                    <h5>{{ $header->date->format('M d, Y') }}</h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="text-muted mb-1">Start Time</label>
                                    <h5>{{ $header->start_time ? $header->start_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}</h5>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="text-muted mb-1">End Time</label>
                                    <h5>{{ $header->end_time ? $header->end_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}</h5>
                                </div>
                            </div>
                            <div class="col-md-3">
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
                    </div>

                    <hr class="my-4">

                    <!-- Question Responses -->
                    <div class="responses-list">
                        @foreach($responses as $response)
                            <div class="response-item mb-4 p-3 bg-light rounded">
                                <div class="mb-2">
                                    <label class="text-muted">Question {{ $loop->iteration }}</label>
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

                        <!-- Comments section removed as we now use improvement areas instead -->

                        <!-- Improvement Areas -->
                        <div class="response-item mb-4 p-3 bg-light rounded">
                            <div class="mb-2">
                                <label class="text-muted">Areas for Improvement</label>
                                @php
                                    $improvementAreas = $header->improvementAreas;
                                    $areasMap = [
                                        'product_quality' => 'Product/Service Quality',
                                        'delivery_logistics' => 'Delivery & Logistics',
                                        'customer_service' => 'Sales & Customer Service',
                                        'timeliness' => 'Timeliness',
                                        'returns_handling' => 'Returns/BO Handling',
                                        'others' => 'Others'
                                    ];
                                @endphp
                                
                                @if($improvementAreas->count() > 0)
                                    <div class="mt-2">
                                        @foreach($improvementAreas as $area)
                                            <div class="mb-3">
                                                <h6 class="fw-bold">{{ $areasMap[$area->area_category] ?? $area->area_category }}</h6>
                                                
                                                @if($area->area_details)
                                                    <ul class="mb-2">
                                                        @foreach(explode("\n", $area->area_details) as $detail)
                                                            <li>{{ $detail }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                                
                                                @if($area->is_other && $area->other_comments)
                                                    <div class="ps-3 py-2 border-start border-primary">
                                                        <p class="mb-0"><strong>Other Comments:</strong> {{ $area->other_comments }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="fw-bold mb-0">None provided</p>
                                @endif
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
    margin-bottom: 1rem;
    padding: 1rem;
}

.response-item:hover {
    transform: translateY(-2px);
}

.rating-display {
    line-height: 1;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.25rem;
}

.text-warning {
    color: #ffc107 !important;
}

/* Responsive styles */
@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem !important;
    }
    
    .card-header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .card-header h4 {
        font-size: 1.25rem;
    }
    
    .card-body {
        padding: 1rem !important;
    }
    
    .row > [class*='col-'] {
        margin-bottom: 1rem;
    }
}

@media (max-width: 576px) {
    .radio-display {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 0.75rem;
        gap: 0.5rem;
    }
    
    .form-check-inline {
        margin: 0;
        flex: 0 0 auto;
    }
    
    .d-flex.align-items-center.mt-2 {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem;
    }
    
    .d-flex.align-items-center.mt-2 .ms-2 {
        margin-left: 0 !important;
    }
    
    .response-item {
        padding: 0.75rem;
    }
    
    h5 {
        font-size: 1rem;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}
</style>



<!-- Include Print.js library -->
<script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://printjs-4de6.kxcdn.com/print.min.css">

<!-- Include html2pdf library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
// Print.js function
function printWithPrintJS() {
    try {
        // Get the survey responses
        const responses = document.querySelectorAll('.response-item');
        const questionsArray = Array.from(responses).filter(item => 
            !item.querySelector('label').textContent.includes('Recommendation Score') && 
            !item.querySelector('label').textContent.includes('Additional Comments')
        );
    
    // Get actual values from the page
    const surveyTitle = "{{ strtoupper($survey->title) }}";
    @php
        $customer = DB::table('TBLCUSTOMER')->where('CUSTNAME', $header->account_name)->first();
        $custcode = $customer ? $customer->CUSTCODE : '';
        $displayAccountName = $custcode ? $custcode . ' - ' . $header->account_name : $header->account_name;
    @endphp
    const accountName = "{{ $displayAccountName }}";
    const accountType = "{{ $header->account_type }}";
    const responseDate = "{{ $header->date->format('M d, Y') }}";
    const startTime = "{{ $header->start_time ? $header->start_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}";
    const endTime = "{{ $header->end_time ? $header->end_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}";
    const duration = "@if($header->start_time && $header->end_time){{ $header->end_time->diffForHumans($header->start_time, ['parts' => 2]) }}@else N/A @endif";
    const recommendation = "{{ $header->recommendation }}";
    const comments = "{{ $header->comments }}";
    const logoPath = "@if($survey->logo){{ asset('storage/' . $survey->logo) }}@else{{ asset('img/logo.png') }}@endif";
    
    // Process account name and type
    const accountNameData = { text: accountName, fontSize: '10pt' };
    const accountTypeData = { text: accountType, fontSize: '10pt' };
    
    // Create header content for Print (Logo + Customer Info)
    const headerContent = `
        <div class="print-header" style="margin-bottom: 20px; page-break-after: avoid;">
            <div class="header-logo" style="text-align: center; margin-bottom: 10px;">
                <img src="${logoPath}" alt="${surveyTitle} Logo" style="max-width: 150px; max-height: 60px; height: auto;">
            </div>
            <div class="header-title" style="text-align: center; margin-bottom: 15px;">
                <h4 style="margin: 0; font-size: 14pt; font-weight: bold;">${surveyTitle} - RESPONSE DETAILS</h4>
            </div>
            <div class="customer-info" style="border: 1px solid #ddd; padding: 12px; background-color: #f9f9f9; margin-bottom: 15px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <div style="flex: 1; margin-right: 15px; min-width: 0; max-width: 75%;">
                        <div style="font-size: 8pt; color: #666; margin-bottom: 3px;">Account Name</div>
                        <div style="font-size: 9pt; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${accountName}</div>
                    </div>
                    <div style="flex: 1; min-width: 0; max-width: 23%;">
                        <div style="font-size: 8pt; color: #666; margin-bottom: 3px;">Account Type</div>
                        <div style="font-size: 10pt; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${accountType}</div>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <div style="flex: 1; margin-right: 15px; min-width: 0; max-width: 25%;">
                        <div style="font-size: 8pt; color: #666; margin-bottom: 3px;">Date</div>
                        <div style="font-size: 10pt; font-weight: bold;">${responseDate}</div>
                    </div>
                    <div style="flex: 1; margin-right: 15px; min-width: 0; max-width: 25%;">
                        <div style="font-size: 8pt; color: #666; margin-bottom: 3px;">Start Time</div>
                        <div style="font-size: 10pt; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${startTime}</div>
                    </div>
                    <div style="flex: 1; margin-right: 15px; min-width: 0; max-width: 25%;">
                        <div style="font-size: 8pt; color: #666; margin-bottom: 3px;">End Time</div>
                        <div style="font-size: 10pt; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${endTime}</div>
                    </div>
                    <div style="flex: 1; min-width: 0; max-width: 25%;">
                        <div style="font-size: 8pt; color: #666; margin-bottom: 3px;">Duration</div>
                        <div style="font-size: 10pt; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${duration}</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Create footer content (Recommendation Score + Comments) - More compact version
    const footerContent = `
        <div style="border: 1px solid #ddd; padding: 8px; background-color: #f9f9f9; margin-top: 10px;">
            <div style="margin-bottom: 8px;">
                <div style="font-size: 8pt; color: #666; margin-bottom: 2px;">Recommendation Score</div>
                <div style="font-size: 10pt; font-weight: bold;">${recommendation} / 10</div>
            </div>
            <div>
                <div style="font-size: 8pt; color: #666; margin-bottom: 2px;">Additional Comments</div>
                <div style="font-size: 10pt; font-weight: bold; word-wrap: break-word;">${comments}</div>
            </div>
        </div>
    `;
    
    // Create pages with 5 questions each
    const questionsPerPage = 5;
    const totalPages = Math.ceil(questionsArray.length / questionsPerPage);
    
    let printHTML = '<div style="font-family: Arial, sans-serif; font-size: 9pt; line-height: 1.4;">';
    
    for (let page = 0; page < totalPages; page++) {
        const startIndex = page * questionsPerPage;
        const endIndex = Math.min(startIndex + questionsPerPage, questionsArray.length);
        const pageQuestions = questionsArray.slice(startIndex, endIndex);
        const isLastPage = (page === totalPages - 1);
        
        // Start page - use flexbox only on last page for footer positioning
        const pageStyle = page > 0 ? 'page-break-before: always;' : '';
        const heightStyle = isLastPage ? 'display: flex; flex-direction: column; min-height: 95vh;' : '';
        
        printHTML += `<div class="print-page" style="${pageStyle} ${heightStyle}">`;
        
        // Add header to every page
        printHTML += headerContent;
        
        // Add questions for this page - flex-shrink for last page to allow footer positioning
        const contentStyle = isLastPage ? 'margin: 5px 0; flex-shrink: 0;' : 'margin: 20px 0;';
        printHTML += `<div class="questions-content" style="${contentStyle}">`;
        
        pageQuestions.forEach((question) => {
            const questionClone = question.cloneNode(true);
            
            // Get question content
            let questionLabel = '';
            let questionText = '';
            try {
                const labelElement = questionClone.querySelector('label');
                questionLabel = labelElement ? labelElement.textContent : 'Question Label';
                
                const textElement = questionClone.querySelector('h5');
                questionText = textElement ? textElement.textContent : 'Question Text';
            } catch (error) {
                console.error('Error getting question content:', error);
                questionLabel = 'Question Label';
                questionText = 'Question Text';
            }
            
            // Get response content based on type
            let responseHTML = '';
            const responseDiv = questionClone.querySelector('div:last-child');
            
            // Check if it's radio type
            const radioDisplay = responseDiv.querySelector('.radio-display');
            if (radioDisplay) {
                try {
                    // First try to get the selected value from the checked radio input
                    const checkedRadio = radioDisplay.querySelector('input[type="radio"]:checked');
                    let selectedNumber = 0;
                    
                    if (checkedRadio) {
                        // Use the checked radio button's adjacent label text or find which position it is
                        const radioInputs = radioDisplay.querySelectorAll('input[type="radio"]');
                        radioInputs.forEach((radio, index) => {
                            if (radio.checked) {
                                selectedNumber = index + 1; // Radio buttons are 1-indexed
                            }
                        });
                    } else {
                        // Fallback: Find the span with the actual selected value (format: "X / 5")
                        const selectedValueSpan = responseDiv.querySelector('span.ms-2.fw-bold');
                        let selectedValue = '';
                        if (selectedValueSpan) {
                            selectedValue = selectedValueSpan.textContent.trim();
                        } else {
                            // Second fallback: look for any span in the responseDiv
                            const anySpan = responseDiv.querySelector('span');
                            selectedValue = anySpan ? anySpan.textContent.trim() : '';
                        }
                        
                        // Extract the number from "X / 5" format or just use the number if it's only a number
                        if (typeof selectedValue === 'string' && selectedValue.includes('/')) {
                            selectedNumber = parseInt(selectedValue.split('/')[0].trim()) || 0;
                        } else {
                            selectedNumber = parseInt(selectedValue) || 0;
                        }
                    }
                    
                    // Get the display value for the print
                    const displaySpan = responseDiv.querySelector('span.ms-2.fw-bold');
                    const displayValue = displaySpan ? displaySpan.textContent.trim() : `${selectedNumber} / 5`;
                    
                    responseHTML = '<div style="display: flex; align-items: center; margin-top: 8px;">';
                    responseHTML += '<div style="display: flex; gap: 10px; margin-right: 15px;">';
                    
                    // Generate 5 radio buttons (1-5) using actual input elements like the working version
                    for (let i = 1; i <= 5; i++) {
                        const checked = (i === selectedNumber) ? 'checked' : '';
                        responseHTML += `<div style="display: flex; align-items: center; gap: 3px;">
                            <input type="radio" style="margin-right: 3px;" ${checked} disabled>
                            <label style="font-size: 8pt;">${i}</label>
                        </div>`;
                    }
                    
                    responseHTML += '</div>';
                    responseHTML += `<span style="font-weight: bold; font-size: 10pt;">${displayValue}</span>`;
                    responseHTML += '</div>';
                } catch (error) {
                    console.error('Error processing radio buttons:', error);
                    // Fallback for radio buttons
                    responseHTML = '<div style="display: flex; align-items: center; margin-top: 8px;">';
                    responseHTML += '<div style="display: flex; gap: 10px; margin-right: 15px;">';
                    
                    for (let i = 1; i <= 5; i++) {
                        responseHTML += `<div style="display: flex; align-items: center; gap: 3px;">
                            <input type="radio" style="margin-right: 3px;" disabled>
                            <label style="font-size: 8pt;">${i}</label>
                        </div>`;
                    }
                    
                    responseHTML += '</div>';
                    responseHTML += `<span style="font-weight: bold; font-size: 10pt;">0 / 5</span>`;
                    responseHTML += '</div>';
                }
            }
            
            // Check if it's star type
            const ratingDisplay = responseDiv.querySelector('.rating-display');
            if (ratingDisplay) {
                try {
                    const stars = ratingDisplay.querySelectorAll('.bi-star-fill');
                    let selectedValue = '0 / 5';
                    const valueSpan = responseDiv.querySelector('span');
                    
                    if (valueSpan) {
                        selectedValue = valueSpan.textContent;
                    }
                    
                    responseHTML = '<div style="display: flex; align-items: center; margin-top: 8px;">';
                    responseHTML += '<div style="display: flex; gap: 2px; margin-right: 15px;">';
                    
                    stars.forEach((star) => {
                        const isSelected = star.classList.contains('text-warning');
                        responseHTML += `<span style="font-size: 12pt; color: ${isSelected ? '#ffc107' : '#6c757d'};">★</span>`;
                    });
                    
                    responseHTML += '</div>';
                    responseHTML += `<span style="font-weight: bold; font-size: 10pt;">${selectedValue}</span>`;
                    responseHTML += '</div>';
                } catch (error) {
                    console.error('Error processing star rating:', error);
                    
                    // Fallback for star ratings
                    responseHTML = '<div style="display: flex; align-items: center; margin-top: 8px;">';
                    responseHTML += '<div style="display: flex; gap: 2px; margin-right: 15px;">';
                    
                    for (let i = 1; i <= 5; i++) {
                        responseHTML += `<span style="font-size: 12pt; color: #6c757d;">★</span>`;
                    }
                    
                    responseHTML += '</div>';
                    responseHTML += `<span style="font-weight: bold; font-size: 10pt;">0 / 5</span>`;
                    responseHTML += '</div>';
                }
            }
            
            // Check if it's text/textarea
            const textResponse = responseDiv.querySelector('p');
            if (textResponse && !radioDisplay && !ratingDisplay) {
                try {
                    responseHTML = `<div style="font-weight: bold; font-size: 10pt; margin-top: 8px;">${textResponse.textContent}</div>`;
                } catch (error) {
                    console.error('Error processing text response:', error);
                    responseHTML = `<div style="font-weight: bold; font-size: 10pt; margin-top: 8px;">No text response available</div>`;
                }
            }
            
            // Adjust spacing - smaller on last page to fit footer perfectly
            const questionMargin = isLastPage ? '6px' : '22px';
            const questionPadding = isLastPage ? '8px' : '18px';
            const labelMargin = isLastPage ? '2px' : '6px';
            const textMargin = isLastPage ? '4px' : '12px';
            
            // Build question HTML
            printHTML += `
                <div style="border: 1px solid #eee; margin-bottom: ${questionMargin}; padding: ${questionPadding}; background-color: #f9f9f9; page-break-inside: avoid;">
                    <div style="margin-bottom: ${labelMargin};">
                        <div style="font-size: 8pt; color: #666; margin-bottom: ${labelMargin};">${questionLabel}</div>
                        <div style="font-size: 10pt; font-weight: bold; margin-bottom: ${textMargin};">${questionText}</div>
                    </div>
                    <div>
                        <div style="font-size: 8pt; color: #666; margin-bottom: ${labelMargin};">Response</div>
                        ${responseHTML}
                    </div>
                </div>
            `;
        });
        
        printHTML += '</div>'; // Close questions-content
        
        // Add footer only on the last page - stick to bottom with flexbox
        if (isLastPage) {
            // Add flexible spacer to push footer to bottom - ensure proper spacing
            printHTML += '<div style="flex: 1 1 auto;"></div>';
            printHTML += footerContent;
        }
        
        printHTML += '</div>'; // Close print-page
    }
    
    printHTML += '</div>'; // Close main container
    
    // Use Print.js to print
    printJS({
        printable: printHTML,
        type: 'raw-html',
        style: `
            @page {
                size: A4;
                margin: 15mm;
                @bottom-right {
                    content: "Page " counter(page) " of " counter(pages);
                    font-size: 10pt;
                    color: #666;
                    margin: 5px;
                }
            }
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                font-size: 9pt;
                line-height: 1.4;
                color: #000;
            }
            .print-page {
                width: 100%;
                position: relative;
                box-sizing: border-box;
                page-break-after: always;
            }
            .print-page:last-child {
                page-break-after: avoid;
            }
            .print-header {
                margin-bottom: 15px;
                page-break-after: avoid;
            }
            .questions-content {
                margin: 15px 0;
                flex-shrink: 0;
            }
        `,
        onLoadingStart: function () {
            // Print started
        },
        onLoadingEnd: function () {
            // Print complete
        }
    });
    } catch (error) {
        console.error('Error in print function:', error);
        alert('An error occurred while preparing the print. Please try again.');
    }
}
</script>

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

    // Get the survey responses (same as print function)
    const responses = document.querySelectorAll('.response-item');
    const questionsArray = Array.from(responses).filter(item => 
        !item.querySelector('label').textContent.includes('Recommendation Score') && 
        !item.querySelector('label').textContent.includes('Additional Comments')
    );
    
    // Get actual values from the page
    const surveyTitleText = "{{ strtoupper($survey->title) }}";
    const accountNameText = "{{ $displayAccountName }}";
    const accountTypeText = "{{ $header->account_type }}";
    const responseDate = "{{ $header->date->format('M d, Y') }}";
    const startTime = "{{ $header->start_time ? $header->start_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}";
    const endTime = "{{ $header->end_time ? $header->end_time->setTimezone('Asia/Manila')->format('h:i:s A') : 'N/A' }}";
    const duration = "@if($header->start_time && $header->end_time){{ $header->end_time->diffForHumans($header->start_time, ['parts' => 2]) }}@else N/A @endif";
    const recommendation = "{{ $header->recommendation }}";
    const comments = "{{ $header->comments }}";
    const logoPath = "@if($survey->logo){{ asset('storage/' . $survey->logo) }}@else{{ asset('img/logo.png') }}@endif";
    
    // Function to truncate text and calculate font size for PDF
    function getTruncatedTextAndFontSizePDF(text, maxLength = 30) {
        let fontSize = '10pt';
        let displayText = text;
        
        if (text.length > maxLength) {
            // Try smaller font first
            if (text.length <= 40) {
                fontSize = '9pt';
            } else if (text.length <= 50) {
                fontSize = '8pt';
            } else {
                // If still too long, truncate with ellipsis
                fontSize = '8pt';
                displayText = text.substring(0, maxLength - 3) + '...';
            }
        }
        
        return { text: displayText, fontSize: fontSize, isOriginal: displayText === text };
    }
    
    // Process account name and type for PDF - show full account name without truncation
    const accountNameDataPDF = { text: accountNameText, fontSize: '10pt' };
    const accountTypeDataPDF = getTruncatedTextAndFontSizePDF(accountTypeText, 25);
    
    // Create header content for PDF (Logo + Customer Info)
    const headerContent = `
        <div class="pdf-header" style="margin-bottom: 20px; padding-top: 7px;">
            <div class="header-logo" style="text-align: center; margin-bottom: 10px;">
                <img src="${logoPath}" alt="${surveyTitleText} Logo" style="max-width: 150px; max-height: 60px; height: auto;">
            </div>
            <div class="header-title" style="text-align: center; margin-bottom: 15px;">
                <h4 style="margin: 0; font-size: 14pt; font-weight: bold;">${surveyTitleText} - RESPONSE DETAILS</h4>
            </div>
            <div class="customer-info" style="border: 1px solid #ddd; padding: 12px; background-color: #f9f9f9; margin-bottom: 15px;">
                <div style="display: flex; gap: 15px; margin-bottom: 10px;">
                    <div style="flex: 1; min-width: 0; max-width: 75%;">
                        <div style="font-size: 8pt; color: #666; margin-bottom: 3px;">Account Name</div>
                        <div style="font-size: ${accountNameDataPDF.fontSize}; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${accountNameText}">${accountNameDataPDF.text}</div>
                    </div>
                    <div style="flex: 1; min-width: 0; max-width: 23%;">
                        <div style="font-size: 8pt; color: #666; margin-bottom: 3px;">Account Type</div>
                        <div style="font-size: ${accountTypeDataPDF.fontSize}; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="${accountTypeText}">${accountTypeDataPDF.text}</div>
                    </div>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div style="flex: 1; min-width: 0; max-width: 25%;">
                        <div style="font-size: 8pt; color: #666; margin-bottom: 3px;">Date</div>
                        <div style="font-size: 10pt; font-weight: bold;">${responseDate}</div>
                    </div>
                    <div style="flex: 1; min-width: 0; max-width: 25%;">
                        <div style="font-size: 8pt; color: #666; margin-bottom: 3px;">Start Time</div>
                        <div style="font-size: 10pt; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${startTime}</div>
                    </div>
                    <div style="flex: 1; min-width: 0; max-width: 25%;">
                        <div style="font-size: 8pt; color: #666; margin-bottom: 3px;">End Time</div>
                        <div style="font-size: 10pt; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${endTime}</div>
                    </div>
                    <div style="flex: 1; min-width: 0; max-width: 25%;">
                        <div style="font-size: 8pt; color: #666; margin-bottom: 3px;">Duration</div>
                        <div style="font-size: 10pt; font-weight: bold; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${duration}</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Create footer content (Recommendation Score + Comments)
    const footerContent = `
        <div style="border: 1px solid #ddd; padding: 8px; background-color: #f9f9f9; margin-top: auto; font-size: 8pt; page-break-inside: avoid;">
            <div style="margin-bottom: 6px;">
                <div style="font-size: 7pt; color: #666; margin-bottom: 2px;">Recommendation Score</div>
                <div style="font-size: 9pt; font-weight: bold;">${recommendation} / 10</div>
            </div>
            <div>
                <div style="font-size: 7pt; color: #666; margin-bottom: 2px;">Additional Comments</div>
                <div style="font-size: 9pt; font-weight: bold; word-wrap: break-word;">${comments}</div>
            </div>
        </div>
    `;
    
    // Create pages with 5 questions each
    const questionsPerPage = 5;
    const totalPages = Math.ceil(questionsArray.length / questionsPerPage);
    
    // Prepare PDF content
    const pdfContainer = document.createElement('div');
    pdfContainer.style.width = '210mm';
    pdfContainer.style.maxWidth = '100%';
    pdfContainer.style.backgroundColor = 'white';
    pdfContainer.style.fontFamily = 'Arial, sans-serif';
    pdfContainer.style.color = '#222';
    pdfContainer.style.fontSize = '9pt';
    pdfContainer.style.lineHeight = '1.4';
    pdfContainer.style.padding = '0';
    
    let pdfHTML = '';
    
    for (let page = 0; page < totalPages; page++) {
        const startIndex = page * questionsPerPage;
        const endIndex = Math.min(startIndex + questionsPerPage, questionsArray.length);
        const pageQuestions = questionsArray.slice(startIndex, endIndex);
        const isLastPage = (page === totalPages - 1);
        
        // Start page with page break for subsequent pages
        const pageStyle = page > 0 ? 'page-break-before: always; ' : '';
        const heightStyle = isLastPage ? 'display: flex; flex-direction: column; height: 270mm; box-sizing: border-box;' : '';
        
        pdfHTML += `<div class="pdf-page" style="${pageStyle}${heightStyle}">`;
        
        // Add header to every page
        pdfHTML += headerContent;
        
        // Add questions for this page
        const contentStyle = isLastPage ? 'flex-shrink: 0;' : '';
        pdfHTML += `<div class="questions-content" style="${contentStyle}">`;
        
        pageQuestions.forEach((question) => {
            const questionClone = question.cloneNode(true);
            
            // Get question content
            let questionLabel = '';
            let questionText = '';
            try {
                const labelElement = questionClone.querySelector('label');
                questionLabel = labelElement ? labelElement.textContent : 'Question Label';
                
                const textElement = questionClone.querySelector('h5');
                questionText = textElement ? textElement.textContent : 'Question Text';
            } catch (error) {
                console.error('Error getting question content:', error);
                questionLabel = 'Question Label';
                questionText = 'Question Text';
            }
            
            // Get response content based on type
            let responseHTML = '';
            const responseDiv = questionClone.querySelector('div:last-child');
            
            // Check if it's radio type
            const radioDisplay = responseDiv.querySelector('.radio-display');
            if (radioDisplay) {
                // Add debug to troubleshoot
                let debugInfo = {};
                let selectedNumber = 0;
                let displayValue = '0 / 5';
                
                try {
                    const checkedRadio = radioDisplay.querySelector('input[type="radio"]:checked');
                    debugInfo.checkedRadio = checkedRadio;
                    
                    if (checkedRadio) {
                        const radioInputs = radioDisplay.querySelectorAll('input[type="radio"]');
                        radioInputs.forEach((radio, index) => {
                            if (radio.checked) {
                                selectedNumber = index + 1;
                            }
                        });
                    } else {
                        const selectedValueSpan = responseDiv.querySelector('span.ms-2.fw-bold');
                        let selectedValue = '';
                        
                        if (selectedValueSpan) {
                            selectedValue = selectedValueSpan.textContent.trim();
                        } else {
                            const anySpan = responseDiv.querySelector('span');
                            selectedValue = anySpan ? anySpan.textContent.trim() : '';
                        }
                        
                        // Make sure selectedValue is a string before using string methods
                        if (typeof selectedValue === 'string' && selectedValue.includes('/')) {
                            selectedNumber = parseInt(selectedValue.split('/')[0].trim()) || 0;
                        } else {
                            selectedNumber = parseInt(selectedValue) || 0;
                        }
                    }
                    
                    const displaySpan = responseDiv.querySelector('span.ms-2.fw-bold');
                    displayValue = displaySpan ? displaySpan.textContent.trim() : `${selectedNumber} / 5`;
                    
                    // Add debug info
                    debugInfo.selectedNumber = selectedNumber;
                    debugInfo.displayValue = displayValue;
                    debugInfo.radioDisplay = radioDisplay;
                    console.log('Radio Debug Info:', debugInfo);
                    
                    responseHTML = '<div style="display: flex; align-items: center; margin-top: 8px;">';
                    responseHTML += '<div style="display: flex; gap: 10px; margin-right: 15px;">';
                    
                    for (let i = 1; i <= 5; i++) {
                        const checked = (i === selectedNumber) ? 'checked' : '';
                        responseHTML += `<div style="display: flex; align-items: center; gap: 3px;">
                            <input type="radio" style="margin-right: 3px;" ${checked} disabled>
                            <label style="font-size: 8pt;">${i}</label>
                        </div>`;
                    }
                    
                    responseHTML += '</div>';
                    responseHTML += `<span style="font-weight: bold; font-size: 10pt;">${displayValue}</span>`;
                    responseHTML += '</div>';
                } catch (error) {
                    console.error('Error processing radio display:', error);
                    
                    // Fallback response HTML in case of error
                    responseHTML = '<div style="display: flex; align-items: center; margin-top: 8px;">';
                    responseHTML += '<div style="display: flex; gap: 10px; margin-right: 15px;">';
                    
                    for (let i = 1; i <= 5; i++) {
                        const checked = (i === selectedNumber) ? 'checked' : '';
                        responseHTML += `<div style="display: flex; align-items: center; gap: 3px;">
                            <input type="radio" style="margin-right: 3px;" ${checked} disabled>
                            <label style="font-size: 8pt;">${i}</label>
                        </div>`;
                    }
                    
                    responseHTML += '</div>';
                    responseHTML += `<span style="font-weight: bold; font-size: 10pt;">${displayValue}</span>`;
                    responseHTML += '</div>';
                }
            }
            
            // Check if it's star type
            const ratingDisplay = responseDiv.querySelector('.rating-display');
            if (ratingDisplay) {
                try {
                    const stars = ratingDisplay.querySelectorAll('.bi-star-fill');
                    let selectedValue = '0 / 5';
                    const valueSpan = responseDiv.querySelector('span');
                    
                    if (valueSpan) {
                        selectedValue = valueSpan.textContent;
                    }
                    
                    responseHTML = '<div style="display: flex; align-items: center; margin-top: 8px;">';
                    responseHTML += '<div style="display: flex; gap: 2px; margin-right: 15px;">';
                    
                    stars.forEach((star) => {
                        const isSelected = star.classList.contains('text-warning');
                        responseHTML += `<span style="font-size: 12pt; color: ${isSelected ? '#ffc107' : '#6c757d'};">★</span>`;
                    });
                    
                    responseHTML += '</div>';
                    responseHTML += `<span style="font-weight: bold; font-size: 10pt;">${selectedValue}</span>`;
                    responseHTML += '</div>';
                } catch (error) {
                    console.error('Error processing star rating in PDF:', error);
                    
                    // Fallback for star ratings
                    responseHTML = '<div style="display: flex; align-items: center; margin-top: 8px;">';
                    responseHTML += '<div style="display: flex; gap: 2px; margin-right: 15px;">';
                    
                    for (let i = 1; i <= 5; i++) {
                        responseHTML += `<span style="font-size: 12pt; color: #6c757d;">★</span>`;
                    }
                    
                    responseHTML += '</div>';
                    responseHTML += `<span style="font-weight: bold; font-size: 10pt;">0 / 5</span>`;
                    responseHTML += '</div>';
                }
            }
            
            // Check if it's text/textarea
            const textResponse = responseDiv.querySelector('p');
            if (textResponse && !radioDisplay && !ratingDisplay) {
                try {
                    responseHTML = `<div style="font-weight: bold; font-size: 10pt; margin-top: 8px;">${textResponse.textContent}</div>`;
                } catch (error) {
                    console.error('Error processing text response in PDF:', error);
                    responseHTML = `<div style="font-weight: bold; font-size: 10pt; margin-top: 8px;">No text response available</div>`;
                }
            }
            
            // Adjust spacing for last page to ensure footer fits
            const questionMargin = isLastPage ? '8px' : '15px';
            const questionPadding = isLastPage ? '10px' : '15px';
            
            // Build question HTML
            pdfHTML += `
                <div style="border: 1px solid #eee; margin-bottom: ${questionMargin}; padding: ${questionPadding}; background-color: #f9f9f9;">
                    <div style="margin-bottom: 8px;">
                        <div style="font-size: 8pt; color: #666; margin-bottom: 4px;">${questionLabel}</div>
                        <div style="font-size: 11pt; font-weight: bold; margin-bottom: 8px;">${questionText}</div>
                    </div>
                    <div>
                        <div style="font-size: 8pt; color: #666; margin-bottom: 4px;">Response</div>
                        ${responseHTML}
                    </div>
                </div>
            `;
        });
        
        pdfHTML += '</div>'; // Close questions-content
        
        // Add footer only on the last page
        if (isLastPage) {
            pdfHTML += '<div style="flex: 1; min-height: 30px;"></div>';
            pdfHTML += footerContent;
        }
        
        pdfHTML += '</div>'; // Close pdf-page
    }
    
    pdfContainer.innerHTML = pdfHTML;
    document.body.appendChild(pdfContainer);
    
    // Configure PDF options
    const opt = {
        margin: [0.5, 0.3, 0.5, 0.3], // top, right, bottom, left - extra bottom margin for page numbers
        filename: `${surveyTitle}_${accountName}_Response.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2,
            useCORS: true,
            allowTaint: true,
            letterRendering: true
        },
        jsPDF: { 
            unit: 'in', 
            format: 'a4', 
            orientation: 'portrait' 
        }
    };
    
    // Generate PDF with page numbering
    html2pdf().set(opt).from(pdfContainer).toPdf().get('pdf').then(function (pdf) {
        const totalPages = pdf.internal.getNumberOfPages();
        
        for (let i = 1; i <= totalPages; i++) {
            pdf.setPage(i);
            pdf.setFontSize(10);
            pdf.setTextColor(102, 102, 102); // #666666
            // Position page numbers in lower right corner
            pdf.text(`Page ${i} of ${totalPages}`, pdf.internal.pageSize.width - 1.2, pdf.internal.pageSize.height - 0.2);
        }
    }).save().then(() => {
        // Remove loading toast and PDF container
        document.body.removeChild(loadingToast);
        document.body.removeChild(pdfContainer);
    }).catch((error) => {
        console.error('PDF generation failed:', error);
        document.body.removeChild(loadingToast);
        document.body.removeChild(pdfContainer);
        alert('Failed to generate PDF. Please try again.');
    });
}
</script>

<!-- Print Styles -->
<style media="print">
@page {
    size: auto;
    margin: 3mm 5mm 5mm 5mm; /* Reduced margins */
    @bottom-right {
        content: "Page " counter(page) " of " counter(pages);
        font-size: 10pt;
        color: #666;
        margin: 5px;
    }
}
body {
    margin: 0;
    padding: 0;
    font-size: 8pt; /* Smaller base font size */
    line-height: 2.5; /* Tighter line height */
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
    max-width: 33% !important;
    padding: 0 0.15rem !important;
    min-width: 0 !important;
}

/* Ensure customer info section doesn't break */
.customer-info {
    page-break-inside: avoid !important;
}
.customer-info > div {
    display: flex !important;
    gap: 0.15rem !important;
}
.customer-info > div > div {
    flex: 1 !important;
    max-width: 33% !important;
    min-width: 0 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
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
    max-height: 60px !important;
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