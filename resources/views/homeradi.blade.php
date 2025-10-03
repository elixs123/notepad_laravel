@extends('default')

@section('content')
<div class="container p-0" style="height: 100vh;">
    <div class="notepad-container d-flex flex-column" style="height: 100%;">

        <!-- HEADER + ACTIONS -->
        <div class="notepad-head p-2 bg-light d-flex align-items-center justify-content-between flex-wrap">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <img src="{{ asset('img/icon.png') }}" alt="icon" style="height: 40px;">
                <h4 class="notepad-title ms-2 m-0">Notepad</h4>
            </div>
            <div class="d-flex flex-wrap">
                <button id="speechBtn" class="btn btn-secondary m-1 btn-sm"><i class="fa-solid fa-microphone"></i></button>
                <livewire:favorites :note-id="$notes->id" />
                <livewire:note-password :note="$notes" />
                <livewire:edit-url :note="$notes" />
                <a href="{{ route('home') }}" class="btn btn-secondary m-1 btn-sm"><i class="fa-solid fa-plus"></i></a>
                <form action="{{ route('upload.file') }}" method="POST" enctype="multipart/form-data" class="d-inline-block">
                    @csrf
                    <label for="fileUpload" class="btn btn-secondary m-1 btn-sm" style="cursor:pointer;"><i class="fa-solid fa-upload"></i></label>
                    <input type="hidden" name="note_url" value="{{ $notes->url }}">
                    <input id="fileUpload" type="file" name="file" class="d-none" onchange="this.form.submit()">
                </form>
                <button id="darkModeToggle" class="btn btn-secondary m-1 btn-sm"><i class="fa-solid fa-toggle-on"></i></button>
            </div>
        </div>

        <!-- STATUS BAR -->
        <div class="editing-status-bar mt-2 p-2 d-flex flex-column">
            <div class="status-indicator d-flex align-items-center">
                <div class="status-dot me-2" id="statusDot"></div>
                <div class="status-text d-flex flex-column">
                    <span class="current-editor" id="currentUser">Nobody is editing</span>
                    <small class="status-subtitle text-muted" id="allUsers">Users online: None</small>
                </div>
            </div>
        </div>

        <!-- TOOLBAR -->
        <div id="toolbar-container">
            <span class="ql-formats">
                <select class="ql-font"></select>
                <select class="ql-size"></select>
            </span>
            <span class="ql-formats">
                <button class="ql-bold"></button>
                <button class="ql-italic"></button>
                <button class="ql-underline"></button>
                <button class="ql-strike"></button>
            </span>
            <span class="ql-formats">
                <select class="ql-color"></select>
                <select class="ql-background"></select>
            </span>
            <span class="ql-formats">
                <button class="ql-script" value="sub"></button>
                <button class="ql-script" value="super"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-header" value="1"></button>
                <button class="ql-header" value="2"></button>
                <button class="ql-blockquote"></button>
                <button class="ql-code-block"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-list" value="ordered"></button>
                <button class="ql-list" value="bullet"></button>
                <button class="ql-indent" value="-1"></button>
                <button class="ql-indent" value="+1"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-direction" value="rtl"></button>
                <select class="ql-align"></select>
            </span>
            <span class="ql-formats">
                <button class="ql-link"></button>
                <button class="ql-image"></button>
                <button class="ql-video"></button>
                <button class="ql-formula"></button>
            </span>
            <span class="ql-formats">
                <button class="ql-clean"></button>
            </span>
        </div>

        <!-- EDITOR -->
        <div id="editor" style="flex-grow: 1; width: 100%;"></div>

        <!-- FOOTER -->
        <div class="notepad-footer border-top d-flex flex-column align-items-center text-center p-2">
            <p id="wordCount" class="mb-1">Words: 0, Characters: 0</p>
            <div class="d-flex flex-wrap justify-content-center">
                <button class="btn btn-outline-secondary m-1 btn-sm" onclick="copyToClipboard(getEditableUrl())">
                    <i class="fa-solid fa-copy"></i> Copy Editable URL
                </button>
                <button class="btn btn-outline-secondary m-1 btn-sm" onclick="copyToClipboard(getShareUrl())">
                    <i class="fa-solid fa-share"></i> Copy Share URL
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Firebase + Quill -->
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.22.0/firebase-database-compat.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const firebaseConfig = {
        apiKey: "AIzaSyBaYLuVXEmk7US9O9SFN4lyUV_N7ewaoGE",
        authDomain: "notepad-7e2a5.firebaseapp.com",
        databaseURL: "https://notepad-7e2a5-default-rtdb.firebaseio.com",
        projectId: "notepad-7e2a5",
        storageBucket: "notepad-7e2a5.appspot.com",
        messagingSenderId: "690331289459",
        appId: "1:690331289459:web:f5c6cef8318c142982a706"
    };
    firebase.initializeApp(firebaseConfig);

    const db = firebase.database();
    const noteId = "{{ $notes->id }}";
    const noteRef = db.ref(`notepad/${noteId}`);
    const editorsRef = db.ref(`notepad-editors/${noteId}`);

    const currentUser = {
        id: "{{ auth()->user()->id ?? 'guest' }}",
        username: "{{ auth()->user()->username ?? 'guest' }}",
        color: "{{ auth()->user()->username ?? 'guest' }}" === 'elvis' ? '#e53e3e' : 
               ("{{ auth()->user()->username ?? 'guest' }}" === 'test' ? '#0ea5e9' : '#6b7280')
    };

    const quill = new Quill('#editor', { 
        theme: 'snow', 
        modules: { toolbar: '#toolbar-container' }
    });

    try {
        const delta = JSON.parse(@json($notes->content ?? "{}"));
        quill.setContents(delta);
    } catch(e) {
        quill.setText(@json($notes->content ?? ""));
    }

    function updateStats(){
        const text = quill.getText().trim();
        const words = text.length > 0 ? text.split(/\s+/).length : 0;
        const characters = text.length;
        document.getElementById("wordCount").innerText = `Words: ${words}, Characters: ${characters}`;
    }
    updateStats();

    // Register user online and remove on disconnect
    editorsRef.child(currentUser.id).onDisconnect().remove();
    editorsRef.child(currentUser.id).set({ username: currentUser.username, lastEdit: Date.now(), color: currentUser.color });

    // Text-change
    quill.on('text-change', (delta, oldDelta, source) => {
        if(source !== 'user') return;

        updateStats();
        noteRef.set({ content: quill.getContents(), lastEdit: Date.now(), userId: currentUser.id });
        editorsRef.child(currentUser.id).update({ lastEdit: Date.now() });

        // Send update to Laravel
        fetch(`/update/${noteUrl}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
               body: JSON.stringify({ content: JSON.stringify(quill.getContents()) })
        });
    });


    // Listen for note content updates
    noteRef.on('value', snapshot => {
        const data = snapshot.val();
        if(!data) return;
        if(data.userId !== currentUser.id){
            quill.setContents(data.content);
        }
    });

    // Listen for editors updates
    editorsRef.on('value', snap => {
        const users = [];
        const editingUsers = [];
        snap.forEach(child => {
            const u = child.val();
            users.push(u.username);
            if(Date.now() - u.lastEdit < 3000){
                editingUsers.push(u.username);
            }
        });

        document.getElementById('allUsers').textContent = 'Users online: ' + (users.length ? users.join(', ') : 'None');
        document.getElementById('currentUser').textContent = editingUsers.length ? editingUsers.join(', ') + ' is editing...' : 'Nobody is editing';
        document.getElementById('statusDot').style.backgroundColor = editingUsers.length ? currentUser.color : '#6c757d';
    });

    document.getElementById('darkModeToggle').addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
    });

    const noteUrl = "{{ $notes->url }}";
    function getBaseUrl(){ return window.location.origin; }
    function getEditableUrl(){ return `${getBaseUrl()}/${noteUrl}`; }
    function getShareUrl(){ return `${getBaseUrl()}/share`; }
    function copyToClipboard(text){
        navigator.clipboard.writeText(text).then(() => {
            const statusElement = document.getElementById('currentUser');
            const original = statusElement.textContent;
            statusElement.textContent = 'URL copied to clipboard!';
            setTimeout(() => { statusElement.textContent = original; }, 1500);
        });
    }
});
</script>

<style>
.editing-status-bar { background: rgba(0,0,0,0.03); border-radius: 8px; padding: 8px 12px; border: 1px solid rgba(0,0,0,0.1); }
.status-dot { width: 12px; height: 12px; border-radius: 50%; background-color: #6c757d; transition: all 0.3s ease; }
.current-editor { font-weight: 600; font-size: .9rem; transition: all 0.3s ease; }
#allUsers { font-size: .8rem; color: #555; }
.ql-editor { line-height: 1.6; }
.dark-mode .editing-status-bar { background: rgba(255,255,255,0.05); }
</style>
@endsection
