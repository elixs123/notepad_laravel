@extends('default')

@section('content')
<div class="d-flex justify-content-center align-items-center" style="height: 100vh; background: linear-gradient(135deg, #667eea, #764ba2);">
    <div class="card shadow-lg p-5 text-center" style="min-width: 380px; max-width: 500px; width: 100%; border-radius: 15px; background-color: #1e1e2f; color: #fff;">
        
        <div class="mb-4">
            <i class="fa-solid fa-lock fa-3x" style="color: #ffce00;"></i>
        </div>

        <h3 class="mb-4">This Notepad is Protected</h3>
        <p class="mb-4 text-muted">Enter your password to unlock the content</p>

        @if($errors->has('password'))
            <div class="alert alert-danger">
                {{ $errors->first('password') }}
            </div>
        @endif

        <form action="{{ route('notes.unlock', $notes->url) }}" method="POST">
            @csrf
            <div class="mb-3">
                <input type="password" name="password" class="form-control form-control-lg" placeholder="Enter password" style="border-radius: 10px;">
            </div>
            <button type="submit" class="btn w-100 btn-lg" style="background: linear-gradient(135deg, #ffce00, #ff9800); color: #1e1e2f; font-weight: bold; border-radius: 10px;">Unlock</button>
        </form>
    </div>
</div>
@endsection
