@use('Modules\Taxido\Models\Zone')
@php
    $zones = Zone::where('status', true)?->get(['id', 'name']);
@endphp
<div class="row g-xl-4 g-3">
    <div class="col-xl-10 col-xxl-8 mx-auto">
        <div class="left-part">
            <div class="contentbox">
                <div class="inside">
                    <div class="contentbox-title">
                        <h3>{{ isset($banner) ? __('taxido::static.banners.edit') : __('taxido::static.banners.add') }}
                            ({{ request('locale', app()->getLocale()) }})
                        </h3>
                    </div>
                    @isset($banner)
                        <div class="form-group row">
                            <label class="col-md-2" for="name">{{ __('taxido::static.language.languages') }}</label>
                            <div class="col-md-10">
                                <ul class="language-list">
                                    @forelse (getLanguages() as $lang)
                                        <li>
                                            <a href="{{ route('admin.banner.edit', ['banner' => $banner->id, 'locale' => $lang->locale]) }}"
                                                class="language-switcher {{ request('locale') === $lang->locale ? 'active' : '' }}"
                                                target="_blank"><img
                                                src="{{ @$lang?->flag ?? asset('admin/images/No-image-found.jpg') }}"
                                                alt=""> {{ @$lang?->name }} ({{ @$lang?->locale }})<i
                                                class="ri-arrow-right-up-line"></i></a>
                                        </li>
                                    @empty
                                        <li>
                                            <a href="{{ route('admin.banner.edit', ['banner' => $banner->id, 'locale' => Session::get('locale', 'en')]) }}"
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
                        <label class="col-md-2" for="banner_image_id">{{ __('taxido::static.banners.image') }}<span>
                                *</span></label>
                        <div class="col-md-10">
                            <div class="form-group">
                                <x-image :name="'banner_image_id'" :data="isset($banner->banner_image)
                                    ? $banner?->banner_image
                                    : old('banner_image_id')" :text="' '"
                                    :multiple="false"></x-image>
                                @error('banner_image_id')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2"
                            for="title">{{ __('taxido::static.banners.title') }}<span>*</span></label>
                        <div class="col-md-10">
                            <div class="position-relative">
                                <input class="form-control" type="text" name="title" id="title"
                                    value="{{ isset($banner->title) ? $banner->getTranslation('title', request('locale', app()->getLocale())) : old('title') }}"
                                    placeholder="{{ __('taxido::static.banners.enter_title') }} ({{ request('locale', app()->getLocale()) }})">
                                <i class="ri-file-copy-line copy-icon" data-target="#title"></i>
                            </div>
                            @error('title')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2" for="order">{{ __('taxido::static.banners.order') }}<span>
                                *</span></label>
                        <div class="col-md-10">
                            <input class="form-control" type="number" name="order"
                                value="{{ isset($banner->order) ? $banner->order : old('order') }}"
                                placeholder="{{ __('taxido::static.banners.enter_order') }}">
                            @error('order')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                   <div class="form-group row">
                        <label class="col-md-2" for="all_zones">{{ __('taxido::static.banners.all_zones') }}</label>
                        <div class="col-md-10">
                            <label class="switch">
                                <input class="form-control" type="hidden" name="is_all_zones" value="0">
                                <input class="form-check-input" type="checkbox" id="is_all_zones" name="is_all_zones" value="1"
                                    @checked(old('is_all_zones', $banner->is_all_zones ?? true))>
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
                        <label class="col-md-2" for="zones">{{ __('taxido::static.banners.zones') }} <span>*</span></label>
                        <div class="col-md-10 select-label-error">
                            <span class="text-gray mt-1">
                                {{ __('taxido::static.banners.no_zones_message') }}
                                <a href="{{ @route('admin.zone.index') }}" class="text-primary">
                                    <b>{{ __('taxido::static.here') }}</b>
                                </a>
                            </span>
                            <select class="form-control select-2 zone" name="zones[]" data-placeholder="{{ __('taxido::static.banners.select_zones') }}" multiple>
                                @foreach ($zones as $index => $zone)
                                    <option value="{{ $zone->id }}"
                                        @if (isset($banner->zones)) 
                                            @if (in_array($zone->id, $banner->zones->pluck('id')->toArray())) selected @endif
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
                        <label class="col-md-2" for="banner">{{ __('taxido::static.banners.status') }}</label>
                        <div class="col-md-10">
                            <div class="editor-space">
                                <label class="switch">
                                    <input class="form-control" type="hidden" name="status" value="0">
                                    <input class="form-check-input" type="checkbox" name="status" id=""
                                        value="1" @checked(@$banner?->status ?? true)>
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
                                    {{ __('taxido::static.banners.save') }}
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
        
        $('#bannerForm').validate({
            rules: {
                "title": "required",
                "order": "required",
                "banner_image_id": "required",
            }
        });

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
