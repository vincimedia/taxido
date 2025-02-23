@use('Modules\Taxido\Models\VehicleType')
@php
    $vehicleTypes = VehicleType::where('status', true)?->get(['id', 'name']);
@endphp
<div class="row g-xl-4 g-3">
    <div class="col-xl-10 col-xxl-8 mx-auto">
        <div class="left-part">
            <div class="contentbox">
                <div class="inside">
                    <div class="contentbox-title">
                        <h3>{{ isset($driverRule) ? __('taxido::static.driver_rules.edit') : __('taxido::static.driver_rules.add') }}
                            ({{ app()->getLocale() }})</h3>
                    </div>
                    @isset($driverRule)
                        <div class="form-group row">
                            <label class="col-md-2" for="name">{{ __('taxido::static.language.languages') }}</label>
                            <div class="col-md-10">
                                <ul class="language-list">
                                    @forelse (getLanguages() as $lang)
                                        <li>
                                            <a href="{{ route('admin.driver-rule.edit', ['driver_rule' => $driverRule->id, 'locale' => $lang->locale]) }}"
                                                class="language-switcher {{ request('locale') === $lang->locale ? 'active' : '' }}"
                                                target="_blank"><img
                                                    src="{{ @$lang?->flag ?? asset('admin/images/No-image-found.jpg') }}"
                                                    alt=""> {{ @$lang?->name }} ({{ @$lang?->locale }})<i
                                                    class="ri-arrow-right-up-line"></i></a>
                                        </li>
                                    @empty
                                        <li>
                                            <a href="{{ route('admin.driver-rule.edit', ['driver_rule' => $driverRule->id, 'locale' => Session::get('locale', 'en')]) }}"
                                                class="language-switcher active" target="blank"><img
                                                    src="{{ asset('admin/images/flags/LR.png') }}" alt="">English<i
                                                    class="ri-arrow-right-up-line"></i></a>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    @endisset
                    <input type="hidden" name="locale" value="{{ request('locale') }}">
                    <div class="form-group row">
                        <label class="col-md-2"
                            for="rule_image_id">{{ __('taxido::static.driver_rules.image') }}</label>
                        <div class="col-md-10">
                            <div class="form-group">
                                <x-image :name="'rule_image_id'" :data="isset($driverRule->rule_image)
                                    ? $driverRule?->rule_image
                                    : old('rule_image_id')" :text="''"
                                    :multiple="false"></x-image>
                                @error('rule_image_id')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2" for="title">{{ __('taxido::static.driver_rules.title') }}<span>
                                *</span></label>
                        <div class="col-md-10">
                            <div class="position-relative">
                                <input class="form-control" type="text" name="title" id="title"
                                    value="{{ isset($driverRule->title) ? $driverRule->getTranslation('title', request('locale', app()->getLocale())) : old('title') }}"
                                    placeholder="{{ __('taxido::static.driver_rules.enter_title') }} ({{ request('locale', app()->getLocale()) }})"><i
                                    class="ri-file-copy-line copy-icon" data-target="#title"></i>
                            </div>
                            @error('title')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row" id="vehicle-type-selection">
                        <label class="col-md-2"
                            for="vehicle_type">{{ __('taxido::static.driver_rules.select_vehicle_type') }}</label>
                        <div class="col-md-10 select-label-error">
                            <span class="text-gray mt-1">
                                {{ __('taxido::static.driver_rules.no_vehicleType_message') }}
                                <a href="{{ @route('admin.vehicle-type.index') }}" class="text-primary">
                                    <b>{{ __('taxido::static.here') }}</b>
                                </a>
                            </span>
                            <select class="form-control select-2" name="vehicle_types[]"
                                data-placeholder="{{ __('taxido::static.driver_rules.select_vehicle_type') }}"
                                multiple>
                                @foreach ($vehicleTypes as $index => $vehicleType)
                                    <option value="{{ $vehicleType->id }}"
                                        @if (@$driverRule?->vehicle_types) @if (in_array($vehicleType->id, $driverRule->vehicle_types->pluck('id')->toArray()))
                                                        selected @endif
                                    @elseif (old('vehicle_types.' . $index) == $vehicleType->id) selected @endif>
                                        {{ $vehicleType->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('vehicle_types')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2" for="role">{{ __('taxido::static.driver_rules.status') }}</label>
                        <div class="col-md-10">
                            <div class="editor-space">
                                <label class="switch">
                                    <input class="form-control" type="hidden" name="status" value="0">
                                    <input class="form-check-input" type="checkbox" name="status" id=""
                                        value="1" @checked(@$driverRule?->status ?? true)>
                                    <span class="switch-state"></span>
                                </label>
                            </div>
                            @error('status')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-12">
                            <div class="submit-btn">
                                <button type="submit" name="save" class="btn btn-solid spinner-btn">
                                    {{ __('taxido::static.driver_rules.save') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        (function($) {
            "use strict";
            $('#driverRuleForm').validate({
                rules: {
                    "title": "required",
                }
            });
        })(jQuery);
    </script>
@endpush
