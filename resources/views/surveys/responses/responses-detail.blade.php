@extends('layouts.app-user')

@section('content')
<div class="container mt-5">
    <!-- Action Buttons Section -->
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary me-2">
                <i class="fas fa-print me-2"></i>Print
            </button>
            <button onclick="generatePDF()" class="btn btn-outline-secondary">
                <i class="fas fa-file-pdf me-2"></i>Download PDF
            </button>
        </div>
        <a href="{{ route('surveys.responses.index', $survey) }}" class="btn btn-outline-primary">
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
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <div class="response-icon">
                                        <i class="fas fa-user-circle fa-3x"></i>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3 mb-md-0">
                                                <label class="text-muted small mb-1">Account Name</label>
                                                <h5 class="mb-0">{{ $response->account_name }}</h5>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3 mb-md-0">
                                                <label class="text-muted small mb-1">Account Type</label>
                                                <h5 class="mb-0"><span class="badge bg-light text-dark">{{ $response->account_type }}</span></h5>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div>
                                                <label class="text-muted small mb-1">Date</label>
                                                <h5 class="mb-0"><i class="fas fa-calendar-alt me-1 small"></i> {{ $response->date }}</h5>
                                            </div>
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

@media print {
    .btn, .hover-lift:hover {
        transform: none !important;
        box-shadow: none !important;
    }
    .card {
        border: 1px solid #ddd;
    }
}
</style>

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
    window.print();
}
</script>
@endsection