<?php

namespace App\Livewire;

use Livewire\Component;

class Favorites extends Component
{
    public $noteId;
    public $isFavorite = false;

    public function mount($noteId)
    {
        $this->noteId = $noteId;
        $favorites = json_decode(request()->cookie('favorites', '[]'), true) ?: [];
        $this->isFavorite = in_array($this->noteId, $favorites);
    }

    public function toggleFavorite()
    {
        $favorites = json_decode(request()->cookie('favorites', '[]'), true) ?: [];

        if (in_array($this->noteId, $favorites)) {
            $favorites = array_values(array_diff($favorites, [$this->noteId]));
            $this->isFavorite = false;
        } else {
            $favorites[] = $this->noteId;
            $favorites = array_values(array_unique($favorites));
            $this->isFavorite = true;
        }

        cookie()->queue(cookie('favorites', json_encode($favorites), 60 * 24 * 30));
    }

    public function render()
    {
        return view('livewire.favorites');
    }
}
