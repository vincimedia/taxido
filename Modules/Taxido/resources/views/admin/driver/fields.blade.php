@use('Modules\Taxido\Models\Zone')
@use('Modules\Taxido\Models\VehicleType')
@php
    $vehicleTypes = VehicleType::where('status', true)?->get(['id', 'name']);
    $zones = Zone::where('status', true)?->get(['id', 'name']);
@endphp

<div class="row">
    <div class="col-12">
        <div class="row g-xl-4 g-3">
            <div class="col-xl-10 col-xxl-8 mx-auto">
                <div class="contentbox">
                    <div class="inside">
                        <div class="contentbox-title">
                            <h3>{{ isset($driver) ? __('taxido::static.drivers.edit') : __('taxido::static.drivers.add') }}
                            </h3>
                        </div>
                        <ul class="nav nav-tabs horizontal-tab custom-scroll" id="account" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="profile-tab" data-bs-toggle="tab" href="#profile"
                                    type="button" role="tab" aria-controls="profile" aria-selected="true">
                                    <i class="ri-shield-user-line"></i>
                                    {{ __('taxido::static.drivers.general') }}
                                    <i class="ri-error-warning-line danger errorIcon"></i>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="address-tab" data-bs-toggle="tab" href="#address" type="button"
                                    role="tab" aria-controls="address" aria-selected="true">
                                    <i class="ri-rotate-lock-line"></i>
                                    {{ __('taxido::static.drivers.address') }}
                                    <i class="ri-error-warning-line danger errorIcon"></i>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="vehicle-tab" data-bs-toggle="tab" href="#vehicle" type="button"
                                    role="tab" aria-controls="vehicle" aria-selected="true">
                                    <i class="ri-shield-user-line"></i>
                                    {{ __('taxido::static.drivers.vehicle') }}
                                    <i class="ri-error-warning-line danger errorIcon"></i>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="payout-tab" data-bs-toggle="tab" href="#payout" type="button"
                                    role="tab" aria-controls="payout" aria-selected="true">
                                    <i class="ri-rotate-lock-line"></i>
                                    {{ __('taxido::static.drivers.payout_details') }}
                                    <i class="ri-error-warning-line danger errorIcon"></i>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="additionalInfo-tab" data-bs-toggle="tab" href="#additionalInfo"
                                    type="button" role="tab" aria-controls="additionalInfo" aria-selected="true">
                                    <i class="ri-rotate-lock-line"></i>
                                    {{ __('taxido::static.drivers.additional_info') }}
                                    <i class="ri-error-warning-line danger errorIcon"></i>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" id="accountContent">
                            <div class="tab-pane fade  {{ session('active_tab') != null ? '' : 'show active' }}"
                                id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="form-group row">
                                    <label class="col-md-2"
                                        for="profile_image_id">{{ __('taxido::static.drivers.profile_image') }}<span>
                                            *</span></label>
                                    <div class="col-md-10">
                                        <div class="form-group">
                                            <x-image :name="'profile_image_id'" :data="isset($driver->profile_image)
                                                ? $driver?->profile_image
                                                : old('profile_image_id')" :text="' '"
                                                :multiple="false"></x-image>
                                            @error('profile_image_id')
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2" for="username">{{ __('taxido::static.drivers.username') }}
                                        <span>
                                            *</span> </label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" id="username" name="username"
                                            placeholder="{{ __('taxido::static.drivers.enter_username') }}"
                                            value="{{ isset($driver->username) ? $driver->username : old('username') }}">
                                        @error('username')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2" for="name">{{ __('taxido::static.drivers.full_name') }}
                                        <span>
                                            *</span> </label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" id="name" name="name"
                                            placeholder="{{ __('taxido::static.drivers.enter_full_name') }}"
                                            value="{{ isset($driver->name) ? $driver->name : old('name') }}">
                                        @error('name')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2" for="email">{{ __('taxido::static.drivers.email') }}
                                        <span>
                                            *</span> </label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="email" name="email"
                                            placeholder="{{ __('taxido::static.drivers.enter_email') }}"
                                            value="{{ isset($driver->email) ? $driver->email : old('email') }}">
                                        @error('email')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2"
                                        for="phone">{{ __('taxido::static.drivers.phone') }}<span>*</span></label>
                                    <div class="col-md-10">
                                        <div class="input-group mb-3 phone-detail">
                                            <div class="col-sm-1">
                                                <select class="select-2 form-control" id="select-country-code"
                                                    name="country_code">
                                                    @foreach (getCountryCodes() as $option)
                                                        <option class="option" value="{{ $option->calling_code }}"
                                                            data-image="{{ asset('images/flags/' . $option->flag) }}"
                                                            @selected($option->calling_code == old('country_code', $driver->country_code ?? 1))>
                                                            {{ $option->calling_code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-sm-11">
                                                <input class="form-control" type="number" name="phone"
                                                    value="{{ isset($driver->phone) ? $driver->phone : old('phone') }}"
                                                    placeholder="{{ __('taxido::static.drivers.enter_phone') }}"
                                                    required>
                                            </div>
                                            @error('phone')
                                                <span class="invalid-feedback d-block" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @if (request()->routeIs('admin.driver.create'))
                                <div class="form-group row">
                                    <label class="col-md-2" for="password">{{ __('taxido::static.drivers.new_password') }}<span>
                                            *</span></label>
                                    <div class="col-md-10">
                                        <div class="position-relative">
                                            <input class="form-control" type="password" id="password" name="password"
                                                placeholder="{{ __('taxido::static.drivers.enter_password') }}">
                                            <i class="ri-eye-line toggle-password"></i>
                                        </div>
                                        @error('password')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2"
                                        for="confirm_password">{{ __('taxido::static.drivers.confirm_password') }}<span>
                                            *</span></label>
                                    <div class="col-md-10">
                                        <div class="position-relative">
                                            <input class="form-control" type="password" name="confirm_password"
                                                placeholder="{{ __('taxido::static.drivers.enter_confirm_password') }}"
                                                required>
                                            <i class="ri-eye-line toggle-password"></i>
                                        </div>
                                        @error('confirm_password')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                    <div class="form-group row">
                                        <label class="col-md-2 mb-0"
                                            for="notify">{{ __('taxido::static.drivers.notification') }}</label>
                                        <div class="col-md-10">
                                            <div class="form-check p-0 w-auto">
                                                <input type="checkbox" name="notify" id="notify" value="1"
                                                    class="form-check-input me-2">
                                                <label
                                                    for="notify">{{ __('taxido::static.drivers.sentence') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="footer">
                                    <button type="button"
                                        class="nextBtn btn btn-primary">{{ __('static.next') }}</button>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="address" role="tabpanel" aria-labelledby="address-tab">
                                <div class="form-group row">
                                    <label for="address[address]"
                                        class="col-md-2">{{ __('taxido::static.drivers.address') }}<span>
                                            *</span></label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control ui-widget autocomplete-google"
                                            id="address-input" name="address[address]"
                                            placeholder="{{ __('taxido::static.drivers.enter_address') }}"
                                            value="{{ @$driver->address ? $driver->address?->address : old('address.address') }}">
                                        @error('address.address')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address[street_address]"
                                        class="col-md-2">{{ __('taxido::static.drivers.street_address') }}</label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control ui-widget" id="street_address_1"
                                            name="address[street_address]"
                                            placeholder="{{ __('taxido::static.drivers.enter_street_address') }}"
                                            value="{{ @$driver->address ? $driver->address?->street_address : old('address.street_address') }}">
                                    </div>
                                    @error('address.street_address')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2"
                                        for="address[area_locality]">{{ __('taxido::static.drivers.area_locality') }}
                                    </label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" name="address[area_locality]"
                                            placeholder="{{ __('taxido::static.drivers.enter_area_locality') }}"
                                            value="{{ @$driver?->address ? $driver?->address?->area_locality : old('address.area_locality') }}"
                                            id="area_locality">
                                        @error('address.area_locality')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address[country_id]"
                                        class="col-md-2">{{ __('taxido::static.drivers.country') }}<span>
                                            *</span></label>
                                    <div class="col-md-10 select-label-error">
                                        <select class="select-2 form-control select-country" id="country_id"
                                            name="address[country_id]"
                                            data-placeholder="{{ __('taxido::static.drivers.select_country') }}">
                                            <option class="option" value="" selected></option>
                                            @foreach (getCountries() as $key => $option)
                                                <option value="{{ $key }}" @selected(old('address.country_id', @$driver?->address?->country_id) == $key)>
                                                    {{ $option }}</option>
                                            @endforeach
                                        </select>
                                        @error('address.country_id')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="address[state_id]"
                                        class="col-md-2">{{ __('taxido::static.drivers.state') }}<span>
                                            *</span></label>
                                    <div class="col-md-10 select-label-error">
                                        <select class="select-2 form-control" data-default-state-id="2"
                                            id="state_id" name="address[state_id]"
                                            data-placeholder="{{ __('taxido::static.drivers.select_state') }}">
                                            <option class="select-placeholder" value=""></option>
                                            @if (@$driver?->address)
                                                @foreach (getStatesByCountryId($driver?->address?->country_id) as $state)
                                                    <option class="option" value={{ $state?->id }}
                                                        @if ($state?->id == @$driver?->address?->state_id) selected @endif>
                                                        {{ $state?->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                        @error('address.state_id')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-2"
                                        for="address[city]">{{ __('taxido::static.drivers.city') }}
                                        <span> *</span></label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" name="address[city]"
                                            placeholder="{{ __('taxido::static.drivers.enter_city') }}"
                                            value="{{ @$driver?->address ? $driver?->address?->city : old('address.city') }}"
                                            id="city">
                                        @error('address.city')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-2"
                                        for="address[postal_code]">{{ __('taxido::static.drivers.postal_code') }}
                                        <span>
                                            *</span></label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" name="address[postal_code]"
                                            placeholder="{{ __('taxido::static.drivers.enter_postal_code') }}"
                                            value="{{ @$driver?->address ? $driver?->address?->postal_code : old('address.postal_code') }}"
                                            id="postal_code">
                                        @error('address.postal_code')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="footer">
                                    <button type="button"
                                        class="previousBtn bg-light-primary btn cancel">{{ __('static.previous') }}</button>
                                    <button type="button"
                                        class="nextBtn btn btn-primary">{{ __('static.next') }}</button>
                                </div>

                            </div>
                            <div class="tab-pane fade" id="vehicle" role="tabpanel" aria-labelledby="vehicle-tab">

                                <div class="form-group row">
                                    <label class="col-md-2"
                                        for="vehicle_info[vehicle_type_id]">{{ __('taxido::static.drivers.vehicle') }}
                                        <span>*</span></label>
                                    <div class="col-md-10 select-label-error">
                                        <span class="text-gray mt-1">
                                            {{ __('taxido::static.drivers.no_vehicle_type_message') }}
                                            <a href="{{ route('admin.vehicle-type.index') }}" class="text-primary">
                                                <b>{{ __('taxido::static.here') }}</b>
                                            </a>
                                        </span>
                                        <select class="form-control select-2 vehicle"
                                            name="vehicle_info[vehicle_type_id]"
                                            data-placeholder="{{ __('taxido::static.drivers.select_vehicle') }}">
                                            <option value=""></option>
                                            @foreach ($vehicleTypes as $vehicle)
                                                <option value="{{ $vehicle->id }}"
                                                    @if (old('vehicle_info.vehicle_type_id', @$driver?->vehicle_info?->vehicle_type_id) == $vehicle->id) selected @endif>
                                                    {{ $vehicle->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('vehicle_info.vehicle_type_id')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2"
                                        for="vehicle_info[model]">{{ __('taxido::static.drivers.model') }}
                                        <span> *</span></label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" name="vehicle_info[model]"
                                            placeholder="{{ __('taxido::static.drivers.enter_model') }}"
                                            value="{{ @$driver?->vehicle_info ? $driver?->vehicle_info?->model : old('vehicle_info.model') }}">
                                        @error('vehicle_info.model')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2"
                                        for="vehicle_info[plate_number]">{{ __('taxido::static.drivers.plate_number') }}
                                        <span>
                                            *</span></label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" name="vehicle_info[plate_number]"
                                            placeholder="{{ __('taxido::static.drivers.enter_plate_number') }}"
                                            value="{{ @$driver?->vehicle_info ? $driver?->vehicle_info?->plate_number : old('vehicle_info.plate_number') }}">
                                        @error('vehicle_info.plate_number')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2"
                                        for="vehicle[seat]">{{ __('taxido::static.drivers.seat') }}
                                        <span> *</span></label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="number" min="1"
                                            name="vehicle_info[seat]"
                                            placeholder="{{ __('taxido::static.drivers.enter_seat') }}"
                                            value="{{ @$driver?->vehicle_info ? $driver?->vehicle_info?->seat : old('vehicle_info.seat') }}">
                                        @error('vehicle_info.seat')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-2"
                                        for="vehicle_info[color]">{{ __('taxido::static.drivers.color') }}
                                        <span> *</span></label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" name="vehicle_info[color]"
                                            placeholder="{{ __('taxido::static.drivers.enter_color') }}"
                                            value="{{ @$driver?->vehicle_info ? $driver?->vehicle_info?->color : old('vehicle_info.color') }}">
                                        @error('vehicle_info.color')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="footer">
                                    <button type="button"
                                        class="previousBtn bg-light-primary btn cancel">{{ __('static.previous') }}</button>
                                    <button class="nextBtn btn btn-primary"
                                        type="button">{{ __('static.next') }}</button>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="payout" role="tabpanel" aria-labelledby="payout-tab">

                                <div class="form-group row">
                                    <label class="col-md-2"
                                        for="bank_account_no">{{ __('taxido::static.drivers.bank_account_no') }}
                                        <span>
                                            *</span></label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text"
                                            name="payment_account[bank_account_no]"
                                            placeholder="{{ __('taxido::static.drivers.enter_bank_account') }}"
                                            value="{{ @$driver?->payment_account ? $driver?->payment_account?->bank_account_no : old('payment_account.bank_account_no') }}">
                                        @error('payment_account.bank_account_no')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-2"
                                        for="bank_name">{{ __('taxido::static.drivers.bank_name') }}
                                        <span> *</span></label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" name="payment_account[bank_name]"
                                            placeholder="{{ __('taxido::static.drivers.enter_bank_name') }}"
                                            value="{{ @$driver?->payment_account ? $driver?->payment_account?->bank_name : old('payment_account.bank_name') }}">
                                        @error('payment_account.bank_name')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-2"
                                        for="bank_holder_name">{{ __('taxido::static.drivers.holder_name') }} <span>
                                            *</span></label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text"
                                            name="payment_account[bank_holder_name]"
                                            placeholder="{{ __('taxido::static.drivers.enter_holder_name') }}"
                                            value="{{ @$driver?->payment_account ? $driver?->payment_account?->bank_holder_name : old('payment_account.bank_holder_name') }}">
                                        @error('payment_account.bank_holder_name')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-2" for="swift">{{ __('taxido::static.drivers.swift') }}
                                        <span>
                                            *</span></label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" name="payment_account[swift]"
                                            placeholder="{{ __('taxido::static.drivers.enter_swift_code') }}"
                                            value="{{ @$driver?->payment_account ? $driver?->payment_account?->swift : old('payment_account.swift') }}">
                                        @error('payment_account.swift')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-2" for="ifsc">{{ __('taxido::static.drivers.ifsc') }}
                                        <span>
                                            *</span></label>
                                    <div class="col-md-10">
                                        <input class="form-control" type="text" name="payment_account[ifsc]"
                                            placeholder="{{ __('taxido::static.drivers.enter_ifsc_code') }}"
                                            value="{{ @$driver?->payment_account ? $driver?->payment_account?->ifsc : old('payment_account.ifsc') }}">
                                        @error('payment_account.ifsc')
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="footer">
                                    <button type="button"
                                        class="previousBtn bg-light-primary btn cancel">{{ __('static.previous') }}</button>
                                    <button class="nextBtn btn btn-primary"
                                        type="button">{{ __('static.next') }}</button>
                                </div>

                            </div>
                            <div class="tab-pane fade {{ session('active_tab') == 'additionalInfo-tab' ? 'show active' : '' }}"
                                id="additionalInfo" role="tabpanel" aria-labelledby="additionalInfo-tab">

                                <div class="form-group row">
                                    <label class="col-md-2"
                                        for="zones">{{ __('taxido::static.drivers.zones') }}<span>
                                            *</span></label>
                                    <div class="col-md-10 select-label-error">
                                        <span class="text-gray mt-1">
                                            {{ __('taxido::static.drivers.no_zones_message') }}
                                            <a href="{{ @route('admin.zone.index') }}" class="text-primary">
                                                <b>{{ __('taxido::static.here') }}</b>
                                            </a>
                                        </span>
                                        <select class="form-control select-2 zone" name="zones[]"
                                            data-placeholder="{{ __('taxido::static.drivers.select_zones') }}"
                                            multiple>
                                            @foreach ($zones as $index => $zone)
                                                <option value="{{ $zone->id }}"
                                                    @if (@$driver?->zones) @if (in_array($zone->id, $driver->zones->pluck('id')->toArray()))
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
                                    <label class="col-md-2" for="status">{{ __('taxido::static.status') }}
                                    </label>
                                    <div class="col-md-10">
                                        <div class="switch-field form-control">
                                            <input value="1" type="radio" name="status" id="status_active"
                                                @checked(boolval(@$driver?->status ?? true) == true) />
                                            <label for="status_active">{{ __('taxido::static.active') }}</label>
                                            <input value="0" type="radio" name="status" id="status_deactive"
                                                @checked(boolval(@$driver?->status ?? true) == false) />
                                            <label for="status_deactive">{{ __('taxido::static.deactive') }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-12">
                                        <div class="submit-btn">
                                            <button type="button"
                                                class="previousBtn bg-light-primary btn cancel">{{ __('static.previous') }}</button>
                                            <button type="submit" name="save" class="btn btn-solid spinner-btn submitBtn">
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
    </div>
</div>

@push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_API_KEY') }}&libraries=places">
    </script>
    <script>
        (function($) {
            "use strict";

            function initializeAutocomplete() {
                var input = document.getElementById('address-input');

                var autocomplete = new google.maps.places.Autocomplete(input);

                autocomplete.addListener('place_changed', function() {
                    var place = autocomplete.getPlace();
                    var country = '';
                    var state = '';

                    // Clear previous values
                    $('#street_address_1, #area_locality, #city, #postal_code').val('');
                    $('#state_id, #country_id').val('').trigger('change.select2');

                    // Process address components
                    place.address_components.forEach(function(component) {
                        var type = component.types[0];
                        var value = component.long_name;
                        switch (type) {
                            case 'street_number':
                            case 'sublocality_level_3':
                            case 'sublocality_level_2':
                            case 'route':
                                $('#street_address_1').val(function(i, val) {
                                    return (val ? val + ', ' : '') + value;
                                });
                                break;
                            case 'locality':
                                $('#city').val(value);
                                break;
                            case 'administrative_area_level_1':
                                state = value;
                                break;
                            case 'country':
                                country = value;
                                break;
                            case 'postal_code':
                                $('#postal_code').val(value);
                                break;
                            case 'sublocality':
                            case 'sublocality_level_1':
                                $('#area_locality').val(value);
                                break;
                        }
                    });

                    // Fetch country ID and states
                    fetchCountryId(country, state);
                });
            }

            function fetchCountryId(country, state) {
                $.ajax({
                    url: "{{url('/api/get-country-id')}}",
                    type: 'GET',
                    data: {
                        name: country
                    },
                    success: function(response) {
                        if (response.id) {
                            $('#country_id').val(response.id).trigger('change.select2');
                            fetchStates(response.id, state);
                        }
                    }
                });
            }

            function fetchStates(countryId, state) {
                $.ajax({
                    url: "{{ url('/api/get-states')}}"+"/" + countryId,
                    type: 'GET',
                    success: function(data) {
                        $('#state_id').empty();
                        $('#state_id').append('<option value="" disabled selected>Select state</option>');
                        $.each(data, function(key, value) {
                            $('#state_id').append(
                                `<option value="${key}" ${value === state ? 'selected' : ''}>${value}</option>`
                            );
                        });

                        $('#state_id').trigger('change.select2');
                    }
                });
            }


            $(window).on('load', function() {
                if (typeof google !== 'undefined') {
                    initializeAutocomplete();
                }
            });

            $('#country_id').on('change', function() {
                var countryId = $(this).val();
                fetchStates(countryId);
            });

            $('#driverForm').validate({
                ignore: [],
                rules: {
                    "username": "required",
                    "name": "required",
                    "email": {
                        required: true,
                        email: true
                    },
                    "phone": "required",
                    "password": {
                        required: isRequired,
                    },
                    "confirm_password": {
                        required: isRequired,
                        equalTo: "#password"
                    },
                    "phone": {
                        "required": true,
                        "minlength": 6,
                        "maxlength": 15
                    },
                    "address[country_id]": "required",
                    "address[state_id]": "required",
                    "address[city]": "required",
                    "address[area]": "required",
                    "address[postal_code]": {
                        "required": true,
                        "maxlength": 12
                    },
                    "address[address]": "required",
                    "vehicle_info[vehicle_type_id]": "required",
                    "vehicle_info[model]": "required",
                    "vehicle_info[plate_number]": "required",
                    "vehicle_info[seat]": "required",
                    "vehicle_info[color]": "required",
                    "payment_account[bank_account_no]": "required",
                    "payment_account[bank_name]": "required",
                    "payment_account[bank_holder_name]": "required",
                    "payment_account[swift]": "required",
                    "payment_account[ifsc]": "required",
                    "zones[]": "required"
                }
            });

            function isRequired() {
                return "{{ isset($driver) }}" ? false : true;
            }

        })(jQuery);
    </script>
@endpush
