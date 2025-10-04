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
    <style>
      .heart {
        background: #dd775a;
        width: 10px;
        height: 10px;
        box-shadow:
          10px -10px 0 black, 20px -10px 0 black, 0px -10px 0 black,
          60px -10px 0 black, 70px -10px 0 black, 80px -10px 0 black,
          10px 0px 0 #dd775a, 20px 0px 0 #dd775a, 30px 0px 0 black, -10px 0px 0 black,
          50px 0px 0 black, 70px 0px 0 #dd775a, 80px 0px 0 #c45a41, 60px 0px 0 #dd775a, 90px 0px 0 black,
          40px 10px 0 black, 60px 10px 0 #dd775a, 70px 10px 0 #dd775a, 80px 10px 0 #dd775a, 90px 10px 0 #c45a41, 100px 10px 0 black,
          50px 10px 0 #dd775a, -20px 10px 0 black, -10px 10px 0 #dd775a, 0px 10px 0 #dd775a, 10px 10px 0 white, 20px 10px 0 #dd775a, 30px 10px 0 #dd775a,
          -20px 20px 0 black, -10px 20px 0 #dd775a, 0px 20px 0 white, 10px 20px 0 #dd775a, 20px 20px 0 #dd775a, 30px 20px 0 #dd775a,
          40px 20px 0 #dd775a, 50px 20px 0 #dd775a, 60px 20px 0 #dd775a, 70px 20px 0 #dd775a, 80px 20px 0 #dd775a, 90px 20px 0 #c45a41, 100px 20px 0 black,
          -20px 30px 0 black, -10px 30px 0 #dd775a, 0px 30px 0 #dd775a, 10px 30px 0 #dd775a, 20px 30px 0 #dd775a, 30px 30px 0 #dd775a,
          40px 30px 0 #dd775a, 50px 30px 0 #dd775a, 60px 30px 0 #dd775a, 70px 30px 0 #dd775a, 80px 30px 0 #dd775a, 90px 30px 0 #c45a41, 100px 30px 0 black,
          -20px 40px 0 black, -10px 40px 0 #dd775a, 0px 40px 0 #dd775a, 10px 40px 0 #dd775a, 20px 40px 0 #dd775a, 30px 40px 0 #dd775a,
          40px 40px 0 #dd775a, 50px 40px 0 #dd775a, 60px 40px 0 #dd775a, 70px 40px 0 #dd775a, 80px 40px 0 #dd775a, 90px 40px 0 #c45a41, 100px 40px 0 black,
          -10px 50px 0 black, 0px 50px 0 #dd775a, 10px 50px 0 #dd775a, 20px 50px 0 #dd775a, 30px 50px 0 #dd775a,
          40px 50px 0 #dd775a, 50px 50px 0 #dd775a, 60px 50px 0 #dd775a, 70px 50px 0 #dd775a, 80px 50px 0 #c45a41, 90px 50px 0 black,
          0px 60px 0 black, 10px 60px 0 #dd775a, 20px 60px 0 #dd775a, 30px 60px 0 #dd775a,
          40px 60px 0 #dd775a, 50px 60px 0 #dd775a, 60px 60px 0 #dd775a, 70px 60px 0 #c45a41, 80px 60px 0 black,
          10px 70px 0 black, 20px 70px 0 #dd775a, 30px 70px 0 #dd775a,
          40px 70px 0 #dd775a, 50px 70px 0 #dd775a, 60px 70px 0 #c45a41, 70px 70px 0 black,
          20px 80px 0 black, 30px 80px 0 #dd775a, 40px 80px 0 #dd775a, 50px 80px 0 #c45a41, 60px 80px 0 black,
          30px 90px 0 black, 40px 90px 0 #c45a41, 50px 90px 0 black;
        animation: heart-beat .8s ease-in-out infinite alternate;
        transform: scale(0.225); /* scaled down from 0.25 to 0.225 (≈0.9x) */
        transform-origin: top left;
      }
      
      .position-center {
        margin-bottom: 10px;
        margin-right: 24px;
      }
      
      @keyframes heart-beat {
        0%, 50% { transform: scale(0.225); }
        25%, 100% { transform: scale(0.25); }
      }
    </style>   
</head>
<body>
    {{-- Header --}}
    <header class="lh-header">
        <div class="container-sm">
            <nav>
                @yield('back')

                <a href="{{ route('home') }}" class="d-flex align-items-center justify-content-center">
                    <div class="heart position-center"></div>
                    <span style="font-size: 20px;text-transform:uppercase;color: #fff;text-decoration:none;">Lonely Hearts</span>
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
