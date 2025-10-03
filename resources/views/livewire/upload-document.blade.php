<div>
   <form wire:submit.prevent="save" enctype="multipart/form-data">
    <input type="file" wire:model="file">
    <button type="submit">Upload</button>

    @error('file') <span class="text-danger">{{ $message }}</span> @enderror
</form>

    @error('file') 
        <span class="text-danger">{{ $message }}</span> 
    @enderror
</div>
