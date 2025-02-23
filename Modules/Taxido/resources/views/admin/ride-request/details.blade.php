@use('Modules\Taxido\Enums\ServiceCategoryEnum')
@use('Modules\Taxido\Enums\ServicesEnum')
@use('Modules\Taxido\Enums\RideStatusEnum')
@php
    $locations = $rideRequest->locations;
    $locationCoordinates = $rideRequest->location_coordinates;
    $settings = getTaxidoSettings();
    $paymentLogoUrl = getPaymentLogoUrl($rideRequest->payment_method);
    $currencySymbol = getDefaultCurrencySymbol();
@endphp
@extends('admin.layouts.master')
@section('title', __('taxido::static.rides.riderequests'))
@section('content')

<div class="row ride-dashboard">
    <div class="col-12">
        <div class="default-sorting mt-0">
            <div class="welcome-box">
                <div class="d-flex">
                    <h2>{{ __('taxido::static.rides.ride_request_details') }}</h2>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-6">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header card-no-border">
                        <div class="header-top">
                            <h5 class="m-0">{{ __('taxido::static.rides.general_detail') }}</h5>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <ul class="ride-details-list">
                            @if($rideRequest->start_time)
                            <li>{{ __('taxido::static.rides.start_date_time') }} :
                                 <span> {{ $rideRequest?->start_time ? \Carbon\Carbon::parse($rideRequest->start_time)->format('Y-m-d H:i:s A') : '' }} </span> 
                            </li>
                            @endif
                            @if (in_array($rideRequest?->service_category?->slug, [ServiceCategoryEnum::PACKAGE, ServiceCategoryEnum::RENTAL]))
                                <li>{{ __('taxido::static.rides.end_date_time') }} :
                                     <span> {{ $rideRequest?->end_time ? \Carbon\Carbon::parse($rideRequest->end_time)->format('Y-m-d H:i:s A') : '' }} </span> 
                                </li>
                            @endif
                            <li>
                                {{ __('taxido::static.rides.service') }} :
                                <span>{{ $rideRequest->service->name }}</span>
                            </li>
                            <li>
                                {{ __('taxido::static.rides.service_category') }} :
                                <span>{{ $rideRequest->service_category->name }}</span>
                            </li>
                            @if (in_array($rideRequest?->service?->slug, [ServicesEnum::PARCEL]))
                                <li><strong>{{ __('taxido::static.rides.parcel_otp') }}: </strong>
                                    {{ $rideRequest?->parcel_delivered_otp }}
                                </li>

                                <li><strong>{{ __('taxido::static.rides.weight') }}: </strong>
                                    {{ $rideRequest?->weight }}
                                </li>
                            @endif
                            @if (in_array($rideRequest?->service_category?->slug, [ServiceCategoryEnum::RENTAL]))
                            <li> 
                                {{ __('taxido::static.rides.no_of_days') }} : <span>{{ $rideRequest->no_of_days}}</span>
                            </li>
                            @endif
                            <li>
                                {{ __('taxido::static.rides.ride_distance') }} : <span>{{ $rideRequest?->distance }}
                                    {{ $rideRequest?->distance_unit }}</span>
                            </li>
                            <li>
                                {{ __('taxido::static.rides.zone') }} :
                                <span>{{ $rideRequest?->zones?->pluck('name')->implode(', ') }}</span>
                            </li>
                            <li>
                                {{ __('taxido::static.rides.ride_fare') }} :
                                <span>{{ getDefaultCurrencySymbol() }}
                                    {{ number_format($rideRequest?->ride_fare, 2) }}</span>
                            </li>
                            <li>
                                {{ __('taxido::static.rides.payment_method') }} :
                                <span>
                                    <img class="img-fluid h-30" alt=""
                                        src="{{ $paymentLogoUrl ?: asset('images/payment/cod.png') }}">
                                </span>
                            </li>
                        </ul>

                        @if ($rideRequest?->service_category?->slug != ServiceCategoryEnum::RENTAL)
                            <div class="total-bidding">
                                <div class="left-bidding">
                                    <div class="bidding-img">
                                        <div class="bg-round">
                                            <img src="{{ asset('images/dashboard/support/user.svg') }}" alt="">
                                            <img src="{{ asset('images/dashboard/support/1.svg') }}" class="half-circle"
                                                alt="">
                                        </div>
                                    </div>

                                    <div class="bidding-text">
                                        <h4>{{ __('taxido::static.rides.total_biddings') }}</h4>
                                        <h3>{{ $rideRequest->bids->count() }}</h3>
                                    </div>
                                </div>
                                <button class="btn bg-primary" data-bs-toggle="modal"
                                    data-bs-target="#bidding">{{ __('taxido::static.rides.biddings') }}</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xxl-12">
                <div class="row">
                    <div class="col-xxl-12">
                        <div class="card">
                            <div class="card-header card-no-border">
                                <div class="header-top">
                                    <div>
                                        <h5 class="m-0">{{ __('taxido::static.rides.rider_information') }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body pt-0 driver-details-card rider-details-card">
                                <div class="personal h-custom-scrollbar">
                                    <div class="information">
                                        <div class="border-image">
                                            <div class="profile-img">
                                                @if ($rideRequest?->rider['profile_image']?->original_url)
                                                    <img src="{{ $rideRequest?->rider['profile_image']?->original_url }}"
                                                        alt="">
                                                @else
                                                    <div class="initial-letter">
                                                        <span>{{ strtoupper($rideRequest?->rider['name'][0]) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="personal-rating">
                                            <h5>{{ $rideRequest['rider']['name'] }}</h5>

                                            <div class="rating">
                                                <span>{{ __('taxido::static.rides.rating') }}:
                                                    @php
                                                        $averageRating = 0;
                                                        if (isset($rideRequest['rider']['reviews']) && count($rideRequest['rider']['reviews']) > 0) {
                                                            $averageRating = (int) collect($rideRequest['rider']['reviews'])->avg('rating');
                                                        }
                                                        $totalStars = 5;
                                                    @endphp
                                                    @for ($i = 0; $i < $averageRating; $i++)
                                                        <img src="{{ asset('images/dashboard/star.svg') }}"
                                                            alt="Filled Star">
                                                    @endfor
                                                    @for ($i = $averageRating; $i < $totalStars; $i++)
                                                        <img src="{{ asset('images/dashboard/outline-star.svg') }}"
                                                            alt="Outlined Star">
                                                    @endfor
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="information-details">
                                        <ul>
                                            <li><strong>{{ __('taxido::static.rides.email') }} :
                                                </strong>{{ $rideRequest?->rider['email'] }} </li>
                                            <li><strong>{{ __('taxido::static.rides.contact_number') }}:
                                                </strong>+{{ $rideRequest?->rider['country_code'] }}
                                                {{ $rideRequest?->rider['phone'] }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xxl-6">
        <div class="card maps-view">
            <div class="card-header card-no-border">
                <div class="header-top">
                    <div>
                        <h5 class="m-0">{{ __('taxido::static.rides.map_view') }}</h5>
                    </div>
                    <span>{{ __('taxido::static.rides.view_all') }}</span>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="map-view" id="map-view" loading="lazy"></div>
                <div class="accordion" id="location-view">
                    <div class="accordion-item location-details">
                        <div class="accordion-header contentbox-title">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#location-viewCollapse">
                                <h4>{{ __('taxido::static.rides.location_details') }}</h4>
                            </button>
                        </div>
                        <div id="location-viewCollapse" class="accordion-collapse collapse show"
                            data-bs-parent="#location-view">
                            <div class="accordion-body">
                                <div class="">
                                    <ul class="tracking-path">
                                        @php
                                            $points = range('A', 'Z');
                                        @endphp
                                        @foreach ($rideRequest->locations as $index => $location)
                                            @if ($loop->last)
                                                <li class="end-point">
                                                    {{ $location }}<span>{{ $points[$index] }}</span>
                                                </li>
                                            @else
                                                <li class="stop-point">
                                                    {{ $location }}<span>{{ $points[$index] }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xxl-5">
        <div class="row">

            @if (in_array($rideRequest?->service?->slug, [ServicesEnum::PARCEL]))
                <div class="col-12">
                    <div class="card">
                        <div class="parcel-box">
                            <div class="left-box">
                                <img src="{{ $rideRequest->cargo_image?->original_url ?? asset('images/nodata1.webp') }}" class="img-fluid" alt="">
                            </div>
                            <ul class="right-list">
                                <li><span>{{ __('taxido::static.rides.receiver_name') }}:</span>
                                    {{ $rideRequest?->parcel_receiver['name'] }}</li>
                                <li><span>{{ __('taxido::static.rides.receiver_no') }}:</span>
                                    +{{ $rideRequest?->parcel_receiver['country_code'] }} {{ $rideRequest?->parcel_receiver['phone'] }}</li>
                                <li><span>{{ __('taxido::static.rides.parcel_otp') }}:</span>
                                    {{ $rideRequest?->parcel_delivered_otp }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if (in_array($rideRequest?->service_category?->slug, [ServiceCategoryEnum::RENTAL]))
                <div class="col-12">
                    <div class="card">
                        <div class="driver-box">
                            <div class="left-box">
                                <img src="{{ $rideRequest?->rental_vehicle?->normal_image?->original_url ?? asset('images/nodata1.webp') }}"
                                    class="img-fluid" alt="">
                            </div>
                            <ul class="right-list">
                                <li><span>{{ __('taxido::static.rides.vehicle_name') }}:</span>
                                    {{ $rideRequest?->rental_vehicle?->name }}</li>
                                    <li><span>{{ __('taxido::static.rides.driver_name') }}:</span>
                                        {{ $rideRequest?->driver?->name }}</li>
                                    <li><span>{{ __('taxido::static.rides.driver_no') }}:</span>
                                    +{{ $rideRequest?->driver?->country_code }} {{ $rideRequest?->driver?->phone }}</li>
                                <li><span>{{ __('taxido::static.rides.vehicle_registration_no') }}:</span>
                                    {{ $rideRequest?->rental_vehicle?->registration_no }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
<div class="modal fade" id="bidding">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('taxido::static.rides.bidding_request') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="bidding-modal">
                    <ul class="h-custom-scrollbar">
                        @forelse ($rideRequest?->bids as $bid)
                            <li class="d-flex align-items-center">
                                <div class="customer-image">
                                    @if ($bid?->driver?->profile_image?->original_url)
                                        <img src="{{ $bid?->driver?->profile_image?->original_url }}" alt="">
                                    @else
                                        <div class="initial-letter">
                                            <span>{{ strtoupper($bid?->driver?->name[0]) }}</span>
                                        </div>
                                    @endif
                                </div>
                               <div class="flex-grow-1">
                                    <h5>{{ $bid?->driver?->name }}</h5>
                                    <span>{{ __('taxido::static.riders.rating') }}:
                                        @php
                                            $averageRating = (int) $rideRequest?->driver?->reviews?->avg('rating');
                                            $totalStars = 5;
                                        @endphp
                                        @for ($i = 0; $i < $averageRating; $i++)
                                            <img src="{{ asset('images/dashboard/star.svg') }}" alt="Filled Star">
                                        @endfor
                                        @for ($i = $averageRating; $i < $totalStars; $i++)
                                            <img src="{{ asset('images/dashboard/outline-star.svg') }}"
                                                alt="Outlined Star">
                                        @endfor
                                    </span>
                                </div>
                                @if ($bid?->status == 'rejected')
                                    <div class="accept-bid">
                                        <h4>{{getDefaultCurrency()->symbol}}{{ $bid?->amount }}</h4>
                                        <a href="#" class="btn btn-reject">{{ ucfirst($bid?->status) }}</a>
                                    </div>
                                @elseif ($bid?->status == 'accepted')
                                    <div class="accept-bid">
                                        <h4>{{getDefaultCurrency()->symbol}}{{ $bid?->amount }}</h4>
                                        <a href="#" class="btn bg-light-primary">{{ ucfirst($bid?->status) }}</a>
                                    </div>
                                @endif
                            </li>
                        @empty
                        <div class="no-data mt-3">
                                    <img src="{{ asset('images/no-data.png') }}" alt="">
                                    <h6 class="mt-2">{{ __('static.no_result') }}</h6>
                                </div>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@if ($settings['location']['map_provider'] == 'google_map')
    @includeIf('taxido::admin.ride.google')
    @elseIf($settings['location']['map_provider'] == 'osm')
    @includeIf('taxido::admin.ride.osm')
@endif
