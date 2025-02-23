@extends('admin.layouts.master')

@section('title', __('static.settings.app_settings'))

@section('content')
    <div class="contentbox">
        <div class="inside">
            <!-- Content Box Title -->
            <div class="contentbox-title">
                <div class="contentbox-subtitle">
                    <h3>{{ __('static.settings.app_settings') }}</h3>
                </div>
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <div class="vertical-tabs">
                    <div class="row g-xl-5 g-4">
                        <!-- Navigation Tabs -->
                        <div class="col-xxl-4 col-xl-5 col-12">
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                aria-orientation="vertical">
                                <a class="nav-link active" id="v-pills-tabContent" data-bs-toggle="pill"
                                    data-bs-target="#general_settings" type="button" role="tab"
                                    aria-controls="App_Settings" aria-selected="true">
                                    <i class="ri-settings-5-line"></i>{{ __('taxido::static.settings.general') }}
                                </a>
                                <a class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill"
                                    data-bs-target="#Ads_Setting" type="button" role="tab"
                                    aria-controls="v-pills-profile" aria-selected="false">
                                    <i class="ri-toggle-line"></i>{{ __('taxido::static.settings.activation') }}
                                </a>
                                <a class="nav-link" id="v-pills-commission-tab" data-bs-toggle="pill"
                                    data-bs-target="#Commission_Setting" type="button" role="tab"
                                    aria-controls="v-pills-commission" aria-selected="false">
                                    <i
                                        class="ri-pie-chart-2-line   "></i>{{ __('taxido::static.settings.driver_commission') }}
                                </a>
                                <a class="nav-link" id="v-pills-commission-tab" data-bs-toggle="pill"
                                    data-bs-target="#Wallet_Setting" type="button" role="tab"
                                    aria-controls="v-pills-commission" aria-selected="false">
                                    <i class="ri-wallet-2-line"></i>{{ __('taxido::static.settings.wallet') }}
                                </a>
                                <a class="nav-link" id="v-pills-referral-tab" data-bs-toggle="pill"
                                    data-bs-target="#Referral_Setting" type="button" role="tab"
                                    aria-controls="v-pills-referral" aria-selected="false">
                                    <i class="ri-user-add-line"></i>{{ __('taxido::static.settings.referral_settings') }}
                                </a>
                                <a class="nav-link" id="v-pills-location-tab" data-bs-toggle="pill"
                                    data-bs-target="#Location_Setting" type="button" role="tab"
                                    aria-controls="v-pills-location" aria-selected="false">
                                    <i class="ri-map-pin-2-line"></i>{{ __('taxido::static.settings.location_settings') }}
                                </a>
                            </div>
                        </div>
                        <!-- Form Content -->
                        <div class="col-xxl-8 col-xl-7 col-12 tab-b-left">
                            <form method="POST" class="needs-validation user-add" id="taxidosettingsForm"
                                action="{{ route('admin.taxido-setting.update', @$id) }}" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <div class="tab-content w-100" id="v-pills-tabContent">

                                    <!-- General Settings -->
                                    <div class="tab-pane fade show active" id="general_settings" role="tabpanel" aria-labelledby="v-pills-tabContent">                                     
                                        <div class="form-group row">
                                            <label class="col-xxl-3 col-md-4"
                                                for="ride_accept">{{ __('taxido::static.settings.ride_accept_decline') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.enter_ride') }}"></i>
                                            </label>
                                            <div class="col-xxl-9 col-md-8">
                                                <input class="form-control" type="number" name="general[ride_accept]"
                                                    id="general[ride_accept]"
                                                    value="{{ isset($taxidosettings['general']['ride_accept']) ? $taxidosettings['general']['ride_accept'] : old('ride_accept') }}"
                                                    placeholder="{{ __('taxido::static.settings.enter_ride') }}">
                                                @error('general[ride_accept]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div id="greeting-group">

                                            @if (!empty(old('greeting', $taxidosettings['general']['greetings'] ?? [])))
                                                @foreach (old('greeting', $taxidosettings['general']['greetings'] ?? []) as $greetings)
                                                    <div class="form-group row">
                                                        <label class="col-xxl-3 col-md-4" for="greeting">
                                                            {{ __('taxido::static.settings.greeting') }}<span>
                                                                *</span>
                                                        </label>
                                                        <div class="col-xxl-9 col-md-8">
                                                            <div class="greeting-fields">
                                                                <input class="form-control" type="text"
                                                                    name="general[greetings][]"
                                                                    placeholder="{{ __('taxido::static.settings.enter_greeting') }}"
                                                                    value="{{ $greetings }}">
                                                                <button type="button"
                                                                    class="btn btn-danger remove-greeting mt-0">
                                                                    <i class="ri-delete-bin-line"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="form-group row">
                                                    <label class="col-xxl-3 col-md-4" for="greeting">
                                                        {{ __('taxido::static.settings.greeting') }}
                                                    </label>
                                                    <div class="col-xxl-9 col-md-8">
                                                        <div class="greeting-fields">
                                                            <input class="form-control" type="text"
                                                                name="general[greetings][]"
                                                                placeholder="{{ __('taxido::static.settings.enter_greeting') }}">
                                                            <button type="button"
                                                                class="btn btn-danger remove-greeting mt-0">
                                                                <i class="ri-delete-bin-line"></i>

                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="added-button">
                                            <button type="button" id="add-greeting"
                                                class="btn btn-primary mt-0">{{ __('taxido::static.settings.add_greeting') }}</button>
                                        </div>
                                    </div>


                                    <!-- Activation Settings -->
                                    <div class="tab-pane fade" id="Ads_Setting" role="tabpanel"
                                        aria-labelledby="v-pills-profile-tab" tabindex="0">

                                        <div class="form-group row">
                                            <label class="col-md-5"
                                                for="activation[coupon_enable]">{{ __('static.settings.coupon_enable') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('static.settings.coupon_span') }}"></i>
                                            </label>
                                            <div class="col-md-7">
                                                <div class="editor-space">
                                                    <label class="switch">
                                                        @if (isset($taxidosettings['activation']['coupon_enable']))
                                                            <input class="form-control" type="hidden"
                                                                name="activation[coupon_enable]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[coupon_enable]" value="1"
                                                                {{ $taxidosettings['activation']['coupon_enable'] ? 'checked' : '' }}>
                                                        @else
                                                            <input class="form-control" type="hidden"
                                                                name="activation[coupon_enable]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[coupon_enable]" value="1">
                                                        @endif
                                                        <span class="switch-state"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>                     

                                        <div class="form-group row">
                                            <label class="col-md-5"
                                                for="activation[driver_wallet]">{{ __('taxido::static.settings.driver_wallet') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.driver_wallets') }}"></i>
                                            </label>
                                            <div class="col-md-7">
                                                <div class="editor-space">
                                                    <label class="switch">
                                                        @if (isset($taxidosettings['activation']['driver_wallet']))
                                                            <input class="form-control" type="hidden"
                                                                name="activation[driver_wallet]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[driver_wallet]" value="1"
                                                                {{ $taxidosettings['activation']['driver_wallet'] ? 'checked' : '' }}>
                                                        @else
                                                            <input class="form-control" type="hidden"
                                                                name="activation[driver_wallet]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[driver_wallet]" value="1">
                                                        @endif
                                                        <span class="switch-state"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-5"
                                                for="activation[rider_wallet]">{{ __('taxido::static.settings.rider_wallet') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.rider_wallets') }}"></i>
                                            </label>
                                            <div class="col-md-7">
                                                <div class="editor-space">
                                                    <label class="switch">
                                                        @if (isset($taxidosettings['activation']['rider_wallet']))
                                                            <input class="form-control" type="hidden"
                                                                name="activation[rider_wallet]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[rider_wallet]" value="1"
                                                                {{ $taxidosettings['activation']['rider_wallet'] ? 'checked' : '' }}>
                                                        @else
                                                            <input class="form-control" type="hidden"
                                                                name="activation[rider_wallet]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[rider_wallet]" value="1">
                                                        @endif
                                                        <span class="switch-state"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-5"
                                                for="activation[online_payments]">{{ __('taxido::static.settings.online_payment') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.online_payments') }}"></i>
                                            </label>
                                            <div class="col-md-7">
                                                <div class="editor-space">
                                                    <label class="switch">
                                                        @if (isset($taxidosettings['activation']['online_payments']))
                                                            <input class="form-control" type="hidden"
                                                                name="activation[online_payments]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[online_payments]" value="1"
                                                                {{ $taxidosettings['activation']['online_payments'] ? 'checked' : '' }}>
                                                        @else
                                                            <input class="form-control" type="hidden"
                                                                name="activation[online_payments]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[online_payments]" value="1">
                                                        @endif
                                                        <span class="switch-state"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-5"
                                                for="activation[cash_payments]">{{ __('taxido::static.settings.cash_payments') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.cash_payments') }}"></i>
                                            </label>
                                            <div class="col-md-7">
                                                <div class="editor-space">
                                                    <label class="switch">
                                                        @if (isset($taxidosettings['activation']['cash_payments']))
                                                            <input class="form-control" type="hidden"
                                                                name="activation[cash_payments]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[cash_payments]" value="1"
                                                                {{ $taxidosettings['activation']['cash_payments'] ? 'checked' : '' }}>
                                                        @else
                                                            <input class="form-control" type="hidden"
                                                                name="activation[cash_payments]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[cash_payments]" value="1">
                                                        @endif
                                                        <span class="switch-state"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-5"
                                                for="activation[driver_tips]">{{ __('taxido::static.settings.driver_tips') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.tips') }}"></i>
                                            </label>
                                            <div class="col-md-7">
                                                <div class="editor-space">
                                                    <label class="switch">
                                                        @if (isset($taxidosettings['activation']['driver_tips']))
                                                            <input class="form-control" type="hidden"
                                                                name="activation[driver_tips]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[driver_tips]" value="1"
                                                                {{ $taxidosettings['activation']['driver_tips'] ? 'checked' : '' }}>
                                                        @else
                                                            <input class="form-control" type="hidden"
                                                                name="activation[driver_tips]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[driver_tips]" value="1">
                                                        @endif
                                                        <span class="switch-state"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-5"
                                                for="activation[allow_driver_negative_balance]">{{ __('taxido::static.settings.allow_driver_negative_balance') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.negative_balance') }}"></i>
                                            </label>
                                            <div class="col-md-7">
                                                <div class="editor-space">
                                                    <label class="switch">
                                                        @if (isset($taxidosettings['activation']['allow_driver_negative_balance']))
                                                            <input class="form-control" type="hidden"
                                                                name="activation[allow_driver_negative_balance]"
                                                                value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[allow_driver_negative_balance]"
                                                                value="1"
                                                                {{ $taxidosettings['activation']['allow_driver_negative_balance'] ? 'checked' : '' }}>
                                                        @else
                                                            <input class="form-control" type="hidden"
                                                                name="activation[allow_driver_negative_balance]"
                                                                value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[allow_driver_negative_balance]"
                                                                value="1">
                                                        @endif
                                                        <span class="switch-state"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-5"
                                                for="activation[ride_otp]">{{ __('taxido::static.settings.ride_otp') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.otp_ride') }}"></i>
                                            </label>
                                            <div class="col-md-7">
                                                <div class="editor-space">
                                                    <label class="switch">
                                                        @if (isset($taxidosettings['activation']['ride_otp']))
                                                            <input class="form-control" type="hidden"
                                                                name="activation[ride_otp]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[ride_otp]" value="1"
                                                                {{ $taxidosettings['activation']['ride_otp'] ? 'checked' : '' }}>
                                                        @else
                                                            <input class="form-control" type="hidden"
                                                                name="activation[ride_otp]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[ride_otp]" value="1">
                                                        @endif
                                                        <span class="switch-state"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-5"
                                                for="activation[parcel_otp]">{{ __('taxido::static.settings.parcel_otp') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.otp_parcel') }}"></i>
                                            </label>
                                            <div class="col-md-7">
                                                <div class="editor-space">
                                                    <label class="switch">
                                                        @if (isset($taxidosettings['activation']['parcel_otp']))
                                                            <input class="form-control" type="hidden"
                                                                name="activation[parcel_otp]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[parcel_otp]" value="1"
                                                                {{ $taxidosettings['activation']['parcel_otp'] ? 'checked' : '' }}>
                                                        @else
                                                            <input class="form-control" type="hidden"
                                                                name="activation[parcel_otp]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[parcel_otp]" value="1">
                                                        @endif
                                                        <span class="switch-state"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-5"
                                                for="activation[referral_enable]">{{ __('taxido::static.settings.referral_enable') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.enable_referral') }}"></i>
                                            </label>
                                            <div class="col-md-7">
                                                <div class="editor-space">
                                                    <label class="switch">
                                                        @if (isset($taxidosettings['activation']['referral_enable']))
                                                            <input class="form-control" type="hidden"
                                                                name="activation[referral_enable]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[referral_enable]" value="1"
                                                                {{ $taxidosettings['activation']['referral_enable'] ? 'checked' : '' }}>
                                                        @else
                                                            <input class="form-control" type="hidden"
                                                                name="activation[referral_enable]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[referral_enable]" value="1">
                                                        @endif
                                                        <span class="switch-state"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-5"
                                                for="activation[bidding]">{{ __('taxido::static.settings.bidding') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.bid_span') }}"></i>
                                            </label>
                                            <div class="col-md-7">
                                                <div class="editor-space">
                                                    <label class="switch">
                                                        @if (isset($taxidosettings['activation']['bidding']))
                                                            <input class="form-control" type="hidden"
                                                                name="activation[bidding]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[bidding]" value="1"
                                                                {{ $taxidosettings['activation']['bidding'] ? 'checked' : '' }}>
                                                        @else
                                                            <input class="form-control" type="hidden"
                                                                name="activation[bidding]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="activation[bidding]" value="1">
                                                        @endif
                                                        <span class="switch-state"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Commission Settings -->
                                    <div class="tab-pane fade" id="Commission_Setting" role="tabpanel"
                                        aria-labelledby="v-pills-commission" tabindex="0">
                                        <div class="form-group row">
                                            <label class="col-md-2"
                                                for="min_withdraw_amount">{{ __('taxido::static.settings.min_withdraw_amount') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.min_withdraw_text') }}"></i>
                                            </label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="number"
                                                    name="driver_commission[min_withdraw_amount]"
                                                    id="driver_commission[min_withdraw_amount]"
                                                    value="{{ isset($taxidosettings['driver_commission']['min_withdraw_amount']) ? $taxidosettings['driver_commission']['min_withdraw_amount'] : old('min_withdraw_amount') }}"
                                                    placeholder="{{ __('taxido::static.settings.enter_min_withdraw_amount') }}">
                                                @error('driver_commission[min_withdraw_amount]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- <div class="form-group row">
                                            <label class="col-md-2"
                                                for="driver_threshold">{{ __('taxido::static.settings.driver_threshold') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.driver_threshold_help') }}"></i>
                                            </label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="number"
                                                    name="driver_commission[driver_threshold]"
                                                    id="driver_commission[driver_threshold]"
                                                    value="{{ isset($taxidosettings['driver_commission']['driver_threshold']) ? $taxidosettings['driver_commission']['driver_threshold'] : old('driver_threshold') }}"
                                                    placeholder="{{ __('taxido::static.settings.enter_driver_threshold') }}">
                                                @error('driver_commission[driver_threshold]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div> --}}
                                        <div class="form-group row">
                                            <label class="col-md-2"
                                                for="driver_commission[status]">{{ __('taxido::static.settings.status') }}</label>
                                            <div class="col-md-10">
                                                <div class="editor-space">
                                                    <label class="switch">
                                                        @if (isset($taxidosettings['driver_commission']['status']))
                                                            <input class="form-control" type="hidden"
                                                                name="driver_commission[status]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="driver_commission[status]" value="1"
                                                                {{ $taxidosettings['driver_commission']['status'] ? 'checked' : '' }}>
                                                        @else
                                                            <input class="form-control" type="hidden"
                                                                name="driver_commission[status]" value="0">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="driver_commission[status]" value="1">
                                                        @endif
                                                        <span class="switch-state"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="Wallet_Setting" role="tabpanel"
                                        aria-labelledby="v-pills-wallet" tabindex="0">
                                        <div class="form-group row">
                                            <label class="col-xxl-3 col-md-4"
                                                for="wallet_denominations">{{ __('taxido::static.settings.wallet_denominations') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.wallet_denominations_help') }}"></i>
                                            </label>
                                            <div class="col-xxl-9 col-md-8">
                                                <input class="form-control" type="text"
                                                    name="wallet[wallet_denominations]" id="wallet[wallet_denominations]"
                                                    value="{{ isset($taxidosettings['wallet']['wallet_denominations']) ? $taxidosettings['wallet']['wallet_denominations'] : old('wallet_denominations') }}"
                                                    placeholder="{{ __('taxido::static.settings.enter_wallet_denominations') }}">
                                                @error('wallet[wallet_denominations]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-xxl-3 col-md-4"
                                                for="tip_denominations">{{ __('taxido::static.settings.tip_denominations') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.tip_denominations_help') }}"></i>
                                            </label>
                                            <div class="col-xxl-9 col-md-8">
                                                <input class="form-control" type="text"
                                                    name="wallet[tip_denominations]" id="wallet[tip_denominations]"
                                                    value="{{ isset($taxidosettings['wallet']['tip_denominations']) ? $taxidosettings['wallet']['tip_denominations'] : old('tip_denominations') }}"
                                                    placeholder="{{ __('taxido::static.settings.enter_tip_denominations') }}">
                                                @error('wallet[tip_denominations]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Referral Settings -->
                                    <div class="tab-pane fade" id="Referral_Setting" role="tabpanel"
                                        aria-labelledby="v-pills-referral" tabindex="0">
                                        <div class="form-group row">
                                            <label class="col-xxl-3 col-md-4"
                                                for="amount">{{ __('taxido::static.settings.referral_amount') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.referral_amount_help') }}"></i>
                                            </label>
                                            <div class="col-xxl-9 col-md-8">
                                                <input class="form-control" type="text"
                                                    name="referral[referral_amount]" id="referral[referral_amount]"
                                                    value="{{ isset($taxidosettings['referral']['referral_amount']) ? $taxidosettings['referral']['referral_amount'] : old('amount') }}"
                                                    placeholder="{{ __('taxido::static.settings.enter_referral_amount') }}">
                                                @error('referral[referral_amount]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="validity"
                                                class="col-xxl-3 col-md-4">{{ __('taxido::static.settings.first_ride_discount') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.discount') }}"></i>
                                            </label>
                                            <div class="col-xxl-9 col-md-8">
                                                <input class="form-control" type="number"
                                                    name="referral[first_ride_discount]"
                                                    id="referral[first_ride_discount]"
                                                    value="{{ isset($taxidosettings['referral']['first_ride_discount']) ? $taxidosettings['referral']['first_ride_discount'] : old('first_ride_discount') }}"
                                                    placeholder="{{ __('taxido::static.settings.enter_first_ride_discount') }}">
                                                @error('referral[first_ride_discount]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="validity"
                                                class="col-xxl-3 col-md-4">{{ __('taxido::static.settings.validity') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.set_validity') }}"></i>
                                            </label>
                                            <div class="col-xxl-9 col-md-8 ">
                                                <input class="form-control" type="number" name="referral[validity]"
                                                    id="referral[validity]"
                                                    value="{{ isset($taxidosettings['referral']['validity']) ? $taxidosettings['referral']['validity'] : old('validity') }}"
                                                    placeholder="{{ __('taxido::static.settings.enter_validity') }}">
                                                @error('referral[validity]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-xxl-3 col-md-4"
                                                for="interval">{{ __('taxido::static.settings.interval') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.interval_help') }}"></i>
                                            </label>
                                            <div class="col-xxl-9 col-md-8">
                                                <select class="form-control" name="referral[interval]"
                                                    id="referral[interval]">
                                                    <option value="day"
                                                        {{ isset($taxidosettings['referral']['interval']) && $taxidosettings['referral']['interval'] == 'day' ? 'selected' : '' }}>
                                                        {{ __('taxido::static.settings.days') }}
                                                    </option>
                                                    <option value="month"
                                                        {{ isset($taxidosettings['referral']['interval']) && $taxidosettings['referral']['interval'] == 'month' ? 'selected' : '' }}>
                                                        {{ __('taxido::static.settings.months') }}
                                                    </option>
                                                    <option value="year"
                                                        {{ isset($taxidosettings['referral']['interval']) && $taxidosettings['referral']['interval'] == 'year' ? 'selected' : '' }}>
                                                        {{ __('taxido::static.settings.years') }}
                                                    </option>
                                                </select>
                                                @error('referral[interval]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location Settings -->
                                    <div class="tab-pane fade" id="Location_Setting" role="tabpanel"
                                        aria-labelledby="v-pills-location" tabindex="0">
                                        <div class="form-group row">
                                            <label for="map_provider"
                                                class="col-xxl-3 col-md-4">{{ __('taxido::static.settings.select_map_type') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.map') }}"></i>
                                            </label>
                                            <div class="col-xxl-9 col-md-8 error-div select-dropdown">
                                                <select class="select-2 form-control select-map"
                                                    id="location[map_provider]" name="location[map_provider]"
                                                    data-placeholder="{{ __('taxido::static.settings.select_map') }}">
                                                    <option class="select-placeholder" value=""></option>
                                                    @foreach (['google_map' => 'Google Map', 'osm' => 'OpenStreetMap (OSM)'] as $key => $option)
                                                        <option class="option" value="{{ $key }}"
                                                            @if (($taxidosettings['location']['map_provider'] ?? old('location.map_provider')) == $key) selected @endif>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('location.map_provider')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-xxl-3 col-md-4"
                                                for="amount">{{ __('taxido::static.settings.radius_meter') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.radius') }}"></i>
                                            </label>
                                            <div class="col-xxl-9 col-md-8">
                                                <input class="form-control" type="number" name="location[radius_meter]"
                                                    id="location[radius_meter]" min="1"
                                                    value="{{ isset($taxidosettings['location']['radius_meter']) ? $taxidosettings['location']['radius_meter'] : old('radius_meter') }}"
                                                    placeholder="{{ __('taxido::static.settings.enter_radius_meter') }}">
                                                @error('location[radius_meter]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-xxl-3 col-md-4"
                                                for="amount">{{ __('taxido::static.settings.radius_per_second') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="{{ __('taxido::static.settings.radius_second') }}"></i>
                                            </label>
                                            <div class="col-xxl-9 col-md-8">
                                                <input class="form-control" type="number"
                                                    name="location[radius_per_second]" id="location[radius_per_second]"
                                                    min="1"
                                                    value="{{ isset($taxidosettings['location']['radius_per_second']) ? $taxidosettings['location']['radius_per_second'] : old('radius_per_second') }}"
                                                    placeholder="{{ __('taxido::static.settings.enter_radius_per_second') }}">
                                                @error('location[radius_per_second]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Submit Button -->
                                    <button type="submit"
                                        class="btn btn-primary spinner-btn">{{ __('static.save') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function($) {
            "use strict";
            $(document).ready(function() {
                const MAX_Greetings = 5;

                function toggleRemoveButtons() {
                    if ($('#greeting-group .form-group').length === 1) {
                        $('#greeting-group .remove-greeting').hide();
                    } else {
                        $('#greeting-group .remove-greeting').show();
                    }
                }

                $('#add-greeting').on('click', function() {

                    const greetingCount = $('#greeting-group .form-group').length;

                    if (greetingCount >= MAX_Greetings) {

                        toastr.warning('You can add up to 5 greetings only.');
                        return;
                    }

                    var newgreetingField = $('#greeting-group .form-group:first').clone();
                    newgreetingField.find('input').val('');
                    $('#greeting-group').append(newgreetingField);
                    toggleRemoveButtons();
                });

                $(document).on('click', '.remove-greeting', function() {
                    $(this).closest('.form-group').remove();
                    toggleRemoveButtons();
                });

                toggleRemoveButtons();
            });
        })(jQuery);
    </script>
@endpush
