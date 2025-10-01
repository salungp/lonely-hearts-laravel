@extends('layouts.app')
@section('title', 'Account | Email')
@section('back')
<a href="{{ route('profile.view') }}" class="lh-nav-button">
    <img src="{{ asset('/icons/arrow-left-bold.svg') }}" alt="Icon back button" />
</a>
@endsection
@section('content')
<div class="container-sm">

    <!-- Form start line -->
    <form action="{{ route('profile.email_update') }}" method="POST">
        @csrf

        @if ($errors->any())
            <div class="lh-alert lh-alert-error d-block mb-3">
                <strong>Whoops!</strong> Please fix the following:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('error'))
          <div class="lh-alert mb-3 lh-alert-error" id="alert">
            {{ session('error') }}
            <button class="lh-alert-close" type="button">
              <img src="{{ asset('icons/close.svg') }}" alt="Close button icon">
            </button>
          </div>
        @endif

        @if (session('success'))
          <div class="lh-alert mb-3 lh-alert-success" id="alert">
            {{ session('success') }}
            <button class="lh-alert-close" type="button">
              <img src="{{ asset('icons/close.svg') }}" alt="Close button icon">
            </button>
          </div>
        @endif


        <h1 class="lh-title mb-3">Email address</h1>

        <div class="input-group mb-3">
            <label class="mb-2" for="email">Email address</label>
            <input type="email" name="email" class="lh-input" placeholder="Email address" value="{{ $user->email }}">
        </div>

        <button class="lh-button" type="submit">Save changes</button>
        
    </form>
    
</div>
@endsection
@section('script')
<script>
const alert = document.getElementById("alert");

clickAction(".lh-alert-close", (e) => {
    alert.style.display = "none";
});
</script>
@endsection