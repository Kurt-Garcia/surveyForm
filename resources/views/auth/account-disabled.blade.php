@extends('layouts.app-welcome')

@section('content')
<div class="account-disabled-container mt-4">    
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-90">
            <div class="col-lg-7 col-md-9 col-sm-11">
                <div class="error-card">
                    <!-- Icon Section -->
                    <div class="icon-section">
                        <div class="icon-wrapper">
                            <div class="lock-icon">
                                <i class="bi bi-shield-lock-fill"></i>
                            </div>
                            <div class="pulse-ring"></div>
                        </div>
                    </div>

                    <!-- Content Section -->
                    <div class="content-section">
                        <h1 class="error-title">Account Suspended</h1>
                        <p class="error-subtitle">Your access has been temporarily restricted</p>
                        
                        <div class="status-badge">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span>Access Denied</span>
                        </div>

                        <div class="info-box">
                            <h6><i class="bi bi-info-circle-fill me-2"></i>What happened?</h6>
                            <ul class="info-list">
                                <li><i class="bi bi-x-circle"></i> Login access suspended</li>
                                <li><i class="bi bi-x-circle"></i> Active sessions terminated</li>
                                <li><i class="bi bi-x-circle"></i> System features restricted</li>
                            </ul>
                        </div>

                        <div class="help-section">
                            <div class="help-card">
                                <i class="bi bi-headset"></i>
                                <div>
                                    <h6>Need Assistance?</h6>
                                    <p>Contact your system administrator to reactivate your account</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <form method="POST" action="{{ route('logout') }}" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-box-arrow-right"></i>
                                    Back to Login
                                </button>
                            </form>
                            
                            <a href="{{ route('welcome') }}" class="btn btn-outline">
                                <i class="bi bi-house"></i>
                                Return Home
                            </a>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer">
                        <div class="status-indicator">
                            <span class="status-dot"></span>
                            <span class="status-text">Account Status: <strong>Disabled</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Container and Background */
.account-disabled-container {
    min-height: 100vh;
    position: relative;
    background-color: var(--background-color);
    overflow: hidden;
}

.min-vh-90 {
    min-height: 90vh;
}

/* Error Card */
.error-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 24px;
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(255, 255, 255, 0.2);
    overflow: hidden;
    animation: slideUp 0.8s ease-out;
    border: none;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Icon Section */
.icon-section {
    text-align: center;
    padding: 2rem 2rem 1rem;
    background: linear-gradient(135deg, #800000, #a52a2a);
    position: relative;
}

.icon-wrapper {
    position: relative;
    display: inline-block;
}

.lock-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    animation: bounce 2s infinite;
}

.lock-icon i {
    font-size: 2.5rem;
    color: white;
}

.pulse-ring {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100px;
    height: 100px;
    border: 3px solid rgba(255, 255, 255, 0.4);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}

@keyframes pulse {
    0% {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1.3);
        opacity: 0;
    }
}

/* Content Section */
.content-section {
    padding: 1.5rem;
    text-align: center;
}

.error-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-color);
    margin-bottom: 0.5rem;
}

.error-subtitle {
    font-size: 1.1rem;
    color: #718096;
    margin-bottom: 2rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: linear-gradient(135deg, #800000, #a52a2a);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-weight: 600;
    margin-bottom: 2rem;
    animation: glow 2s infinite alternate;
}

@keyframes glow {
    from { box-shadow: 0 0 20px rgba(128, 0, 0, 0.3); }
    to { box-shadow: 0 0 30px rgba(128, 0, 0, 0.6); }
}

.info-box {
    background: #f7fafc;
    border-left: 4px solid var(--primary-color);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    text-align: left;
}

.info-box h6 {
    color: #2d3748;
    margin-bottom: 1rem;
    font-weight: 600;
}

.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-list li {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
    color: #4a5568;
}

.info-list li i {
    color: #e53e3e;
    font-size: 0.9rem;
}

.help-section {
    margin-bottom: 2rem;
}

.help-card {
    background: #f7fafc;
    border-left: 4px solid var(--primary-color);
    color: #2d3748;
    padding: 1.5rem;
    border-radius: 16px;
    display: flex;
    align-items: center;
    gap: 1rem;
    text-align: left;
}

.help-card i {
    font-size: 2rem;
    opacity: 0.9;
}

.help-card h6 {
    margin: 0 0 0.5rem 0;
    font-weight: 600;
}

.help-card p {
    margin: 0;
    opacity: 0.9;
    font-size: 0.9rem;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 1rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    border: none;
    text-decoration: none;
    width: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
}

.btn-outline {
    background: transparent;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
    box-shadow: none;
}

.btn-outline:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

/* Card Footer */
.card-footer {
    background: #f8fafc;
    padding: 1.5rem 2rem;
    border-top: none;
    text-align: center;
}

.status-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    color: #4a5568;
    font-size: 0.9rem;
}

.status-dot {
    width: 8px;
    height: 8px;
    background: #e53e3e;
    border-radius: 50%;
    animation: blink 1.5s infinite;
}

@keyframes blink {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0.3; }
}

.status-text strong {
    color: #e53e3e;
}

/* Responsive Design */
@media (max-width: 768px) {
    .error-title {
        font-size: 2rem;
    }
    
    .content-section {
        padding: 1.5rem;
    }
    
    .icon-section {
        padding: 2rem 1.5rem 1rem;
    }
    
    .action-buttons {
        gap: 0.75rem;
    }
    
    .btn {
        padding: 0.875rem 1.5rem;
        font-size: 0.95rem;
    }
}

@media (max-width: 480px) {
    .error-title {
        font-size: 1.75rem;
    }
    
    .help-card {
        flex-direction: column;
        text-align: center;
        gap: 0.75rem;
    }
    
    .help-card i {
        font-size: 1.5rem;
    }
}
</style>
@endsection
