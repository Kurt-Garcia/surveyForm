@extends('layouts.app')

@section('content')
<head>
    @vite(['resources/js/app.js'])
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
    document.addEventListener('DOMContentLoaded', function() {
        new DataTable('#customersTable');
    });
</script>
@endsection