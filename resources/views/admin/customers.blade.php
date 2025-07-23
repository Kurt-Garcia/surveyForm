@extends('layouts.app')

@section('content')
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
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
            
            .dataTables_wrapper .row:last-child {
                display: flex;
                align-items: center;
                flex-direction: column;
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
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .dataTables_info {
            padding-top: 0 !important;
            display: flex;
            align-items: center;
        }

        .dataTables_wrapper .row:last-child {
            display: flex;
            align-items: center;
            margin-top: 1rem;
        }

        .dataTables_wrapper .dataTables_paginate,
        .dataTables_wrapper .dataTables_info {
            width: 50%;
            float: left;
            padding: 0;
            margin: 0;
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

        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_paginate,
            .dataTables_wrapper .dataTables_info {
                width: 100%;
                text-align: center;
                justify-content: center;
                margin: 0.5rem 0;
            }
            
            .dataTables_wrapper .row:last-child {
                flex-direction: column;
            }
            
            .dataTables_paginate .paginate_button {
                padding: 6px 12px !important;
            }
        }

        /* Action Buttons */
        .btn-sm.btn-primary {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 15px;
            transition: all 0.3s ease;
        }
        
        .btn-sm.btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
                                <th>CUSTCODE</th>
                                <th>CUSTNAME</th>
                                <th>ADDRESS</th>
                                <th>CONTACT PERSON</th>
                                <th>CONTACT#</th>
                                <th>EMAIL</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $customer->CUSTCODE ?? '-' }}</td>
                                    <td>{{ $customer->CUSTNAME ?? '-' }}</td>
                                    <td>{{ $customer->ADDRESS ?? '-' }}</td>
                                    <td>{{ $customer->CONTACTPERSON ?? '-' }}</td>
                                    <td>{{ $customer->CONTACTCELLNUMBER ?? '-' }}</td>
                                    <td>{{ $customer->EMAIL ?? '-' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary edit-customer" 
                                            data-id="{{ $customer->id ?? '' }}" 
                                            data-custcode="{{ $customer->CUSTCODE ?? '' }}" 
                                            data-custname="{{ $customer->CUSTNAME ?? '' }}" 
                                            data-phone="{{ $customer->CONTACTCELLNUMBER ?? '' }}" 
                                            data-email="{{ $customer->EMAIL ?? '' }}">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                    </td>
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

<!-- Edit Customer Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer Contact Information</h5>
                <button type="button" class="btn-close" id="closeModal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCustomerForm">
                    @csrf
                    <input type="hidden" id="customer_id" name="customer_id">
                    <div class="mb-3">
                        <label for="custcode" class="form-label">Customer Code</label>
                        <input type="text" class="form-control" id="custcode" name="custcode" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="custname" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="custname" name="custname" readonly autocomplete="name">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Contact Cell Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" required 
                               placeholder="09123456789 or +639123456789" autocomplete="tel">
                        <div class="invalid-feedback" id="phone-error"></div>
                        <small class="form-text text-muted">09 format: 11 characters | +639 format: 13 characters</small>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" maxlength="50" 
                               placeholder="Enter email address" autocomplete="email">
                        <div class="invalid-feedback" id="email-error"></div>
                        <small class="form-text text-muted">Maximum 50 characters</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelChanges">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveCustomerChanges">Save Changes</button>
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
                            },
                            action: function(e, dt, button, config) {
                                try {
                                    // Log the export activity
                                    $.ajax({
                                        url: '{{ route("admin.log.export") }}',
                                        method: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            export_type: 'copy',
                                            entity_type: 'customers',
                                            description: 'Customers list has been exported as a Copy'
                                        },
                                        async: false,
                                        success: function() {
                                            // Call the original action after successful logging
                                            $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config);
                                        }.bind(this),
                                        error: function(xhr, status, error) {
                                            console.error('Logging error:', error);
                                            // Still proceed with export even if logging fails
                                            $.fn.dataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config);
                                        }.bind(this)
                                    });
                                } catch (error) {
                                    console.error('Copy export error:', error);
                                    alert('Error copying data: ' + error.message);
                                }
                            }
                        },
                        {
                            extend: 'csv',
                            text: '<i class="bi bi-filetype-csv me-1"></i> CSV',
                            className: 'btn btn-sm',
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5, 6, 7]
                            },
                            title: 'Customer List',
                            action: function(e, dt, button, config) {
                                try {
                                    // Log the export activity
                                    $.ajax({
                                        url: '{{ route("admin.log.export") }}',
                                        method: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            export_type: 'csv',
                                            entity_type: 'customers',
                                            description: 'Customers list has been exported as CSV'
                                        },
                                        async: false,
                                        success: function() {
                                            // Call the original action after successful logging
                                            $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                                        }.bind(this),
                                        error: function(xhr, status, error) {
                                            console.error('Logging error:', error);
                                            // Still proceed with export even if logging fails
                                            $.fn.dataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                                        }.bind(this)
                                    });
                                } catch (error) {
                                    console.error('CSV export error:', error);
                                    alert('Error exporting to CSV: ' + error.message);
                                }
                            }
                        },
                        {
                            extend: 'excel',
                            text: '<i class="bi bi-file-earmark-excel me-1"></i> Excel',
                            className: 'btn btn-sm',
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5, 6, 7]
                            },
                            title: 'Customer List',
                            action: function(e, dt, button, config) {
                                try {
                                    // Log the export activity
                                    $.ajax({
                                        url: '{{ route("admin.log.export") }}',
                                        method: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            export_type: 'excel',
                                            entity_type: 'customers',
                                            description: 'Customers list has been exported as Excel'
                                        },
                                        async: false,
                                        success: function() {
                                            // Call the original action after successful logging
                                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                                        }.bind(this),
                                        error: function(xhr, status, error) {
                                            console.error('Logging error:', error);
                                            // Still proceed with export even if logging fails
                                            $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                                        }.bind(this)
                                    });
                                } catch (error) {
                                    console.error('Excel export error:', error);
                                    alert('Error exporting to Excel: ' + error.message);
                                }
                            }
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
                            },
                            action: function(e, dt, button, config) {
                                try {
                                    // Log the export activity
                                    $.ajax({
                                        url: '{{ route("admin.log.export") }}',
                                        method: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            export_type: 'pdf',
                                            entity_type: 'customers',
                                            description: 'Customers list has been exported as PDF'
                                        },
                                        async: false,
                                        success: function() {
                                            // Call the original action after successful logging
                                            $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                                        }.bind(this),
                                        error: function(xhr, status, error) {
                                            console.error('Logging error:', error);
                                            // Still proceed with export even if logging fails
                                            $.fn.dataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                                        }.bind(this)
                                    });
                                } catch (error) {
                                    console.error('PDF export error:', error);
                                    alert('Error exporting to PDF: ' + error.message);
                                }
                            }
                        },
                        {
                            extend: 'print',
                            text: '<i class="bi bi-printer me-1"></i> Print',
                            className: 'btn btn-sm',
                            exportOptions: {
                                columns: [1, 2, 3, 4, 5, 6, 7]
                            },
                            title: 'Customer List',
                            action: function(e, dt, button, config) {
                                try {
                                    // Log the export activity
                                    $.ajax({
                                        url: '{{ route("admin.log.export") }}',
                                        method: 'POST',
                                        data: {
                                            _token: '{{ csrf_token() }}',
                                            export_type: 'print',
                                            entity_type: 'customers',
                                            description: 'Customers list has been exported as a Print'
                                        },
                                        async: false,
                                        success: function() {
                                            // Call the original action after successful logging
                                            $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                                        }.bind(this),
                                        error: function(xhr, status, error) {
                                            console.error('Logging error:', error);
                                            // Still proceed with export even if logging fails
                                            $.fn.dataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                                        }.bind(this)
                                    });
                                } catch (error) {
                                    console.error('Print export error:', error);
                                    alert('Error printing: ' + error.message);
                                }
                            }
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

        // Handle edit button click
        $(document).on('click', '.edit-customer', function() {
            const id = $(this).data('id');
            const custcode = $(this).data('custcode');
            const custname = $(this).data('custname');
            const phone = $(this).data('phone');
            const email = $(this).data('email');
            
            // Populate the modal form
            $('#customer_id').val(id);
            $('#custcode').val(custcode);
            $('#custname').val(custname);
            $('#phone').val(phone);
            $('#email').val(email);
            
            // Reset any previous validation errors
            $('#phone').removeClass('is-invalid');
            $('#email').removeClass('is-invalid');
            $('#phone-error').text('');
            $('#email-error').text('');
            
            // Show the modal
            $('#editCustomerModal').modal('show');
        });
        
        // SweetAlert2 configuration
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success me-3",
                cancelButton: "btn btn-outline-danger",
                actions: 'gap-2 justify-content-center'
            },
            buttonsStyling: false
        });
        
        // Handle save changes button click
        $('#saveCustomerChanges').click(function() {
            // Validate form before showing confirmation
            let hasErrors = false;
            
            // Phone validation - check format and length
            const phone = $('#phone').val().trim();
            if (!phone) {
                $('#phone-error').text('Contact number is required.').show();
                $('#phone').addClass('is-invalid');
                hasErrors = true;
            } else if (phone.startsWith('+639')) {
                // For +639 format: must be exactly 13 characters
                if (phone.length !== 13 || !/^\+639\d{9}$/.test(phone)) {
                    $('#phone-error').text('Contact number must be exactly 13 characters for +639 format.').show();
                    $('#phone').addClass('is-invalid');
                    hasErrors = true;
                } else {
                    $('#phone-error').text('');
                    $('#phone').removeClass('is-invalid');
                }
            } else if (phone.startsWith('09')) {
                // For 09 format: must be exactly 11 characters
                if (phone.length !== 11 || !/^09\d{9}$/.test(phone)) {
                    $('#phone-error').text('Contact number must be exactly 11 characters for 09 format.').show();
                    $('#phone').addClass('is-invalid');
                    hasErrors = true;
                } else {
                    $('#phone-error').text('');
                    $('#phone').removeClass('is-invalid');
                }
            } else {
                $('#phone-error').text('Contact number must start with 09 or +639.').show();
                $('#phone').addClass('is-invalid');
                hasErrors = true;
            }
            
            // Email validation - check format and limit to 50 characters
            const email = $('#email').val().trim();
            if (email) {
                if (email.length > 50) {
                    $('#email-error').text('Email cannot exceed 50 characters.').show();
                    $('#email').addClass('is-invalid');
                    hasErrors = true;
                } else if (!isValidEmail(email)) {
                    $('#email-error').text('Please enter a valid email address.').show();
                    $('#email').addClass('is-invalid');
                    hasErrors = true;
                } else {
                    // Check if there's an email uniqueness error in the main error div
                    const emailErrorText = $('#email-error').text();
                    if (emailErrorText && emailErrorText.includes('already used by customer')) {
                        hasErrors = true;
                    } else {
                        $('#email-error').text('');
                        $('#email').removeClass('is-invalid');
                    }
                }
            } else {
                $('#email-error').text('');
                $('#email').removeClass('is-invalid');
            }
            
            // If there are validation errors, don't proceed
            if (hasErrors) {
                return;
            }
            
            swalWithBootstrapButtons.fire({
                title: "Save Changes?",
                text: "Are you sure you want to save these changes?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Yes, save changes!",
                cancelButtonText: "No, cancel!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // If user confirms, proceed with saving
                    const form = $('#editCustomerForm');
                    const customerId = $('#customer_id').val();
                    const formData = new FormData(form[0]);
                
                    $.ajax({
                        url: `/admin/customers/${customerId}`,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-HTTP-Method-Override': 'PATCH'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Find the edit button and its row
                                const editButton = $(`button[data-id="${customerId}"]`);
                                const row = editButton.closest('tr');
                                
                                // Get the updated values
                                const updatedPhone = formData.get('phone') || '-';
                                const updatedEmail = formData.get('email') || '-';
                                
                                // Update the table cells
                                row.find('td:eq(5)').text(updatedPhone); // CONTACT# column (6th column, index 5)
                                row.find('td:eq(6)').text(updatedEmail); // EMAIL column (7th column, index 6)
                                
                                // Update the data attributes on the edit button
                                editButton.data('phone', updatedPhone);
                                editButton.data('email', updatedEmail);
                                
                                // Also update the HTML attributes to ensure jQuery .data() cache is consistent with DOM
                                editButton.attr('data-phone', updatedPhone);
                                editButton.attr('data-email', updatedEmail);
                                
                                // Close the modal
                                $('#editCustomerModal').modal('hide');
                                
                                // Show success message with SweetAlert2
                                swalWithBootstrapButtons.fire({
                                    title: "Updated!",
                                    text: "Customer information has been updated successfully.",
                                    icon: "success"
                                });
                            }
                        },
                        error: function(xhr) {
                            const response = xhr.responseJSON;
                            console.log('Error response:', response); // Add debugging
                            if (response && response.errors) {
                                // Clear previous errors
                                $('.invalid-feedback').text('');
                                $('.is-invalid').removeClass('is-invalid');
                                
                                // Display new errors
                                Object.keys(response.errors).forEach(field => {
                                    $(`#${field}-error`).text(response.errors[field][0]).show();
                                    $(`#${field}`).addClass('is-invalid');
                                });
                                
                                // Prevent modal from closing when there are errors
                                swalWithBootstrapButtons.fire({
                                    title: "Validation Error",
                                    text: "Please fix the errors and try again.",
                                    icon: "error"
                                });
                            } else {
                                swalWithBootstrapButtons.fire({
                                    title: "Error!",
                                    text: "An error occurred. Please try again.",
                                    icon: "error"
                                });
                            }
                        }
                    });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // If user cancels
                    swalWithBootstrapButtons.fire({
                        title: "Cancelled",
                        text: "Your changes were not saved",
                        icon: "error"
                    });
                }
            });
        });
        
        // Real-time phone number validation for Philippine mobile numbers
        $('#phone').on('input', function() {
            let value = $(this).val();
            const feedback = $('#phone-error');
            const phoneInput = $(this);
            
            // Strict validation - only allow valid Philippine mobile number patterns
            if (value.length === 0) {
                // Empty is allowed
                value = '';
            } else if (value === '+') {
                // Allow starting with +
                value = '+';
            } else if (value === '+6') {
                // Allow +6 as partial typing of +63
                value = '+6';
            } else if (value === '+63') {
                // Allow +63 as partial typing of +639
                value = '+63';
            } else if (value.startsWith('+639')) {
                // For +639 format: allow only digits after +639
                value = '+639' + value.substring(4).replace(/\D/g, '');
                // Limit to 13 characters total
                if (value.length > 13) {
                    value = value.substring(0, 13);
                }
            } else if (value === '0') {
                // Allow starting with 0
                value = '0';
            } else if (value.startsWith('09')) {
                // For 09 format: allow only digits
                value = value.replace(/\D/g, '');
                // Limit to 11 characters
                if (value.length > 11) {
                    value = value.substring(0, 11);
                }
                // Ensure it still starts with 09
                if (!value.startsWith('09')) {
                    value = '09' + value.substring(2);
                }
            } else {
                // If it doesn't match any valid pattern, clear the field
                value = '';
            }
            
            // Update the input value
            $(this).val(value);
            
            // Provide real-time feedback
            if (value === '') {
                feedback.text('').removeClass('text-danger text-warning text-success text-info');
                phoneInput.removeClass('is-valid is-invalid');
            } else if (value === '+' || value === '+6' || value === '+63') {
                feedback.text('Continue typing +639...').removeClass('text-danger text-warning text-success').addClass('text-info');
                phoneInput.removeClass('is-valid is-invalid');
            } else if (value === '0') {
                feedback.text('Continue typing 09...').removeClass('text-danger text-warning text-success').addClass('text-info');
                phoneInput.removeClass('is-valid is-invalid');
            } else if (value.startsWith('+639')) {
                if (value.length < 13) {
                    feedback.text(`+639 format: ${value.length}/13 characters`).removeClass('text-danger text-success text-info').addClass('text-warning');
                    phoneInput.removeClass('is-valid is-invalid');
                } else if (value.length === 13) {
                    feedback.text('Valid +639 format').removeClass('text-danger text-warning text-info').addClass('text-success');
                    phoneInput.removeClass('is-invalid').addClass('is-valid');
                }
            } else if (value.startsWith('09')) {
                if (value.length < 11) {
                    feedback.text(`09 format: ${value.length}/11 characters`).removeClass('text-danger text-success text-info').addClass('text-warning');
                    phoneInput.removeClass('is-valid is-invalid');
                } else if (value.length === 11) {
                    feedback.text('Valid 09 format').removeClass('text-danger text-warning text-info').addClass('text-success');
                    phoneInput.removeClass('is-invalid').addClass('is-valid');
                }
            } else {
                feedback.text('Must start with 09 or +639').removeClass('text-warning text-success text-info').addClass('text-danger');
                phoneInput.removeClass('is-valid').addClass('is-invalid');
            }
        });

        // Handle paste events for phone number
        $('#phone').on('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
            let cleanedText = '';
            
            if (pastedText.startsWith('+639')) {
                // Clean +639 format
                cleanedText = '+639' + pastedText.substring(4).replace(/\D/g, '');
                if (cleanedText.length > 13) {
                    cleanedText = cleanedText.substring(0, 13);
                }
            } else if (pastedText.startsWith('09')) {
                // Clean 09 format
                cleanedText = pastedText.replace(/\D/g, '');
                if (cleanedText.length > 11) {
                    cleanedText = cleanedText.substring(0, 11);
                }
                if (!cleanedText.startsWith('09')) {
                    cleanedText = '09' + cleanedText.substring(2);
                }
            }
            
            // Only set the value if it's a valid format
            if (cleanedText.startsWith('+639') || cleanedText.startsWith('09')) {
                $(this).val(cleanedText).trigger('input');
            }
        });
        
        // Real-time validation for email input
        $('#email').on('input', function() {
            const email = $(this).val().trim();
            const maxLength = 50;
            
            // Remove any existing uniqueness feedback when user starts typing
            const existingUniqueFeedback = $('#email-unique-feedback');
            if (existingUniqueFeedback.length) {
                existingUniqueFeedback.remove();
            }
            
            // Clear any existing error and reset validation state
            $('#email-error').text('').hide();
            $(this).removeClass('is-invalid is-valid');
            
            if (email.length > maxLength) {
                $(this).val(email.substring(0, maxLength));
                $('#email-error').text(`Email cannot exceed ${maxLength} characters`).show();
                $(this).addClass('is-invalid');
            } else if (email && !isValidEmail(email)) {
                $('#email-error').text('Please enter a valid email address').show();
                $(this).addClass('is-invalid');
            }
        });
        
        // Email validation helper function
        function isValidEmail(email) {
            // More comprehensive email regex that checks for proper format
            const emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
            return email && emailRegex.test(email) && email.length <= 50;
        }

        // Real-time email uniqueness validation on blur
        $('#email').on('blur', function() {
            const email = $(this).val().trim();
            const customerId = $('#customer_id').val();
            
            if (email && isValidEmail(email)) {
                // Remove any existing validation feedback (both format and uniqueness)
                $('#email-error').text('').hide();
                const existingUniqueFeedback = $('#email-unique-feedback');
                if (existingUniqueFeedback.length) {
                    existingUniqueFeedback.remove();
                }
                
                // Check if email is unique via AJAX
                fetch(`/admin/customers/check-email-availability?email=${encodeURIComponent(email)}&customer_id=${encodeURIComponent(customerId)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            // Email already exists - show only uniqueness error
                            $('#email-error').text(data.message).show();
                            $('#email').addClass('is-invalid').removeClass('is-valid');
                        } else {
                            // Email is available
                            const successFeedback = $('<div class="text-success mt-1" id="email-unique-feedback"><small><i class="bi bi-check-circle-fill me-1"></i>' + data.message + '</small></div>');
                            $('#email').parent().append(successFeedback);
                            $('#email').removeClass('is-invalid').addClass('is-valid');
                        }
                    })
                    .catch(error => {
                        console.error('Error checking email uniqueness:', error);
                    });
            }
        });
        
        // Handle cancel button click
        $('#cancelChanges').click(function() {
            swalWithBootstrapButtons.fire({
                title: "Discard Changes?",
                text: "Any unsaved changes will be lost!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, discard changes!",
                cancelButtonText: "No, keep editing!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // If user confirms to discard
                    $('#editCustomerModal').modal('hide');
                    
                    swalWithBootstrapButtons.fire({
                        title: "Cancelled",
                        text: "Your changes have been discarded",
                        icon: "info"
                    });
                }
                // If canceled, just stay on the modal
            });
        });
        
        // Handle close button (x) click
        $('#closeModal').click(function() {
            swalWithBootstrapButtons.fire({
                title: "Close Without Saving?",
                text: "Any unsaved changes will be lost!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, close!",
                cancelButtonText: "No, keep editing!",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // If user confirms to close
                    $('#editCustomerModal').modal('hide');
                    
                    swalWithBootstrapButtons.fire({
                        title: "Closed",
                        text: "Your changes have been discarded",
                        icon: "info"
                    });
                }
                // If canceled, just stay on the modal
            });
        });
    });
</script>
@endsection