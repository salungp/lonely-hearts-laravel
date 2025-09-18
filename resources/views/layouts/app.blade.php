<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @yield('meta')
    <title>@yield('title', 'Lonely hearts')</title>
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous" />

    <!-- favicon -->
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" />

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components.css') }}">
</head>
<body>
    {{-- Header --}}
    <header class="lh-header">
        <div class="container-sm">
            <nav>
                @yield('back')

                <a href="#">
                    <img
                    src="{{ asset('images/logo.svg') }}"
                    alt="Lonely hearts logo heart pixelated"
                    />
                    <h2>LONELY HEARTS</h2>
                </a>
    
              <div class="lh-hamburger" id="lhHamburger">
                <span></span>
                <span></span>
                <span></span>
              </div>
    
              <div class="lh-dropdown" id="lhMobileMenu">
                <a href="{{ route('home') }}">Home</a>
                <a href="#">Message</a>
                <a href="{{ route('profile.view') }}">Account</a>
                <a href="#">ABOUT LONELY HEARTS DATING</a>
                <a href="#">LOOKING FOR LOVE?</a>
                <a href="#">LOOKING FOR LOVE?</a>
              </div>
            </nav>
    
            <div class="lh-sub-header d-flex mt-3 justify-content-between">
              <h3 id="current-date">FRI 6th Jun 2025</h3>
              <a href="#" class="lh-location" id="ctaLocation"
                >{{ session('selected_location') ?? 'All Locations' }}</a
              >
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="lh-footer">
        <div class="container-sm text-center">
            <p>&copy; {{ date('Y') }} My App. All rights reserved.</p>
        </div>
    </footer>

    <!-- bootstrap script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"
    ></script>
    <script src="{{ asset('js/script.js') }}"></script>
    @yield('script')
</body>
</html>
