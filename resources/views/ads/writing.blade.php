<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Lonely Hearts</title>

    <!-- bootstrap css -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr"
      crossorigin="anonymous"
    />

    <!-- google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&display=swap"
      rel="stylesheet"
    />

    <!-- favicon -->
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" />

    <!-- css -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" />
  </head>
  <body>
    <p id="current-date" style="display: none"></p>
    <p id="lhHamburger" style="display: none"></p>
    <div
      class="d-flex justify-content-center align-items-center w-100"
      style="flex-direction: column !important; min-height: 100vh"
    >
      <img
        src="{{ asset('images/loading-icon.png') }}"
        alt="Loading icon"
        style="width: 180px; margin-bottom: 20px"
      />
      <h1 class="lh-title" id="loading-text">Writing ad...</h1>
    </div>
    <!-- bootstrap script -->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
      crossorigin="anonymous"
    ></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
      // Typing dots animation
      const loadingText = document.getElementById("loading-text");
      let dotCount = 0;

      const interval = setInterval(() => {
        dotCount = (dotCount + 1) % 4;
        loadingText.textContent = "WRITING AD" + ".".repeat(dotCount);
      }, 500);

      // Redirect after 5 seconds
      setTimeout(() => {
        clearInterval(interval); // stop the animation
        window.location.href = "{{ route('confirmation', ['box' => $box]) }}";
      }, 5000);
    </script>
  </body>
</html>
