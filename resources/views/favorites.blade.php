@extends('default')

@section('content')
<div class="container py-4">
    <h3 class="mb-4">Favorites</h3>

    @if($notes->count() > 0)
        <div class="row">
            @foreach($notes as $note)
                <div class="col-md-4 mb-3">
                    <a href="{{ route('notes.show', $note->url) }}" class="text-decoration-none">
                        <div class="card shadow-sm h-100 border-0 hover-shadow">
                            <div class="card-body">
                                <h5 class="card-title text-dark mb-2">
                                    {{ $note->url ?? 'Untitled Note' }}
                                </h5>
                                <p class="card-text text-muted small">
                                    Created: {{ $note->created_at->format('d.m.Y H:i') }}
                                </p>
                            </div>
                            <div class="card-footer bg-white border-0 text-end">
                                <i class="fa-solid fa-heart text-danger"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fa-solid fa-heart text-danger fa-3x mb-3"></i>
            <h5 class="text-muted">Nema favorita</h5>
        </div>
    @endif
</div>

<style>
.hover-shadow:hover {
    box-shadow: 0 6px 16px rgba(0,0,0,.15);
    transition: box-shadow .2s ease-in-out;
}
</style>
@endsection
