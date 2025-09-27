<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Find and connect with people on Lonely Hearts')" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:title" content="@yield('og_title', 'Lonely Hearts')" />
    <meta property="og:description" content="@yield('og_description', 'Find and connect with people on Lonely Hearts')" />
    <meta property="og:image" content="@yield('og_image', asset('default-og-image.jpg'))" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="@yield('og_type', 'website')" />
    <meta property="og:site_name" content="Lonely Hearts" />

    <!-- Twitter card (optional but recommended) -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="@yield('og_title', 'Lonely Hearts')" />
    <meta name="twitter:description" content="@yield('og_description', 'Find and connect with people on Lonely Hearts')" />
    <meta name="twitter:image" content="@yield('og_image', asset('default-og-image.jpg'))" />
    @yield('meta')
    <title>@yield('title', 'Lonely hearts')</title>
    <!-- bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous" />

    <!-- favicon -->
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" />

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XNXTESCVT4"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-XNXTESCVT4');
    </script>

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
                    src="{{ asset('images/logo-new-white.svg') }}"
                    alt="Lonely hearts logo heart pixelated"
                    />
                </a>
    
              <div class="lh-hamburger" id="lhHamburger">
                <span></span>
                <span></span>
                <span></span>
              </div>
    
              <div class="lh-dropdown" id="lhMobileMenu">
                <a href="{{ route('home') }}">🏠 Home</a>
                <a href="{{ route('message') }}">💌 Message</a>
                <a href="{{ route('profile.view') }}">🤵 Account</a>
                <a href="{{ route('about') }}">📄 ABOUT LONELY HEARTS DATING</a>
                <a href="{{ route('create_ad') }}">♥️ LOOKING FOR LOVE?</a>
              </div>
            </nav>
    
            <div class="lh-sub-header d-flex mt-3 justify-content-between">
              <h3 id="current-date">FRI 6th Jun 2025</h3>
              <a href="#" class="lh-location" id="ctaLocation" data-target="locationPopup"
                ></a
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
            <a href="{{ route('about') }}" class="lh-link">WHAT IS LONELY HEARTS?</a>
            <a href="{{ route('how_it_works') }}" class="lh-link">HOW TO USE LONELY HEARTS?</a>
            <a href="{{ route('toc') }}" class="lh-link">Terms of service</a>
            <a href="{{ route('policy') }}" class="lh-link">Privacy policy</a>
            <p class="mt-3 mb-3">&copy; {{ date('Y') }} My App. All rights reserved.</p>
        </div>
    </footer>

    <!-- bootstrap script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"
    ></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
      async function getLocation() {
        try {
            const res = await fetch("{{ url('/my-location') }}");
            const data = await res.json();
            return data;
        } catch (err) {
            console.error("Error:", err);
            return null;
        }
      }

      document.addEventListener("DOMContentLoaded", async () => {
          const location = await getLocation(); // waits for fetch
          document.getElementById("ctaLocation").textContent = location.city || location.cityName || "Unknown";
      });
    </script>
    @yield('script')
</body>
</html>
