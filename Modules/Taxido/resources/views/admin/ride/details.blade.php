@use('Modules\Taxido\Enums\ServicesEnum')
@use('Modules\Taxido\Enums\RideStatusEnum')
@use('Modules\Taxido\Enums\ServiceCategoryEnum')
@php
    $locations = $ride->locations;
    $settings = getTaxidoSettings();
    $ridestatuscolorClasses = getRideStatusColorClasses();
    $paymentstatuscolorClasses = getPaymentStatusColorClasses();
    $locationCoordinates = $ride->location_coordinates;
    $paymentLogoUrl = getPaymentLogoUrl($ride->payment_method);
    $currencySymbol = getDefaultCurrencySymbol();
@endphp

@extends('admin.layouts.master')
@section('title', __('taxido::static.rides.rides'))
@section('content')

    <div class="row ride-dashboard">
        <div class="default-sorting mt-0">
            <div class="welcome-box">
                <div class="d-flex">
                    <h2>{{ __('taxido::static.rides.ride_details') }}</h2>
                </div>
            </div>
        </div>

        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header card-no-border">
                    <div class="header-top">
                        <h5 class="m-0">{{ __('taxido::static.rides.general_detail') }}</h5>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <ul class="ride-details-list">
                        <li>
                            {{ __('taxido::static.rides.ride_id') }}:<span
                                class="bg-light-primary">#{{ $ride->ride_number }}</span>
                        </li>
                        @if($ride->start_time)
                        <li>{{ __('taxido::static.rides.start_date_time') }} :
                            <span>
                                {{ $ride?->start_time?->format('Y-m-d H:i:s A') }}
                            </span>
                        </li>
                        @endif
                        @if (in_array($ride?->service_category?->slug, [ServiceCategoryEnum::PACKAGE, ServiceCategoryEnum::RENTAL]))
                            <li>{{ __('taxido::static.rides.end_date_time') }} :
                                <span>
                                    {{ $ride?->end_time->format('Y-m-d H:i:s A')}}
                                </span>
                            </li>
                        @endif
                        <li>
                            {{ __('taxido::static.rides.ride_status') }} :
                            <span class="badge badge-{{ $ridestatuscolorClasses[ucfirst($ride->ride_status->name)] }}">
                                {{ ucfirst($ride->ride_status->name) }}
                            </span>
                        </li>
                        <li>
                            {{ __('taxido::static.rides.payment_status') }} :
                            <span class="badge badge-{{ $paymentstatuscolorClasses[ucfirst($ride->payment_status)] }}">
                                {{ ucfirst(strtolower($ride->payment_status)) }}
                            </span>
                        </li>
                        <li>
                            {{ __('taxido::static.rides.service') }} : <span>{{ $ride->service->name }}</span>
                        </li>
                        <li>
                            {{ __('taxido::static.rides.service_category') }} :
                            <span>{{ $ride->service_category->name }}</span>
                        </li>
                        <li>
                            {{ __('taxido::static.rides.otp') }} : <span>{{ $ride->otp }}</span>
                        @if (in_array($ride?->service?->slug, [ServicesEnum::PARCEL]))
                        <li>{{ __('taxido::static.rides.parcel_otp') }}: <span>
                            {{ $ride?->parcel_delivered_otp }}</span>
                        </li>
                        <li>
                            {{ __('taxido::static.rides.weight') }}: <span>
                            {{ $ride?->weight }}</span>
                        </li>
                        @endif
                        </li>
                        @if (in_array($ride?->service_category?->slug, [ServiceCategoryEnum::RENTAL]))
                        <li> 
                            {{ __('taxido::static.rides.no_of_days') }} : <span>{{ $ride->no_of_days}}</span>
                        </li>
                        @endif
                        @if (!in_array($ride?->service_category?->slug, [ServiceCategoryEnum::RENTAL]))
                        <li>
                            {{ __('taxido::static.rides.ride_distance') }} : <span>{{ $ride?->distance }}
                                {{ $ride?->distance_unit }}</span>
                        </li>
                        
                        @endif
                        <li>
                            {{ __('taxido::static.rides.zone') }} :
                            <span>{{ $ride?->zones?->pluck('name')->implode(', ') }}</span>
                        </li>
                        <li>
                            {{ __('taxido::static.rides.payment_method') }} :
                            <span>
                                <img class="img-fluid h-30" alt=""
                                    src="{{ $paymentLogoUrl ?: asset('images/payment/cod.png') }}">
                            </span>
                        </li>

                    </ul>

                    @if ($ride?->service_category?->slug != ServiceCategoryEnum::RENTAL)
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
                                    <h3>{{ $ride->bids->count() }}</h3>
                                </div>
                            </div>
                            <button class="btn bg-primary" data-bs-toggle="modal"
                                data-bs-target="#bidding">{{ __('taxido::static.rides.biddings') }}</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xxl-6">
            <div class="row">
                <div class="col-xxl-12">
                    <div class="card h-auto">
                        <div class="card-header card-no-border">
                            <div class="header-top">
                                <h5 class="m-0">{{ __('taxido::static.rides.driver_detail') }}</h5>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="personal">
                                <div class="information">
                                    <div class="border-image">
                                        <div class="profile-img">
                                            @if ($ride?->driver?->profile_image?->original_url)
                                                <img src="{{ $ride?->driver?->profile_image?->original_url }}"
                                                    alt="">
                                            @else
                                                <div class="initial-letter">
                                                    <span>{{ strtoupper($ride?->driver?->name[0]) }}</span>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                    <div class="personal-rating">
                                        <h5>
                                            <a href="{{ route('admin.driver.show', ['driver' => $ride?->driver?->id]) }}"
                                                class="text-decoration-none">
                                                {{ $ride?->driver?->name }}
                                            </a>
                                        </h5>
                                        <div class="rating">
                                            <span>{{ __('taxido::static.riders.rating') }}:
                                                @php
                                                    $averageRating = (int) $ride?->driver?->reviews?->avg('rating');
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
                                    </div>
                                </div>
                                <ul class="personal-details-list">
                                    <li>
                                        <span>{{ __('taxido::static.rides.email') }}: </span>{{ $ride?->driver?->email }}
                                    </li>
                                    <li>
                                        <span>{{ __('taxido::static.rides.phone') }}:
                                        </span>+{{ $ride?->driver?->country_code }}
                                        {{ $ride?->driver?->phone }}
                                    </li>
                                    @if(isset($document->document_no))
                                    <li>
                                        <span>{{ __('taxido::static.rides.document_no') }}:</span>{{ $ride?->driver?->document?->document_no }}
                                    </li>
                                    @endif
                                    <li>
                                        <span>{{ __('taxido::static.riders.vehicle_num') }}: </span>
                                        <span class="vehicle-number">{{ $ride?->driver?->vehicle_info?->plate_number }}</span>
                                    </li>
                                    @if (!in_array($ride?->service_category?->slug, [ServiceCategoryEnum::RENTAL]))
                                        <li>
                                            <span>{{ __('taxido::static.rides.vehicle_type') }}: </span>
                                            <div class="vehicle-image">
                                                <img src="{{ $ride?->driver?->vehicle_info?->vehicle?->vehicle_image?->original_url ?? '/images/user.png' }}"
                                                    class="img-fluid" alt="">
                                                </div>
                                                <span>({{  $ride?->driver?->vehicle_info?->vehicle?->name }})</span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-12">
                    <div class="card h-auto">
                        <div class="card-header card-no-border">
                            <div class="header-top">
                                <h5 class="m-0">{{ __('taxido::static.rides.rider_details') }}</h5>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="personal">
                                <div class="information">
                                    <div class="border-image">
                                        <div class="profile-img">
                                            @if ($ride?->rider['profile_image']?->original_url)
                                                <img src="{{ $ride?->rider['profile_image']?->original_url }}"
                                                    class="img-fluid" alt="">
                                            @else
                                                <div class="initial-letter">
                                                    <span>{{ strtoupper($ride?->rider['name'][0]) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="personal-rating">
                                        <h5>{{ $ride['rider']['name'] }}</h5>

                                        <div class="rating">
                                            <span>{{ __('taxido::static.rides.rating') }}:
                                                @php
                                                    $averageRating = 0;
                                                    if (
                                                        isset($ride['rider']['reviews']) &&
                                                        count($ride['rider']['reviews']) > 0
                                                    ) {
                                                        $averageRating = (int) collect($ride['rider']['reviews'])->avg(
                                                            'rating',
                                                        );
                                                    }
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
                                    </div>

                                </div>
                                <ul class="personal-details-list">
                                    <li>
                                        <span>{{ __('taxido::static.rides.email') }}: </span>{{ $ride?->rider['email'] }}

                                    </li>
                                    <li>
                                        <span>{{ __('taxido::static.rides.contact_number') }}:
                                        </span>+{{ $ride?->rider['country_code'] }}
                                        {{ $ride?->rider['phone'] }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-5">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header card-no-border">
                            <div class="header-top">
                                <h5 class="m-0">{{ __('taxido::static.rides.price_details') }}</h5>
                                <a href="{{ route('ride.invoice', $ride?->ride_number) }}" class="btn btn-primary">
                                    <i class="ri-download-line"></i> {{ __('taxido::static.rides.invoice') }}
                                </a>
                            </div>
                        </div>
                        <div class="card-body pt-0">

                            <ul class="price-details-list">
                                <li class="title-list">

                                </li>
                                @if($ride?->service_category?->slug == ServiceCategoryEnum::PACKAGE)
                                    <li>
                                        {{ __('taxido::static.rides.extra_charge') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->extra_charge, 2) }}</span>
                                    </li>
                                    <li class="success-text">
                                        {{ __('taxido::static.rides.subtotal') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->sub_total, 2) }}</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.rides.processing_fee') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->processing_fee, 2) }}</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.rides.platform_fee') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->platform_fees, 2) }}</span>
                                    </li>

                                    <li class="danger-text">
                                        {{ __('taxido::static.rides.coupon_discount') }} @if($ride?->coupon?->code)(#{{ $ride?->coupon?->code }}) @endif :
                                        <span>-${{ round($ride?->coupon_total_discount, 2) }}</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.rides.tax') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->tax, 2) }}</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.invoice.driver_tips') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->driver_tips, 2) }}</span>
                                    </li>
                                    <li class="total-box">
                                        {{ __('taxido::static.rides.total') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->total, 2) }}</span>
                                    </li>
                                @elseif ($ride?->service_category?->slug == ServiceCategoryEnum::RENTAL)
                                     <li>
                                        {{ __('taxido::static.rides.vehicle_charge') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->vehicle_per_day_price, 2) * $ride?->no_of_days }}
                                            ({{ $currencySymbol }}{{ round($ride?->vehicle_per_day_price, 2) }} *
                                            {{ $ride?->no_of_days }} {{ __('taxido::static.rides.days') }})</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.rides.driver_charge') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->driver_per_day_charge, 2) * $ride?->no_of_days }}
                                            ({{ $currencySymbol }}{{ round($ride?->driver_per_day_charge, 2) }} *
                                            {{ $ride?->no_of_days }} {{ __('taxido::static.rides.days') }})</span>
                                    </li>

                                    <li class="success-text">
                                        {{ __('taxido::static.rides.subtotal') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->sub_total, 2) }}</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.rides.processing_fee') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->processing_fee, 2) }}</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.rides.platform_fee') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->platform_fees, 2) }}</span>
                                    </li>

                                    <li class="danger-text">
                                        {{ __('taxido::static.rides.coupon_discount') }}  @if($ride?->coupon?->code)(#{{ $ride?->coupon?->code }}) @endif :
                                        <span>-${{ round($ride?->coupon_total_discount, 2) }}</span>
                                    </li>

                                    <li>
                                        {{ __('taxido::static.rides.tax') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->tax, 2) }}</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.invoice.driver_tips') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->driver_tips, 2) }}</span>
                                    </li>
                                    <li class="total-box">
                                        {{ __('taxido::static.rides.total') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->total, 2) }}</span>
                                    </li>
                                @elseif($ride?->service?->slug == ServicesEnum::PARCEL)
                                    <li class="success-text">
                                        {{ __('taxido::static.rides.subtotal') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->sub_total, 2) }}</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.rides.processing_fee') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->processing_fee, 2) }}</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.rides.platform_fee') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->platform_fees, 2) }}</span>
                                    </li>
                                    <li class="danger-text">
                                        {{ __('taxido::static.rides.coupon_discount') }}  @if($ride?->coupon?->code)(#{{ $ride?->coupon?->code }}) @endif :
                                        <span>-${{ round($ride?->coupon_total_discount, 2) }}</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.rides.tax') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->tax, 2) }}</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.invoice.driver_tips') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->driver_tips, 2) }}</span>
                                    </li>
                                    <li class="total-box">
                                        {{ __('taxido::static.rides.total') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->total, 2) }}</span>
                                    </li>
                                    @elseif(in_array($ride?->service_category?->slug, [
                                        ServiceCategoryEnum::INTERCITY,
                                        ServiceCategoryEnum::RIDE,
                                        ServiceCategoryEnum::SCHEDULE,
                                    ]) || $ride?->service?->slug == ServicesEnum::FREIGHT)
                                    <li>
                                        {{ __('taxido::static.rides.admin_commission') }} :
                                        <span>${{ round($ride?->admin_commission, 2) }}</span>
                                    </li>
                                    <li class="success-text">
                                        {{ __('taxido::static.rides.subtotal') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->sub_total, 2) }}</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.rides.processing_fee') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->processing_fee, 2) }}</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.rides.platform_fee') }}
                                        :<span>{{ $currencySymbol }}{{ round($ride?->platform_fees, 2) }}</span>
                                    </li>
                                    <li class="danger-text">
                                        {{ __('taxido::static.rides.coupon_discount') }}  @if($ride?->coupon?->code)(#{{ $ride?->coupon?->code }}) @endif :
                                        <span>-${{ round($ride?->coupon_total_discount, 2) }}</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.rides.tax') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->tax, 2) }}</span>
                                    </li>
                                    <li>
                                        {{ __('taxido::static.invoice.driver_tips') }}
                                        :<span>{{ $currencySymbol }}{{ round($ride?->driver_tips, 2) }}</span>
                                    </li>
                                    <li class="total-box">
                                        {{ __('taxido::static.rides.total') }} :
                                        <span>{{ $currencySymbol }}{{ round($ride?->total, 2) }}</span>
                                    </li>
                                @endif
                            </ul>

                            <ul class="comment-box-list">
                                @if($ride?->comment)
                                    <li>
                                        <h4>{{ __('taxido::static.rides.comments') }}</h4>
                                        <p>{{ $ride?->comment }}</p>
                                    </li>
                                @endif
                                @if(in_array($ride?->ride_status?->slug, [RideStatusEnum::CANCELLED]))
                                    <li>
                                        <h4>{{ __('taxido::static.rides.cancellation_reason') }}</h4>
                                        <p>{{ $ride?->cancellation_reason ?? __('taxido::static.rides.default_cancel_reason') }}
                                        </p>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                @if (in_array($ride?->service?->slug, [ServicesEnum::PARCEL]))
                <div class="col-12">
                    <div class="card">
                        <div class="parcel-box">
                            <div class="left-box">
                                <img src="{{ $ride->cargo_image?->original_url ?? asset('images/nodata1.webp') }}" class="img-fluid" alt="">
                            </div>
                            <ul class="right-list">
                                <li><span>{{ __('taxido::static.rides.receiver_name') }}:</span>
                                    {{ $ride?->parcel_receiver['name'] }}</li>
                                <li><span>{{ __('taxido::static.rides.receiver_no') }}:</span>
                                    +{{ $ride?->parcel_receiver['country_code'] }} {{ $ride?->parcel_receiver['phone'] }}</li>
                                <li><span>{{ __('taxido::static.rides.parcel_otp') }}:</span>
                                    {{ $ride?->parcel_delivered_otp }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                @if (in_array($ride?->service_category?->slug, [ServiceCategoryEnum::RENTAL]))
                <div class="col-12">
                    <div class="card">
                        <div class="driver-box">
                            <div class="left-box">
                                 <img src="{{ $ride?->rental_vehicle?->normal_image?->original_url ?? asset('images/nodata1.webp') }}"
                                    class="img-fluid" alt="">
                            </div>
                            <ul class="right-list">
                                <li><span>{{ __('taxido::static.rides.vehicle_name') }}:</span> {{ $ride?->rental_vehicle?->name }}</li>
                                @if($ride?->is_with_driver == 1)
                                <li><span>{{ __('taxido::static.rides.assign_driver_name') }}:</span> {{ $ride?->assigned_driver['name'] }}</li>
                                <li><span>{{ __('taxido::static.rides.assign_driver_no') }}:</span> +{{ $ride?->assigned_driver['country_code'] }}{{ $ride?->assigned_driver['phone'] }}</li>
                                @else
                                <li><span>{{ __('taxido::static.rides.driver_name') }}:</span> {{ $ride?->driver?->name }}</li>
                                <li><span>{{ __('taxido::static.rides.driver_no') }}:</span> {{ $ride?->driver?->phone }}</li>
                                @endif
                                <li><span>{{ __('taxido::static.rides.vehicle_registration_no') }}:</span> {{ $ride?->rental_vehicle?->registration_no }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div class="col-xxl-7">
            <div class="card maps-view">
                <div class="card-header card-no-border">
                    <div class="header-top">
                        <div>
                            <h5 class="m-0">{{ __('taxido::static.rides.map_view') }}</h5>
                        </div>
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
                                            @foreach ($ride->locations as $index => $location)
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
            <div class="card h-auto">
                @if (isset($ride?->riderReview))
                <!-- Rider Reviews Section -->
                <div class="card-header card-no-border">
                    <div class="header-top">
                        <h5 class="m-0">{{ __('taxido::static.rides.rider_reviews') }}</h5>
                    </div>
                </div>
                <div class="card-body rider-reviews p-0">
                    @if (isset($ride?->riderReview))
                        <div class="table-responsive h-custom-scrollbar">
                            <table class="table driver-review-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>{{ __('taxido::static.rides.driver') }}</th>
                                        <th>{{ __('taxido::static.rides.rating') }}</th>
                                        <th>{{ __('taxido::static.rides.description') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="customer-image">
                                                    @if ($ride?->riderReview?->driver?->profile_image?->original_url)
                                                        <img src="{{ $ride?->riderReview?->driver?->profile_image?->original_url }}"
                                                            alt="">
                                                    @else
                                                        @isset($ride?->riderReview?->driver)
                                                            <div class="initial-letter">
                                                                <span>{{ strtoupper($ride?->riderReview?->driver?->name[0]) }}</span>
                                                            </div>
                                                        @endisset
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5>{{ $ride?->riderReview?->driver?->name }}</h5>
                                                    <span>{{ $ride?->riderReview?->driver?->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="rating">
                                                @isset($ride?->riderReview)
                                                    @php
                                                        $averageRating = (int) $ride?->riderReview?->rating;
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
                                                @endisset
                                            </div>
                                        </td>
                                        <td>
                                            <div class="position-relative">
                                                <p class="review-text" id="rider-review-{{ $ride?->riderReview?->id }}">
                                                    <span class="initial-review">
                                                        {{ \Illuminate\Support\Str::limit($ride?->riderReview?->message, 15) }}
                                                    </span>
                                                    <span class="full-review d-none">
                                                        {{ $ride?->riderReview?->message }}
                                                    </span>
                                                </p>
                                                <a href="javascript:void(0);" class="read-more"
                                                    onclick="toggleRiderReview('{{ $ride?->riderReview?->id }}')">
                                                    {{ __('taxido::static.rides.read_more') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="table-no-data">
                            <img src="{{ asset('images/dashboard/data-not-found.svg') }}" class="img-fluid"
                                alt="data not found">
                            <h6 class="text-center">
                                {{ __('taxido::static.widget.no_data_available') }}
                            </h6>
                        </div>
                    @endif
                </div>
                @endif
            </div>
            @if (isset($ride?->driverReview))
            <div class="card h-auto">
                <div class="card-header card-no-border">
                    <div class="header-top">
                        <h5 class="m-0">{{ __('taxido::static.rides.driver_reviews') }}</h5>
                    </div>
                </div>
                <div class="card-body rider-reviews p-0">
                    @if (isset($ride?->driverReview))
                        <div class="table-responsive h-custom-scrollbar">
                            <table class="table driver-review-table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>{{ __('taxido::static.rides.rider') }}</th>
                                        <th>{{ __('taxido::static.rides.rating') }}</th>
                                        <th>{{ __('taxido::static.rides.description') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="customer-image">
                                                    @if ($ride?->driverReview?->rider?->profile_image?->original_url)
                                                        <img src="{{ $ride?->driverReview?->rider?->profile_image?->original_url }}"
                                                            alt="">
                                                    @else
                                                        @isset($ride?->driverReview?->rider)
                                                            <div class="initial-letter">
                                                                <span>{{ strtoupper($ride?->driverReview?->rider?->name[0]) }}</span>
                                                            </div>
                                                        @endisset
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5>{{ $ride?->driverReview?->rider?->name }}</h5>
                                                    <span>{{ $ride?->driverReview?->rider?->email }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="rating">
                                                @isset($ride?->driverReview)
                                                    @php
                                                        $averageRating = (int) $ride?->driverReview?->rating;
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
                                                @endisset
                                            </div>
                                        </td>
                                        <td>
                                            <div class="position-relative">
                                                <p class="review-text" id="driver-review-{{ $ride?->driverReview?->id }}">
                                                    <span class="initial-review">
                                                        {{ \Illuminate\Support\Str::limit($ride?->driverReview?->message, 15) }}
                                                    </span>
                                                    <span class="full-review d-none">
                                                        {{ $ride?->driverReview?->message }}
                                                    </span>
                                                </p>
                                                <a href="javascript:void(0);" class="read-more"
                                                    onclick="toggleDriverReview('{{ $ride?->driverReview?->id }}')">
                                                    {{ __('taxido::static.rides.read_more') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="table-no-data">
                            <img src="{{ asset('images/dashboard/data-not-found.svg') }}" class="img-fluid"
                                alt="data not found">
                            <h6 class="text-center">
                                {{ __('taxido::static.widget.no_data_available') }}
                            </h6>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    <div class="modal fade" id="bidding">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('taxido::static.rides.biddings') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="bidding-modal">
                        <ul class="h-custom-scrollbar">
                            @forelse ($ride?->bids as $bid)
                                <li class="d-flex align-items-center">
                                    <div class="customer-image">
                                        @if ($bid?->driver?->profile_image?->original_url)
                                            <img src="{{ $bid?->driver?->profile_image?->original_url }}"
                                                alt="">
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
                                                $averageRating = (int) $ride?->driver?->reviews?->avg('rating');
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
</div>
@endsection
@if ($settings['location']['map_provider'] == 'google_map')
    @includeIf('taxido::admin.ride.google')
@elseIf($settings['location']['map_provider'] == 'osm')
    @includeIf('taxido::admin.ride.osm')
@endif

@push('scripts')
<script>
    function toggleRiderReview(reviewId) {
        var reviewText = document.getElementById('rider-review-' + reviewId);

        var initialReview = reviewText.querySelector('.initial-review');
        var fullReview = reviewText.querySelector('.full-review');

        var readMoreLink = reviewText.nextElementSibling;

        initialReview.classList.toggle('d-none');
        fullReview.classList.toggle('d-none');

        if (fullReview.classList.contains('d-none')) {
            readMoreLink.innerHTML = "{{ __('taxido::static.rides.read_more') }}";
        } else {
            readMoreLink.innerHTML = "{{ __('taxido::static.rides.read_less') }}";
        }
    }

    function toggleDriverReview(reviewId) {
        var reviewText = document.getElementById('driver-review-' + reviewId);

        var initialReview = reviewText.querySelector('.initial-review');
        var fullReview = reviewText.querySelector('.full-review');

        var readMoreLink = reviewText.nextElementSibling;

        initialReview.classList.toggle('d-none');
        fullReview.classList.toggle('d-none');

        if (fullReview.classList.contains('d-none')) {
            readMoreLink.innerHTML = "{{ __('taxido::static.rides.read_more') }}";
        } else {
            readMoreLink.innerHTML = "{{ __('taxido::static.rides.read_less') }}";
        }
    }
</script>
@endpush
