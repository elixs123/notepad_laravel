<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notes;
use App\Services\NoteService;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $note_url = $this->store(new NoteService());

        return redirect(route('notes.show', $note_url));
    }

    public function home(){

    }

    public function shareUrl($note_url){
        $notes = Notes::where('url', $note_url)->firstOrFail();
        
        return view('share-url', compact('notes'));
    }

    public function update($note_url, NoteService $service, Request $request){
        $service->updateNote($note_url, $request->input('content'));
    }

    public function show($note_url){

        $notes = Notes::where('url', $note_url)
        ->firstOrFail();


        if (!empty($notes->password) && !session()->has('note_unlocked_'.$notes->id)) {
            return view('notes-unlock', compact('notes'));
        }
        
        return view('home', compact('notes'));
    }

    public function unlock(Request $request, $note_url)
    {
        $note = Notes::where('url', $note_url)->firstOrFail();

        $request->validate([
            'password' => 'required|string',
        ]);

        if (password_verify($request->password, $note->password)) {
            session(['note_unlocked_'.$note->id => true]);
            return redirect()->route('notes.show', $note->url);
        } else {
            return back()->withErrors(['password' => 'Wrong password.']);
        }
    }

    public function editUrl($note_url){
        $note = Notes::where('url', $note_url)->firstOrFail();

        return view('edit-url', compact('note'));
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120|mimes:txt',
        ]);

        $file = $request->file('file');

        $content = file_get_contents($file->getRealPath());

        $note = Notes::where('url', $request->input('note_url'))->firstOrFail();
        $note->content = $content;
        $note->save();

        return back()->with('success', 'Fajl je uspjesno uploadovan i sadrzaj dodat u content!');
    }

    public function store(NoteService $service){
        $note = $service->createNote(
            Str::random(5),
            'test',
            Str::random(20),
            '',
            Str::random(20),
            Str::random(20),
            Str::random(20)
        );

        return $note->url;
    }
    public function favorites(){
        $favorites = json_decode(request()->cookie('favorites', '[]'), true) ?: [];
        $notes = Notes::whereIn('id', $favorites)->get();
        return view('favorites', compact('notes'));
    }

    public function search(Request $request){
        $query = $request->get('query');

        $results = [];
        if ($query) {
            $results = Notes::where('url', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->withQueryString();
        }

        return view('search', compact('query', 'results'));
    }
}
