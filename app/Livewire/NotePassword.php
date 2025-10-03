<?php
namespace App\Livewire;

use App\Services\NoteService;
use Livewire\Component;


class NotePassword extends Component
{
    public $showModal = false;
    public $password = '';
    public $note;
    public $unlocked = false;

    public function mount($note)
    {
        $this->note = $note;
    }

    public function lock(NoteService $service)
    {
        $service->lockNote($this->note->url, $this->password);

        $this->showModal = false;

        $this->dispatch('success', message: 'Note locked successfully.');

        
    }

    public function render()
    {
        return view('livewire.note-password');
    }
}
