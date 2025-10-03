<div>
    <button wire:click="toggleFavorite" class="btn btn-secondary m-1 btn-sm">
        <i class="fa-solid fa-heart {{ $isFavorite ? 'text-danger' : '' }}" data-bs-toggle="tooltip" title="Favorites"></i>
    </button>
</div>