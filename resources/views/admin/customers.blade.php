@extends('layouts.app')

@section('content')
<head>
    @vite(['resources/js/app.js'])
    <style>
        /* DataTables Export Button Styling */
        div.dt-buttons {
            margin-bottom: 1rem;
        }

        div.dt-button-collection {
            width: auto !important;
        }

        .dt-button {
            color: var(--text-color) !important;
            background-color: white !important;
            border: 1px solid #dee2e6 !important;
            padding: 8px 22px !important;
            border-radius: 20px !important;
            margin: 0 6px 8px 0 !important;
            font-weight: 600;
            transition: background 0.3s, color 0.3s, box-shadow 0.3s, transform 0.2s;
            outline: none;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }

        .dt-button:hover, .dt-button:focus {
            background: linear-gradient(90deg, var(--secondary-color) 0%, var(--primary-color) 100%) !important;
            color: #fff !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.13);
            transform: translateY(-2px) scale(1.04);
            border: none;
        }

        .dt-button:active {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transform: scale(0.98);
            color: #fff !important;
        }
        
        /* DataTables General Styling */
        .dataTables_wrapper .dataTables_length, 
        .dataTables_wrapper .dataTables_filter, 
        .dataTables_wrapper .dataTables_info, 
        .dataTables_wrapper .dataTables_processing, 
        .dataTables_wrapper .dataTables_paginate {
            color: var(--text-color);
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%) !important;
            color: #fff !important;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
            transform: translateY(-1px) scale(1.03);
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            background: #f8f9fa !important;
            color: #bdbdbd !important;
            border: 1px solid #e0e0e0 !important;
            box-shadow: none;
            cursor: not-allowed;
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button:active {
            transform: scale(0.97);
        }
        
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            cursor: pointer;
            background: linear-gradient(90deg, #fff 0%, #f0f4fa 100%);
            border: 1px solid var(--primary-color);
            color: var(--primary-color) !important;
            border-radius: 18px !important;
            margin: 0 3px;
            padding: 6px 16px;
            font-weight: 500;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
            transition: background 0.3s, color 0.3s, box-shadow 0.3s, transform 0.2s;
        }

        table.dataTable tbody tr.even {
            background-color: #f8f9fa;
        }
        
        table.dataTable tbody tr.odd {
            background-color: white;
        }
    </style>
</head>

<div class="container-fluid px-4 mt-4">
    <div class="row justify-content-start">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h3 class="mb-0 fw-bold">Customer List</h3>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <table id="customersTable" class="display" style="width:100%">
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
            dom: 'Blfrtip', // B-buttons, l-length, f-filter, r-processing, t-table, i-info, p-pagination
            buttons: [
                {
                    extend: 'copy',
                    text: '<i class="bi bi-clipboard me-1"></i> Copy',
                    className: 'btn btn-sm',
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7] // Export only important columns
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