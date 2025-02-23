<!-- Link Swiper's CSS -->
<link rel="stylesheet" type="text/css" href="{{ asset('css/vendors/swiper-slider.css') }}">

@use('App\Models\Tax')
@use('Modules\Taxido\Models\Zone')
@use('Modules\Taxido\Models\Service')
@use('Modules\Taxido\Models\ServiceCategory')
@php
    $zones = Zone::where('status', true)?->get(['id', 'name']);
    $taxes = Tax::where('status', true)?->get(['id', 'name']);
    $services = Service::where('status', true)?->get(['id', 'name']);
@endphp
<div class="row g-xl-4 g-3">
    <div class="col-xl-8">
        <div class="left-part">
            <div class="contentbox">
                <div class="inside">
                    <div class="contentbox-title">
                        <h3>{{ isset($vehicleType) ? __('taxido::static.vehicle_types.edit') : __('taxido::static.vehicle_types.add') }}
                            ({{ request('locale', app()->getLocale()) }})
                        </h3>
                        <!-- Button to trigger modal -->
                        <button class="btn btn-calculate" data-bs-toggle="modal" data-bs-target="#fareCalculationModal">
                            <i class="ri-information-line"></i> {{__('taxido::static.vehicle_types.calculated')}}
                        </button>
                    </div>
                    @isset($vehicleType)
                        <div class="form-group row">
                            <label class="col-md-2" for="name">{{ __('taxido::static.language.languages') }}</label>
                            <div class="col-md-10">
                                <ul class="language-list">
                                    @forelse (getLanguages() as $lang)
                                        <li>
                                            <a href="{{ route('admin.vehicle-type.edit', ['vehicle_type' => $vehicleType->id, 'locale' => $lang->locale]) }}"
                                                class="language-switcher {{ request('locale') === $lang->locale ? 'active' : '' }}"
                                                target="_blank">
                                                <img src="{{ @$lang?->flag ?? asset('admin/images/No-image-found.jpg') }}"
                                                    alt="">
                                                {{ @$lang?->name }} ({{ @$lang?->locale }})
                                                <i class="ri-arrow-right-up-line"></i>
                                            </a>
                                        </li>
                                    @empty
                                        <li>
                                            <a href="{{ route('admin.vehicle-type.edit', ['vehicle_type' => $vehicleType->id, 'locale' => Session::get('locale', 'en')]) }}"
                                                class="language-switcher active" target="blank">
                                                <img src="{{ asset('admin/images/flags/LR.png') }}" alt="">
                                                English
                                                <i class="ri-arrow-right-up-line"></i>
                                            </a>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    @endisset
                    <input type="hidden" name="locale" value="{{ request('locale') }}">
                    <div class="form-group row">
                        <label class="col-md-2"
                            for="vehicle_image_id">{{ __('taxido::static.vehicle_types.image') }}<span>
                                *</span></label>
                        <div class="col-md-10">
                            <div class="form-group">
                                <x-image :name="'vehicle_image_id'" :data="isset($vehicleType->vehicle_image)
                                    ? $vehicleType?->vehicle_image
                                    : old('vehicle_image_id')" :text="''"
                                    :multiple="false"></x-image>
                                @error('vehicle_image_id')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2"
                            for="vehicle_image_id">{{ __('taxido::static.vehicle_types.map_icon') }}<span>
                                *</span></label>
                        <div class="col-md-10">
                            <div class="form-group">
                                <x-image :name="'vehicle_map_icon_id'" :data="isset($vehicleType->vehicle_map_icon)
                                    ? $vehicleType?->vehicle_map_icon
                                    : old('vehicle_map_icon_id')" :text="''"
                                    :multiple="false"></x-image>
                                @error('vehicle_map_icon_id')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2" for="name">{{ __('taxido::static.vehicle_types.name') }} <span>
                                *</span></label>
                        <div class="col-md-10">
                            <div class="position-relative">
                                <input class="form-control" type="text" id="name" name="name"
                                    value="{{ isset($vehicleType->name) ? $vehicleType->getTranslation('name', request('locale', app()->getLocale())) : old('name') }}"
                                    placeholder="{{ __('taxido::static.vehicle_types.enter_name') }} ({{ request('locale', app()->getLocale()) }})"><i
                                    class="ri-file-copy-line copy-icon" data-target="#name"></i>
                            </div>
                            @error('name')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row min_per_unit_charge amount-input">
                        <label class="col-md-2"
                            for="base_amount">{{ __('taxido::static.vehicle_types.base_amount') }}<span>
                                *</span></label>
                        <div class="col-md-10 amount">
                            <div class="input-group mb-3 flex-nowrap">
                                <span class="input-group-text">{{ getDefaultCurrency()?->symbol }}</span>
                                <div class="w-100">
                                    <input class="form-control" type="number" id="base_amount" name="base_amount"
                                        min="1"
                                        value="{{ isset($vehicleType->base_amount) ? $vehicleType->base_amount : old('base_amount') }}"
                                        placeholder="{{ __('taxido::static.vehicle_types.enter_base_amount') }}">
                                    @error('base_amount')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row min_per_unit_charge amount-input">
                        <label class="col-md-2"
                            for="min_per_unit_charge">{{ __('taxido::static.vehicle_types.min_per_unit_charge') }}<span>
                                *</span></label>
                        <div class="col-md-10 amount">
                            <div class="input-group mb-3 flex-nowrap">
                                <span class="input-group-text">{{ getDefaultCurrency()?->symbol }}</span>
                                <div class="w-100">
                                    <input class="form-control" type="number" id="min_per_unit_charge"
                                        name="min_per_unit_charge" min="1"
                                        value="{{ isset($vehicleType->min_per_unit_charge) ? $vehicleType->min_per_unit_charge : old('min_per_unit_charge') }}"
                                        placeholder="{{ __('taxido::static.vehicle_types.enter_min_per_unit_charge') }}">
                                    @error('min_per_unit_charge')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row max_per_unit_charge amount-input">
                        <label class="col-md-2"
                            for="max_per_unit_charge">{{ __('taxido::static.vehicle_types.max_per_unit_charge') }}<span>
                                *</span></label>
                        <div class="col-md-10 amount">
                            <div class="input-group mb-3 flex-nowrap">
                                <span class="input-group-text">{{ getDefaultCurrency()?->symbol }}</span>
                                <div class="w-100">
                                    <input class="form-control" type="number" id="max_per_unit_charge"
                                        name="max_per_unit_charge" min="1"
                                        value="{{ isset($vehicleType->max_per_unit_charge) ? $vehicleType->max_per_unit_charge : old('max_per_unit_charge') }}"
                                        placeholder="{{ __('taxido::static.vehicle_types.enter_max_per_unit_charge') }}">
                                    @error('max_per_unit_charge')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row min_per_unit_charge amount-input">
                        <label class="col-md-2"
                            for="min_per_min_charge">{{ __('taxido::static.vehicle_types.min_per_min_charge') }}<span>*</span></label>
                        <div class="col-md-10 amount">
                            <div class="input-group mb-3 flex-nowrap">
                                <span class="input-group-text">{{ getDefaultCurrency()?->symbol }}</span>
                                <div class="w-100">
                                    <input class="form-control" type="number" id="min_per_min_charge"
                                        name="min_per_min_charge"
                                        value="{{ isset($vehicleType->min_per_min_charge) ? $vehicleType->min_per_min_charge : old('min_per_min_charge') }}"
                                        placeholder="{{ __('taxido::static.vehicle_types.enter_min_per_min_charge') }}">
                                    @error('min_per_min_charge')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row min_per_unit_charge amount-input">
                        <label class="col-md-2"
                            for="max_per_min_charge">{{ __('taxido::static.vehicle_types.max_per_min_charge') }}<span>*</span></label>
                        <div class="col-md-10 amount">
                            <div class="input-group mb-3 flex-nowrap">
                                <span class="input-group-text">{{ getDefaultCurrency()?->symbol }}</span>
                                <div class="w-100">
                                    <input class="form-control" type="number" id="max_per_min_charge"
                                        name="max_per_min_charge"
                                        value="{{ isset($vehicleType->max_per_min_charge) ? $vehicleType->max_per_min_charge : old('max_per_min_charge') }}"
                                        placeholder="{{ __('taxido::static.vehicle_types.enter_max_per_min_charge') }}">
                                    @error('max_per_min_charge')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row min_per_unit_charge amount-input">
                        <label class="col-md-2" for="min_per_weight_charge">
                            {{ __('taxido::static.vehicle_types.min_per_weight_charge') }}<span>*</span>
                        </label>
                        <div class="col-md-10 amount">
                            <div class="input-group mb-3 flex-nowrap">
                                <span class="input-group-text">{{ getDefaultCurrency()?->symbol }}</span>
                                <div class="w-100">
                                    <input class="form-control" type="number" id="min_per_weight_charge"
                                        name="min_per_weight_charge" min="0"
                                        value="{{ isset($vehicleType->min_per_weight_charge) ? $vehicleType->min_per_weight_charge : old('min_per_weight_charge') }}"
                                        placeholder="{{ __('taxido::static.vehicle_types.enter_min_per_weight_charge') }}">
                                    @error('min_per_weight_charge')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row min_per_unit_charge amount-input">
                        <label class="col-md-2" for="max_per_weight_charge">
                            {{ __('taxido::static.vehicle_types.max_per_weight_charge') }}<span>*</span>
                        </label>
                        <div class="col-md-10 amount">
                            <div class="input-group mb-3 flex-nowrap">
                                <span class="input-group-text">{{ getDefaultCurrency()?->symbol }}</span>
                                <div class="w-100">
                                    <input class="form-control" type="number" id="max_per_weight_charge"
                                        name="max_per_weight_charge" min="0"
                                        value="{{ isset($vehicleType->max_per_weight_charge) ? $vehicleType->max_per_weight_charge : old('max_per_weight_charge') }}"
                                        placeholder="{{ __('taxido::static.vehicle_types.enter_max_per_weight_charge') }}">
                                    @error('max_per_weight_charge')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row waiting_time_charge_div amount-input">
                        <label class="col-md-2"
                            for="waiting_time_charge">{{ __('taxido::static.vehicle_types.waiting_time_charge') }}<span>
                                *</span></label>
                        <div class="col-md-10 amount">
                            <div class="input-group mb-3 flex-nowrap">
                                <span class="input-group-text">{{ getDefaultCurrency()?->symbol }}</span>
                                <div class="w-100">
                                    <input class="form-control" type="number" id="waiting_time_charge"
                                        name="waiting_time_charge"
                                        value="{{ isset($vehicleType->waiting_time_charge) ? $vehicleType->waiting_time_charge : old('waiting_time_charge') }}"
                                        placeholder="{{ __('taxido::static.vehicle_types.enter_waiting_time_charge') }}">
                                    @error('waiting_time_charge')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row cancellation_charge_div amount-input">
                        <label class="col-md-2"
                            for="cancellation_charge">{{ __('taxido::static.vehicle_types.cancellation_charge') }}<span>
                                *</span></label>
                        <div class="col-md-10 amount">
                            <div class="input-group mb-3 flex-nowrap">
                                <span class="input-group-text">{{ getDefaultCurrency()?->symbol }}</span>
                                <div class="w-100">
                                    <input class="form-control" type="number" id="cancellation_charge"
                                        name="cancellation_charge" min="1"
                                        value="{{ isset($vehicleType->cancellation_charge) ? $vehicleType->cancellation_charge : old('cancellation_charge') }}"
                                        placeholder="{{ __('taxido::static.vehicle_types.enter_cancellation_charge') }}">
                                    @error('cancellation_charge')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2"
                            for="commission_type">{{ __('taxido::static.vehicle_types.commission_type') }}<span>
                                *</span></label>
                        <div class="col-md-10 select-label-error">
                            <select class="select-2 form-control" id="commission_type" name="commission_type"
                                data-placeholder="{{ __('taxido::static.vehicle_types.select_commission_type') }}">
                                <option class="select-placeholder" value=""></option>
                                @foreach (['fixed' => 'Fixed', 'percentage' => 'Percentage'] as $key => $option)
                                    <option class="option" value="{{ $key }}"
                                        @if (old('commission_type', $vehicleType->commission_type ?? '') == $key) selected @endif>{{ $option }}</option>
                                @endforeach
                            </select>
                            @error('commission_type')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row amount-input" id="commission_rate_field" style="display: none;">
                        <label class="col-md-2"
                            for="commission_rate">{{ __('taxido::static.vehicle_types.commission_rate') }}<span>
                                *</span></label>
                        <div class="col-md-10 select-label-error amount">
                            <div class="input-group">
                                <span class="input-group-text" id="currencyIcon"
                                    style="display: none">{{ getDefaultCurrency()?->symbol }}</span>
                                <input class="form-control" type="number" name="commission_rate"
                                    value="{{ isset($vehicleType->commission_rate) ? $vehicleType->commission_rate : old('commission_rate') }}"
                                    placeholder="{{ __('taxido::static.vehicle_types.enter_commission_rate') }}"
                                    required>
                                <span class="input-group-text" id="percentageIcon" style="display: none;"><i
                                        class="ri-percent-line"></i></span>
                            </div>
                            @error('commission_rate')
                                <div class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="p-sticky">
            <div class="contentbox">
                <div class="inside">
                    <div class="contentbox-title">
                        <h3>{{ __('taxido::static.vehicle_types.publish') }}</h3>

                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex align-items-center gap-2 icon-position">
                                        <button type="submit" name="save" class="btn btn-primary">
                                            <i class="ri-save-line text-white lh-1"></i> {{ __('static.save') }}
                                        </button>
                                        <button type="submit" name="save_and_exit"
                                            class="btn btn-primary spinner-btn">
                                            <i
                                                class="ri-expand-left-line text-white lh-1"></i>{{ __('static.save_and_exit') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="contentbox">
                <div class="inside">
                    <div class="contentbox-title">
                        <h3>{{ __('static.additional_info') }}</h3>
                    </div>
                    <div class="row g-3">
                        <div class="col-xl-12 col-md-4 col-sm-6">
                            <div class="form-group row">
                                <label class="col-md-2"
                                    for="tax_id">{{ __('taxido::static.vehicle_types.tax') }}<span>
                                        *</span></label>
                                <div class="col-md-10 select-label-error">
                                    <span class="text-gray mt-1">
                                        {{ __('taxido::static.vehicle_types.no_tax_message') }}
                                        <a href="{{ @route('admin.tax.index') }}" class="text-primary">
                                            <b>{{ __('taxido::static.here') }}</b>
                                        </a>
                                    </span>
                                    <select class="form-select select-2" id="tax_id" name="tax_id"
                                        data-placeholder="{{ __('taxido::static.vehicle_types.select_tax') }}"
                                        required>
                                        <option class="option" value="" selected></option>
                                        @foreach ($taxes as $key => $tax)
                                            <option value="{{ $tax->id }}"
                                                @if (isset($vehicleType->tax)) @selected(old('tax_id', $vehicleType->tax_id) == $tax->id) @endif>
                                                {{ $tax->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tax_id')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2"
                                    for="all_zones">{{ __('taxido::static.vehicle_types.all_zones') }}</label>
                                <div class="col-md-10">
                                    <label class="switch">
                                        <input class="form-control" type="hidden" name="is_all_zones"
                                            value="0">
                                        <input class="form-check-input" type="checkbox" id="is_all_zones"
                                            name="is_all_zones" value="1" @checked(old('is_all_zones', $vehicleType->is_all_zones ?? true))>
                                        <span class="switch-state"></span>
                                    </label>
                                    @error('is_all_zones')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row" id="zones-field" style="display: none;">
                                <label class="col-md-2"
                                    for="zones">{{ __('taxido::static.vehicle_types.zones') }}<span>
                                        *</span></label>
                                <div class="col-md-10 select-label-error">
                                    <span class="text-gray mt-1">
                                        {{ __('taxido::static.vehicle_types.no_zones_message') }}
                                        <a href="{{ @route('admin.zone.index') }}" class="text-primary">
                                            <b>{{ __('taxido::static.here') }}</b>
                                        </a>
                                    </span>
                                    <select class="form-control select-2 zone" name="zones[]"
                                        data-placeholder="{{ __('taxido::static.vehicle_types.select_zones') }}"
                                        multiple>
                                        @foreach ($zones as $index => $zone)
                                            <option value="{{ $zone->id }}"
                                                @if (@$vehicleType?->zones) @if (in_array($zone->id, $vehicleType->zones->pluck('id')->toArray()))
                                            selected @endif
                                            @elseif (old('zones.' . $index) == $zone->id) selected @endif>
                                                {{ $zone->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('zones')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2"
                                    for="services">{{ __('taxido::static.vehicle_types.services') }}<span>
                                        *</span></label>
                                <div class="col-md-10 select-label-error">
                                    <select class="form-control select-2" id="service_id" name="services[]"
                                        data-placeholder="{{ __('taxido::static.vehicle_types.select_services') }}"
                                        multiple>
                                        @foreach ($services as $index => $service)
                                            <option value="{{ $service->id }}"
                                                @if (@$vehicleType?->services) @if (in_array($service->id, $vehicleType->services->pluck('id')->toArray())) selected @endif
                                            @elseif (old('services.' . $index) == $service->id) selected @endif>
                                                {{ $service->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('services')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-2"
                                    for="serviceCategories">{{ __('taxido::static.vehicle_types.service_categories') }}<span>
                                        *</span></label>
                                <div class="col-md-10 select-label-error">
                                    <select class="form-control select-2" id="service_category_id"
                                        name="serviceCategories[]"
                                        data-placeholder="{{ __('taxido::static.vehicle_types.select_service_categories') }}"
                                        multiple>
                                    </select>
                                    @error('serviceCategories')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-12" for="status">{{ __('taxido::static.vehicle_types.status') }}
                                </label>
                                <div class="col-12">
                                    <div class="switch-field form-control">
                                        <input value="1" type="radio" name="status" id="status_active"
                                            @checked(boolval(@$vehicleTypes?->status ?? true) == true) />
                                        <label for="status_active">{{ __('static.active') }}</label>
                                        <input value="0" type="radio" name="status" id="status_deactive"
                                            @checked(boolval(@$vehicleTypes?->status ?? true) == false) />
                                        <label for="status_deactive">{{ __('static.deactive') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Detailed Fare Calculation Instructions -->
<div class="modal fade fare-calculation-modal" id="fareCalculationModal">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fareCalculationModalLabel"> {{ __('taxido::static.vehicle_types.fare_calculation_instructions') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="swiper face-calculation-slider theme-pagination">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <h5 class="modal-title">{{ __('taxido::static.vehicle_types.key_fields_and_usage') }}</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('taxido::static.vehicle_types.field') }}</th>
                                            <th>{{ __('taxido::static.vehicle_types.description') }}</th>
                                            <th>{{ __('taxido::static.vehicle_types.where_used') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>min_per_unit_charge</code></td>
                                            <td>{{ __('taxido::static.vehicle_types.description') }}</td>
                                            <td>Used in <span>CAB</span>, <span>FREIGHT</span>, and
                                                <span>PARCEL</span> services.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><code>max_per_unit_charge</code></td>
                                            <td>{{ __('taxido::static.vehicle_types.min_per_unit_charge') }}</td>
                                            <td>Used in <span>CAB</span>, <span>FREIGHT</span>, and
                                                <span>PARCEL</span> services.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><code>cancellation_charge</code></td>
                                            <td>Fixed charge for ride cancellation.</td>
                                            <td>Applied in all services when a ride is canceled.</td>
                                        </tr>
                                        <tr>
                                            <td><code>waiting_time_charge</code></td>
                                            <td>{{__('taxido::static.vehicle_types.waiting_time_charge')}}</td>
                                            <td>Applied in <span>CAB</span> and <span>FREIGHT</span>
                                                services.</td>
                                        </tr>
                                        <tr>
                                            <td><code>commission_type</code></td>
                                            <td>{{__('taxido::static.vehicle_types.commission_type')}}</td>
                                            <td>{{__('taxido::static.vehicle_types.apply_service')}}</td>
                                        </tr>
                                        <tr>
                                            <td><code>commission_rate</code></td>
                                            <td>{{__('taxido::static.vehicle_types.commission_rate')}}</td>
                                            <td>{{__('taxido::static.vehicle_types.apply_service')}}</td>
                                        </tr>
                                        <tr>
                                            <td><code>tax_id</code></td>
                                            <td>{{__('taxido::static.vehicle_types.tax_id')}}</td>
                                            <td>{{__('taxido::static.vehicle_types.apply_service')}}</td>
                                        </tr>
                                        <tr>
                                            <td><code>min_per_min_charge</code></td>
                                            <td>Minimum charge per minute.</td>
                                            <td>Used in <span>PACKAGE</span> and <span>RENTAL</span>
                                                services.</td>
                                        </tr>
                                        <tr>
                                            <td><code>max_per_min_charge</code></td>
                                            <td>Maximum charge per minute.</td>
                                            <td>Used in <span>PACKAGE</span> and <span>RENTAL</span>
                                                services.</td>
                                        </tr>
                                        <tr>
                                            <td><code>min_per_weight_charge</code></td>
                                            <td>Minimum charge per kilogram.</td>
                                            <td>Used in <span>FREIGHT</span> and <span>PARCEL</span>
                                                services.</td>
                                        </tr>
                                        <tr>
                                            <td><code>max_per_weight_charge</code></td>
                                            <td>Maximum charge per kilogram.</td>
                                            <td>Used in <span>FREIGHT</span> and <span>PARCEL</span>
                                                services.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <h5 class="modal-title mb-3">{{ __('taxido::static.vehicle_types.bidding_details') }}</h5>
                            <p class="content-text">
                                <strong>Bidding</strong> is used in <strong>CAB</strong> and <strong>FREIGHT</strong>
                                services when the <code>activation.bidding</code> setting is enabled.
                            </p>
                            <ul class="content-list mb-4">
                                <li>{{ __('taxido::static.vehicle_types.bidding_active') }}</li>
                                <li>
                                    If bidding is inactive, the fare is calculated based on the 
                                    <code>min_per_unit_charge</code> and 
                                    <code>max_per_unit_charge</code>.
                                </li>
                        </ul>

                            <h5 class="modal-title">{{ __('taxido::static.vehicle_types.example_calculation') }}</h5>
                            
                            <div class="accordion fare-accordion mt-3" id="exampleCalculations">
                                <!-- Example 1 -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="example1">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#collapseExample1">
                                            {{ __('taxido::static.vehicle_types.example_1') }}
                                        </button>
                                    </h2>
                                    <div id="collapseExample1" class="accordion-collapse collapse show" 
                                        data-bs-parent="#exampleCalculations">
                                        <div class="accordion-body">
                                            <p class="content-text">
                                                <strong>Scenario:</strong> A 15 km ride with a base fare of $20.
                                            </p>
                                            <p class="content-text"><strong>Calculation:</strong></p>
                                            <ul class="content-list">
                                                <li>Minimum Distance Charge: <code>15 km * $1.5/km = $22.5</code></li>
                                                <li>Since <code>$22.5 > $20</code>, the fare is <strong>$22.5</strong>.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Example 2 -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="example2">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapseExample2">
                                            {{ __('taxido::static.vehicle_types.example_2') }}
                                        </button>
                                    </h2>
                                    <div id="collapseExample2" class="accordion-collapse collapse" 
                                        data-bs-parent="#exampleCalculations">
                                        <div class="accordion-body">
                                            <p class="content-text">
                                                <strong>{{ __('taxido::static.vehicle_types.scenario') }}:</strong> 
                                                A 50 km freight delivery with 100 kg weight.
                                            </p>
                                            <p class="content-text"><strong>{{ __('taxido::static.vehicle_types.calculation') }}:</strong></p>
                                            <ul class="content-list">
                                                <li>{{ __('taxido::static.vehicle_types.distance_charge') }}: 
                                                    <code>50 km * $1.5/km = $75</code>
                                                </li>
                                                <li>{{ __('taxido::static.vehicle_types.total_fare') }} (Intercity): 
                                                    <code>$75</code>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Example 3 -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="example3">
                                        <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#collapseExample3">
                                            {{ __('taxido::static.vehicle_types.example_3_rental') }}
                                        </button>
                                    </h2>
                                    <div id="collapseExample3" class="accordion-collapse collapse" 
                                        data-bs-parent="#exampleCalculations">
                                        <div class="accordion-body">
                                            <p class="content-text">
                                                <strong>{{ __('taxido::static.vehicle_types.scenario') }}:</strong> 
                                                Rental ride for 2 days with 3 days of additional minute charges.
                                            </p>
                                            <p class="content-text"><strong>{{ __('taxido::static.vehicle_types.calculation') }}:</strong></p>
                                            <ul class="content-list">
                                                <li>{{ __('taxido::static.vehicle_types.vehicle_charge') }}: 
                                                    <code>20 * 2 days = $50</code>
                                                </li>
                                                <li>{{ __('taxido::static.vehicle_types.total_per_minute_charge') }}: 
                                                    <code>10 * 3 days = $604</code>
                                                </li>
                                                <li>{{ __('taxido::static.vehicle_types.total_fare') }}: 
                                                    <code>$50 + $604 = $654</code>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Example 4 -->
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="example4">
                                        <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#collapseExample4">
                                            {{ __('taxido::static.vehicle_types.example_4_package') }}
                                        </button>
                                    </h2>
                                    <div id="collapseExample4" class="accordion-collapse collapse" 
                                        data-bs-parent="#exampleCalculations">
                                        <div class="accordion-body">
                                            <p class="content-text">
                                                <strong>{{ __('taxido::static.vehicle_types.scenario') }}:</strong> 
                                                A package delivery with 10 km distance and 20 km per minute charges.
                                            </p>
                                            <p class="content-text"><strong>{{ __('taxido::static.vehicle_types.calculation') }}:</strong></p>
                                            <ul class="content-list">
                                                <li>{{ __('taxido::static.vehicle_types.total_per_distance_charge') }}: 
                                                    <code>10 km * $3/km = $30</code>
                                                </li>
                                                <li>{{ __('taxido::static.vehicle_types.total_per_minute_charge') }}: 
                                                    <code>20 km * $3/min = $60</code>
                                                </li>
                                                <li>{{ __('taxido::static.vehicle_types.total_fare') }}: 
                                                    <code>$30 + $60 = $90</code>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="example5">
                                        <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#collapseExample5">
                                            {{ __('taxido::static.vehicle_types.example_5_package') }}
                                        </button>
                                    </h2>
                                    <div id="collapseExample5" class="accordion-collapse collapse" 
                                        data-bs-parent="#exampleCalculations">
                                        <div class="accordion-body">
                                            <p class="content-text">
                                                <strong>{{ __('taxido::static.vehicle_types.scenario') }}:</strong> 
                                                A package delivery with 10 km distance and 20 km per minute charges.
                                            </p>
                                            <p class="content-text"><strong>{{ __('taxido::static.vehicle_types.calculation') }}:</strong></p>
                                            <ul class="content-list">
                                                <li>{{ __('taxido::static.vehicle_types.distance_charge') }}: 
                                                    <code>50 km * $2.0/km = $100</code>
                                                </li>
                                                <li>{{ __('taxido::static.vehicle_types.weight_charge') }}: 
                                                    <code>100 kg * $1.0/kg = $100</code>
                                                </li>
                                                <li>{{ __('taxido::static.vehicle_types.total_fare') }}: 
                                                    <code>$100 + $100 = $200</code>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="slider-bottom-box">
                        <div class="swiper-button-prev">
                            <i class="ri-arrow-left-s-line"></i>
                        </div>
                        <div class="swiper-button-next">
                            <i class="ri-arrow-right-s-line"></i>
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <!-- Swiper JS -->
    <script src="{{ asset('js/swiper-slider/swiper.js') }}"></script>
    <script src="{{ asset('js/swiper-slider/custom-slider.js') }}"></script>
    <script>
        (function($) {
            "use strict";

            $('#vehicleTypeForm').validate({
                ignore: [],
                rules: {
                    "name": "required",
                    "cancellation_charge": "required",
                    "tax_id": "required",
                    "services[]": "required",
                    "vehicle_charge": "required",
                    "serviceCategories[]": "required",
                    "base_amount": "required",
                    "waiting_time_charge": "required",
                    "max_per_unit_charge": "required",
                    "min_per_unit_charge": "required",
                    "max_per_min_charge": "required",
                    "min_per_min_charge": "required",
                    "status": "required",
                    "min_per_weight_charge": "required",
                    "max_per_weight_charge": "required",
                    "commission_type": {
                        required: true
                    },
                    "commission_rate": "required"
                }
            });

            $('#service_id').on('change', function() {
                $('#service_category_id').empty();
                $('#service_category_id').attr('disabled', 'disabled');

                var serviceId = $(this).val();

                if (serviceId) {
                    loadServiceCategories(serviceId);
                } else {
                    $('#service_category_id').empty();
                }
            });

            function loadServiceCategories(serviceId) {
                let url = "{{ route('serviceCategory.index') }}";

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
            }

            function selectCommissionTypeField(type) {
                if (type === 'fixed') {
                    $('#currencyIcon').show();
                    $('#percentageIcon').hide();
                } else if (type === 'percentage') {
                    $('#currencyIcon').hide();
                    $('#percentageIcon').show();
                }
                $('#commission_rate_field').show();
            }

            $('#commission_rate_field').hide();

            $('#commission_type').on('change', function() {
                const selectedType = $(this).val();
                if (selectedType) {
                    selectCommissionTypeField(selectedType);
                } else {
                    $('#commission_rate_field').hide();
                }
            });

            const initialType = $('#commission_type').val();
            if (initialType) {
                selectCommissionTypeField(initialType);
            }

            $('#is_all_zones').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#zones-field').hide();
                } else {
                    $('#zones-field').show();
                }
            });

            if (!$('#is_all_zones').is(':checked')) {
                $('#zones-field').show();
            }

        })(jQuery);
    </script>
@endpush
