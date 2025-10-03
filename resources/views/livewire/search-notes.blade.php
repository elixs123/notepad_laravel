<div>
    <div class="input-group w-100">
        <input type="text" 
               wire:model.live.debounce.300ms="query" 
               placeholder="Search notes..." 
               class="form-control mb-1">

       <button class="btn mb-1"
                style="background-color: #000; color: #fff; border: 1px solid #fff;"
                onclick="window.location.href='{{ url('/search') }}?query=' + document.querySelector('[wire\\:model\\.live\\.debounce\\.300ms=query]').value">
            <i class="bi bi-search"></i> Search
        </button>
    </div>
</div>
