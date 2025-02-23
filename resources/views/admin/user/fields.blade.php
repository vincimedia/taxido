<div class="row">
    <div class="col-xl-10 col-xxl-8 mx-auto">
        <div class="contentbox">
            <div class="inside">
                <div class="contentbox-title">
                    <h3>{{ isset($user) ? __('static.users.edit') : __('static.users.add') }}</h3>
                </div>
                <div class="form-group row">
                    <label class="col-md-2" for="name">{{ __('static.users.full_name') }}<span> *</span></label>
                    <div class="col-md-10">
                        <input class="form-control" value="{{ isset($user->name) ? $user->name : old('name') }}"
                            type="text" name="name" placeholder="{{ __('static.users.enter_full_name') }}" required>
                        @error('name')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2" for="email">{{ __('static.users.email') }}<span> *</span></label>
                    <div class="col-md-10">
                        <input class="form-control" value="{{ isset($user->email) ? $user->email : old('email') }}"
                         type="email" name="email" placeholder="{{ __('static.users.enter_email') }}" required>
                        @error('email')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2" for="phone">{{ __('static.users.phone') }}<span> *</span></label>
                    <div class="col-md-10">
                        <div class="input-group mb-3 phone-detail">
                            <div class="col-sm-1">
                                <select class="select-2 form-control" id="select-country-code" name="country_code">
                                    @foreach (getCountryCodes() as $option)
                                        <option class="option" value="{{ $option->calling_code }}"
                                            data-image="{{ asset('images/flags/' . $option->flag) }}"
                                            @selected($option->calling_code == old('country_code', $user->country_code ?? '1'))>
                                            {{ $option->calling_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-11">
                                <input class="form-control" type="number" name="phone"
                                    value="{{ old('phone', $user->phone ?? '') }}"
                                    placeholder="{{ __('static.users.enter_phone') }}" required>
                            </div>
                            @error('phone')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                @if (request()->routeIs('admin.user.create'))
                    <div class="form-group row">
                        <label class="col-md-2" for="password">{{ __('static.users.new_password') }}<span>
                                *</span></label>
                        <div class="col-md-10">
                            <div class="position-relative">
                                <input class="form-control" type="password" id="password" name="password"
                                    placeholder="{{ __('static.users.enter_password') }}" required>
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
                        <label class="col-md-2" for="confirm_password">{{ __('static.users.confirm_password') }}<span> *</span></label>
                        <div class="col-md-10">
                            <div class="position-relative">
                                <input class="form-control" type="password" name="confirm_password"
                                    placeholder="{{ __('static.users.enter_confirm_password') }}" required>
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
                        <label class="col-md-2 mb-0" for="notify">{{ __('static.users.notification') }}</label>
                        <div class="col-md-10">
                            <div class="form-check p-0 w-auto">
                                <input type="checkbox" name="notify" id="notify" value="1"
                                    class="form-check-input me-2">
                                <label for="notify">{{ __('static.users.sentence') }}</label>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="form-group row">
                    <label class="col-md-2" for="role">{{ __('static.status') }}</label>
                    <div class="col-md-10">
                        <div class="editor-space">
                            <label class="switch">
                                <input class="form-control" type="hidden" name="status" value="0">
                                <input class="form-check-input" type="checkbox" name="status" id=""
                                    value="1" @checked(@$user?->status ?? true)>
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
                                {{ __('static.save') }}
                            </button>
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
            $('#userForm').validate({
                rules: {
                    "name": "required",
                    "email": {
                        "required": true,
                        "email": true
                    },
                    "phone": {
                        "required": true,
                        "minlength": 6,
                        "maxlength": 15
                    },
                    "password": {
                        "required": true,
                        "minlength": 8
                    },
                    "confirm_password": {
                        "required": true,
                        "equalTo": "#password"
                    }
                },
            });
        })(jQuery);
    </script>
@endpush
