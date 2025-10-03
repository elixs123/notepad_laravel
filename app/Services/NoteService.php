<?php

namespace App\Services;

use App\Models\Notes;
use Illuminate\Support\Str;


class NoteService
{
    function generateRandomString($length = 5)
    {
        return Str::random($length);
    }

    public function createNote(string $url, string $content, string $share_url, string $password, string $raw_url, string $markdown_url, string $code_url): Notes
    {   
        
        if($this->existNote($url)){
            $url = $this->generateRandomString(5);
        }

        return Notes::create([
            'url' => $url,
            'content' => $content,
            'share_url' => $share_url,
            'password' => $password,
            'raw_url' => $raw_url,
            'markdown_url' => $markdown_url,
            'code_url' => $code_url
        ]);
    }

    public function existNote(string $url): bool
    {
        return Notes::where('url', $url)->exists();
    }

    public function getNote(int $id): ?Notes
    {
        return Notes::find($id);
    }

    public function deleteNote(int $id): bool
    {
        return Notes::where('id', $id)->delete() > 0;
    }
    public function updateNote(string $url, string $content): bool
    {
        return Notes::where('url', $url)->update(['content' => $content]) > 0;
    }
    public function lockNote(string $url, string $password): bool
    {
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        
        return Notes::where('url', $url)->update(['password' => $hashPassword]) > 0;
    }
    public function editUrl(string $url, string $text): bool{

        return Notes::where('url', $url)->update(['url' => $text]) > 0;
    }
}
