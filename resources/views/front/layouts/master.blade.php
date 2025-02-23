@use('App\Models\Language')
@php

    $lang = Language::where('locale', Session::get('front-locale', 'en'))?->whereNull('deleted_at')->first();
@endphp

<!DOCTYPE html>
<html lang="{{ Session::get('front-locale', 'en') }}">

<head>
    @include('front.layouts.head')
</head>

<body class="theme {{($lang->is_rtl) ? 'rtl' : 'ltr'}} {{ session('front_theme', '') }}">
    @include('front.layouts.header')

    @yield('content')

<!-- Loader Start -->
<div class="loader-box" id="fullScreenLoader">
<img class="img-fluid" alt="loader-image" src="{{ asset('front/images/preloader.gif') }}">
</div>
<!-- Loader End -->

    @include('front.layouts.footer')

    @include('front.layouts.script')

    @stack('scripts')

</body>
</html>