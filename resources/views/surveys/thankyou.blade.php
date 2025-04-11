@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mt-5">
                <div class="card-body text-center">
                    <h1>Thank You!</h1>
                    <p class="lead">Your survey response has been submitted successfully.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary">Return Home</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
