<?php
namespace App\Livewire;

use App\Services\NoteService;
use Livewire\Component;

class EditUrl extends Component
{
    public $showModal = false;
    public $note;
    public $text;

    public function mount($note)
    {
        $this->note = $note;
    }

    public function edit(NoteService $service)
    {
        $service->editUrl($this->note->url, $this->text);

        $this->dispatch('note-updated', url: $this->text);

        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.edit-url');
    }
}
