<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notepad.link</title>

    <link rel="icon" type="image/png" href="{{ asset('img/icon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <meta property="og:image" content="{{ asset('img/icon.png') }}">

    @livewireStyles
    @stack('styles')
    
    <link href="https://cdn.quilljs.com/2.0.0-dev.5/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/2.0.0-dev.5/quill.js"></script>

</head>
<body>
    @include('includes.nav')
    @yield('content')
    
  
    @livewireScripts
    <!-- Quill -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>   

   
    <script async src="https://securepubads.g.doubleclick.net/tag/js/gpt.js"></script>

    @stack('scripts')
    <script>
        Livewire.on('success', e => {
            alert(e.message);
        });
         Livewire.on('note-updated', url => {
            window.location.href = `/notes/${url['url']}`;
        })
    </script>
    <script>
        window.addEventListener('documentUploaded', event => {
            const quill = window.quillEditor; 
            const range = quill.getSelection();
            const position = range ? range.index : quill.getLength();
            quill.insertText(position, event.detail.text);
            quill.setSelection(position + event.detail.text.length);
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
    <script>
        document.getElementById('darkModeToggle').addEventListener('click', function () {
            document.body.classList.toggle('dark-mode');
        });
    </script>

</body>
</html>
