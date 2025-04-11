@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>{{ $survey->title }}</h2>
                    <div>
                        <a href="{{ route('admin.surveys.edit', $survey) }}" class="btn btn-primary">Edit Survey</a>
                        <form action="{{ route('admin.surveys.destroy', $survey) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this survey?')">Delete</button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <h3>Questions</h3>
                    @if($survey->questions->count() > 0)
                        <div class="list-group">
                            @foreach($survey->questions as $question)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-1">{{ $question->text }}</h5>
                                            <small class="text-muted">Type: {{ ucfirst($question->type) }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p>No questions added to this survey yet.</p>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('admin.surveys.index') }}" class="btn btn-secondary">Back to Surveys</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection