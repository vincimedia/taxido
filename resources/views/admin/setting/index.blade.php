@use('App\Models\Page')
@php
    $pages = Page::where('status', true)?->get(['id', 'title']);
    $smsGateways = getSMSGatewayList();
@endphp
@extends('admin.layouts.master')
@section('title', __('static.settings.settings'))
@section('content')
    <div class="contentbox">
        <div class="inside">
            <div class="contentbox-title">
                <div class="contentbox-subtitle">
                    <h3>{{ __('static.settings.settings') }}</h3>
                </div>
            </div>
            <div class="contentbox-body">
                <div class="vertical-tabs">
                    <div class="row g-xl-5 g-4">
                        <div class="col-xl-4 col-12">
                            <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                aria-orientation="vertical">
                                <a class="nav-link active" id="v-pills-tabContent" data-bs-toggle="pill"
                                    data-bs-target="#general_settings" type="button" role="tab"
                                    aria-controls="v-pills-general" aria-selected="true">
                                    <i class="ri-settings-5-line"></i>{{ __('static.settings.general') }}
                                </a>
                                <a class="nav-link" id="v-pills-profile-tab" data-bs-toggle="pill"
                                    data-bs-target="#Ads_Setting" type="button" role="tab"
                                    aria-controls="v-pills-profile" aria-selected="false">
                                    <i class="ri-toggle-line"></i>{{ __('static.settings.activation') }}
                                </a>
                                <a class="nav-link" id="v-pills-messages-tab" data-bs-toggle="pill"
                                    data-bs-target="#Email_Setting" type="button" role="tab"
                                    aria-controls="v-pills-messages" aria-selected="false">
                                    <i class="ri-mail-open-line"></i>{{ __('static.settings.email_configuration') }}
                                </a>
                                <a class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill"
                                    data-bs-target="#Google_Recaptcha" type="button" role="tab"
                                    aria-controls="v-pills-settings" aria-selected="false">
                                    <i class="ri-google-line"></i>{{ __('static.settings.google_recaptcha') }}
                                </a>
                                <a class="nav-link" id="v-pills-settings-tab" data-bs-toggle="pill"
                                    data-bs-target="#firebase" type="button" role="tab"
                                    aria-controls="v-pills-settings" aria-selected="false">
                                    <i class="ri-fire-line"></i>{{ __('static.settings.firebase') }}
                                </a>
                                <a class="nav-link " id="v-pills-tabContent" data-bs-toggle="pill"
                                    data-bs-target="#app_settings" type="button" role="tab"
                                    aria-controls="App_Settings" aria-selected="true">
                                    <i class="ri-settings-line"></i>{{ __('static.settings.app_settings') }}
                                </a>
                                <a class="nav-link " id="v-pills-tabContent" data-bs-toggle="pill" data-bs-target="#agora"
                                    type="button" role="tab" aria-controls="v-pills-agora" aria-selected="true">
                                    <i class="ri-chat-3-line"></i>{{ __('static.settings.agora') }}
                                </a>
                                @if (@$settings['activation']['social_login_enable'])
                                    <a class="nav-link " id="v-pills-social-tab" data-bs-toggle="pill"
                                        data-bs-target="#social" type="button" role="tab"
                                        aria-controls="v-pills-social" aria-selected="true">
                                        <i class="ri-global-line"></i>{{ __('static.settings.social') }}
                                    </a>
                                @endif
                                <a class="nav-link " id="v-pills-maintenance-mode-tab" data-bs-toggle="pill"
                                    data-bs-target="#maintenance_mode" type="button" role="tab"
                                    aria-controls="v-pills-maintenance-mode" aria-selected="true">
                                    <i class="ri-alert-line"></i>{{ __('static.settings.maintenance_mode') }}
                                </a>
                            </div>
                        </div>
                        <div class="col-xxl-7 col-xl-8 col-12 tab-b-left">
                            <form method="POST" class="needs-validation user-add" id="settingsForm"
                                action="{{ route('admin.setting.update', @$id) }}" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <div class="tab-content w-100" id="v-pills-tabContent">
                                    <div class="tab-pane fade show active" id="general_settings" role="tabpanel"
                                        aria-labelledby="v-pills-general" tabindex="0">
                                        <div class="form-group row">
                                            <label class="col-md-2"
                                                for="light_logo_image_id">{{ __('static.settings.light_logo') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="*Upload image size 50x50px recommended"></i>
                                            </label>
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <x-image :name="'general[light_logo_image_id]'" :data="isset($settings['general']['light_logo_image'])
                                                        ? $settings['general']['light_logo_image']
                                                        : old('general.light_logo_image_id')" :text="false"
                                                        :multiple="false"></x-image>
                                                    @error('light_logo_image_id')
                                                        <span class="invalid-feedback d-block" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-2"
                                                for="dark_logo_image_id">{{ __('static.settings.dark_logo') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="*Upload image size 50x50px recommended"></i>
                                            </label>
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <x-image :name="'general[dark_logo_image_id]'" :data="isset($settings['general']['dark_logo_image'])
                                                        ? $settings['general']['dark_logo_image']
                                                        : old('general.dark_logo_image_id')" :text="false"
                                                        :multiple="false"></x-image>
                                                    @error('dark_logo_image_id')
                                                        <span class="invalid-feedback d-block" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-2"
                                                for="favicon_image_id">{{ __('static.settings.favicon') }}
                                                <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                    data-bs-title="*Upload image size 50x50px recommended"></i>
                                            </label>
                                            <div class="col-md-10">
                                                <div class="form-group">
                                                    <x-image :name="'general[favicon_image_id]'" :data="isset($settings['general']['favicon_image'])
                                                        ? $settings['general']['favicon_image']
                                                        : old('general.favicon_image_id')" :text="false"
                                                        :multiple="false"></x-image>
                                                    @error('favicon_image_id')
                                                        <span class="invalid-feedback d-block" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-2"
                                                for="site_name">{{ __('static.settings.site_name') }}</label>
                                            <div class="col-md-10">
                                                <input class="form-control" type="text" id="general[site_name]"
                                                    name="general[site_name]"
                                                    value="{{ $settings['general']['site_name'] ?? old('site_name') }}"
                                                    placeholder="{{ __('static.settings.enter_site_name') }}">
                                                @error('general[site_name]')
                                                    <span class="invalid-feedback d-block" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="country"
                                                class="col-md-2">{{ __('static.settings.timezone') }}</label>
                                            <div class="col-md-10 error-div select-dropdown">
                                                <select class="select-2 form-control select-country"
                                                    id="general[default_timezone]" name="general[default_timezone]"
                                                    data-placeholder="{{ __('static.settings.select_timezone') }}">
                                                    <option class="select-placeholder" value=""></option>
                                                    @forelse ($timeZones as $timeZone)
                                                        <option class="option" value={{ $timeZone->value }}
                                                            @if ($settings['general']['default_timezone'] ?? old('default_timezone')) @if ($timeZone->value == $settings['general']['default_timezone']) selected @endif
                                                            @endif>{{ $timeZone->label() }}</option>
                                                        @empty
                                                            <option value="" disabled></option>
                                                        @endforelse
                                                    </select>
                                                    @error('general[default_timezone]')
                                                        <span class="invalid-feedback d-block" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="country"
                                                    class="col-md-2">{{ __('static.settings.sms_gateway') }}<span>*</span></label>
                                                <div class="col-md-10 error-div select-dropdown">
                                                    <select class="select-2 form-control select-country"
                                                        id="general[default_sms_gateway]" name="general[default_sms_gateway]"
                                                        data-placeholder="{{ __('static.settings.select_sms_gateway') }}">
                                                        <option class="select-placeholder" value=""></option>
                                                        <option class="option" value="custom">
                                                            {{ __('static.settings.custom_sms_gateway') }}
                                                        </option>
                                                        @forelse ($smsGateways as $smsGateway)
                                                            <option class="option" value="{{ $smsGateway['slug'] }}"
                                                                @if ($settings['general']['default_sms_gateway'] ?? old('default_sms_gateway')) @if ($smsGateway['slug'] == $settings['general']['default_sms_gateway']) selected @endif
                                                                @endif>{{ $smsGateway['name'] }}</option>
                                                            @empty
                                                                <option value="" disabled></option>
                                                            @endforelse
                                                        </select>
                                                        @error('general[default_sms_gateway]')
                                                            <span class="invalid-feedback d-block" role="alert">
                                                                <strong>{{ $message }}</strong>
                                                            </span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="general[default_language_id]"
                                                        class="col-md-2">{{ __('static.settings.default_language_id') }}</label>
                                                    <div class="col-md-10 error-div select-dropdown">
                                                        <select class="select-2 form-control select-country"
                                                            id="general[default_language_id]" name="general[default_language_id]"
                                                            data-placeholder="{{ __('static.settings.select_language') }}">
                                                            <option class="select-placeholder" value=""></option>
                                                            @forelse (getLanguage() as $key => $option)
                                                                <option class="option" value={{ $key }}
                                                                    @if ($settings['general']['default_language_id'] ?? old('default_language_id')) @if ($key == $settings['general']['default_language_id']) selected @endif
                                                                    @endif>{{ $option }}</option>
                                                                @empty
                                                                    <option value="" disabled></option>
                                                                @endforelse
                                                            </select>
                                                            <span class="text-gray mt-1">
                                                                {{ __('static.settings.no_languages_message') }}
                                                                <a href="{{ @route('admin.language.index') }}" class="text-primary">
                                                                    <b>{{ __('static.here') }}</b>
                                                                </a>
                                                            </span>
                                                            @error('general[default_language_id]')
                                                                <span class="invalid-feedback d-block" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="general[default_currency_id]"
                                                            class="col-md-2">{{ __('static.settings.currency') }}</label>
                                                        <div class="col-md-10 error-div select-dropdown">
                                                            <select class="select-2 form-control select-currency"
                                                                id="general[default_currency_id]" name="general[default_currency_id]"
                                                                data-placeholder="{{ __('static.settings.select_currency') }}">
                                                                <option class="select-placeholder" value=""></option>
                                                                @forelse (getCurrencies() as $key => $option)
                                                                    <option class="option" value={{ $key }}
                                                                        @if ($settings['general']['default_currency_id'] ?? old('default_currency_id')) @if ($key == $settings['general']['default_currency_id']) selected @endif
                                                                        @endif>{{ $option }}</option>
                                                                    @empty
                                                                        <option value="" disabled></option>
                                                                    @endforelse
                                                                </select>
                                                                <span class="text-gray mt-1">
                                                                    {{ __('static.settings.no_currencies_message') }}
                                                                    <a href="{{ @route('admin.currency.index') }}" class="text-primary">
                                                                        <b>{{ __('static.here') }}</b>
                                                                    </a>
                                                                </span>
                                                                @error('general[default_currency_id]')
                                                                    <span class="invalid-feedback d-block" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label for="currency_symbol"
                                                                class="col-md-2">{{ __('static.settings.currency_symbol') }}</label>
                                                            <div class="col-md-10 error-div select-dropdown">
                                                                <select class="select-2 form-control select-country" id="currency_symbol"
                                                                    name="general[currency_symbol]"
                                                                    data-placeholder="{{ __('static.settings.select_direction') }}">
                                                                    <option class="select-placeholder" value="right"
                                                                        @if ($settings['general']['currency_symbol'] ?? old('currency_symbol') == 'right') selected @endif>
                                                                        {{ __('static.settings.right') }}
                                                                    </option>
                                                                    <option class="option" value="left"
                                                                        @if ($settings['general']['currency_symbol'] ?? old('currency_symbol') == 'left') selected @endif>
                                                                        {{ __('static.settings.left') }}
                                                                    </option>
                                                                </select>
                                                                @error('general[currency_symbol]')
                                                                    <span class="invalid-feedback d-block" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group row">
                                                            <label class="col-md-2"
                                                                for="platform_fees">{{ __('static.settings.platform_fees') }}</label>
                                                            <div class="col-md-10">
                                                                <div class="input-group mb-3 flex-nowrap">
                                                                    <span
                                                                        class="input-group-text">{{ getDefaultCurrency()?->symbol }}</span>
                                                                    <input class="form-control" type="number" min="1"
                                                                        id="general[platform_fees]" name="general[platform_fees]"
                                                                        value="{{ $settings['general']['platform_fees'] ?? old('platform_fees') }}"
                                                                        placeholder="{{ __('static.settings.enter_platform_fees') }}">
                                                                    @error('general[platform_fees]')
                                                                        <span class="invalid-feedback d-block" role="alert">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="form-group row">
                                                            <label for="mode"
                                                                class="col-md-2">{{ __('static.settings.mode') }}</label>
                                                            <div class="col-md-10 error-div select-dropdown">
                                                                <select class="select-2 form-control select-mode" id="mode"
                                                                    name="general[mode]"
                                                                    data-placeholder="{{ __('static.settings.select_mode') }}">
                                                                    <option class="select-placeholder" value=""></option>
                                                                    @forelse (['dark' => 'Dark', 'light' => 'Light'] as $key => $option)
                                                                        <option class="option" value={{ $key }}
                                                                            @if ($settings['general']['mode'] ?? old('mode')) @if ($key == $settings['general']['mode']) selected @endif
                                                                            @endif>{{ $option }}</option>
                                                                        @empty
                                                                            <option value="" disabled></option>
                                                                        @endforelse
                                                                    </select>
                                                                    @error('mode')
                                                                        <span class="invalid-feedback d-block" role="alert">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-md-2"
                                                                    for="copyright_text">{{ __('static.settings.copyright_text') }}</label>
                                                                <div class="col-md-10">
                                                                    <input class="form-control" type="text" id="general[copyright]"
                                                                        name="general[copyright]"
                                                                        value="{{ $settings['general']['copyright'] ?? old('copyright') }}"
                                                                        placeholder="{{ __('static.settings.enter_copyright_text') }}">
                                                                    @error('general[copyright]')
                                                                        <span class="invalid-feedback d-block" role="alert">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane fade" id="Ads_Setting" role="tabpanel"
                                                            aria-labelledby="v-pills-profile-tab" tabindex="0">
                                                            <div class="form-group row">
                                                                <label class="col-xxl-3 col-md-4"
                                                                    for="activation[platform_fees]">{{ __('static.settings.platform_fees') }}
                                                                    <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                                        data-bs-title="{{ __('static.settings.enter_platform_fees') }}"></i>
                                                                </label>
                                                                <div class="col-xxl-9 col-md-8">
                                                                    <div class="editor-space">
                                                                        <label class="switch">
                                                                            @if (isset($settings['activation']['platform_fees']))
                                                                                <input class="form-control" type="hidden"
                                                                                    name="activation[platform_fees]" value="0">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    name="activation[platform_fees]" value="1"
                                                                                    {{ $settings['activation']['platform_fees'] ? 'checked' : '' }}>
                                                                            @else
                                                                                <input class="form-control" type="hidden"
                                                                                    name="activation[platform_fees]" value="0">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    name="activation[platform_fees]" value="1">
                                                                            @endif
                                                                            <span class="switch-state"></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-group row">
                                                                <label class="col-xxl-3 col-md-4"
                                                                    for="activation[social_login_enable]">{{ __('static.settings.social_login_enable') }}
                                                                    <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                                        data-bs-title="{{ __('static.settings.enter_social_login_enable') }}"></i>
                                                                </label>
                                                                <div class="col-xxl-9 col-md-8">
                                                                    <div class="editor-space">
                                                                        <label class="switch">
                                                                            @if (isset($settings['activation']['social_login_enable']))
                                                                                <input class="form-control" type="hidden"
                                                                                    name="activation[social_login_enable]" value="0">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    name="activation[social_login_enable]" value="1"
                                                                                    {{ $settings['activation']['social_login_enable'] ? 'checked' : '' }}>
                                                                            @else
                                                                                <input class="form-control" type="hidden"
                                                                                    name="activation[social_login_enable]" value="0">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    name="activation[social_login_enable]" value="1">
                                                                            @endif
                                                                            <span class="switch-state"></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>                                                          

                                                            <div class="form-group row">
                                                                <label class="col-md-2"
                                                                    for="activation[login_number]">{{ __('static.settings.sms_login') }}
                                                                    <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                                        data-bs-title="{{ __('static.settings.login_span') }}"></i>
                                                                </label>
                                                                <div class="col-md-10">
                                                                    <div class="editor-space">
                                                                        <label class="switch">
                                                                            @if (isset($settings['activation']['login_number']))
                                                                                <input class="form-control" type="hidden"
                                                                                    name="activation[login_number]" value="0">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    name="activation[login_number]" value="1"
                                                                                    {{ $settings['activation']['login_number'] ? 'checked' : '' }}>
                                                                            @else
                                                                                <input class="form-control" type="hidden"
                                                                                    name="activation[login_number]" value="0">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    name="activation[login_number]" value="1">
                                                                            @endif
                                                                            <span class="switch-state"></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-md-2"
                                                                    for="activation[send_sms]">{{ __('static.settings.send_sms') }}
                                                                    <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                                        data-bs-title="{{ __('static.settings.sms_span') }}"></i>
                                                                </label>
                                                                <div class="col-md-10">
                                                                    <div class="editor-space">
                                                                        <label class="switch">
                                                                            @if (isset($settings['activation']['send_sms']))
                                                                                <input class="form-control" type="hidden"
                                                                                    name="activation[send_sms]" value="0">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    name="activation[send_sms]" value="1"
                                                                                    {{ $settings['activation']['send_sms'] ? 'checked' : '' }}>
                                                                            @else
                                                                                <input class="form-control" type="hidden"
                                                                                    name="activation[send_sms]" value="0">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    name="activation[send_sms]" value="1">
                                                                            @endif
                                                                            <span class="switch-state"></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-md-2"
                                                                    for="activation[default_credentials]">{{ __('static.settings.default_credentials') }}
                                                                    <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                                        data-bs-title="{{ __('static.settings.default_credentials_span') }}"></i>
                                                                </label>
                                                                <div class="col-md-10">
                                                                    <div class="editor-space">
                                                                        <label class="switch">
                                                                            @if (isset($settings['activation']['default_credentials']))
                                                                                <input class="form-control" type="hidden"
                                                                                    name="activation[default_credentials]" value="0">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    name="activation[default_credentials]" value="1"
                                                                                    {{ $settings['activation']['default_credentials'] ? 'checked' : '' }}>
                                                                            @else
                                                                                <input class="form-control" type="hidden"
                                                                                    name="activation[default_credentials]" value="0">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    name="activation[default_credentials]" value="1">
                                                                            @endif
                                                                            <span class="switch-state"></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                <label class="col-xxl-3 col-md-4"
                                                                    for="activation[demo_mode]">{{ __('static.settings.demo_mode') }}
                                                                    <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                                        data-bs-title="{{ __('static.settings.enter_demo_mode') }}"></i>
                                                                </label>
                                                                <div class="col-xxl-9 col-md-8">
                                                                    <div class="editor-space">
                                                                        <label class="switch">
                                                                            @if (isset($settings['activation']['demo_mode']))
                                                                                <input class="form-control" type="hidden"
                                                                                    name="activation[demo_mode]" value="0">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    name="activation[demo_mode]" value="1"
                                                                                    {{ $settings['activation']['demo_mode'] ? 'checked' : '' }}>
                                                                            @else
                                                                                <input class="form-control" type="hidden"
                                                                                    name="activation[demo_mode]" value="0">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    name="activation[demo_mode]" value="1">
                                                                            @endif
                                                                            <span class="switch-state"></span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="tab-pane fade" id="Email_Setting" role="tabpanel"
                                                            aria-labelledby="v-pills-settings-tab" tabindex="0">
                                                            <div class="form-group row">
                                                                <label for="country"
                                                                    class="col-md-2">{{ __('static.settings.mailer') }}</label>
                                                                <div class="col-md-10 error-div select-dropdown">
                                                                    <select class="select-2 form-control select-country"
                                                                        id="email[mail_mailer]" name="email[mail_mailer]"
                                                                        data-placeholder="{{ __('static.settings.select_mail_mailer') }}">
                                                                        <option class="select-placeholder" value=""></option>
                                                                        @forelse (['smtp' => 'SMTP', 'sendmail' => 'Sendmail'] as $key => $option)
                                                                            <option class="option" value={{ $key }}
                                                                                @if ($settings['email']['mail_mailer'] ?? old('mail_mailer')) @if ($key == $settings['email']['mail_mailer']) selected @endif
                                                                                @endif>{{ $option }}</option>
                                                                            @empty
                                                                                <option value="" disabled></option>
                                                                            @endforelse
                                                                        </select>
                                                                        @error('mode')
                                                                            <span class="invalid-feedback d-block" role="alert">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="col-md-2"
                                                                        for="mail_host">{{ __('static.settings.host') }}</label>
                                                                    <div class="col-md-10">
                                                                        <input class="form-control" type="text" name="email[mail_host]"
                                                                            id="email[mail_host]"
                                                                            value="{{ isset($settings['email']['mail_host']) ? $settings['email']['mail_host'] : old('mail_host') }}"
                                                                            placeholder="{{ __('static.settings.enter_host') }}">
                                                                        @error('email[mail_host]')
                                                                            <span class="invalid-feedback d-block" role="alert">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="col-md-2"
                                                                        for="mail_port">{{ __('static.settings.port') }}</label>
                                                                    <div class="col-md-10">
                                                                        <input class="form-control" type="number" min="1"
                                                                            name="email[mail_port]" id="email[mail_port]"
                                                                            value="{{ isset($settings['email']['mail_port']) ? $settings['email']['mail_port'] : old('mail_host') }}"
                                                                            placeholder="{{ __('static.settings.enter_port') }}">
                                                                        @error('mail_port')
                                                                            <span class="invalid-feedback d-block" role="alert">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label for="country"
                                                                        class="col-md-2">{{ __('static.settings.mail_encryption') }}</label>
                                                                    <div class="col-md-10 select-label-error">
                                                                        <select class="select-2 form-control select-country"
                                                                            id="email[mail_encryption]" name="email[mail_encryption]"
                                                                            data-placeholder="{{ __('static.settings.select_mail_encryption') }}">
                                                                            <option class="select-placeholder" value=""></option>
                                                                            @forelse (['tls' => 'TLS', 'ssl' => 'SSL'] as $key => $option)
                                                                                <option class="option" value={{ $key }}
                                                                                    @if ($settings['email']['mail_encryption'] ?? old('mail_encryption')) @if ($key == $settings['email']['mail_encryption']) selected @endif
                                                                                    @endif>{{ $option }}</option>
                                                                                @empty
                                                                                    <option value="" disabled></option>
                                                                                @endforelse
                                                                            </select>
                                                                            @error('mode')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="mail_username">{{ __('static.settings.mail_username') }}</label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="text" name="email[mail_username]"
                                                                                id="email[mail_username]"
                                                                                value="{{ isset($settings['email']['mail_username']) ? $settings['email']['mail_username'] : old('mail_username') }}"
                                                                                placeholder="{{ __('static.settings.enter_username') }}">
                                                                            @error('mail_username')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="password">{{ __('static.settings.mail_password') }}<span>
                                                                                *</span></label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="password" name="email[mail_password]"
                                                                                id="email[mail_password]"
                                                                                value="{{ encryptKey(isset($settings['email']['mail_password']) ? $settings['email']['mail_password'] : old('mail_password')) }}"
                                                                                placeholder="{{ __('static.settings.enter_password') }}">
                                                                            @error('mail_password')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="mail_from_name">{{ __('static.settings.mail_from_name') }}</label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="text" name="email[mail_from_name]"
                                                                                id="email[mail_from_name]"
                                                                                value="{{ isset($settings['email']['mail_from_name']) ? $settings['email']['mail_from_name'] : old('mail_from_name') }}"
                                                                                placeholder="{{ __('static.settings.enter_email_from_name') }}">
                                                                            @error('mail_from_name')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="mail_from_address">{{ __('static.settings.mail_from_address') }}</label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="text"
                                                                                name="email[mail_from_address]" id="email[mail_from_address]"
                                                                                value="{{ isset($settings['email']['mail_from_address']) ? $settings['email']['mail_from_address'] : old('mail_from_address') }}"
                                                                                placeholder="{{ __('static.settings.enter_email_from_address') }}">
                                                                            @error('mail_from_address')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <hr>
                                                                    <h4 class="fw-semibold mb-3 text-primary w-100">
                                                                        {{ __('static.settings.test_mail') }}
                                                                    </h4>
                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="mail">{{ __('static.settings.to_mail') }}</label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="text" name="mail" id="mail"
                                                                                placeholder="{{ __('static.enter_email') }}">
                                                                            @error('mail')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <button id="send-test-mail" name="test_mail" class="btn btn-primary">
                                                                        {{ __('static.settings.send_test_mail') }}
                                                                    </button>

                                                                    <div class="instruction-box">
                                                                        <div class="instruction-title">
                                                                            <h4>{{ __('static.settings.instruction') }}</h4>
                                                                            <p>
                                                                                {{ __('static.settings.test_mail_note') }}
                                                                            </p>
                                                                        </div>
                                                                        <div class="list-box">
                                                                            <h5>{{ __('static.settings.test_mail_not_using_ssl') }}</h5>
                                                                            <ul>
                                                                                <li>{{ __('static.settings.test_mail_not_ssl_msg_1') }}</li>
                                                                                <li>{{ __('static.settings.test_mail_not_ssl_msg_2') }}</li>
                                                                                <li>{{ __('static.settings.test_mail_not_ssl_msg_3') }}</li>
                                                                                <li>{{ __('static.settings.test_mail_not_ssl_msg_4') }}</li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="list-box">

                                                                            <h5>{{ __('static.settings.test_mail_using_ssl') }}</h5>
                                                                            <ul>
                                                                                <li>{{ __('static.settings.test_mail_ssl_msg_1') }}</li>
                                                                                <li>{{ __('static.settings.test_mail_ssl_msg_2') }}</li>
                                                                                <li>{{ __('static.settings.test_mail_ssl_msg_3') }}</li>
                                                                                <li>{{ __('static.settings.test_mail_ssl_msg_4') }}</li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>

                                                                </div>

                                                                <div class="tab-pane" id="Readings" role="tabpanel"
                                                                    aria-labelledby="v-pills-settings-tab" tabindex="0">
                                                                    <div class="form-group row">
                                                                        <label for="display_homepage"
                                                                            class="col-xl-3 col-md-4">{{ __('static.settings.homepage_displays') }}</label>
                                                                        <div class="col-xl-8 col-md-7">
                                                                            <div
                                                                                class="form-group m-checkbox-inline mb-0 custom-radio-ml d-flex radio-animated gap-4">
                                                                                <label class="d-block" for="post">
                                                                                    <input class="radio_animated select_home_page" id="post"
                                                                                        checked="checked" name="readings[status]" type="radio"
                                                                                        value="1">
                                                                                    {{ __('static.settings.latest_posts') }}
                                                                                </label>
                                                                                <label class="d-block" for="page">
                                                                                    <input class="radio_animated select_home_page" id="page"
                                                                                        name="readings[status]" type="radio" value="0">
                                                                                    {{ __('static.settings.static_page') }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="homepage">{{ __('static.settings.home_page') }}</label>
                                                                        <div class="col-md-10">
                                                                            <select class="form-control select-2" id="readings[homepage]"
                                                                                name="readings[home_page]"
                                                                                data-placeholder="{{ __('static.settings.select_home_page') }}">
                                                                                <option class="select-placeholder" value=""></option>
                                                                                @foreach ($pages as $index => $page)
                                                                                    <option value="{{ $page->id }}">
                                                                                        {{ $page->title }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="Google_Recaptcha" role="tabpanel"
                                                                    aria-labelledby="v-pills-settings-tab" tabindex="0">
                                                                    <div class="form-group row">
                                                                        <label class="col-md-2" for="google_reCaptcha[secret]">{{ __('static.settings.secret') }}
                                                                             <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                                                data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                                                data-bs-title="{{ __('static.settings.google_client') }}"></i>
                                                                        </label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="password" id="google_reCaptcha[secret]"
                                                                                name="google_reCaptcha[secret]"
                                                                                value="{{ encryptKey($settings['google_reCaptcha']['secret'] ?? old('secret')) }}"
                                                                                placeholder="{{ __('static.settings.enter_secret') }}">
                                                                            @error('google_reCaptcha[secret]')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="google_reCaptcha[site_key]">{{ __('static.settings.site_key') }}</label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="password"
                                                                                id="google_reCaptcha[site_key]" name="google_reCaptcha[site_key]"
                                                                                value="{{ encryptKey($settings['google_reCaptcha']['site_key'] ?? old('site_key')) }}"
                                                                                placeholder="{{ __('static.settings.enter_site_key') }}">
                                                                            @error('google_reCaptcha[site_key]')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="google_reCaptcha[status]">{{ __('static.settings.status') }}</label>
                                                                        <div class="col-md-10">
                                                                            <div class="editor-space">
                                                                                <label class="switch">
                                                                                    @if (isset($settings['google_reCaptcha']['status']))
                                                                                        <input class="form-control" type="hidden"
                                                                                            name="google_reCaptcha[status]" value="0">
                                                                                        <input class="form-check-input" type="checkbox"
                                                                                            name="google_reCaptcha[status]" value="1"
                                                                                            {{ $settings['google_reCaptcha']['status'] ? 'checked' : '' }}>
                                                                                    @else
                                                                                        <input class="form-control" type="hidden"
                                                                                            name="google_reCaptcha[status]" value="0">
                                                                                        <input class="form-check-input" type="checkbox"
                                                                                            name="google_reCaptcha[status]" value="1">
                                                                                    @endif
                                                                                    <span class="switch-state"></span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="tab-pane fade" id="firebase" role="tabpanel"
                                                                    aria-labelledby="v-pills-settings-tab" tabindex="0">
                                                                    <div class="form-group row">
                                                                        <label for="image" class="col-md-2">{{ __('static.settings.firebase_service_json') }}</label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="file" id="firebase[service_json]" accept="application/JSON" name="firebase[service_json]">
                                                                            <span class="text-gray mt-1">
                                                                                * Need help creating a Firebase service JSON file? Follow the steps in the  <a href="https://support.google.com/firebase/answer/7015592?hl=en#zippy=%2Cin-this-article" target="_blank" class="text-primary">Firebase Documentation</a>.
                                                                            </span>
                                                                            @error('firebase[service_json]')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-md-2" for="google_map_api_key">{{ __('static.settings.google_map_api_key') }}</label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="password" id="firebase[google_map_api_key]" name="firebase[google_map_api_key]"
                                                                                value="{{ encryptKey($settings['firebase']['google_map_api_key'] ?? old('google_map_api_key')) }}"
                                                                                placeholder="{{ __('static.settings.enter_google_map_api_key') }}">
                                                                            <span class="text-gray mt-1">
                                                                                * Need help generating a Google Maps API key? Follow the steps in the  <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank" class="text-primary">Google Maps API Documentation</a>.
                                                                            </span>
                                                                            @error('firebase[google_map_api_key]')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                                                                                    </div>
                                                                <div class="tab-pane fade " id="app_settings" role="tabpanel"
                                                                    aria-labelledby="v-pills-app-settings" tabindex="0">
                                                                    <div class="form-group row">
                                                                        <label class="col-md-2" for="logo_image_id">{{ __('static.settings.logo') }}
                                                                            <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                                                data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                                                data-bs-title="*Upload image size 50x50px recommended"></i>
                                                                        </label>
                                                                        <div class="col-md-10">
                                                                            <div class="form-group">
                                                                                <x-image :name="'app_setting[logo_image_id]'" :data="isset(
                                                                                    $settings['app_setting'][
                                                                                        'logo_image'],)
                                                                                    ? $settings['app_setting'][
                                                                                        'logo_image'
                                                                                    ]
                                                                                    : old('app_setting.logo_image_id')" :text="false"
                                                                                    :multiple="false" />
                                                                                @error('logo_image_id')
                                                                                    <span class="invalid-feedback d-block" role="alert">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="app_name">{{ __('static.settings.app_name') }}</label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="text" id="app_setting[app_name]"
                                                                                name="app_setting[app_name]"
                                                                                value="{{ $settings['app_setting']['app_name'] ?? old('app_name') }}"
                                                                                placeholder="{{ __('static.settings.enter_app_name') }}">
                                                                            @error('app_settings[app_name]')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="privacy_policy_link">{{ __('static.settings.privacy_policy_link') }}</label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="text"
                                                                                id="app_setting[privacy_policy_link]"
                                                                                name="app_setting[privacy_policy_link]"
                                                                                value="{{ $settings['app_setting']['privacy_policy_link'] ?? old('privacy_policy_link') }}"
                                                                                placeholder="{{ __('static.settings.enter_privacy_policy_link') }}">
                                                                            @error('app_settings[privacy_policy_link]')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="term_condition_link">{{ __('static.settings.term_condition_link') }}</label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="text"
                                                                                id="app_setting[term_condition_link]"
                                                                                name="app_setting[term_condition_link]"
                                                                                value="{{ $settings['app_setting']['term_condition_link'] ?? old('term_condition_link') }}"
                                                                                placeholder="{{ __('static.settings.enter_term_condition_link') }}">
                                                                            @error('app_settings[term_condition_link]')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="apple_store_link">{{ __('static.settings.apple_store_link') }}</label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="text"
                                                                                id="app_setting[apple_store_link]"
                                                                                name="app_setting[apple_store_link]"
                                                                                value="{{ $settings['app_setting']['apple_store_link'] ?? old('apple_store_link') }}"
                                                                                placeholder="{{ __('static.settings.enter_apple_store_link') }}">
                                                                            @error('app_settings[apple_store_link]')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="play_store_link">{{ __('static.settings.play_store_link') }}</label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="text"
                                                                                id="app_setting[play_store_link]" name="app_setting[play_store_link]"
                                                                                value="{{ $settings['app_setting']['play_store_link'] ?? old('play_store_link') }}"
                                                                                placeholder="{{ __('static.settings.enter_play_store_link') }}">
                                                                            @error('app_settings[play_store_link]')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="agora" role="tabpanel"
                                                                    aria-labelledby="v-pills-agora" tabindex="0">
                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="agora[app_id]">{{ __('static.settings.app_id') }}<span>
                                                                                *</span></label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="password" id="agora[app_id]"
                                                                                name="agora[app_id]"
                                                                                value="{{ encryptKey($settings['agora']['app_id']) ?? old('agora[app_id]') }}"
                                                                                placeholder="{{ __('static.settings.enter_agora_app_id') }}">
                                                                            @error('agora[app_id]')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="agora[certificate]">{{ __('static.settings.agora_certificate') }}<span>
                                                                                *</span></label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="password" id="agora[certificate]"
                                                                                name="agora[certificate]"
                                                                                value="{{ encryptKey($settings['agora']['certificate']) ?? old('agora[certificate]') }}"
                                                                                placeholder="{{ __('static.settings.enter_agora_certificate') }}">
                                                                            @error('agora[certificate]')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="v-pills-social" tabindex="0">
                                                                    <div class="form-group row">
                                                                        
                                                                        <label class="col-md-2" for="social_login[google][client_id]">
                                                                            {{ __('static.settings.google_client_id') }}
                                                                            <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                                        data-bs-title="{{ __('static.settings.google_client') }}"></i>
                                                                        </label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="password"
                                                                                id="social_login[google][client_id]"
                                                                                name="social_login[google][client_id]"
                                                                                value="{{ encryptKey($settings['social_login']['google']['client_id'] ?? '') ?? old('social_login.google.client_id') }}"
                                                                                placeholder="{{ __('static.settings.enter_google_client_id') }}">
                                                                            @error('social_login.google.client_id')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="social_login[google][client_secret]">{{ __('static.settings.google_client_secret') }}
                                                                        <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                                        data-bs-title="{{ __('static.settings.google_secret') }}"></i></label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="password"
                                                                                id="social_login[google][client_secret]"
                                                                                name="social_login[google][client_secret]"
                                                                                value="{{ encryptKey($settings['social_login']['google']['client_secret'] ?? '') ?? old('social_login.google.client_secret') }}"
                                                                                placeholder="{{ __('static.settings.enter_google_client_secret') }}">
                                                                            @error('social_login.google.client_secret')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="social_login[facebook][client_id]">{{ __('static.settings.facebook_client_id') }}
                                                                        <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                                        data-bs-title="{{ __('static.settings.facebook_client') }}"></i></label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="password"
                                                                                id="social_login[facebook][client_id]"
                                                                                name="social_login[facebook][client_id]"
                                                                                value="{{ encryptKey($settings['social_login']['facebook']['client_id'] ?? '') ?? old('social_login.facebook.client_id') }}"
                                                                                placeholder="{{ __('static.settings.enter_facebook_client_id') }}">
                                                                            @error('social_login.facebook.client_id')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="social_login[facebook][client_secret]">{{ __('static.settings.facebook_client_secret') }}
                                                                        <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                                        data-bs-title="{{ __('static.settings.facebook_secret') }}"></i></label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="password"
                                                                                id="social_login[facebook][client_secret]"
                                                                                name="social_login[facebook][client_secret]"
                                                                                value="{{ encryptKey($settings['social_login']['facebook']['client_secret'] ?? '') ?? old('social_login.facebook.client_secret') }}"
                                                                                placeholder="{{ __('static.settings.enter_facebook_client_secret') }}">
                                                                            @error('social_login.facebook.client_secret')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="social_login[apple][client_id]">{{ __('static.settings.apple_client_id') }}
                                                                        <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                                        data-bs-title="{{ __('static.settings.apple_client') }}"></i></label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="password"
                                                                                id="social_login[apple][client_id]"
                                                                                name="social_login[apple][client_id]"
                                                                                value="{{ encryptKey($settings['social_login']['apple']['client_id'] ?? '') ?? old('social_login.apple.client_id') }}"
                                                                                placeholder="{{ __('static.settings.enter_apple_client_id') }}">
                                                                            @error('social_login.apple.client_id')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                    <div class="form-group row">
                                                                        <label class="col-md-2"
                                                                            for="social_login[apple][client_secret]">{{ __('static.settings.apple_client_secret') }}
                                                                        <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                                        data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                                        data-bs-title="{{ __('static.settings.apple_secret') }}"></i></label>
                                                                        <div class="col-md-10">
                                                                            <input class="form-control" type="password"
                                                                                id="social_login[apple][client_secret]"
                                                                                name="social_login[apple][client_secret]"
                                                                                value="{{ encryptKey($settings['social_login']['apple']['client_secret'] ?? '') ?? old('social_login.apple.client_secret') }}"
                                                                                placeholder="{{ __('static.settings.enter_apple_client_secret') }}">
                                                                            @error('social_login.apple.client_secret')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="tab-pane fade" id="maintenance_mode" role="tabpanel"
                                                                    aria-labelledby="v-pills-maintenance-mode" tabindex="0">
                                                                    <div class="form-group row">
                                                                        <label class="col-xxl-3 col-md-4"
                                                                            for="maintenance[maintenance_mode]">{{ __('static.settings.maintenance_mode') }}
                                                                            <i class="ri-error-warning-line" data-bs-toggle="tooltip"
                                                                                data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                                                                data-bs-title="{{ __('static.settings.enter_maintenance_mode') }}"></i>
                                                                        </label>
                                                                        <div class="col-xxl-9 col-md-8">
                                                                            <div class="editor-space">
                                                                                <label class="switch">
                                                                                    @if (isset($settings['maintenance']['maintenance_mode']))
                                                                                        <input class="form-control" type="hidden"
                                                                                            name="maintenance[maintenance_mode]" value="0">
                                                                                        <input class="form-check-input" type="checkbox"
                                                                                            name="maintenance[maintenance_mode]" value="1"
                                                                                            {{ $settings['maintenance']['maintenance_mode'] ? 'checked' : '' }}>
                                                                                    @else
                                                                                        <input class="form-control" type="hidden"
                                                                                            name="maintenance[maintenance_mode]" value="0">
                                                                                        <input class="form-check-input" type="checkbox"
                                                                                            name="maintenance[maintenance_mode]" value="1">
                                                                                    @endif
                                                                                    <span class="switch-state"></span>
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form-group row ">
                                                                        <label class="col-md-2"
                                                                            for="content">{{ __('static.notify_templates.content') }}</label>
                                                                        <div class="col-md-10">
                                                                            <textarea class="form-control image-embed-content" placeholder="{{ __('static.notify_templates.enter_content') }}"
                                                                                rows="4" id="maintenance[content]" name="maintenance[content]" cols="50">{{ $settings['maintenance']['content'] ?? old('content') }}</textarea>
                                                                            @error('maintenance.content')
                                                                                <span class="invalid-feedback d-block" role="alert">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>

                                                                </div>
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
                                    $(document).ready(function() {
                                        "use strict";

                                        var selectedMode = $('#mode').val();
                                        var modeFunction = (selectedMode === 'dark') ? darkMode : lightMode;
                                        modeFunction();

                                        $('#send-test-mail').click(function(e) {
                                            e.preventDefault();

                                            var form = $('#settingsForm');
                                            var url = form.attr('action');
                                            var formData = form.serializeArray();
                                            var additionalData = {
                                                test_mail: 'true',
                                            };

                                            $.each(additionalData, function(key, value) {
                                                formData.push({
                                                    name: key,
                                                    value: value
                                                });
                                            });

                                            $.ajax({
                                                type: "POST",
                                                url: url,
                                                data: formData,
                                                success: function(response) {
                                                    let obj = JSON.parse(response);
                                                    console.log(obj);
                                                    if (obj.success == true) {
                                                        toastr.success(obj.message);
                                                    } else {
                                                        toastr.error(obj.message);
                                                    }
                                                },
                                                error: function(response) {
                                                    obj = JSON.parse(response);
                                                    console.log(obj);
                                                    toastr.error(obj.message, 'Error');
                                                }
                                            });
                                        });

                                        function toggleDropdowns() {
                                            const isPostSelected = $('input:radio[name="readings[status]"]:checked').val() === '1';
                                            $('#homepage').prop('disabled', isPostSelected);
                                        }

                                        toggleDropdowns();

                                        $('input:radio[name="readings[status]"]').change(function() {
                                            toggleDropdowns();
                                        });

                                        $("#settingsForm").validate({
                                            ignore: [],
                                            rules: {
                                                "email[mail_mailer]": "required",
                                                "email[mail_host]": "required",
                                                "email[mail_port]": "required",
                                                "email[mail_encryption]": "required",
                                                "email[mail_username]": "required",
                                                "email[mail_password]": "required",
                                                "email[mail_from_name]": "required",
                                                "email[mail_from_address]": "required",
                                                "general[site_name]": "required",
                                                "general[default_language_id]": "required",
                                                "general[default_currency_id]": "required",
                                                "general[platform_fees]": "required",
                                                "general[mode]": "required",
                                                "general[copyright]": "required",
                                                "firebase[google_map_api_key]": "required",
                                                "general[default_timezone]": "required",
                                                "app_setting[app_name]": "required",
                                            },
                                            invalidHandler: function(event, validator) {
                                                let invalidTabs = [];
                                                $.each(validator.errorList, function(index, error) {
                                                    const tabId = $(error.element).closest('.tab-pane').attr('id');
                                                    if (tabId) {
                                                        const tabLink = $(`.nav-link[data-bs-target="#${tabId}"]`);
                                                        tabLink.find('.errorIcon').show();
                                                        if (!invalidTabs.includes(tabId)) {
                                                            invalidTabs.push(tabId);
                                                        }
                                                    }
                                                });
                                                if (invalidTabs.length) {

                                                    $(".nav-link.active").removeClass("active");
                                                    $(".tab-pane.show").removeClass("show active");


                                                    const firstInvalidTabId = invalidTabs[0];
                                                    $(`.nav-link[data-bs-target="#${firstInvalidTabId}"]`).addClass("active");
                                                    $(`#${firstInvalidTabId}`).addClass("show active");
                                                }
                                            },
                                            success: function(label, element) {

                                            }
                                        });

                                    });
                                </script>
                            @endpush
 