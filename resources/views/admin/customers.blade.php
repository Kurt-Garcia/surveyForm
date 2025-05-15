@extends('layouts.app')

@section('content')
<head>
    @vite(['resources/js/app.js'])
    <style>
        /* Modern Card Styling */
        .card {
            border: none;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 30px rgba(0,0,0,0.1);
        }

        .card-header {
            border-bottom: none;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 12px 12px 0 0 !important;
        }

        .card-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .card-body {
            padding: 2rem;
        }

        /* DataTable Styling */
        .dataTables_wrapper {
            margin-top: 1rem;
            width: 100%;
            overflow-x: auto;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dt-buttons,
        .dataTables_wrapper .dataTables_filter {
            display: inline-block;
            vertical-align: middle;
            margin-bottom: 0;
        }
        .dataTables_wrapper .dataTables_length {
            margin-right: 1.5rem;
        }
        .dataTables_wrapper .dt-buttons {
            margin-right: 1.5rem;
        }
        .dataTables_wrapper .dataTables_filter {
            float: right;
        }
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            margin-bottom: 0;
        }
        .dataTables_wrapper .dt-buttons {
            display: inline-flex;
            align-items: center;
            vertical-align: middle;
            margin-bottom: 0;
        }
        .dataTables_wrapper .dataTables_length {
            margin-right: 1.5rem;
        }
        .dataTables_wrapper .dt-buttons {
            margin-right: 1.5rem;
        }
        .dataTables_wrapper .dataTables_filter {
            float: right;
        }
        @media (max-width: 768px) {
            .dt-buttons,
            .dataTables_length {
                justify-content: center;
                margin-bottom: 1rem;
                width: 100%;
            }
            .dataTables_filter {
                width: 100%;
                text-align: center;
                margin-bottom: 1rem;
            }
        }
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            margin-bottom: 0;
        }
        .dataTables_wrapper .dt-buttons {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        table.dataTable {
            border-collapse: separate !important;
            border-spacing: 0 8px !important;
            margin-top: 1rem !important;
            width: 100% !important;
            min-width: 1500px; /* Ensures table maintains minimum width for all columns */
        }

        table.dataTable thead th {
            background: #f8f9fa;
            font-weight: 600;
            padding: 15px;
            border: none;
            color: var(--text-color);
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        table.dataTable tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-top: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
        }

        table.dataTable tbody tr {
            background: white;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 8px;
        }

        table.dataTable tbody tr:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        /* Export Buttons */
        div.dt-buttons {
            margin-right: 20px;
            margin-bottom: 1.5rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .dt-button {
            background: white !important;
            color: var(--text-color) !important;
            padding: 8px 20px !important;
            border-radius: 25px !important;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .dt-button:hover {
            background: var(--primary-color) !important;
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(var(--primary-color-rgb), 0.2);
        }

        .dt-button:active {
            transform: translateY(0);
        }

        /* Search and Length Controls */
        .dataTables_filter input {
            border: 2px solid #e0e0e0;
            border-radius: 25px;
            padding: 8px 20px;
            width: 250px;
            transition: all 0.3s;
        }

        .dataTables_filter input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(var(--primary-color-rgb), 0.1);
        }

        .dataTables_length select {
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            padding: 5px 15px;
            margin: 0 5px;
            transition: all 0.3s;
            min-width: 120px;
        }

        .dataTables_length select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(var(--primary-color-rgb), 0.1);
        }

        /* Pagination */
        .dataTables_paginate {
            margin-top: 1.5rem !important;
        }

        .paginate_button {
            border: none !important;
            border-radius: 20px !important;
            padding: 8px 16px !important;
            margin: 0 3px !important;
            color: var(--text-color) !important;
            font-weight: 500;
            transition: all 0.3s ease !important;
        }

        .paginate_button.current,
        .paginate_button:hover {
            background: var(--primary-color) !important;
            color: white !important;
            box-shadow: 0 5px 15px rgba(var(--primary-color-rgb), 0.2);
        }

        .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-body {
                padding: 1rem;
            }

            .dt-buttons {
                justify-content: center;
                margin-bottom: 1rem;
            }

            .dataTables_filter input {
                width: 100%;
                margin-bottom: 1rem;
            }

            .dataTables_length,
            .dataTables_filter {
                text-align: center;
                margin-bottom: 1rem;
            }

            table.dataTable tbody td {
                padding: 0.75rem;
                font-size: 0.9rem;
                white-space: nowrap;
            }

            .dataTables_wrapper {
                margin: 0 -1rem;
                padding: 0 1rem;
                width: calc(100% + 2rem);
            }
        }
    </style>
</head>

<div class="container-fluid px-4 mt-4">
    <div class="row justify-content-start">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3><i class="bi bi-people me-2"></i>Customer List</h3>
                    <a href="{{ route('admin.dashboard') }}" class="close-button" onclick="window.close();">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-1"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <table id="customersTable" class="table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>MDCODE</th>
                                <th>CUSTCODE</th>
                                <th>CUSTNAME</th>
                                <th>CONTACTCELLNUMBER</th>
                                <th>CONTACTPERSON</th>
                                <th>CONTACTLANDLINE</th>
                                <th>ADDRESS</th>
                                <th>FREQUENCYCATEGORY</th>
                                <th>MCPDAY</th>
                                <th>MCPSCHEDULE</th>
                                <th>GEOLOCATION</th>
                                <th>LASTUPDATED</th>
                                <th>LASTPURCHASE</th>
                                <th>LATITUDE</th>
                                <th>LONGITUDE</th>
                                <th>STOREIMAGE</th>
                                <th>SYNCSTAT</th>
                                <th>DATES_TAMP</th>
                                <th>TIME_STAMP</th>
                                <th>ISLOCKON</th>
                                <th>PRICECODE</th>
                                <th>STOREIMAGE2</th>
                                <th>CUSTTYPE</th>
                                <th>ISVISIT</th>
                                <th>DEFAULTORDTYPE</th>
                                <th>CITYMUNCODE</th>
                                <th>REGION</th>
                                <th>PROVINCE</th>
                                <th>MUNICIPALITY</th>
                                <th>BARANGAY</th>
                                <th>AREA</th>
                                <th>WAREHOUSE</th>
                                <th>KASOSYO</th>
                                <th>EMAIL</th>
                                <th>created_at</th>
                                <th>updated_at</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $customer->MDCODE ?? '-' }}</td>
                                    <td>{{ $customer->CUSTCODE ?? '-' }}</td>
                                    <td>{{ $customer->CUSTNAME ?? '-' }}</td>
                                    <td>{{ $customer->CONTACTCELLNUMBER ?? '-' }}</td>
                                    <td>{{ $customer->CONTACTPERSON ?? '-' }}</td>
                                    <td>{{ $customer->CONTACTLANDLINE ?? '-' }}</td>
                                    <td>{{ $customer->ADDRESS ?? '-' }}</td>
                                    <td>{{ $customer->FREQUENCYCATEGORY ?? '-' }}</td>
                                    <td>{{ $customer->MCPDAY ?? '-' }}</td>
                                    <td>{{ $customer->MCPSCHEDULE ?? '-' }}</td>
                                    <td>{{ $customer->GEOLOCATION ?? '-' }}</td>
                                    <td>{{ $customer->LASTUPDATED ?? '-' }}</td>
                                    <td>{{ $customer->LASTPURCHASE ?? '-' }}</td>
                                    <td>{{ $customer->LATITUDE ?? '-' }}</td>
                                    <td>{{ $customer->LONGITUDE ?? '-' }}</td>
                                    <td>{{ $customer->STOREIMAGE ?? '-' }}</td>
                                    <td>{{ $customer->SYNCSTAT ?? '-' }}</td>
                                    <td>{{ $customer->DATES_TAMP ?? '-' }}</td>
                                    <td>{{ $customer->TIME_STAMP ?? '-' }}</td>
                                    <td>{{ $customer->ISLOCKON ?? '-' }}</td>
                                    <td>{{ $customer->PRICECODE ?? '-' }}</td>
                                    <td>{{ $customer->STOREIMAGE2 ?? '-' }}</td>
                                    <td>{{ $customer->CUSTTYPE ?? '-' }}</td>
                                    <td>{{ $customer->ISVISIT ?? '-' }}</td>
                                    <td>{{ $customer->DEFAULTORDTYPE ?? '-' }}</td>
                                    <td>{{ $customer->CITYMUNCODE ?? '-' }}</td>
                                    <td>{{ $customer->REGION ?? '-' }}</td>
                                    <td>{{ $customer->PROVINCE ?? '-' }}</td>
                                    <td>{{ $customer->MUNICIPALITY ?? '-' }}</td>
                                    <td>{{ $customer->BARANGAY ?? '-' }}</td>
                                    <td>{{ $customer->AREA ?? '-' }}</td>
                                    <td>{{ $customer->WAREHOUSE ?? '-' }}</td>
                                    <td>{{ $customer->KASOSYO ?? '-' }}</td>
                                    <td>{{ $customer->EMAIL ?? '-' }}</td>
                                    <td>{{ $customer->created_at ?? '-' }}</td>
                                    <td>{{ $customer->updated_at ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="37" class="text-center">No customers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#customersTable').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search customers..."
            },
            dom: '<"d-flex align-items-center justify-content-between mb-3"<"d-flex align-items-center gap-3"Bl>f>rtip', // B-buttons, l-length, f-filter
            buttons: [
                {
                    extend: 'collection',
                    text: '<i class="bi bi-download me-1"></i> Export',
                    className: 'btn btn-sm btn-primary',
                    autoClose: true,
                    buttons: [
                        {
                            extend: 'copy',
                            text: '<i class="bi bi-clipboard me-1"></i> Copy',
                            className: 'btn btn-sm',
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5, 6, 7]
                            }
                        },
                        {
                            extend: 'csv',
                            text: '<i class="bi bi-filetype-csv me-1"></i> CSV',
                            className: 'btn btn-sm',
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5, 6, 7]
                            },
                            title: 'Customer List'
                        },
                        {
                            extend: 'excel',
                            text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                            className: 'btn btn-sm',
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5, 6, 7]
                            },
                            title: 'Customer List'
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF',
                            className: 'btn btn-sm',
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5, 6, 7]
                            },
                            title: 'Customer List',
                            customize: function(doc) {
                                doc.defaultStyle.fontSize = 10;
                                doc.styles.tableHeader.fontSize = 11;
                                doc.styles.tableHeader.alignment = 'left';
                            }
                        },
                        {
                            extend: 'print',
                            text: '<i class="bi bi-printer me-1"></i> Print',
                            className: 'btn btn-sm',
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5, 6, 7]
                            },
                            title: 'Customer List'
                        }
                    ]
                }
            ],
            initComplete: function() {
                // Style the search input
                $('.dataTables_filter input').addClass('form-control');
                $('.dataTables_filter input').css({
                    'border-radius': '20px',
                    'padding-left': '15px',
                    'border-color': '#ced4da',
                    'outline': 'none',
                    'box-shadow': 'none'
                }).focus(function() {
                    $(this).css('border-color', 'var(--accent-color)');
                }).blur(function() {
                    $(this).css('border-color', '#ced4da');
                });
                
                // Style the length select
                $('.dataTables_length select').addClass('form-select');
                $('.dataTables_length select').css({
                    'border-radius': '20px',
                    'padding-left': '10px',
                    'border-color': '#ced4da'
                });
            }
        });
    });
</script>
@endsection