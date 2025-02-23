<!-- Header section start -->
@use('App\Models\Language')
@php

    $flag = Language::where('locale', Session::get('front-locale', 'en'))->pluck('flag')->first();
@endphp
<header class="wow fadeIn">
    <div class="container">
        <div class="top-header">
            <div class="header-left">
                <button class="navbar-toggler btn">
                    <i class="ri-menu-line"></i>
                </button>
                <a href="{{ route('home') }}" class="logo-box">
                @if(file_exists_public(@$content['header']['logo']))
                    <img class="img-fluid" alt="Logo" src="{{ asset(@$content['header']['logo']) }}"> @endif
                </a>
            </div>

            <div class="header-middle">
                <div class="menu-title">
                    <h3>Menu</h3>
                    <a href="#!" class="close-menu"><i class="ri-close-line"></i></a>
                </div>
                <ul class="navbar-nav">
                    @forelse ($content['header']['menus'] as $menu)
                        <li class="nav-item">
                            @if(Route::is('home'))
                            <a class="nav-link" href="#{{ Str::slug($menu) }}">{{ $menu }}</a>
                            @else
                            <a class="nav-link" href="{{ route('home') }}#{{ Str::slug($menu) }}">{{ $menu }}</a>
                            @endif
                        </li>
                    @empty
                    @endforelse
                </ul>
            </div>

            <div class="header-right">
                <div class="dropdown language-dropdown">
                    <button class="btn language-btn" data-bs-toggle="dropdown">
                        <img class="img-fluid" alt="flag-image" src="{{ @$flag ?? asset('images/flags/default.png') }}">
                        <span>{{ strtoupper(Session::get('front-locale', 'en')) }}</span>
                    </button>
                    <ul class="dropdown-menu">
                        @forelse (getLanguages() as $lang)
                            <li>
                                <a class="dropdown-item" href="{{ route('lang', @$lang?->locale) }}"
                                    data-lng="{{ @$lang?->locale }}">
                                    <img class="img-fluid" alt="flag-image"
                                        src="{{ @$lang?->flag ?? asset('images/flags/default.png') }}">
                                    <span>{{ @strtoupper($lang?->locale) }}</span>
                                </a>
                            </li>
                        @empty
                            <li>
                                <a href="{{ route('lang', 'en') }}" data-lng="en">
                                    <img class="active-icon img-fluid" src="{{ asset('images/flags/US.png') }}">
                                    <span data-lng="en">{{ __('static.english') }}</span>
                                </a>
                            </li>
                        @endforelse
                    </ul>
                </div>
                <button class="btn dark-light-mode" id="dark-mode">
                    <i class="ri-moon-line light-mode"></i>
                    <i class="ri-sun-line dark-mode"></i>
                </button>

                <a href="{{ route('ticket.form') }}"
                    class="btn gradient-bg-color">{{ @$content['header']['btn_text'] }}</a>
            </div>
        </div>
        <a href="#!" class="overlay"></a>
</header>
<!-- Header section end -->
