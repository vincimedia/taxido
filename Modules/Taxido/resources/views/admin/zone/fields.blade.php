@use('App\Models\Currency')
@php
    $settings = getTaxidoSettings();
    $currencies = Currency::where('status', true)?->get(['id', 'code']);
@endphp
<div class="col-12">
    <div class="row g-xl-4 g-3">
        <div class="col-xl-12">
            <div class="left-part">
                <div class="contentbox">
                    <div class="inside">
                        <div class="contentbox-title">
                            <h3>
                                {{ isset($zone) ? __('taxido::static.zones.edit') : __('taxido::static.zones.add') }}
                                ({{ request('locale', app()->getLocale()) }})
                            </h3>
                        </div>
                        @isset($zone)
                            <div class="form-group row">
                                <label class="col-md-2" for="name">{{ __('taxido::static.language.languages') }}</label>
                                <div class="col-md-10">
                                    <ul class="language-list">
                                        @forelse (getLanguages() as $lang)
                                            <li>
                                                <a href="{{ route('admin.zone.edit', ['zone' => $zone->id, 'locale' => $lang->locale]) }}"
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
                                                <a href="{{ route('admin.zone.edit', ['zone' => $zone->id, 'locale' => Session::get('locale', 'en')]) }}"
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
                            <label class="col-md-2" for="name">{{ __('taxido::static.zones.name') }}<span>*</span></label>
                            <div class="col-md-10">
                                    <input class="form-control" type="text" id="name" name="name"
                                        placeholder="{{ __('taxido::static.zones.enter_name') }} ({{ request('locale', app()->getLocale()) }})"
                                        value="{{ isset($zone->name) ? $zone->getTranslation('name', request('locale', app()->getLocale())) : old('name') }}">
                                    <i class="ri-file-copy-line copy-icon" data-target="#name"></i>
                                @error('name')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2" for="type">{{ __('taxido::static.zones.distance_type') }}<span>
                                    *</span></label>
                            <div class="col-md-10 select-label-error">
                                <select class="select-2 form-control" id="type" name="distance_type"
                                    data-placeholder="{{ __('taxido::static.zones.select_distance_type') }}">
                                    <option class="select-placeholder" value=""></option>
                                    @foreach (['mile' => 'Mile', 'km' => 'Km'] as $key => $option)
                                        <option class="option" value="{{ $key }}"
                                            @if (old('distance_type', $zone->distance_type ?? '') == $key) selected @endif>
                                            {{ $option }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('distance_type')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2" for="type">{{ __('taxido::static.zones.currency') }}<span>
                                    *</span></label>
                            <div class="col-md-10 select-label-error">
                                <span class="text-gray mt-1">{{ __('taxido::static.zones.add_currency_message') }}
                                    <a href="{{ @route('admin.currency.index') }}" class="text-primary">
                                        <b>{{ __('taxido::static.here') }}</b>
                                    </a>
                                </span>
                                <select class="select-2 form-control" id="currency_id" name="currency_id"
                                    data-placeholder="{{ __('taxido::static.zones.select_currency') }}">
                                    <option class="select-placeholder" value=""></option>
                                    @foreach ($currencies as $key => $currency)
                                        <option value="{{ $currency->id }}"
                                            @if (old('currency_id', $zone->currency_id ?? '') == $currency->id) selected @endif>{{ $currency->code }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('currency_id')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2"
                                for="place_points">{{ __('taxido::static.zones.place_points') }}<span>
                                    *</span></label>
                            <div class="col-md-10">
                                <input class="form-control" type="text" id="place_points" name="place_points"
                                    placeholder="{{ __('taxido::static.zones.select_place_points') }}"
                                    value="{{ isset($zone->locations) ? json_encode($zone->locations, true) : old('place_points') }}"
                                    readonly>
                                @error('place_points')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2"
                                for="search-box">{{ __('taxido::static.zones.search_location') }}</label>
                            <div class="col-md-10">
                                <input id="search-box" class="form-control" type="text"
                                    placeholder="{{ __('taxido::static.zones.search_locations') }}">
                                <ul id="suggestions-list" class="map-location-list custom-scrollbar"></ul>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2" for="map">{{ __('taxido::static.zones.map') }}</label>
                            <div class="col-md-10">
                                <div class="map-warper dark-support rounded overflow-hidden">
                                    <div class="map-container" id="map-container"></div>
                                </div>
                                <div id="coords"></div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2" for="status">{{ __('taxido::static.status') }}</label>
                            <div class="col-md-10">
                                <div class="editor-space">
                                    <label class="switch">
                                        @if (isset($zone))
                                            <input class="form-control" type="hidden" name="status"
                                                value="0">
                                            <input class="form-check-input" type="checkbox" name="status"
                                                id="" value="1" {{ $zone->status ? 'checked' : '' }}>
                                        @else
                                            <input class="form-control" type="hidden" name="status"
                                                value="0">
                                            <input class="form-check-input" type="checkbox" name="status"
                                                id="" value="1" checked>
                                        @endif
                                        <span class="switch-state"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-12">
                                <div class="submit-btn">
                                    <button type="submit" name="save" class="btn btn-solid spinner-btn">
                                        {{ __('taxido::static.save') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($settings['location']['map_provider'] == 'google_map')
    @includeIf('taxido::admin.zone.google')
@elseIf($settings['location']['map_provider'] == 'osm')
    @includeIf('taxido::admin.zone.osm')
@endif

@push('scripts')
    <script>
        (function($) {
            "use strict";
            $('#zoneForm').validate({
                rules: {
                    "name": "required",
                    "currency_id": "required",
                    "amount": "required",
                    "distance_type": "required",
                    "place_points": "required",
                }
            });
        })(jQuery);
    </script>
@endpush
