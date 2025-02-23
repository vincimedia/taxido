@use('Modules\Taxido\Models\Zone')
@use('Modules\Taxido\Models\Driver')
@php
    $zones = Zone::where('status', true)->get(['id', 'name']);
    $drivers = Driver::with(['onRides'])
        ?->where('status', true)
        ->whereNull('deleted_at')
        ?->get();
    $settings = getTaxidoSettings();
    $vehicleTypes = getVehicleType();
    $ride = $drivers->first();
    $paymentLogoUrl = getPaymentLogoUrl($ride->payment_method);
@endphp

@extends('admin.layouts.master')
@section('title', __('taxido::static.drivers.driver_location'))
@section('content')
    <div class="search-box">
        <div class="row g-0">
            <div class="custom-col-xxl-3 custom-col-lg-4">
                <button class="btn toggle-menu">
                    <i class="ri-menu-2-line"></i>
                </button>
                <div class="left-location-box custom-scrollbar">
                    <div class="title">
                        <h4>{{ __('taxido::static.locations.taxi_drivers') }}</h4>
                        <button class="location-close-btn btn d-xl-none">
                            <i class="ri-close-line"></i>
                        </button>
                    </div>
                    <div class="search-input">
                        <div class="position-relative w-100">
                            <input type="search" id="driver-search" placeholder="Search Driver" class="form-control">
                            <i class="ri-search-line"></i>
                        </div>
                        <button id="refresh-map" class="btn btn-primary">
                            <i class="ri-refresh-line"></i>
                            <span>{{ __('taxido::static.locations.refresh') }}</span>
                        </button>
                    </div>

                    <ul class="nav nav-tabs driver-tabs" id="myTab">
                        <li class="nav-item">
                            <button class="nav-link active" id="all" data-bs-toggle="tab" data-bs-target="#all-pane">
                                {{ __('taxido::static.locations.all') }} <span id="all-count">(0)</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane">
                                {{ __('taxido::static.locations.onride') }} <span id="onride-count">(0)</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab"
                                data-bs-target="#profile-tab-pane">
                                {{ __('taxido::static.locations.offline') }} <span id="offline-count">(0)</span>
                            </button>
                        </li>
                    </ul>
                    <div id="no-data-message" class="no-data mt-3" style="display: none;">
                        <img src="{{ asset('images/no-data.png') }}" alt="no-data" loading="lazy">
                        <h6 class="mt-2">{{ __('static.drivers.no_driver_found') }}</h6>
                    </div>
                    <div class="tab-content driver-content" id="myTabContent">
                        <!-- On Ride Tab -->
                        <div class="tab-pane fade" id="home-tab-pane">
                            <div class="accordion location-accordion" id="driver-list">
                                @if ($drivers->isEmpty())
                                    <div class="no-data mt-3" id="no-data-onride">
                                        <img src="{{ url('/images/no-data.png') }}" alt="">
                                        <h6 class="mt-2">{{ __('static.drivers.no_driver_found') }}</h6>
                                    </div>
                                @else
                                    @foreach ($drivers as $driver)
                                        @php
                                            if (!$driver->is_verified || !$driver->status) {
                                                continue;
                                            }
                                            $statusClass = 'driver-deactive';
                                            $statusTitle = 'Offline';
                                            if ($driver->is_online) {
                                                if ($driver->is_on_ride) {
                                                    $statusClass = 'driver-not-assign';
                                                    $statusTitle = 'On Ride';
                                                } else {
                                                    $statusClass = 'driver-active';
                                                    $statusTitle = 'Online';
                                                }
                                            }
                                            $driverStatus = $driver->is_online
                                                ? ($driver->is_on_ride
                                                    ? 'on_ride'
                                                    : 'online')
                                                : 'offline';
                                        @endphp

                                        @if ($driverStatus == 'on_ride')
                                            <div class="accordion-item" id="driver-accordion-item-{{ $driver->id }}"
                                                data-vehicle-type="{{ $driver->vehicle_info?->vehicle?->id }}"
                                                data-status="{{ $driverStatus }}"
                                                data-zone-id="{{ $driver->zones->first()->id ?? '' }}">
                                                <h4 class="accordion-header">
                                                    <div class="position-relative">
                                                        @if ($driver?->profile_image?->original_url)
                                                            <img src="{{ $driver->profile_image->original_url }}"
                                                                alt="" class="img">
                                                        @else
                                                            <div class="initial-letter">
                                                                <span>{{ strtoupper($driver->name[0]) }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="name">{{ $driver->name }}

                                                        </span>

                                                        <div class="rate-box">
                                                            <i class="ri-star-fill"></i>
                                                            {{ $driver->reviews->avg('rating') ? number_format($driver->reviews->avg('rating'), 1) : 'Unrated' }}
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                        class="btn btn-solid btn-sm ms-auto view-location-btn"
                                                        data-driver-id="{{ $driver->id }}">{{ __('taxido::static.locations.view_location') }}</button>
                                                    <button class="accordion-button" data-bs-toggle="collapse"
                                                        data-bs-target="#panelsStayOpen-collapse{{ $driver->id }}">
                                                        <i class="ri-arrow-down-s-line"></i>
                                                    </button>
                                                </h4>
                                                @if ($driver->is_on_ride)
                                                    @foreach ($driver->onRides as $ride)
                                                        <div id="panelsStayOpen-collapse{{ $driver->id }}"
                                                            class="accordion-collapse collapse">
                                                            <div class="accordion-body">
                                                                <ul class="details-list">
                                                                    <li><span
                                                                            class="bg-light-primary">#{{ $ride?->ride_number }}</span>
                                                                    <li>
                                                                        <span
                                                                            class="vehicle-number">{{ $driver->vehicle_info?->plate_number }}</span>
                                                                    </li>
                                                                    <li><span
                                                                            class="badge badge-progress">{{ $ride->ride_status->name }}</span>
                                                                    </li>
                                                                </ul>
                                                                <ul class="location-driver-details">
                                                                    <li>
                                                                        <div class="driver-main-box">
                                                                            <h5>{{ __('taxido::static.locations.rider_details') }}:
                                                                            </h5>
                                                                            <div class="name-box">
                                                                                @if ($ride->rider['profile_image']?->original_url)
                                                                                    <img src="{{ $ride->rider['profile_image']->original_url }}"
                                                                                        alt="" class="img">
                                                                                @else
                                                                                    <div class="initial-letter">
                                                                                        <span>{{ strtoupper($ride->rider['name'][0]) }}</span>
                                                                                    </div>
                                                                                @endif
                                                                                <div>
                                                                                    <h5 class="name">
                                                                                        {{ $ride->rider['name'] }}
                                                                                    </h5>
                                                                                    <h6>{{ $ride->rider['email'] }}</h6>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                    <li>{{ __('taxido::static.locations.service') }}
                                                                        <span>{{ $ride?->service?->name }}</span>
                                                                    </li>
                                                                    <li>{{ __('taxido::static.locations.service_category') }}
                                                                        <span>{{ $ride?->service_category?->name }}</span>
                                                                    </li>
                                                                    <li class="detail-item">
                                                                        <h5>{{ __('taxido::static.rides.vehicle_type') }}
                                                                        </h5>
                                                                        <div class="vehicle-box">
                                                                            <img src="{{ $ride?->driver?->vehicle_info?->vehicle?->vehicle_image?->original_url ?? '/images/user.png' }} "
                                                                                alt="">
                                                                            <span>
                                                                                {{ $ride?->driver?->vehicle_info?->vehicle?->name }}</span>
                                                                        </div>
                                                                    </li>
                                                                    <li class="detail-item">
                                                                        <h5>{{ __('taxido::static.rides.zones') }}</h5>
                                                                        <span>{{ $ride?->zones?->pluck('name')->implode(', ') }}</span>
                                                                    </li>
                                                                    <li class="ride-main">
                                                                        <h5>{{ __('taxido::static.rides.payment_status') }}:
                                                                        </h5>
                                                                        <span
                                                                            class="badge badge-pending text-white">{{ ucfirst($ride?->payment_status) }}</span>
                                                                    </li>
                                                                    <li class="detail-item">
                                                                        <h5>{{ __('taxido::static.rides.payment_method') }}
                                                                        </h5>
                                                                        <span>
                                                                            <img src="{{ $paymentLogoUrl ?: asset('images/payment/cod.png') }}"
                                                                                class="img-fluid cash-img"
                                                                                alt="{{ $ride?->payment_method }}">
                                                                        </span>
                                                                    </li>
                                                                    <li>
                                                                        {{ __('taxido::static.locations.distance') }}
                                                                        <span>{{ $ride?->distance }}
                                                                            {{ $ride?->distance_unit }}</span>
                                                                    </li>
                                                                    <li class="detail-item">
                                                                        <h5>{{ __('taxido::static.rides.date_time') }}</h5>
                                                                        <span>{{ $ride->created_at->format('Y-m-d H:i:s A') }}</span>
                                                                    </li>
                                                                </ul>

                                                                <div class="button-details-box">
                                                                    <a href="{{ route('admin.ride.details', $ride->ride_number) }}"
                                                                        class="btn">
                                                                        {{ __('taxido::static.locations.view_more') }}
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div id="panelsStayOpen-collapse{{ $driver->id }}"
                                                        class="accordion-collapse collapse">
                                                        <div class="no-ride-data">
                                                            <p>{{ __('taxido::static.locations.no_rides_yet') }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <!-- All Tab -->
                        <div class="tab-pane fade show active" id="all-pane">
                            <div class="accordion location-accordion" id="driver-list">
                                @if ($drivers->isEmpty())
                                    <div class="no-data mt-3">
                                        <img src="{{ asset('images/no-data.png') }}" alt="no-data" loading="lazy">
                                        <h6 class="mt-2">{{ __('static.driver.no_driver_found') }}</h6>
                                    </div>
                                @else
                                    @foreach ($drivers as $driver)
                                        @php
                                            if (!$driver->is_verified || !$driver->status) {
                                                continue;
                                            }
                                            $statusClass = 'driver-deactive';
                                            $statusTitle = 'Offline';
                                            if ($driver->is_online) {
                                                if ($driver->is_on_ride) {
                                                    $statusClass = 'driver-not-assign';
                                                    $statusTitle = 'On Ride';
                                                } else {
                                                    $statusClass = 'driver-active';
                                                    $statusTitle = 'Online';
                                                }
                                            }
                                            $driverStatus = $driver->is_online
                                                ? ($driver->is_on_ride
                                                    ? 'on_ride'
                                                    : 'online')
                                                : 'offline';
                                        @endphp

                                        <div class="accordion-item driver-item"
                                            id="driver-accordion-item-{{ $driver->id }}"
                                            data-vehicle-type="{{ $driver->vehicle_info?->vehicle?->id }}"
                                            data-status="{{ $driverStatus }}">
                                            <h4 class="accordion-header">
                                                <div class="position-relative">
                                                    @if ($driver?->profile_image?->original_url)
                                                        <img src="{{ $driver->profile_image->original_url }}"
                                                            alt="" class="img">
                                                    @else
                                                        <div class="initial-letter">
                                                            <span>{{ strtoupper($driver->name[0]) }}</span>
                                                        </div>
                                                    @endif
                                                    <span class="{{ $statusClass }}"></span>
                                                </div>
                                                <div>
                                                    <span class="name">{{ $driver->name }}</span>
                                                    <div class="rate-box">
                                                        <i class="ri-star-fill"></i>
                                                        {{ $driver->reviews->avg('rating') ? number_format($driver->reviews->avg('rating'), 1) : 'Unrated' }}
                                                    </div>
                                                </div>
                                                <button type="button"
                                                    class="btn btn-solid btn-sm ms-auto view-location-btn"
                                                    data-driver-id="{{ $driver->id }}">
                                                    {{ __('taxido::static.locations.view_location') }}
                                                </button>
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#panelsStayOpen-collapse{{ $driver->id }}">
                                                    <i class="ri-arrow-down-s-line"></i>
                                                </button>
                                            </h4>

                                            <div id="panelsStayOpen-collapse{{ $driver->id }}"
                                                class="accordion-collapse collapse">
                                                <div class="accordion-body">
                                                    @if ($driver->onRides->isEmpty())
                                                    <div class="no-ride-data">
                                                        <p>{{ __('taxido::static.locations.no_rides_yet') }}</p>
                                                    </div>
                                                    @else
                                                        @foreach ($driver->onRides as $ride)
                                                            <ul class="details-list">
                                                                <li><span
                                                                        class="bg-light-primary">#{{ $ride?->ride_number }}</span>
                                                                </li>
                                                                <li><span
                                                                        class="vehicle-number">{{ $driver->vehicle_info?->plate_number }}</span>
                                                                </li>
                                                                <li><span
                                                                        class="badge badge-progress">{{ $ride->ride_status->name }}</span>
                                                                </li>
                                                            </ul>
                                                            <ul class="location-driver-details">
                                                                <li>
                                                                    <div class="driver-main-box">
                                                                        <h5>{{ __('taxido::static.locations.rider_details') }}:
                                                                        </h5>
                                                                        <div class="name-box">
                                                                            @if ($ride->rider['profile_image']?->original_url)
                                                                                <img src="{{ $ride->rider['profile_image']->original_url }}"
                                                                                    alt="" class="img">
                                                                            @else
                                                                                <div class="initial-letter">
                                                                                    <span>{{ strtoupper($ride->rider['name'][0]) }}</span>
                                                                                </div>
                                                                            @endif
                                                                            <div>
                                                                                <h5 class="name">
                                                                                    {{ $ride->rider['name'] }}</h5>
                                                                                <h6>{{ $ride->rider['email'] }}</h6>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                                <li>{{ __('taxido::static.locations.service') }}
                                                                    <span>{{ $ride->service->name }}</span>
                                                                </li>
                                                                <li>{{ __('taxido::static.locations.service_category') }}
                                                                    <span>{{ $ride->service_category->name }}</span>
                                                                </li>
                                                                <li class="detail-item">
                                                                    <h5>{{ __('taxido::static.rides.vehicle_type') }} </h5>
                                                                    <div class="vehicle-box">
                                                                        <img src="{{ $ride?->driver?->vehicle_info?->vehicle?->vehicle_image?->original_url ?? '/images/user.png' }} "
                                                                            alt="">
                                                                        <span>{{ $ride?->driver?->vehicle_info?->vehicle?->name }}</span>
                                                                    </div>
                                                                </li>
                                                                <li class="detail-item">
                                                                    <h5>{{ __('taxido::static.rides.zones') }}</h5>
                                                                    <span>{{ $ride?->zones?->pluck('name')->implode(', ') }}</span>
                                                                </li>
                                                                <li class="ride-main">
                                                                    <h5>{{ __('taxido::static.rides.payment_status') }}:
                                                                    </h5>
                                                                    <span
                                                                        class="badge badge-pending text-white">{{ ucfirst($ride?->payment_status) }}</span>
                                                                </li>
                                                                <li class="detail-item">
                                                                    <h5>{{ __('taxido::static.rides.payment_method') }}
                                                                    </h5>
                                                                    <span>
                                                                        <img src="{{ $paymentLogoUrl ?: asset('images/payment/cod.png') }}"
                                                                            class="img-fluid cash-img"
                                                                            alt="{{ $ride->payment_method }}">
                                                                    </span>
                                                                </li>
                                                                <li>
                                                                    {{ __('taxido::static.locations.distance') }}
                                                                    <span>{{ $ride->distance }}
                                                                        {{ $ride->distance_unit }}</span>
                                                                </li>
                                                                <li class="detail-item">
                                                                    <h5>{{ __('taxido::static.rides.date_time') }}</h5>
                                                                    <span>{{ $ride->created_at->format('Y-m-d H:i:s A') }}</span>
                                                                </li>
                                                            </ul>
                                                        @endforeach
                                                    @endif

                                                    @if ($driver->onRides->isNotEmpty())
                                                        <div class="button-details-box">
                                                            <a href="{{ route('admin.ride.details', $ride->ride_number) }}"
                                                                class="btn">
                                                                {{ __('taxido::static.locations.view_more') }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Offline Tab -->
                        <div class="tab-pane fade" id="profile-tab-pane">
                            <div class="accordion location-accordion" id="driver-list">
                                @foreach ($drivers as $driver)
                                    @php
                                        if (!$driver->is_verified || !$driver->status) {
                                            continue;
                                        }
                                        if (!$driver->is_online && !$driver->is_on_ride) {
                                            $statusClass = 'driver-deactive';
                                            $statusTitle = 'Offline';
                                        }
                                    @endphp

                                    @if (!$driver->is_online && !$driver->is_on_ride)
                                        <div class="accordion-item driver-item"
                                            id="driver-accordion-item-{{ $driver->id }}"
                                            data-vehicle-type="{{ $driver->vehicle_info?->vehicle?->id }}"
                                            data-status="{{ $statusClass }}">
                                            <h4 class="accordion-header">
                                                <div class="position-relative">
                                                    @if ($driver?->profile_image?->original_url)
                                                        <img src="{{ $driver->profile_image->original_url }}"
                                                            alt="" class="img">
                                                    @else
                                                        <div class="initial-letter">
                                                            <span>{{ strtoupper($driver->name[0]) }}</span>
                                                        </div>
                                                    @endif
                                                    <span class="{{ $statusClass }}"></span>
                                                </div>
                                                <div>
                                                    <span class="name">{{ $driver->name }}</span>
                                                    <div class="rate-box">
                                                        <i class="ri-star-fill"></i>
                                                        {{ $driver->reviews->avg('rating') ? number_format($driver->reviews->avg('rating'), 1) : 'Unrated' }}
                                                    </div>
                                                </div>
                                                <button type="button"
                                                    class="btn btn-solid btn-sm ms-auto view-location-btn"
                                                    data-driver-id="{{ $driver->id }}">
                                                    {{ __('taxido::static.locations.view_location') }}
                                                </button>
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#panelsStayOpen-collapse{{ $driver->id }}">
                                                    <i class="ri-arrow-down-s-line"></i>
                                                </button>
                                            </h4>
                                            <div id="panelsStayOpen-collapse{{ $driver->id }}"
                                                class="accordion-collapse collapse">
                                                <div class="accordion-body">
                                                <div class="no-ride-data">
                                                    <p>{{ __('taxido::static.locations.no_rides_yet') }}</p>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="custom-col-xxl-9 custom-col-lg-8">
                <div class="location-map">
                    <div id="map_canvas"></div>

                    <div class="location-top-select">
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne">{{ __('taxido::static.vehicle_types.vehicle') }}</button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul class="driver-category-box" id="vehicle-type-list">
                                            @if ($vehicleTypes->isEmpty())
                                                <div class="no-data" id="no-vehicle-types">
                                                    <img src="{{ asset('images/no-data.png') }}" alt="no-data"
                                                        loading="lazy">
                                                    <h6 class="mt-2">
                                                        {{ __('taxido::static.vehicle_types.no_vehicle_types_found') }}
                                                    </h6>
                                                </div>
                                            @else
                                                <li class="category-input">
                                                    <input class="form-control" type="text" id="vehicle-type-search"
                                                        placeholder="{{ __('taxido::static.vehicle_types.search_vehicle_types') }}"
                                                        onkeyup="filterVehicleTypes()">
                                                </li>
                                                @foreach ($vehicleTypes as $vehicleType)
                                                    <li class="vehicle-list"
                                                        data-name="{{ strtolower($vehicleType['name']) }}">
                                                        <div class="form-check">
                                                            <input class="form-check-input vehicle-filter" type="checkbox"
                                                                value="{{ $vehicleType['id'] }}"
                                                                id="vehicle-{{ $vehicleType['id'] }}">
                                                            <label for="vehicle-{{ $vehicleType['id'] }}">
                                                                <img src="{{ $vehicleType['image'] }}"
                                                                    alt="{{ $vehicleType['name'] }} image"
                                                                    class="vehicle-icon img-fluid">
                                                                {{ $vehicleType['name'] }}
                                                            </label>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="zone-select-box">
                        <div class="select-label-error">
                            <select class="form-select select-2" id="zone_id" name="zone_id"
                                data-placeholder="{{ __('taxido::static.locations.select_zone') }}" required>
                                <option class="option" value="" selected>
                                    {{ __('taxido::static.locations.select_zone') }}
                                </option>
                                @foreach ($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@if ($settings['location']['map_provider'] == 'google_map')
    @includeIf('taxido::admin.driver-location.google')
@elseif($settings['location']['map_provider'] == 'osm')
    @includeIf('taxido::admin.driver-location.osm')
@endif

@push('scripts')
    <script>
        $(".select-ride-btn").click(function() {
            $(".driver-category-box").addClass("show");
        });
       
        $(".location-close-btn").click(function() {
            $(".driver-category-box").removeClass("show");
        });

        document.getElementById('driver-search').addEventListener('input', function(e) {
            var searchQuery = e.target.value.toLowerCase();
            var allDrivers = document.querySelectorAll('.accordion-item');
            var foundDriver = false;

            allDrivers.forEach(function(driverItem) {
                var driverName = driverItem.querySelector('.name').textContent.toLowerCase();
                if (driverName.includes(searchQuery)) {
                    driverItem.style.display = 'block';
                    foundDriver = true;
                } else {
                    driverItem.style.display = 'none';
                }
            });

            if (!foundDriver) {
                document.getElementById('no-data-message').style.display = 'block';
            } else {
                document.getElementById('no-data-message').style.display = 'none';
            }

            updateDriverCounts();
        });

        document.addEventListener('DOMContentLoaded', function() {
            updateDriverCounts();

            $(".toggle-menu").click(function() {
                $(".left-location-box").toggleClass("show");
            });

            function filterVehicleTypes() {
                var searchTerm = document.getElementById("vehicle-type-search").value.toLowerCase();
                var vehicleLists = document.querySelectorAll("#vehicle-type-list .vehicle-list");
                vehicleLists.forEach(function(listItem) {
                    var vehicleName = listItem.getAttribute("data-name");
                    if (vehicleName.indexOf(searchTerm) > -1) {
                        listItem.style.display = "block";
                    } else {
                        listItem.style.display = "none";
                    }
                });

                var selectedVehicleTypes = [];
                document.querySelectorAll('.vehicle-filter:checked').forEach(function(input) {
                    selectedVehicleTypes.push(input.value);
                });

                var drivers = document.querySelectorAll('.accordion-item');
                drivers.forEach(function(driverItem) {
                    var driverVehicleType = driverItem.getAttribute('data-vehicle-type');
                    if (selectedVehicleTypes.length > 0) {
                        if (selectedVehicleTypes.some(function(type) {
                                return driverVehicleType.includes(type.toLowerCase());
                            })) {
                            driverItem.style.display = "block";
                        } else {
                            driverItem.style.display = "none";
                        }
                    } else {
                        driverItem.style.display = "block";
                    }
                });

                updateDriverCounts();
                updateMapWithVehicleTypes(selectedVehicleTypes);
            }

            function updateDriverCounts() {
                var allDrivers = document.querySelectorAll('.accordion-item');
                
                var onRideDrivers = document.querySelectorAll('.accordion-item[data-status="on_ride"]');
                var offlineDrivers = document.querySelectorAll('.accordion-item[data-status="offline"]');
                var onlineDrivers = document.querySelectorAll('.accordion-item[data-status="online"]');
                
                var totalDrivers = onlineDrivers.length + onRideDrivers.length + offlineDrivers.length;

                document.getElementById('all-count').textContent = `(${totalDrivers})`;  
                document.getElementById('onride-count').textContent = `(${onRideDrivers.length})`; 
                document.getElementById('offline-count').textContent = `(${offlineDrivers.length})`;
            }

            document.getElementById('zone-filter').addEventListener('change', function() {
                var selectedZoneId = this.value;
                var drivers = document.querySelectorAll('.accordion-item');

                drivers.forEach(function(driverItem) {
                    var driverZoneId = driverItem.getAttribute('data-zone-id');
                    if (selectedZoneId === '' || driverZoneId === selectedZoneId) {
                        driverItem.style.display = 'block';
                    } else {
                        driverItem.style.display = 'none';
                    }
                });

                updateDriverCounts();
                updateMapWithZone(selectedZoneId);
            });

            function updateMapWithVehicleTypes(selectedVehicleTypes) {
                markers.forEach(function(marker) {
                    var driverVehicleType = marker.get('vehicleType');
                    if (selectedVehicleTypes.length === 0 || selectedVehicleTypes.includes(
                            driverVehicleType)) {
                        marker.setVisible(true);
                    } else {
                        marker.setVisible(false);
                    }
                });
            }

            function updateMapWithZone(zoneId) {
                markers.forEach(function(marker) {
                    var driverZoneId = marker.get('zoneId');
                    if (zoneId === '' || driverZoneId === zoneId) {
                        marker.setVisible(true);
                    } else {
                        marker.setVisible(false);
                    }
                });
            }
        });
    </script>
@endpush
