<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8" xmlns:fb="http://ogp.me/ns/fb#">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="/files/favicons/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/files/favicons/apple.png">
    <link rel="manifest" href="/manifest.webmanifest">
    <title>F-AUTOMATION – Автоматизация функций социальных сетей</title>
    <meta name="description" content="Трансялции, постинг, истории в социальных сетяъ есть и остаются лучшими средствами продвижения услуг и товаров. F-AUTOMATION – экономия вашего времени">
    <meta property="og:title" content="F-AUTOMATION – Автоматизация функций социальных сетей"/>
    <meta property="og:description" content="Трансялции, постинг, истории в социальных сетяъ есть и остаются лучшими средствами продвижения услуг и товаров. F-AUTOMATION – экономия вашего времени"/>
    <meta property="og:image" content="https://f-automation.ru/files/background.jpg"/>
    <meta property="og:url" content= "https://f-automation.ru/" />
    <meta property="og:locale" content="ru_RU" />
    <link rel="stylesheet" href="/assets/css/preloader.css">
    <link rel="stylesheet" href="/assets/css/index.css">
</head>
<body>
    <div class="preloader">
        <div class="preloaderWrapper">
            <div class="preloaderContainer">
              <div class="preloaderItemTop"></div>
              <div class="preloaderItemBottom"></div>
            </div>
            <div class="preloaderSquare">
                <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="50" height="50" fill="#FF5437"/>
                    <path d="M19.0329 35V15.12H32.3889V18.508H22.8969V23.632H30.7929V26.768H22.8969V35H19.0329Z" fill="white"/>
                </svg>
            </div>
        </div>
    </div>

    @yield('content')

    <script src="/assets/js/jquery-3.6.1.min.js"></script>
    <script src="/assets/js/functions.js"></script>
    <script src="/assets/js/index.js"></script>
    <script src="/assets/js/ajaxForm.js"></script>
</body>

</html>
