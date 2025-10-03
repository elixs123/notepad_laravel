@extends('default')

@section('content')
<div class="container p-0" style="height: 100vh;">
    <div class="notepad-container d-flex flex-column" style="height: 100%;">

        <!-- HEADER -->
        <div class="notepad-head p-2 bg-light">
            <div class="d-flex align-items-center justify-content-between flex-wrap">
                <div class="notepad-logo d-flex align-items-center mb-2 mb-md-0">
                    <img src="{{ asset('img/icon.png') }}" alt="icon" style="height: 40px;">
                    <h4 class="notepad-title ms-2 m-0">Notepad</h4>
                </div>
                <div class="d-flex flex-wrap justify-content-end">
                    <button class="btn btn-secondary m-1 btn-sm"><i class="fa-solid fa-microphone"></i></button>
                    <button class="btn btn-secondary m-1 btn-sm"><i class="fa-solid fa-heart"></i></button>
                    <button class="btn btn-secondary m-1 btn-sm"><i class="fa-solid fa-lock"></i></button>
                    <button class="btn btn-secondary m-1 btn-sm"><i class="fa-solid fa-edit"></i></button>
                    <button class="btn btn-secondary m-1 btn-sm"><i class="fa-solid fa-plus"></i></button>
                    <button class="btn btn-secondary m-1 btn-sm"><i class="fa-solid fa-upload"></i></button>
                    <button class="btn btn-secondary m-1 btn-sm"><i class="fa-solid fa-toggle-on"></i></button>
                </div>
            </div>
        </div>

        <!-- EDITOR -->
        <div class="notepad-body flex-grow-1 d-flex flex-column">
            <div id="toolbar-container" class="mb-2"></div>
            <div id="editor" style="flex-grow:1; width:100%;"></div>
        </div>

        <!-- FOOTER -->
        <div class="notepad-footer border-top d-flex flex-column align-items-center text-center p-2">
            <div class="mb-1"><p class="mb-0">Words: 0, Characters: 0</p></div>
            <div class="mb-1"><p class="mb-0">Created at: August 17, 2025, 11:27 am</p></div>
            <div class="mb-1"><p class="mb-0">Last modified: August 17, 2025, 11:27 am</p></div>
            <div class="d-flex flex-wrap justify-content-center mt-2">
                <button class="btn btn-secondary m-1 btn-sm"><i class="fa-solid fa-copy"></i> Copy Editable URL</button>
                <button class="btn btn-secondary m-1 btn-sm"><i class="fa-solid fa-share"></i> Copy Share URL</button>
                <button class="btn btn-secondary m-1 btn-sm"><i class="fa-solid fa-file"></i> Raw View</button>
                <button class="btn btn-secondary m-1 btn-sm"><i class="fa-brands fa-markdown"></i> Markdown View</button>
                <button class="btn btn-secondary m-1 btn-sm"><i class="fa-solid fa-code"></i> Code View</button>
            </div>
        </div>

    </div>
</div>

<!-- Quill -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
  var quill = new Quill('#editor', {
      theme: 'snow',
      modules: {
          toolbar: '#toolbar-container'
      }
  });

  var delta = JSON.parse(@json($notes[0]->content));
  quill.setContents(delta);
</script>

<style>
.notepad-body #editor {
    padding: 10px;
    box-sizing: border-box;
    overflow-y: auto;
    background-color: #fff;
}

/* Mobilna prilagodba */
@media (max-width: 576px) {
    .notepad-logo h4 {
        font-size: 1rem;
    }
    .notepad-head .btn {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
    }
    #toolbar-container {
        flex-wrap: wrap;
        font-size: 0.75rem;
        overflow-x: auto;
    }
    .notepad-footer p {
        font-size: 0.8rem;
    }
    .notepad-footer .btn {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>
@endsection
