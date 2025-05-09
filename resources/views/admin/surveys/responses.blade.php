@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <div class="row justify-content-start">
        <div class="col-12">
            <!-- Survey Title Section -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="display-6 fw-bold mb-3 text-color">{{ strtoupper($survey->title) }} - RESPONSES</h2>
                        <div class="d-flex gap-3 text-muted">
                            <div><i class="bi bi-people-fill me-2"></i>{{ $responses->count() }} Respondents</div>
                            <div><i class="bi bi-star-fill me-2"></i>Average Rating: {{ number_format($avgRecommendation, 1) }}/10</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="text-color fw-bold mb-3"><i class="bi bi-bar-chart-fill text-primary me-2"></i>Response Rate</h5>
                            <div class="display-6 text-primary mb-2">{{ $responses->count() }}</div>
                            <p class="text-muted">Total responses received</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="text-color fw-bold mb-3"><i class="bi bi-calendar-fill text-success me-2"></i>Latest Response</h5>
                            <div class="display-6 text-success mb-2">
                                {{ $responses->first() ? $responses->first()->date->format('M d') : 'N/A' }}
                            </div>
                            <p class="text-muted">Last submission date</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="text-color fw-bold mb-3"><i class="bi bi-person-fill text-info me-2"></i>Unique Respondents</h5>
                            <div class="display-6 text-info mb-2">{{ $responses->unique('account_name')->count() }}</div>
                            <p class="text-muted">Individual participants</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Question Statistics -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h4 class="text-color mb-0 fw-bold">Response Summary</h4>
                        </div>
                        <div class="card-body">
                            @foreach($questions as $question)
                                <div class="question-stats mb-5">
                                    <h5 class="text-color fw-bold mb-3">{{ $question->text }}</h5>
                                    @php
                                        $stats = $statistics[$question->id];
                                        $total = array_sum($stats['responses']);
                                    @endphp
                                    
                                    @if($question->type === 'radio' || $question->type === 'star')
                                        <div class="stats-bars">
                                            @foreach($stats['responses'] as $response => $count)
                                                <div class="stat-row mb-2">
                                                    <div class="d-flex justify-content-between mb-1">
                                                        <span>{{ $response == 1 ? 'Poor' : ($response == 2 ? 'Need Improvement' : ($response == 3 ? 'Satisfactory' : ($response == 4 ? 'Very Satisfactory' : 'Excellent'))) }}</span>
                                                        <span class="text-muted">{{ $count }} responses</span>
                                                    </div>
                                                    <div class="progress" style="height: 25px;">
                                                        <div class="progress-bar {{ $response == 1 ? 'bg-danger' : ($response == 2 ? 'bg-warning' : ($response == 3 ? 'bg-info' : ($response == 4 ? 'bg-primary' : 'bg-success'))) }}" 
                                                             role="progressbar" 
                                                             style="width: {{ ($count / $total) * 100 }}%">
                                                            {{ round(($count / $total) * 100) }}%
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Individual Responses Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm border-0" id="individual-responses">
                        <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div class="d-flex align-items-center gap-3 flex-grow-1">
                                <h4 class="text-color me-4 mb-0 fw-bold ms-0 ms-md-3">Individual Responses</h4>
                                <div class="input-group w-auto">
                                    <label for="entriesPerPage" class="me-2 align-self-center small text-muted">Show</label>
                                    <select id="entriesPerPage" class="form-select form-select-sm w-auto me-2">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <span class="align-self-center small text-muted">entries</span>
                                </div>
                            </div>
                            <div class="input-group w-auto mt-3 mt-md-0">
                                <input type="text" id="searchInput" class="form-control form-control-sm ms-3" placeholder="Search by account name or type...">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" id="customResponsesTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Account Name</th>
                                            <th>Account Type</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="responsesTableBody">
                                        @foreach($responses as $response)
                                            <tr class="response-row">
                                                <td>{{ $response->account_name }}</td>
                                                <td>{{ $response->account_type }}</td>
                                                <td>{{ $response->date->format('M d, Y') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.surveys.responses.show', ['survey' => $survey->id, 'account_name' => $response->account_name]) }}" 
                                                       class="btn btn-sm" 
                                                       style="border-color: var(--primary-color); color: var(--primary-color)"
                                                       onmouseover="this.style.backgroundColor='var(--secondary-color)'; this.style.color='white'"
                                                       onmouseout="this.style.borderColor='var(--primary-color)'; this.style.backgroundColor='white'; this.style.color='var(--primary-color)'">
                                                        <i class="bi bi-eye-fill me-1"></i>View Details
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination Status -->
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <div id="paginationStatus" class="text-muted small"></div>
                            </div>
                            <!-- Pagination -->
                            <nav>
                                <ul class="pagination justify-content-end mt-3" id="pagination"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const rows = Array.from(document.querySelectorAll('#responsesTableBody tr'));
                    const searchInput = document.getElementById('searchInput');
                    const pagination = document.getElementById('pagination');
                    const paginationStatus = document.getElementById('paginationStatus');
                    let currentPage = 1;
                    let rowsPerPage = 10;
                    const entriesPerPageSelect = document.getElementById('entriesPerPage');
                    
                    function renderTable(filteredRows) {
                        const start = (currentPage - 1) * rowsPerPage;
                        const end = start + rowsPerPage;
                        rows.forEach(row => row.style.display = 'none');
                        filteredRows.slice(start, end).forEach(row => row.style.display = '');
                        updatePaginationStatus(filteredRows.length, start, Math.min(end, filteredRows.length));
                    }
                    
                    function renderPagination(filteredRows) {
                        const pageCount = Math.ceil(filteredRows.length / rowsPerPage);
                        let html = '';
                        for (let i = 1; i <= pageCount; i++) {
                            html += `<li class="page-item${i === currentPage ? ' active' : ''}"><a class="page-link" href="#">${i}</a></li>`;
                        }
                        pagination.innerHTML = html;
                        Array.from(pagination.querySelectorAll('a')).forEach((a, idx) => {
                            a.addEventListener('click', function(e) {
                                e.preventDefault();
                                currentPage = idx + 1;
                                renderTable(filteredRows);
                                renderPagination(filteredRows);
                            });
                        });
                    }
                    
                    function updatePaginationStatus(total, start, end) {
                        if (total === 0) {
                            paginationStatus.textContent = 'Showing 0 entries';
                        } else {
                            paginationStatus.textContent = `Showing ${total === 0 ? 0 : start + 1} to ${end} of ${total} entries`;
                        }
                    }
                    
                    function filterRows() {
                        const query = searchInput.value.toLowerCase();
                        const filtered = rows.filter(row => {
                            return row.children[0].textContent.toLowerCase().includes(query) ||
                                   row.children[1].textContent.toLowerCase().includes(query);
                        });
                        currentPage = 1;
                        renderTable(filtered);
                        renderPagination(filtered);
                    }
                    entriesPerPageSelect.addEventListener('change', function() {
                        rowsPerPage = parseInt(this.value, 10);
                        currentPage = 1;
                        filterRows();
                    });
                    searchInput.addEventListener('input', filterRows);
                    filterRows();
                });
            </script>
            <style>
                #individual-responses .table th, #individual-responses .table td {
                    vertical-align: middle;
                }
                #individual-responses .table-hover tbody tr:hover {
                    background-color: #f8f9fa;
                }
                #individual-responses .pagination .page-item.active .page-link {
                    background-color: var(--primary-color);
                    border-color: var(--primary-color);
                    color: #fff;
                }
                #individual-responses .pagination .page-link {
                    color: var(--primary-color);
                }
            </style>
        </div>
    </div>
</div>
@endsection

<style>
    .text-color {
        color: var(--text-color);
    }

    .question-stats {
        border-bottom: 1px solid #eee;
        padding-bottom: 2rem;
    }

    .question-stats:last-child {
        border-bottom: none;
    }

    .progress {
        border-radius: 100px;
        background-color: #f8f9fa;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
        height: 25px !important;
    }

    .progress-bar {
        border-radius: 100px;
        transition: all 0.4s ease;
        background-image: linear-gradient(45deg, rgba(255,255,255,.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,.15) 50%, rgba(255,255,255,.15) 75%, transparent 75%, transparent);
        background-size: 1rem 1rem;
        animation: progress-bar-stripes 1s linear infinite;
    }

    .stat-row:hover .progress-bar {
        filter: brightness(1.1);
        transform: scaleX(1.01);
    }
    @keyframes progress-bar-stripes {
        from { background-position: 1rem 0; }
        to { background-position: 0 0; }
    }

    .stat-row {
        transition: all 0.3s ease;
        padding: 10px;
        border-radius: 8px;
    }

    .stat-row:hover {
        background-color: #f8f9fa;
    }

    .card {
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-5px);
    }
    
    .response-row {
        transition: all 0.2s ease;
    }
    
    .response-row:hover {
        background-color: #f8f9fa;
    }
</style>