@use('Modules\Taxido\Models\Service')
@php
    $services = Service::where('status', true)?->get(['id', 'name']);
@endphp

<div class="row g-xl-4 g-3">
    <div class="col-xl-10 col-xxl-8 mx-auto">
        <div class="left-part">
            <div class="contentbox">
                <div class="inside">
                    <div class="contentbox-title">
                        <h3>{{ isset($serviceCategory) ? __('taxido::static.service_categories.edit') : __('taxido::static.service_categories.add') }}
                            ({{ request('locale', app()->getLocale()) }})</h3>
                    </div>
                    @isset($serviceCategory)
                        <div class="form-group row">
                            <label class="col-md-2" for="name">{{ __('taxido::static.language.languages') }}</label>
                            <div class="col-md-10">
                                <ul class="language-list">
                                    @forelse (getLanguages() as $lang)
                                        <li>
                                            <a href="{{ route('admin.service-category.edit', ['service_category' => $serviceCategory->id, 'locale' => $lang->locale]) }}"
                                                class="language-switcher {{ request('locale') === $lang->locale ? 'active' : '' }}"
                                                target="_blank"><img
                                                    src="{{ @$lang?->flag ?? asset('admin/images/No-image-found.jpg') }}"
                                                    alt=""> {{ @$lang?->name }} ({{ @$lang?->locale }})<i
                                                    class="ri-arrow-right-up-line"></i></a>
                                        </li>
                                    @empty
                                        <li>
                                            <a href="{{ route('admin.service-category.edit', ['service_category' => $serviceCategory->id, 'locale' => Session::get('locale', 'en')]) }}"
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
                    <form id="serviceCategoryForm" method="POST"
                        action="{{ isset($serviceCategory) ? route('admin.service-category.update', $serviceCategory->id) : route('admin.service-category.store') }}">
                        @csrf
                        @method(isset($serviceCategory) ? 'PUT' : 'POST')
                        <div class="form-group row">
                            <label class="col-md-2"
                                for="service_category_image_id">{{ __('taxido::static.service_categories.service_image') }}</label>
                            <div class="col-md-10">
                                <div class="form-group">
                                    <x-image :name="'service_category_image_id'" :data="isset($serviceCategory->service_category_image)
                                        ? $serviceCategory?->service_category_image
                                        : old('service_category_image_id')" :text="''"
                                        :multiple="false"></x-image>
                                    @error('service_category_image')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2"
                                for="name">{{ __('taxido::static.service_categories.name') }}<span>
                                    *</span></label>
                            <div class="col-md-10">
                                <div class="position-relative">
                                    <input class="form-control" type="text" id="name" name="name"
                                        value="{{ isset($serviceCategory->name) ? $serviceCategory->getTranslation('name', request('locale', app()->getLocale())) : old('name') }}"
                                        placeholder="{{ __('taxido::static.service_categories.enter_name') }} ({{ request('locale', app()->getLocale()) }})"
                                        required><i class="ri-file-copy-line copy-icon" data-target="#name"></i>
                                </div>
                                @error('name')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2"
                                for="description">{{ __('taxido::static.service_categories.description') }} <span>
                                    *</span></label>
                            <div class="col-md-10">
                                <div class="position-relative">
                                    <textarea class="form-control"
                                        placeholder="{{ __('taxido::static.service_categories.enter_description') }} ({{ request('locale', app()->getLocale()) }})"
                                        rows="4" id="description" name="description" cols="50">{{ isset($serviceCategory->description) ? $serviceCategory->getTranslation('description', request('locale', app()->getLocale())) : old('description') }}</textarea><i class="ri-file-copy-line copy-icon"
                                        data-target="#description"></i>
                                </div>
                            </div>
                            @error('description')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2"
                                for="zone">{{ __('taxido::static.service_categories.services') }} <span>
                                    *</span></label>
                            <div class="col-md-10 select-label-error">
                                <select class="form-control select-2 service" name="services[]"
                                    data-placeholder="{{ __('taxido::static.service_categories.select_services') }}"
                                    multiple>
                                    @foreach ($services as $index => $service)
                                        <option value="{{ $service->id }}"
                                            @if (isset($serviceCategory->services)) @if (in_array($service->id, $serviceCategory->services->pluck('id')->toArray()))
                                        selected @endif
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
                            <label class="col-md-2" for="status">{{ __('taxido::static.status') }}</label>
                            <div class="col-md-10">
                                <div class="editor-space">
                                    <label class="switch">
                                        <input class="form-control" type="hidden" name="status" value="0">
                                        <input class="form-check-input" type="checkbox" name="status" id=""
                                            value="1" @checked(@$serviceCategory?->status ?? true)>
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
                                        {{ __('taxido::static.service_categories.save') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        (function($) {
            "use strict";

            $('#serviceCategoryForm').validate({
                ignore: [],
                rules: {
                    "name": "required",
                    "description": "required",
                    "services[]": "required"
                }
            });
        })(jQuery);
    </script>
@endpush
