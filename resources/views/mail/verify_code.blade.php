<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email verification code</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous" />

    <style>
        @import url("https://lonelyhearts.me/assets/font-style.ttf");
        @import url('https://fonts.googleapis.com/css2?family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&display=swap');

        body {
            font-family: 'VCR OSD Mono', sans-serif;
            background: #FFF5DF;
        }

        .mail-wrapper {
            padding: 0 20px;
            background: #FFF5DF;
        }

        .mail-header {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 0;
        }

        .mail-header-logo img {
            width: 300px;
        }

        .mail-body {
            background: #88A5A0;
            border: 4px solid #5C7377;
            padding: 40px 20px;
        }

        .mail-icon {
            width: 140px;
        }

        .mail-body img {
            margin-bottom: 20px;
        }

        .mail-body h1 {
            font-size: 24px;
            text-transform: uppercase;
        }

        .mail-body p {
            font-family: 'Merriweather', serif;
            text-align: center;
        }

        .verification-code {
            text-align: center;
            font-size: 56px;
            letter-spacing: 10px;
        }

        .mail-after-body {
            padding: 30px 0;
            border-bottom: 2px solid #212121;
        }

        .mail-after-body p {
            font-family: 'Merriweather', serif;
            line-height: 190%;
        }

        .mail-footer {
            padding: 16px 0;
        }

        .mail-footer small {
            text-align: center;
            font-family: 'Merriweather', serif;
            font-size: 12px;
            display: block;
        }

        .mail-footer-link {
            font-size: 12px;
            font-family: 'Merriweather', serif;
            color: #212121;
            display: block;
            text-align: center;
            margin-bottom: 20px;
        }

        .mail-footer-logo img {
            width: 180px;
        }
    </style>
</head>
<body>
    <div class="mail-wrapper">
        <div class="mail-header">
            <a class="mail-header-logo" href="{{ route('home') }}">
                <img src="https://ik.imagekit.io/pras09jeor/logo-new.svg?updatedAt=1758210771190" alt="Mail logo lonely hearts">
            </a>
        </div>
        <div class="mail-body">
            <div style="display: flex; justify-content: center;">
                <img class="mail-icon" src="https://lonelyhearts.me/assets/envelope-icon.png" alt="Envelope icon">
            </div>
            <h1 style="text-align: center;">Your verification code</h1>
            <p>This code will expire in 10 minutes.</p>
            <h2 class="verification-code">{{ $code }}</h2>
        </div>
        <div class="mail-after-body">
            <p>Simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
        </div>
        <div class="mail-footer">
            <small style="text-align: center;">You are receiving this email because you opted in via our website.</small>
            <a class="mail-footer-link" href="#">Unsubscribe.</a>
            <div style="display: flex; justify-content: center;">
                <a class="mail-footer-logo" href="{{ route('home') }}">
                    <img src="https://ik.imagekit.io/pras09jeor/logo-new.svg?updatedAt=1758210771190" alt="Mail logo lonely hearts">
                </a>
            </div>
        </div>
    </div>
</body>
</html>