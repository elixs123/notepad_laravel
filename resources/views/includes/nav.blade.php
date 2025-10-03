<nav class="navbar navbar-expand-lg navbar-light bg-dark">
  <div class="container">
    <a class="navbar-brand" href="{{ route('home') }}">
      Online Notepad
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('changelog') }}">Changelog</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('favorites') }}">Favorite Notes</a></li>
        @if(!Auth::check())
        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
        @endif
      </ul>
      <livewire:search-notes />
    </div>
  </div>
</nav>
