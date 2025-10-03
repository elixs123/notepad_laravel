<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class UploadDocument extends Component
{
    use WithFileUploads;

    public $file;

    protected $rules = [
        'file' => 'required|mimes:txt,md,docx,pdf|max:2048',
    ];

    public function save()
    {
        $this->validate();
         $path = $this->file->store('tmp_uploads');

    }

    public function render()
    {
        return view('livewire.upload-document');
    }
}
