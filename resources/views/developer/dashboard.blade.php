<!DOCTYPE html>
<html lang="en">
<head>    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Developer Portal - {{ config('app.name') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Developer-specific styles -->
<style>
body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
}
.developer-dashboard {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    min-height: 100vh;
    color: white;
    position: relative;
}

/* Animated background particles */
.bg-particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 1;
    pointer-events: none;
}

.particle {
    position: absolute;
    width: 2px;
    height: 2px;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0) rotate(0deg); opacity: 1; }
    50% { transform: translateY(-20px) rotate(180deg); opacity: 0.5; }
}

.dev-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 10;
}

.dev-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.dev-action-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(15px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 20px;
    padding: 2.5rem;
    text-align: center;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    color: white;
    text-decoration: none;
    display: block;
    height: 100%;
    min-height: 280px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: relative;
    z-index: 10;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
}

.dev-action-card:hover {
    transform: translateY(-10px) translateZ(20px) scale(1.02);
    color: white;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    border-color: rgba(255, 255, 255, 0.5);
    position: relative;
    overflow: hidden;
}

.dev-action-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -150%;
    width: 200%;
    height: 200%;
    background: linear-gradient(
        45deg,
        transparent 30%,
        rgba(255, 255, 255, 0.1) 40%,
        rgba(255, 255, 255, 0.3) 50%,
        rgba(255, 255, 255, 0.1) 60%,
        transparent 70%
    );
    transform: rotate(45deg);
    transition: left 0.6s ease-out;
    pointer-events: none;
    z-index: 2;
    opacity: 0;
}

.dev-action-card:hover::before {
    left: 50%;
    opacity: 1;
}

.pulse-effect {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.1);
    transform: translate(-50%, -50%);
    animation: pulse 2s infinite;
    z-index: -1;
}

@keyframes pulse {
    0% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    50% { transform: translate(-50%, -50%) scale(1.05); opacity: 0.7; }
    100% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
}

.stats-compact-card {
    background: rgba(255, 255, 255, 0.08);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
    color: white;
    min-height: 100px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.stats-compact-card:hover {
    background: rgba(255, 255, 255, 0.12);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.danger-zone {
    background: linear-gradient(135deg, #e74c3c, #c0392b);
    border: 2px solid #e74c3c;
    box-shadow: 0 0 30px rgba(231, 76, 60, 0.3);
}

.admin-zone {
    background: linear-gradient(135deg, #f39c12, #e67e22);
    border: 2px solid #f39c12;
    box-shadow: 0 0 30px rgba(243, 156, 18, 0.3);
}

.user-zone {
    background: linear-gradient(135deg, #3498db, #2980b9);
    border: 2 solid #3498db;
    box-shadow: 0 0 30px rgba(52, 152, 219, 0.3);
}

.survey-zone {
    background: linear-gradient(135deg, #27ae60, #229954);
    border: 2px solid #27ae60;
    box-shadow: 0 0 30px rgba(39, 174, 96, 0.3);
}

.translation-zone {
    background: linear-gradient(135deg, #9b59b6, #8e44ad);
    border: 2px solid #9b59b6;
    box-shadow: 0 0 30px rgba(155, 89, 182, 0.3);
}

.survey-zone:hover {
    box-shadow: 0 0 50px rgba(39, 174, 96, 0.5);
}

.admin-zone:hover {
    box-shadow: 0 0 50px rgba(243, 156, 18, 0.5);
}

.user-zone:hover {
    box-shadow: 0 0 50px rgba(52, 152, 219, 0.5);
}

.translation-zone:hover {
    box-shadow: 0 0 50px rgba(142, 68, 173, 0.5);
}
</style>
</head>
<body>

<div class="bg-particles" id="particles"></div>

<div class="developer-dashboard">
    <div class="container-fluid px-4 pt-3 pb-5" style="position: relative; z-index: 10;">
        <!-- Header with logout -->
        <div class="row mb-3">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold text-danger">
                    <i class="bi bi-code-slash"></i> Developer Portal
                </h1>
                <p class="text-light">Welcome, {{ Auth::guard('developer')->user()->name }}</p>
            </div>
            <div class="col-md-6 text-end">
                <form method="POST" action="{{ route('developer.logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light">
                        <i class="bi bi-power"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Management Actions (Highlighted) -->
        <div class="row g-5 mb-5">
            <div class="col-12">
                <h2 class="text-center mb-5 text-warning display-5 fw-bold">
                    <i class="bi bi-gear-wide-connected"></i> SYSTEM MANAGEMENT
                </h2>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-5">
                <div class="dev-action-card survey-zone" style="cursor: pointer; min-height: 280px; transform: scale(1.05);" onclick="showSbuModal('surveys')">
                    <div>
                        <i class="bi bi-clipboard-data" style="font-size: 4rem; margin-bottom: 1.5rem;"></i>
                        <h4 class="fw-bold mb-3">Survey Management</h4>
                        <p class="mb-3" style="font-size: 1.1rem;">View, Edit & Delete All Surveys</p>
                        <div class="badge bg-light text-dark px-3 py-2 mb-3">
                            <i class="bi bi-filter"></i> Filter by SBU
                        </div>
                        <div class="pulse-effect"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-5">
                <div class="dev-action-card admin-zone" style="cursor: pointer; min-height: 280px; transform: scale(1.05);" onclick="showSbuModal('admins')">
                    <div>
                        <i class="bi bi-shield-lock" style="font-size: 4rem; margin-bottom: 1.5rem;"></i>
                        <h4 class="fw-bold mb-3">Admin Management</h4>
                        <p class="mb-3" style="font-size: 1.1rem;">Manage Admin Accounts & Permissions</p>
                        <div class="badge bg-light text-dark px-3 py-2 mb-3">
                            <i class="bi bi-filter"></i> Filter by SBU
                        </div>
                        <div class="pulse-effect"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-5">
                <div class="dev-action-card user-zone" style="cursor: pointer; min-height: 280px; transform: scale(1.05);" onclick="showSbuModal('users')">
                    <div>
                        <i class="bi bi-people" style="font-size: 4rem; margin-bottom: 1.5rem;"></i>
                        <h4 class="fw-bold mb-3">User Management</h4>
                        <p class="mb-3" style="font-size: 1.1rem;">Control All User Accounts</p>
                        <div class="badge bg-light text-dark px-3 ">
                            <i class="bi bi-filter"></i> Filter by SBU
                        </div>
                        <div class="pulse-effect"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Developer Tools Section -->
        <div class="row g-4 mb-5">
            <div class="col-12">
                <h2 class="text-center mb-5 text-info display-5 fw-bold">
                    <i class="bi bi-tools"></i> DEVELOPER TOOLS
                </h2>
            </div>
            
            <div class="col-lg-6 col-md-8 mx-auto">
                <a href="{{ route('developer.translations.index') }}" class="dev-action-card translation-zone text-decoration-none">
                    <div>
                        <i class="bi bi-translate" style="font-size: 4rem; margin-bottom: 1.5rem;"></i>
                        <h4 class="fw-bold mb-3">Translation Management</h4>
                        <p class="mb-3" style="font-size: 1.1rem;">Manage Multi-language Content</p>
                        <div class="badge bg-light text-dark px-3 py-2 mb-3">
                            <i class="bi bi-database"></i> Database-driven
                        </div>
                        <div class="pulse-effect"></div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Compact Stats Overview -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <h5 class="text-center mb-4 text-white">
                    <i class="bi bi-bar-chart"></i> System Statistics
                </h5>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="stats-compact-card text-center p-3">
                    <i class="bi bi-clipboard-data text-success mb-2" style="font-size: 1.8rem;"></i>
                    <h6 class="fw-bold text-white">{{ $stats['total_surveys'] }}</h6>
                    <small class="text-white-50">Total Surveys</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="stats-compact-card text-center p-3">
                    <i class="bi bi-shield-lock text-warning mb-2" style="font-size: 1.8rem;"></i>
                    <h6 class="fw-bold text-white">{{ $stats['total_admins'] }}</h6>
                    <small class="text-white-50">Total Admins</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="stats-compact-card text-center p-3">
                    <i class="bi bi-people text-info mb-2" style="font-size: 1.8rem;"></i>
                    <h6 class="fw-bold text-white">{{ $stats['total_users'] }}</h6>
                    <small class="text-white-50">Total Users</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="stats-compact-card text-center p-3">
                    <i class="bi bi-graph-up text-danger mb-2" style="font-size: 1.8rem;"></i>
                    <h6 class="fw-bold text-white">{{ $stats['total_responses'] }}</h6>
                    <small class="text-white-50">Total Responses</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="stats-compact-card text-center p-3">
                    <i class="bi bi-check-circle text-success mb-2" style="font-size: 1.8rem;"></i>
                    <h6 class="fw-bold text-white">{{ $stats['active_surveys'] }}</h6>
                    <small class="text-white-50">Active Surveys</small>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6">
                <div class="stats-compact-card text-center p-3">
                    <i class="bi bi-person-check text-primary mb-2" style="font-size: 1.8rem;"></i>
                    <h6 class="fw-bold text-white">{{ $stats['active_users'] }}</h6>
                    <small class="text-white-50">Active Users</small>
                </div>
            </div>
        </div>

        <!-- Warning Notice -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-shield-exclamation"></i>
                    <strong>Developer Access:</strong> You have unrestricted access to all system components. Use with extreme caution.
                </div>
            </div>
        </div>    </div>
</div>

<!-- SBU Selection Modal -->
<div class="modal fade" id="sbuSelectionModal" tabindex="-1" aria-labelledby="sbuSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: rgba(26, 26, 46, 0.95); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); color: white;">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title text-warning" id="sbuSelectionModalLabel">
                    <i class="bi bi-building"></i> Select SBU
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="text-light mb-4">Choose which Strategic Business Unit you want to manage:</p>
                
                <div class="row g-3">
                    <div class="col-6">
                        <div class="sbu-option-card" onclick="navigateToManagement('FDC')" style="background: linear-gradient(135deg, #e74c3c, #c0392b); border-radius: 12px; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.3s ease; border: 2px solid #e74c3c;">
                            <i class="bi bi-building display-4 mb-3 text-white"></i>
                            <h4 class="fw-bold text-white">FDC</h4>
                            <p class="mb-0 text-white-50">Fast Distribution</p>
                            <small class="text-white-50 mt-2 d-block">
                                <i class="bi bi-arrow-right"></i> View FDC Records
                            </small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="sbu-option-card" onclick="navigateToManagement('FUI')" style="background: linear-gradient(135deg, #3498db, #2980b9); border-radius: 12px; padding: 2rem; text-align: center; cursor: pointer; transition: all 0.3s ease; border: 2px solid #3498db;">
                            <i class="bi bi-building display-4 mb-3 text-white"></i>
                            <h4 class="fw-bold text-white">FUI</h4>
                            <p class="mb-0 text-white-50">Fast Unimerchant</p>
                            <small class="text-white-50 mt-2 d-block">
                                <i class="bi bi-arrow-right"></i> View FUI Records
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="button" class="btn btn-outline-light" onclick="navigateToManagement('ALL')">
                        <i class="bi bi-list"></i> View All (No Filter)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Create floating particles
function createParticles() {
    const particlesContainer = document.getElementById('particles');
    const particleCount = 50;
    
    for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.left = Math.random() * 100 + '%';
        particle.style.top = Math.random() * 100 + '%';
        particle.style.animationDelay = Math.random() * 6 + 's';
        particle.style.animationDuration = (Math.random() * 3 + 4) + 's';
        particlesContainer.appendChild(particle);
    }
}

// Variables to store current management type and modal instance
let currentManagementType = '';
let sbuModal;

// Show SBU selection modal
function showSbuModal(managementType) {
    currentManagementType = managementType;
    sbuModal = new bootstrap.Modal(document.getElementById('sbuSelectionModal'));
    sbuModal.show();
}

// Navigate to management page with SBU
function navigateToManagement(sbu) {
    if (sbuModal) {
        sbuModal.hide();
    }
    
    let url = '';
    if (sbu === 'ALL') {
        // No SBU filter - show all records
        if (currentManagementType === 'surveys') {
            url = '{{ route("developer.surveys") }}';
        } else if (currentManagementType === 'admins') {
            url = '{{ route("developer.admins") }}';
        } else if (currentManagementType === 'users') {
            url = '{{ route("developer.users") }}';
        }
    } else {
        // With SBU filter
        if (currentManagementType === 'surveys') {
            url = '{{ route("developer.surveys") }}' + '?sbu=' + sbu;
        } else if (currentManagementType === 'admins') {
            url = '{{ route("developer.admins") }}' + '?sbu=' + sbu;
        } else if (currentManagementType === 'users') {
            url = '{{ route("developer.users") }}' + '?sbu=' + sbu;
        }
    }
    
    window.location.href = url;
}

// Add hover effects to SBU option cards
document.addEventListener('DOMContentLoaded', function() {
    createParticles();
    
    const dashboard = document.querySelector('.developer-dashboard');
    setTimeout(() => {
        dashboard.style.opacity = '1';
    }, 100);
    
    // Add hover effects to SBU cards when modal is shown
    document.getElementById('sbuSelectionModal').addEventListener('shown.bs.modal', function() {
        const sbuCards = document.querySelectorAll('.sbu-option-card');
        sbuCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.05)';
                this.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.5)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
                this.style.boxShadow = 'none';
            });
        });
    });
});
</script>

</body>
</html>
