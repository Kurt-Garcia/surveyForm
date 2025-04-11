@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('My Surveys') }}</span>
                    <a href="{{ route('admin.surveys.create') }}" class="btn btn-primary btn-sm">
                        {{ __('Create New Survey') }}
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('Title') }}</th>
                                    <th>{{ __('Questions') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($surveys as $survey)
                                    <tr>
                                        <td>{{ $survey->title }}</td>
                                        <td>{{ $survey->questions->count() }}</td>
                                        <td>{{ $survey->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('admin.surveys.show', $survey) }}" class="btn btn-info btn-sm">
                                                {{ __('View') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            {{ __('No surveys found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $surveys->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection