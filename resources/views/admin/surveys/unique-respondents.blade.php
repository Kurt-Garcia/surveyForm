@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12">
            <!-- Survey Title Section -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="display-6 fw-bold text-primary mb-3">{{ $survey->title }} - Unique Respondents</h2>
                        <div class="d-flex gap-3 text-muted">
                            <div><i class="bi bi-people-fill me-2"></i>{{ $responses->count() }} Unique Respondents</div>
                        </div>
                    </div>
                    <a href="{{ route('admin.surveys.responses.index', $survey) }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Summary
                    </a>
                </div>
            </div>

            <!-- Individual Responses Table -->
            <div class="card shadow-sm border-0" id="individual-responses">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold">Individual Responses</h4>
                    <div class="search-container w-100 w-md-50 w-lg-25">
                        <form method="GET" action="{{ route('admin.surveys.unique-respondents', $survey) }}">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control border-start-0" 
                                    value="{{ request('search') }}" 
                                    placeholder="Search by name, type or date..." 
                                    style="border-radius: 0 20px 20px 0;">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="responsesTable">
                            <thead>
                                <tr>
                                    <th>Account Name</th>
                                    <th>Account Type</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($responses as $response)
                                    <tr class="response-row">
                                        <td>{{ $response->account_name }}</td>
                                        <td>{{ $response->account_type }}</td>
                                        <td>{{ $response->date->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.surveys.responses.show', ['survey' => $survey->id, 'account_name' => $response->account_name]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye-fill me-1"></i>View Details
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchForm = document.querySelector('.search-container form');
                    const searchInput = searchForm.querySelector('input[name="search"]');
                    
                    // Auto-submit form after a short delay when typing
                    let timeout = null;
                    searchInput.addEventListener('input', function() {
                        clearTimeout(timeout);
                        timeout = setTimeout(() => {
                            searchForm.submit();
                        }, 500);
                    });
                });
            </script>
            @endpush

            <style>
            .search-container {
                max-width: 100%;
                transition: all 0.3s ease;
            }

            @media (min-width: 768px) {
                .search-container {
                    max-width: 50%;
                }
            }

            @media (min-width: 992px) {
                .search-container {
                    max-width: 300px;
                }
            }

            .card {
                transition: transform 0.2s;
            }

            .card:hover {
                transform: translateY(-5px);
            }
            </style>
        </div>
    </div>
</div>
@endsection