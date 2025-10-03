<div>
    <button wire:click="$set('showModal', true)" class="btn btn-secondary m-1 btn-sm" data-bs-toggle="tooltip" title="Lock note">
        <i class="fa-solid fa-lock"></i>
    </button>

    @if($showModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Enter Password</h5>
                        <button type="button" wire:click="$set('showModal', false)" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="password" wire:model="password" class="form-control" placeholder="Password">
                        @if (session()->has('error'))
                            <p class="text-danger mt-2">{{ session('error') }}</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" wire:click="lock">Lock</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
