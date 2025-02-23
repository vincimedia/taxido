@use('Modules\Taxido\Models\Rider')
@use('Modules\Taxido\Models\VehicleType')
@use('Modules\Taxido\Models\Service')
@use('Modules\Taxido\Models\HourlyPackage')
@use('Modules\Taxido\Enums\ServiceCategoryEnum')
@use('Modules\Taxido\Enums\ServicesEnum')
@php
    $riders = Rider::whereNull('deleted_at')->where('status', true)?->get();
    $vehicleTypes = VehicleType::whereNull('deleted_at')->where('status', true)?->get();
    $services = Service::whereNull('deleted_at')->where('status', true)?->get();
    $packages = HourlyPackage::whereNull('deleted_at')->where('status', true)->get();
    $packageId = getServiceCategoryIdBySlug(ServiceCategoryEnum::PACKAGE);
    $scheduleId = getServiceCategoryIdBySlug(ServiceCategoryEnum::SCHEDULE);
    $rentalId = getServiceCategoryIdBySlug(ServiceCategoryEnum::RENTAL);
    $drivers = getAllVerifiedDrivers();
    $PaymentMethodList = getPaymentMethodList();
@endphp
@extends('admin.layouts.master')
@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/vendors/mobiscroll/mobiscroll.css') }}">
@endpush

@section('content')
    <div class="ride-create">
        <form id="rideForm" action="{{ route('admin.ride-request.store') }}" method="POST" enctype="multipart/form-data">
            @method('POST')
            @csrf
            <div class="contentbox">
                <div class="inside">
                    <div class="contentbox-title">
                        <h3>{{ __('taxido::static.rides.create') }}</h3>
                    </div>

                    <div class="row g-md-4 g-3">
                        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                            <div class="form-group row">
                                <label class="col-12"
                                    for="rider_id">{{ __('taxido::static.rides.rider') }}<span>*</span></label>
                                <div class="col-12 select-item">
                                    <select class="form-select form-select-transparent select2-option" name="rider_id"
                                        id="rider_id" data-placeholder="{{ __('taxido::static.wallets.select_rider') }}">
                                        <option></option>
                                        @foreach ($riders as $rider)
                                            <option value="{{ $rider->id }}" sub-title="{{ $rider->email }}"
                                                image="{{ $rider?->profile_image ? $rider?->profile_image?->original_url : asset('images/user.png') }}">
                                                {{ $rider->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('rider_id')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <span class="text-gray">
                                        {{ __('taxido::static.wallets.add_rider_message') }}
                                        <a href="{{ route('admin.rider.create') }}" class="text-primary">
                                            <b>{{ __('taxido::static.here') }}</b>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                            <div class="form-group row">
                                <label class="col-12"
                                    for="service_id">{{ __('taxido::static.rides.service') }}<span>*</span></label>
                                <div class="col-12 select-item">
                                    <select id="service_id" class="form-select form-select-transparent select2-option"
                                        name="service_id"
                                        data-placeholder="{{ __('taxido::static.rides.select_service') }}">
                                        <option></option>
                                        @foreach ($services as $service)
                                            <option value="{{ $service->id }}" sub-title="{{ $service->description }}"
                                                image="{{ $service?->service_image ? $service?->service_image?->original_url : asset('images/user.png') }}">
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('service_id')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                            <div class="form-group row">
                                <label class="col-12"
                                    for="service_category_id">{{ __('taxido::static.rides.service_category') }}<span>*</span></label>
                                <div class="col-12">
                                    <select class="form-control select-2"
                                        data-placeholder="{{ __('taxido::static.rides.select_service_category') }}"
                                        id="service_category_id" name="service_category_id">
                                    </select>
                                    <span id="slug-loader" class="spinner-border ride-loader" role="status"
                                        style="display: none;"></span>
                                    @error('service_category_id')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6" id="locations-form" style="display: none;">
                            <div class="form-group row">
                                <label class="col-12" for="locations">{{ __('taxido::static.rides.locations') }}</label>
                                <div class="col-12" id="location-container">
                                    <div class="location-row">
                                        <input type="text" name="locations[]"
                                            class="form-control ui-widget autocomplete-google location-input"
                                            placeholder="{{ __('taxido::static.rides.enter_pickup_location') }}">
                                        <input type="hidden" class="lat-input" name="location_coordinates[0][lat]">
                                        <input type="hidden" class="lng-input" name="location_coordinates[0][lng]">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="add-location-button">
                                        <button type="button" id="add-location" class="btn btn-primary ms-auto">Add
                                            Location</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8" id="rental-locations-form" style="display: none;">
                            <div class="form-group row">
                                <div class="col-6">
                                    <div class="form-group row">
                                        <label class="col-12"
                                            for="rental_pickup_location">{{ __('taxido::static.rides.pickup_location') }}<span>*</span></label>
                                        <div class="col-12" id="rental-location-container">
                                            <div class="rental-location-row">
                                                <input type="text"
                                                    class="form-control ui-widget autocomplete-google location-input"
                                                    name="rental_locations[]" id="rental_pickup_location"
                                                    placeholder="{{ __('taxido::static.rides.enter_pickup_location') }}">
                                                <input type="hidden" class="lat-input"
                                                    name="rental_location_coordinates[0][lat]">
                                                <input type="hidden" class="lng-input"
                                                    name="rental_location_coordinates[0][lng]">
                                            </div>
                                        </div>
                                        @error('rental_locations[]')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group row">
                                        <label class="col-12"
                                            for="rental_destination_location">{{ __('taxido::static.rides.destination_location') }}<span>*</span></label>
                                        <div class="col-12" id="rental-location-container-2">
                                            <div class="rental-location-row">
                                                <input type="text"
                                                    class="form-control ui-widget autocomplete-google location-input"
                                                    name="rental_locations[]" id="rental_destination_location"
                                                    placeholder="{{ __('taxido::static.rides.enter_destination_location') }}">
                                                <input type="hidden" class="lat-input"
                                                    name="rental_location_coordinates[1][lat]">
                                                <input type="hidden" class="lng-input"
                                                    name="rental_location_coordinates[1][lng]">
                                            </div>
                                        </div>
                                        @error('rental_locations[]')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6" id="packages-list-container"
                            style="display: none;">
                            <div class="form-group row">
                                <label class="col-12"
                                    for="package_id">{{ __('taxido::static.rides.hourly_package') }}<span>*</span></label>
                                <div class="col-12 select-item">
                                    <select id="hourly_package_id" class="form-control select-2" name="hourly_package_id"
                                        data-placeholder="{{ __('taxido::static.rides.select_package') }}">
                                        <option></option>
                                        @foreach ($packages as $package)
                                            <option value="{{ $package->id }}">
                                                 {{ $package->hour }} hour - {{ $package->distance }} {{ $package->distance_type }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('hourly_package_id')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <span class="text-gray">
                                        {{ __('taxido::static.hourly_package.no_hourly_package_message') }}
                                        <a href="{{ route('admin.hourly-package.create') }}" class="text-primary">
                                            <b>{{ __('taxido::static.here') }}</b>
                                        </a>
                                    </span>
                                </div>
                            </div>

                        </div>

                        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6" id="start-time-container"
                            style="display: none;">
                            <div class="form-group row">
                                <label class="col-12"
                                    for="start_time">{{ __('taxido::static.rides.start_date_time') }}<span>*</span></label>
                                <div class="col-12">
                                    <input id="start_time" class="form-control picker" type="text"
                                        placeholder="{{ __('taxido::static.rides.select_start_date_and_time') }}"
                                        name="start_time" />
                                </div>
                                @error('start_time')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6" id="end-time-container" style="display: none;">
                            <div class="form-group row">
                                <label class="col-12"
                                    for="end_time">{{ __('taxido::static.rides.end_date_time') }}<span>*</span></label>
                                <div class="col-12">
                                    <input id="end_time" class="form-control picker" type="text"
                                        placeholder="{{ __('taxido::static.rides.select_end_date_and_time') }}"
                                        name="end_time" />
                                </div>
                                @error('end_time')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                            <div class="form-group row">
                                <label class="col-12"
                                    for="vehicle_type_id">{{ __('taxido::static.rides.vehicle_type') }}<span>*</span></label>
                                <div class="col-12 select-item">
                                    <select class="form-control select-2" name="vehicle_type_id" id="vehicle_type_id"
                                        data-placeholder="{{ __('taxido::static.drivers.select_vehicle') }}">
                                        <option></option>
                                    </select>
                                    @error('vehicle_type_id')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <span id="vehicle-slug-loader" class="spinner-border ride-loader" role="status"
                                        style="display: none;"></span>
                                    <span class="text-gray">
                                        {{ __('taxido::static.coupons.no_vehicleType_message') }}
                                        <a href="{{ route('admin.vehicle-type.create') }}" class="text-primary">
                                            <b>{{ __('taxido::static.here') }}</b>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6" id="cargo_image_div" style="display: none;">
                            <div class="form-group row">
                                <label class="col-12" for="cargo_image_id">
                                    {{ __('taxido::static.rides.cargo_image') }}
                                </label>
                                <div class="col-12">
                                    <div class="form-group">
                                        <x-image :name="'cargo_image_id'" :data="isset($rides->cargo_image)
                                            ? $rides->cargo_image
                                            : old('cargo_image_id')" :text="''"
                                            :multiple="false"></x-image>
                                        @error('cargo_image_id')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6" id="rental-vehicle" style="display: none;">

                            <div class="form-group row">
                                <label class="col-12"
                                    for="rental_vehicle">{{ __('taxido::static.rides.rental_vehicle') }}<span>*</span></label>
                                <div class="col-12 select-item">
                                    <select class="form-control select-2" name="rental_vehicle_id" id="rental_vehicle_id"
                                        data-placeholder="{{ __('taxido::static.drivers.select_rental_vehicle') }}">
                                        <option></option>

                                    </select>
                                    @error('rental_vehicle_id')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <span id="rental-slug-loader" class="spinner-border ride-loader" role="status"
                                        style="display: none;"></span>
                                    <span class="text-gray">
                                        {{ __('taxido::static.drivers.no_rental_message') }}
                                        <a href="{{ route('admin.rental-vehicle.create') }}" class="text-primary">
                                            <b>{{ __('taxido::static.here') }}</b>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6" id="weight_div" style="display: none;">
                            <div class="form-group row">
                                <label class="col-12" for="weight">
                                    {{ __('taxido::static.rides.weight') }}<span>*</span>
                                </label>
                                <div class="col-12 amount">
                                    <div class="w-100">
                                        <input class="form-control" type="number" id="weight" name="weight"
                                            min="0"
                                            value="{{ isset($vehicleType->weight) ? $vehicleType->weight : old('weight') }}"
                                            placeholder="{{ __('taxido::static.rides.enter_weight') }}">
                                        @error('weight')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                            <div class="form-group row">
                                <label class="col-12"
                                    for="payment_method">{{ __('taxido::static.rides.payment_method') }}</label>
                                <div class="col-12 select-item">
                                    <select class="form-control select-2" name="payment_method" id="payment_method"
                                        data-placeholder="{{ __('taxido::static.rides.select_payment_method') }}">
                                        <option></option>
                                        @forelse ($PaymentMethodList as $paymentmethod)
                                            <option value="{{ $paymentmethod['slug'] }}">
                                                {{ $paymentmethod['name'] }}
                                            </option>
                                        @empty
                                        @endforelse
                                        <option></option>
                                    </select>
                                    @error('payment_method')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8" id="parcel-container" style="display : none;">
                            <div class="form-group row g-sm-4 g-3">
                                <div class="col-sm-6">
                                    <div class="form-group row">
                                        <label class="col-12" for="name">
                                            {{ __('taxido::static.rides.reciever_full_name') }}<span>*</span>
                                        </label>
                                        <div class="col-12">
                                            <input class="form-control" type="text" id="parcel_receiver[name]"
                                                name="parcel_receiver[name]"
                                                placeholder="{{ __('taxido::static.rides.enter_reciever_full_name') }}">
                                            @error('parcel_receiver[name]')
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group row">
                                        <label class="col-12"
                                            for="phone">{{ __('taxido::static.rides.reciever_phone') }}<span>*</span></label>
                                        <div class="col-12">
                                            <div class="input-group mb-3 phone-detail row g-0 phone-details-2">
                                                <div class="col-sm-1">
                                                    <select class="select-2 form-control" id="select-country-code"
                                                        name="parcel_receiver[country_code]">
                                                        @foreach (getCountryCodes() as $option)
                                                            <option class="option" value="{{ $option->calling_code }}"
                                                                data-image="{{ asset('images/flags/' . $option->flag) }}">
                                                                {{ $option->calling_code }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-sm-11">
                                                    <input class="form-control" type="number"
                                                        name="parcel_receiver[phone]" id="parcel_receiver[phone]"
                                                        placeholder="{{ __('taxido::static.rides.enter_reciever_phone') }}">
                                                </div>
                                                @error('parcel_receiver[phone]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                            <div class="form-group row">
                                <label for="driver"
                                    class="col-12">{{ __('taxido::static.rides.drivers') }}<span>*</span></label>
                                <div class="col-12">
                                    <select class="select-2 form-control driver-control" id="driver[]" name="driver[]" data-placeholder="{{ __('taxido::static.rides.select_drivers') }}" multiple>
                                        <option></option>
                                        @foreach ($drivers as $driver)
                                            <option value="{{ $driver->id }}" sub-title="{{ $driver->email }}"
                                                image="{{ $driver?->profile_image?->original_url }}">
                                                {{ $driver->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('driver[]')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <span class="text-gray">
                                        {{ __('taxido::static.wallets.add_driver_message') }}
                                        <a href="{{ route('admin.driver.create') }}" class="text-primary">
                                            <b>{{ __('taxido::static.here') }}</b>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row" id="with-driver-container" style="display: none;">
                            <label class="col-md-2" for="with_driver">{{ __('taxido::static.rental_vehicle.with_driver') }}</label>
                            <div class="col-md-10">
                                <label class="switch">
                                    <input class="form-control" type="hidden" name="is_with_driver" value="0">
                                    <input class="form-check-input" type="checkbox" id="is_with_driver" name="is_with_driver" value="1" @checked(old('is_with_driver', $rentalVehicle->is_with_driver ?? true))>
                                    <span class="switch-state"></span>
                                </label>
                                @error('is_with_driver')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6">
                            <div class="form-group row">
                                <label class="col-12" for="description">{{ __('taxido::static.service_categories.description') }}</label>
                                <div class="col-12">
                                    <div class="position-relative">
                                        <textarea class="form-control" placeholder="{{ __('taxido::static.service_categories.enter_description') }}" rows="4" id="description" name="description" cols="50">{{ isset($serviceCategory->description) ? $serviceCategory->getTranslation('description', request('locale', app()->getLocale())) : old('description') }}</textarea><i class="ri-file-copy-line copy-icon" data-target="#description"></i>
                                    </div>
                                </div>
                                @error('description')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-12">
                            <div class="submit-btn">
                                <button type="submit" name="save" class="btn btn-solid spinner-btn">{{ __('static.save') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_API_KEY') }}&libraries=places"></script>
    <script src="{{ asset('js/mobiscroll/mobiscroll.js') }}"></script>
    <script src="{{ asset('js/mobiscroll/custom-mobiscroll.js') }}"></script>

    <script>
        (function($) {
            "use strict";

            $("#rideForm").validate({
                ignore: [],
                rules: {
                    "rider_id": "required",
                    "service_id": "required",
                    "service_category_id": "required",
                    "vehicle_type_id": "required",
                    "driver[]": "required",
                    "weight": {
                        required: setParcelRule
                    },
                    "parcel_receiver[phone]": {
                        required: setParcelRule
                    },
                    "parcel_receiver[name]": {
                        required: setParcelRule
                    },
                    "rental_locations[]": {
                        required: setLocationRule
                    },
                    'start_time': {
                        required: setStartTimeRule
                    },
                    'rental_vehicle_id': {
                        required: setRentalRule
                    },
                    'end_time': {
                        required: setRentalRule
                    },
                    'hourly_package_id': {
                        required: setPackageRule
                    }

                }
            });

            const packageId = <?php echo $packageId; ?>;
            const scheduleId = <?php echo $scheduleId; ?>;
            const rentalId = <?php echo $rentalId; ?>;
            const parcelId = <?php echo getServiceIdBySlug(ServicesEnum::PARCEL); ?>;
            const freightId = <?php echo getServiceIdBySlug(ServicesEnum::FREIGHT); ?>;
            const $serviceCategoryDropdown = $('#service_category_id');
            const $packagesContainer = $('#packages-list-container');
            const $startTimeContainer = $('#start-time-container');
            const $endTimeContainer = $('#end-time-container');
            const $locationRows = $('#locations-form');
            const $rentalLocationRows = $('#rental-locations-form');
            const $rentalVehicle = $('#rental-vehicle');
            const $weight = $('#weight_div');
            const $cargeImage = $('#cargo_image_div');
            const $parcelContainer = $('#parcel-container');

            $serviceCategoryDropdown.on('change', function() {
                $('#vehicle_type_id').attr('disabled', 'disabled');

                const selectedValue = parseInt($(this).val(), 10);

                if (selectedValue === packageId) {
                    $packagesContainer.show();

                } else {
                    $packagesContainer.hide();
                }

                if (selectedValue === scheduleId) {
                    $startTimeContainer.show();
                    $endTimeContainer.hide();
                } else if (selectedValue === rentalId) {
                    $startTimeContainer.show();
                    $endTimeContainer.show();
                } else {
                    $startTimeContainer.hide();
                    $endTimeContainer.hide();
                }

                if (selectedValue === rentalId || selectedValue === packageId) {
                    $rentalLocationRows.show();
                    $locationRows.hide();
                } else {
                    $locationRows.show();
                    $rentalLocationRows.hide();
                }

                if (selectedValue === rentalId) {
                    $rentalVehicle.show();
                    $('#with-driver-container').show();
                    $('#driver').closest('.form-group').hide();  
                } else {
                    $rentalVehicle.hide();
                    $('#with-driver-container').hide();
                    $('#driver').closest('.form-group').show();  
                }
            });
            
            function initializeAutocomplete($inputRow) {

                const $locationInput = $inputRow.find('.location-input');
                const $latInput = $inputRow.find('.lat-input');
                const $lngInput = $inputRow.find('.lng-input');

                const autocomplete = new google.maps.places.Autocomplete($locationInput[0]);

                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (place.geometry) {
                        $latInput.val(place.geometry.location.lat());
                        $lngInput.val(place.geometry.location.lng());
                    }
                });
            }

            const selectUser = () => {
                let queryString = window.location.search;
                let params = new URLSearchParams(queryString);
                params.set('rider_id', document.getElementById("select-rider").value);
                document.location.href = "?" + params.toString();
            }

            $(window).on('load', function() {
                if (typeof google !== 'undefined') {
                    initializeAutocomplete($('#location-container .location-row').first());
                    initializeAutocomplete($('#rental-location-container .rental-location-row').first());
                    initializeAutocomplete($('#rental-location-container-2 .rental-location-row').first());
                }
            });

            let locationIndex = 1;
            $('#add-location').click(function() {
                const totalLocations = $('#location-container .location-row').length;
                const isFirstLocation = totalLocations === 0;

                const newRow = $(`
                    <div class="row g-2 mt-3">
                        <div class="custom-col-md-11 ms-0">
                            <div class="location-row">
                                <input type="text" class="form-control ui-widget autocomplete-google location-input"
                                        name="locations[]" placeholder="${isFirstLocation ? '{{ __('taxido::static.rides.enter_pickup_location') }}' : '{{ __('taxido::static.rides.enter_destination_location') }}'}">
                                <input type="hidden" class="lat-input" name="location_coordinates[${locationIndex}][lat]">
                                <input type="hidden" class="lng-input" name="location_coordinates[${locationIndex}][lng]">
                            </div>
                        </div>
                        <div class="custom-col-md-1 ms-0">
                            <button type="button" class="btn remove-location w-100 justify-content-center btn-sm h-100">
                                <i class="ri-delete-bin-line text-danger"></i>
                            </button>
                        </div>
                    </div>
                `);

                $('#location-container').append(newRow);
                locationIndex++;
                initializeAutocomplete(newRow);
            });

            $('#location-container').on('click', '.remove-location', function() {
                $(this).closest('.row').remove();
            });

            const optionFormat = (item) => {
                if (!item.id) {
                    return item.text;
                }

                var span = document.createElement('span');
                var html = '';
                html += '<div class="selected-item">';
                html += '<img src="' + item.element.getAttribute('image') +
                    '" class="rounded-circle h-30 w-30" alt="' + item.text + '"/>';
                html += '<div class="detail">'
                html += '<h6>' + item.text + '</h6>';
                html += '<p>' + item.element.getAttribute('sub-title') + '</p>';
                html += '</div>';
                html += '</div>';
                span.innerHTML = html;
                return $(span);
            }

            $('.select2-option').select2({
                placeholder: "Select an option",
                templateSelection: optionFormat,
                templateResult: optionFormat
            });

            $('#service_id').on('change', function() {

                $locationRows.hide();
                $packagesContainer.hide();
                $endTimeContainer.hide();
                $startTimeContainer.hide();
                $rentalVehicle.hide();
                $rentalLocationRows.hide();


                $('#service_category_id').empty();
                $('#service_category_id').attr('disabled', 'disabled');

                var serviceId = $(this).val();

                if (serviceId == parcelId) {
                    $parcelContainer.show();
                    $weight.show();
                } else {
                    $parcelContainer.hide();
                    $weight.hide();
                }

                if (serviceId == freightId) {

                    $cargeImage.show();

                } else {
                    $cargeImage.hide();
                }

                loadServiceCategories(serviceId);
                var serviceCategoryID = $('#service_category_id').val();
                loadVehicles(serviceId, serviceCategoryID);
            });

            function loadServiceCategories(serviceId) {

                let url = "{{ route('serviceCategory.index') }}";

                if (serviceId) {
                    $.ajax({
                        url: url + '?service_id=' + serviceId,
                        type: "GET",
                        success: function(data) {
                            $('#service_category_id').empty();

                            $('#service_category_id').append('<option value=""></option>');
                            $.each(data.data, function(index, item) {
                                var option = new Option(item.name, item.id);
                                $('#service_category_id').append(option);
                            });
                            $('#service_category_id').removeAttr('disabled');
                        },
                        error: function(xhr, status, error) {
                            console.log("Error status: " + status);
                            console.log("Error response: " + xhr.responseText);
                            console.log("Error code: " + error);
                            $('#service_category_id').empty();
                        },
                    });
                } else {
                    $('#service_category_id').empty();
                    $serviceCategory.append('<option value=""></option>');
                }
            }

            $('form').on('submit', function(event) {
                if ($('#service_category_id').val() != rentalId && $('#service_category_id').val() !=
                    packageId) {
                    const locationRows = $('#location-container .location-row').length;

                    if (locationRows < 2) {
                        event.preventDefault();

                        toastr.error('{{ __('taxido::static.rides.minimum_2_locations_required') }}', {
                            closeButton: true,
                            progressBar: true,
                            timeOut: 5000,
                        });

                        setTimeout(function() {
                            $('.spinner-btn').prop('disabled', false);
                            $('.spinner-btn .spinner').remove();
                        }, 1000);
                    }
                }
            });

            var initialServiceCategoryID = $('#service_category_id').val();
            var selectedVehicleID = {!! json_encode(old('vehicle_type_id', @$rentalVehicle->vehicle_type_id ?? null)) !!};

            if (initialServiceCategoryID) {
                loadVehicles(initialServiceCategoryID, selectedVehicleID);
            }

            $('#service_category_id').on('change', function() {
                var serviceCategoryID = $(this).val();
                var serviceID = $('#service_id').val();
                loadVehicles(serviceID, serviceCategoryID);
            });

            function loadVehicles(serviceID, serviceCategoryID, selectedVehicleID = null) {

                let url = "{{ route('vehicleType.index') }}";
                if (serviceCategoryID) {
                    $.ajax({

                        url: url + '?service_id=' + serviceID + '&service_category_id=' + serviceCategoryID,
                        type: "GET",
                        success: function(data) {
                            $('#vehicle_type_id').empty();

                            $.each(data.data, function(index, item) {
                                var option = new Option(item.name, item.id);

                                if (String(item.id) === String(selectedVehicleID)) {
                                    $(option).prop("selected", true);
                                }

                                $('#vehicle_type_id').append(option);
                            });
                            $('#vehicle_type_id').removeAttr('disabled');
                            $('#vehicle_type_id').val(selectedVehicleID).trigger('change');
                        },
                        error: function() {

                        },
                    });
                } else {
                    $('#vehicle_type_id').empty().append('<option></option>');
                }
            }

            var initialVehicleTypeID = $('#vehicle_type_id').val();
            var selectedRentalVehicleID = {!! json_encode(old('rental_vehicle', @$rentalVehicle->id ?? null)) !!};


            if (initialVehicleTypeID) {
                loadRentalVehicles(initialVehicleTypeID, selectedRentalVehicleID);
            }


            $('#vehicle_type_id').on('change', function() {
                var vehicleTypeID = $(this).val();
                $('#rental_vehicle_id').attr('disabled', 'disabled');
                loadRentalVehicles(vehicleTypeID);
            });

            function loadRentalVehicles(vehicleTypeID, selectedRentalVehicleID = null) {
                let url = "{{ route('admin.rental-vehicle.filter', '') }}";
                if (vehicleTypeID) {
                    $.ajax({
                        url: url + '/' + vehicleTypeID,
                        type: "GET",
                        success: function(data) {

                            $('#rental_vehicle_id').empty();

                            $.each(data, function(id, name) {
                                console.log(name);
                                var option = new Option(name, id);

                                if (String(id) === String(selectedRentalVehicleID)) {
                                    $(option).prop("selected", true);
                                }

                                $('#rental_vehicle_id').append(option);
                            });
                            $('#rental_vehicle_id').removeAttr('disabled');
                            $('#rental_vehicle_id').val(selectedRentalVehicleID).trigger('change');
                        },
                        error: function() {
                            $('#rental_vehicle_id').empty().append('<option></option>');
                            $('#rental_vehicle_id').removeAttr('disabled');
                        }
                    });
                } else {
                    $('#rental_vehicle_id').empty().append('<option></option>');
                    $('#rental_vehicle_id').removeAttr('disabled');

                }
            }
            $('.picker').mobiscroll().datepicker({
                controls: ['calendar', 'time'],
                touchUi: true,
                dateFormat: 'YYYY-MM-DD',
                timeFormat: 'HH:mm',
                step: {
                    hour: 1,
                    minute: 1,
                },
                min: new Date(),
                touchUi: false,
            });

            function setParcelRule() {
                return $('#service_id').val() == parcelId;
            }

            function setLocationRule() {
                return $('#service_category_id').val() == rentalId || $('#service_category_id').val() == packageId;
            }

            function setRentalRule() {
                return $('#service_category_id').val() == rentalId;
            }

            function setStartTimeRule() {
                return $('#service_category_id').val() == rentalId || $('#service_category_id').val() == scheduleId;
            }

            function setPackageRule() {
                return $('#service_category_id').val() == packageId;
            }

        })(jQuery);
    </script>
@endpush
