<?php 
namespace App\Livewire;

use Livewire\Component;
use App\Models\Notes;

class SearchNotes extends Component
{
    public $query = '';
    public $results = [];

    public function updatedQuery()
    {
        $this->results = Notes::where('url', 'like', '%' . $this->query . '%')
            ->orWhere('content', 'like', '%' . $this->query . '%')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.search-notes');
    }
}
