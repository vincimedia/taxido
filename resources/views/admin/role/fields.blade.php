<div class="row">
    <div class="col-xl-10 col-xxl-8 mx-auto">
        <div class="contentbox">
            <div class="inside">
                <div class="contentbox-title">
                    <h3>{{ isset($role) ? __('static.roles.edit_role') : __('static.roles.add_role') }}</h3>
                </div>
                <div class="roles">
                    <div class="roles-body mb-sm-4 mb-3">
                        <div class="form-group row">
                            <label class="col-md-2" for="name">{{ __('static.roles.name') }}<span> *</span></label>
                            <div class="col-md-10">
                                <div class="position-relative">
                                    <input class="form-control" value="{{ @$role->name ?? old('name') }}" type="text"
                                        name="name" placeholder="{{ __('static.roles.enter_role_name') }}" required>
                                    <div class="invalid-feedback">{{ __('static.roles.name_is_required') }}</div>
                                </div>
                                @error('name')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="permission mt-sm-4 mt-3">
                        <h5>{{ __('static.roles.permissions') }}<span> *</span></h5>
                        @error('permission[]')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <div class="permission-section">
                            <ul>
                                <li>
                                    <label for="all_permissions">
                                        <h5>{{ __('static.roles.select_all_permissions') }} &nbsp;<input type="checkbox"
                                                id="all_permissions" class="checkbox_animated"></h5>
                                    </label>
                                </li>
                                @foreach ($modules as $key => $module)
                                    <li class="h-custom-scrollbar">
                                        <h5 class="text-truncate">{{ ucwords(str_replace('_', ' ', $module->name)) }}:
                                        </h5>
                                        <div class="form-group m-checkbox-inline mb-0 d-flex">
                                            @php
                                                $permissions =
                                                    @$role?->getAllPermissions()->pluck('name')->toArray() ?? [];
                                                $isAllSelected =
                                                    count(array_diff(array_values($module->actions), $permissions)) ===
                                                    0;
                                            @endphp
                                            <label class="d-block" for="all-{{ $module->name }}">
                                                <input type="checkbox"
                                                    class="checkbox_animated select-all-permission select-all-for-{{ $module->name }}"
                                                    id="all-{{ $module->name }}" value="{{ $module->name }}"
                                                    {{ $isAllSelected ? 'checked' : '' }}>{{ __('static.roles.all') }}
                                            </label>
                                            @foreach ($module->actions as $action => $permission)
                                                <label class="d-block" for="{{ $permission }}"
                                                    data-action="{{ $action }}"
                                                    data-module="{{ $module->name }}">
                                                    <input type="checkbox" name="permissions[]"
                                                        class="checkbox_animated module_{{ $module->name }} module_{{ $module->name }}_{{ $action }}"
                                                        value="{{ $permission }}" id="{{ $permission }}"
                                                        {{ in_array($permission, $permissions) ? 'checked' : '' }}
                                                        required>{{ $action }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="form-group row mt-sm-4 mt-3">
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
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#all_permissions').on('change', function() {
                $('.checkbox_animated').prop('checked', $(this).prop('checked'));
            });

            $(document).on('click', '.select-all-permission', function() {
                let value = $(this).prop('value');
                let isChecked = $(this).prop('checked');
                $('.module_' + value).prop('checked', isChecked);

                updateGlobalSelectAll();
            });

            $('.checkbox_animated').not('.select-all-permission').on('change', function() {
                let $this = $(this);
                let $label = $this.closest('label');
                let module = $label.data('module');
                let total_permissions = $('.module_' + module).length;
                let total_checked = $('.module_' + module).filter(':checked').length;

                if (total_checked < total_permissions) {
                    $('.select-all-for-' + module).prop('checked', false);
                }

                if (total_checked === total_permissions) {
                    $('.select-all-for-' + module).prop('checked', true);
                }

                updateGlobalSelectAll();
            });

            function updateGlobalSelectAll() {
                let allChecked = true;
                $('.select-all-permission').each(function() {
                    if (!$(this).prop('checked')) {
                        allChecked = false;
                    }
                });
                $('#all_permissions').prop('checked', allChecked);
            }

            $('#roleForm').validate({
                rule: {
                    "name": "required"
                }
            });
        });
    </script>
@endpush
