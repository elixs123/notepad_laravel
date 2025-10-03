<div>
    <button wire:click="$set('showModal', true)" class="btn btn-secondary m-1 btn-sm" data-bs-toggle="tooltip" title="Edit url">
        <i class="fa-solid fa-edit"></i>
    </button>

    @if($showModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit URL </h5>
                        <button type="button" wire:click="$set('showModal', false)" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" wire:model="text" class="form-control" placeholder="Enter url">
                        @if (session()->has('error'))
                            <p class="text-danger mt-2">{{ session('error') }}</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" wire:click="edit">Edit URL</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
