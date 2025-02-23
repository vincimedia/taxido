<!-- Page Header Start-->
<div class="page-main-header">
    <div class="main-header row">
        <div class="main-header-left d-lg-none d-flex">
            <div
                class="d-flex align-items-center flex-lg-row flex-row-reverse justify-content-lg-between justify-content-end gap-md-3 gap-2">
                <div class="logo-wrapper">
                    <a href="{{ route('admin.dashboard.index') }}">
                        @if (isset(getSettings()['general']['light_logo_image']))
                            <img src="{{ getSettings()['general']['light_logo_image']?->original_url }}" alt="user"
                                class="light-mode">
                            <img src="{{ getSettings()['general']['dark_logo_image']?->original_url }}" alt="user"
                                class="dark-mode">
                        @else
                            <img src="{{ asset('images/logo.svg') }}" alt="user">
                        @endif
                    </a>
                </div>
                <a href="javascript:void(0)" class="toggle">
                    <img src="{{ asset('images/svg/toggle.svg') }}" class="sidebar-toggle" alt="">
                </a>
            </div>
        </div>
        <div class="nav-left w-auto d-lg-block d-none">
            <ul class="nav-menus">
                <li class="onhover-dropdown">
                    <div class="quick-dropdown-box">
                        <div class="d-flex gap-1 align-items-center new-btn custom-padding">
                            <span>{{ __('static.quick_links') }}</span>
                            <i class="ri-add-line add"></i>
                        </div>
                        <div class="onhover-show-div">
                            <div class="dropdown-title">
                                <h4>{{ __('static.quick_links') }}</h4>
                            </div>

                            <ul class="h-custom-scrollbar dropdown-list">
                                @php
                                    $quickLinks = get_quick_links() ?? [];
                                @endphp
                                @forelse($quickLinks as $link)
                                    <li>
                                        <a href="{{ route($link['route']) }}">
                                            <div class="svg-box">
                                                <i class="{{ $link['icon'] }}"></i>
                                            </div>
                                            <span>{{ $link['label'] }}</span>
                                        </a>
                                    </li>
                                @empty
                                    <li class="no-notifications">
                                        <div class="media">
                                            <div class="no-data mt-3">
                                                <img src="{{ url('/images/no-data.png') }}" alt="">
                                                <h6 class="mt-2">{{ __('static.quick_links_not_found') }}</h6>
                                            </div>
                                    </li>
                                @endforelse

                            </ul>

                        </div>
                    </div>
                </li>
                <li>
                    @if (Route::has('admin.taxido.ride.create'))
                        <a href="{{ route('admin.taxido.ride.create') }}" class="btn btn-outline"
                            data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="">
                            {{ __('static.pos') }}
                        </a>
                    @endif
                </li>
            </ul>
        </div>
        <div class="nav-right col">
            <ul class="nav-menus">
                <li class="d-flex onhover-dropdown">
                    <a href="javascript:void(0)" data-bs-toggle="tooltip" data-bs-placement="bottom"
                        {{-- class="cleaning" --}} data-bs-title="Clear Cache">
                        <i class="ri-brush-line"></i>
                    </a>
                </li>
                <li class="onhover-dropdown">
                    <a href="{{ route('home') }}" target="_blank" data-bs-toggle="tooltip" data-bs-placement="bottom"
                        data-bs-title="Browse Frontend">
                        <i class="ri-global-line"></i>
                    </a>
                </li>
                <li class="onhover-dropdown">
                    <a class="txt-dark" href="javascript:void(0)">
                        <h6 class="mb-0 text-dark">{{ strtoupper(Session::get('locale', 'en')) }}</h6>
                    </a>
                    <ul class="language-dropdown onhover-show-div p-20  language-dropdown-hover">
                        @forelse (getLanguages() as $lang)
                            <li>
                                <a href="{{ route('admin.lang', @$lang?->locale) }}"
                                    data-lng="{{ @$lang?->locale }}"><img class="active-icon"
                                        src="{{ @$lang?->flag ?? asset('images/flags/default.png') }}"><span>{{ @$lang?->name }}
                                        ({{ @$lang?->locale }})
                                    </span></a>
                            </li>
                        @empty
                            <li>
                                <a href="{{ route('admin.lang', 'en') }}" data-lng="en"><img class="active-icon"
                                        src="{{ asset('images/flags/US.png') }}"><a href="javascript:void(0)"
                                        data-lng="en">{{ __('static.english') }}</a>
                            </li>
                        @endforelse
                    </ul>
                </li>
                <li class="dark-light-mode onhover-dropdown" id="dark-mode">
                    <i class="ri-moon-line  light-mode"></i>
                    <i class="ri-sun-line dark-mode"></i>
                </li>

                <li class="onhover-dropdown">
                    <div class="notify-bell">
                        <i class="ri-notification-2-line"></i>
                    </div>
                    @php
                        $notifications = auth()
                            ?->user()
                            ?->notifications()
                            ?->whereNull('read_at')
                            ?->latest()
                            ?->take(5)
                            ?->get();
                    @endphp
                    @if ($notifications?->count() > 0)
                        <span class="badge badge-secondary">{{ $notifications->count() }}</span>
                    @endif
                    <div class="notification-dropdown onhover-show-div">
                        <h5 class="dropdown-title">{{ __('static.recent_notifications') }}</h5>
                        <ul class="notification-box custom-scrollbar" id="notification-list">
                            @forelse ($notifications as $notification)
                                <li data-id="{{ $notification->id }}">
                                    <div class="media">
                                        <div class="d-flex align-items-start gap-2">
                                            <div class="media-img bg-white">
                                                @if ($notification->module == 'ticket')
                                                    <i class="ri-ticket-2-line text-primary"></i>
                                                @else
                                                    <i class="ri-notification-2-line text-primary"></i>
                                                @endif
                                            </div>
                                            <div class="media-content">
                                                <div>
                                                    <a href="javascript:void(0)"
                                                        class="text-dark">{{ $notification->data['message'] }}</a>
                                                    <p>{{ $notification->created_at->diffForHumans() }}</p>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                                <li class="no-notifications">
                                    <div class="media">
                                        <div class="no-data mt-3">
                                            <img src="{{ url('/images/no-data.png') }}" alt="">
                                            <h6 class="mt-2">{{ __('static.no_notification_found') }}</h6>
                                        </div>
                                </li>
                            @endforelse
                        </ul>
                        @if ($notifications->count())
                            <div class="dropdown-footer">
                                <a class="btn btn-solid view-chat w-100"
                                    href="{{ route('admin.notification.index') }}">{{ __('static.all_notifications') }}</a>
                            </div>
                        @endif
                    </div>
                </li>
                <li class="onhover-dropdown">
                    <div class="media align-items-center profile-box">
                        <div class="profile-img">
                            @if (Auth::user()->profile_image)
                                <img src="{{ Auth::user()->profile_image->original_url }}">
                            @else
                                <div class="user-round">
                                    <h6>{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</h6>
                                </div>
                            @endif
                        </div>
                        <div class="d-lg-block d-none">
                            <h6>{{ Auth::user()->name }}</h6>
                            <span class="d-md-block d-none">{{ Auth::user()->getRoleNames()->first() }}</span>
                        </div>
                    </div>
                    <div class="profile-dropdown onhover-show-div profile-dropdown-hover custom-scrollbar">
                        <ul>
                            @if (Route::has('admin.account.profile'))
                                <li>
                                    <a href="{{ route('admin.account.profile') }}">
                                        <i class="ri-user-line"></i>
                                        <span>{{ __('static.edit_profile') }}</span>
                                    </a>
                                </li>
                            @endif
                            <li>
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="ri-logout-box-line"></i>
                                    <span>{{ __('Logout') }}</span>
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="d-none"
                                    id="logout-form">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- Page Header Ends -->
