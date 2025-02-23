<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="{{ $content['seo']['meta_description'] ?? '' }}">
<meta name="keywords" content="{{ $content['seo']['meta_tags'] ?? '' }}">
<meta property="og:image" content="{{ asset($content['seo']['meta_image'] ?? '') }}">
<meta property="og:title" content="{{ $content['seo']['og_title'] ?? '' }}">
<meta property="og:description" content="{{ $content['seo']['og_description'] ?? '' }}">
<meta name="author" content="Taxido">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon"
    href="{{ isset($content['header']['favicon']) ? asset($content['header']['favicon']) : asset('favicon.ico') }}"
    type="image/x-icon">

<link rel="shortcut icon"
    href="{{ isset($content['header']['favicon']) ? asset($content['header']['favicon']) : asset('favicon.ico') }}"
    type="image/x-icon">

<title>{{ config('app.title') }} - @yield('title')</title>
<link rel="apple-touch-icon" href="{{ asset('front/images/logo/favicon.png') }}">
<meta name="theme-color" content="#7C57FF">
<link rel="canonical" href="{{ url()?->current() }}" />
<meta property="og:url" content="{{ url()?->current() }}" />
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="apple-mobile-web-app-title" content="Taxido">
<meta name="msapplication-TileImage" content="{{ asset('front/images/logo/favicon.png') }}">
<meta name="msapplication-TileColor" content="#FFFFFF">

<!-- Font link -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap">

<!-- bootstrap css -->
<link rel="stylesheet" id="rtl-link" type="text/css" href="{{ asset('front/css/vendors/bootstrap.css') }}">

<!-- Swiper css link -->
<link rel="stylesheet" type="text/css" href="{{ asset('front/css/vendors/swiper.css') }}">

{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css" /> --}}

<!-- Swiper css link -->
<link rel="stylesheet" type="text/css" href="{{ asset('front/css/vendors/wow-animate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('front/css/vendors/wow.css') }}">

<!-- Swiper css link -->
<link rel="stylesheet" type="text/css" href="{{ asset('front/css/vendors/remixicon.css') }}">

<!-- aos css -->
<link rel="stylesheet" type="text/css" href="{{ asset('front/css/aos.css') }}">

@if (!empty($content['analytics']['measurement_id']) && $content['analytics']['measurement_id'] != 'UA-XXXXXX-XX')
    <!-- Global site tag - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $content['analytics']['measurement_id'] }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', <?php echo $content['analytics']['measurement_id']; ?>);
    </script>
@endif

<!-- Conditional Facebook Pixel Script -->
@if (!empty($content['analytics']['pixel_id']) && $content['analytics']['pixel_id'] != 'XXXXXXXXXXXXX')
    <!-- Facebook Pixel -->
    <script>
        ! function(f, b, e, v, n, t, s) {
            if (f.fbq) return;
            n = f.fbq = function() {
                n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', <?php echo $content['analytics']['pixel_id']; ?>);
        fbq('track', 'PageView');
    </script>
    <!-- Google tag (gtag.js) -->
@endif
@if (!empty($content['analytics']['tag_id']) && $content['analytics']['tag_id'] != 'XXXXXXXXXXXXX')
    <script async src="https://www.googletagmanager.com/gtag/js?id=TAG_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'TAG_ID');
    </script>
@endif
@vite(['resources/scss/front-style.scss'])
