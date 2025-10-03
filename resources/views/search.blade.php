@extends('default')

@section('content')
<div class="container py-4">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="car-header">
            <img src="{{ asset('img/logo.png') }}" width="150" class="m-2" alt="logo">
        </div>
        <hr>
        <div class="card-body">

            @if($query)
                <h5 class="mb-3">
                    {{ number_format($results->total()) }} Search Results for 
                    "<span class="text-muted">{{ $query }}</span>"
                </h5>

                @if($results->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($results as $note)
                            <li class="list-group-item">
                                <a href="{{ route('notes.show', $note->url) }}" class="text-decoration-none">
                                    <strong>{{ $note->url }}</strong>
                                </a>
                                <p class="mb-0 text-muted small">{{ Str::limit($note->content, 100) }}</p>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-3">
                        {{ $results->links() }}
                    </div>
                @else
                    <div class="alert alert-warning rounded-3">
                        No results found for "{{ $query }}".
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
