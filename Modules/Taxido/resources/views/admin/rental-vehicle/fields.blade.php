  <!-- Link Swiper's CSS -->
  <link rel="stylesheet" type="text/css" href="{{ asset('css/vendors/swiper-slider.css') }}">

  @use('Modules\Taxido\Enums\RoleEnum')
  @use('App\Enums\RoleEnum as BaseRoleEnum')
  @php
      $drivers = getAllVerifiedDrivers();
  @endphp
  <div class="col-xl-9">
      <div class="left-part">
          <div class="contentbox">
              <div class="inside">
                  <div class="contentbox-title">
                      <h3>{{ isset($rentalVehicle) ? __('taxido::static.rental_vehicle.edit') : __('taxido::static.rental_vehicle.add') }}({{ request('locale', app()->getLocale()) }})
                      </h3>
                  </div>
                  @isset($rentalVehicle)
                      <div class="form-group row">
                          <label class="col-md-2" for="name">{{ __('taxido::static.language.languages') }}</label>
                          <div class="col-md-10">
                              <ul class="language-list">
                                  @forelse (getLanguages() as $lang)
                                      <li>
                                          <a href="{{ route('admin.rental-vehicle.edit', ['rental_vehicle' => $rentalVehicle->id, 'locale' => $lang->locale]) }}"
                                              class="language-switcher {{ request('locale') === $lang->locale ? 'active' : '' }}"
                                              target="_blank"><img
                                                  src="{{ @$lang?->flag ?? asset('admin/images/No-image-found.jpg') }}"
                                                  alt=""> {{ @$lang?->name }} ({{ @$lang?->locale }})<i
                                                  class="ri-arrow-right-up-line"></i></a>
                                      </li>
                                  @empty
                                      <li>
                                          <a href="{{ route('admin.rental-vehicle.edit', ['rental_vehicle' => $rentalVehicle->id, 'locale' => Session::get('locale', 'en')]) }}"
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
                  @if (getCurrentRoleName() !== RoleEnum::DRIVER)
                      <div class="form-group row">
                          <label class="col-md-2" for="driver">{{ __('taxido::static.reports.driver') }}</label>
                          <div class="col-md-10 select-label-error">
                              <span class="text-gray mt-1">
                                  {{ __('taxido::static.driver_documents.add_driver_message') }}
                                  <a href="{{ route('admin.driver.index') }}" class="text-primary">
                                      <b>{{ __('taxido::static.here') }}</b>
                                  </a>
                              </span>
                              <select class="select-2 form-control filter-dropdown disable-all" id="driver_id"
                                  name="driver_id" data-placeholder="{{ __('taxido::static.reports.select_driver') }}">

                                  @foreach ($drivers as $driver)
                                      <option value="{{ $driver->id }}" @selected(old('driver_id', $rentalVehicle->driver_id ?? '') == $driver->id)>
                                          {{ $driver->name }}
                                      </option>
                                  @endforeach
                              </select>
                          </div>
                      </div>
                  @endif

                  <div class="form-group row">
                      <label class="col-md-2" for="name">{{ __('taxido::static.rental_vehicle.name') }}<span>
                              *</span></label>
                      <div class="col-md-10">
                          <input class="form-control" type="text" id="name" name="name"
                              value="{{ isset($rentalVehicle->name) ? $rentalVehicle->name : old('name') }}"
                              placeholder="{{ __('taxido::static.rental_vehicle.enter_name') }}">
                          @error('name')
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-2"
                          for="description">{{ __('taxido::static.rental_vehicle.description') }}</label>
                      <div class="col-md-10">
                          <textarea class="form-control" id="description" name="description"
                              placeholder="{{ __('taxido::static.rental_vehicle.enter_description') }}">{{ isset($rentalVehicle->description) ? $rentalVehicle->description : old('description') }}</textarea>
                          @error('description')
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-2"
                          for="vehicle_type_id">{{ __('taxido::static.rental_vehicle.vehicle_type') }}<span>
                              *</span></label>
                      <div class="col-md-10 select-label-error">
                          <span class="text-gray mt-1">
                              {{ __('taxido::static.coupons.no_vehicleType_message') }}
                              <a href="{{ @route('admin.vehicle-type.index') }}" class="text-primary">
                                  <b>{{ __('taxido::static.here') }}</b>
                              </a>
                          </span>
                          <select class="form-control select-2"
                              data-placeholder="{{ __('taxido::static.rental_vehicle.select_vehicle_type') }}"
                              id="vehicle_type_id" name="vehicle_type_id">
                              <option value="">{{ __('taxido::static.rental_vehicle.select_vehicle_type') }}
                              </option>
                              @foreach ($vehicleTypes as $vehicleType)
                                  <option value="{{ $vehicleType->id }}" @selected(old('vehicle_type_id', $rentalVehicle->vehicle_type_id ?? '') == $vehicleType->id)>
                                      {{ $vehicleType->name }}
                                  </option>
                              @endforeach
                          </select>
                          @error('vehicle_type_id')
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-2" for="zone_id">{{ __('taxido::static.rental_vehicle.zone') }}<span>
                              *</span></label>
                      <div class="col-md-10 select-label-error">
                          <span class="text-gray mt-1">
                              {{ __('taxido::static.vehicle_types.no_zones_message') }}
                              <a href="{{ @route('admin.zone.index') }}" class="text-primary">
                                  <b>{{ __('taxido::static.here') }}</b>
                              </a>
                          </span>
                          <select class="form-control select-2"
                              data-placeholder="{{ __('taxido::static.rental_vehicle.select_zones') }}" id="zone_id"
                              name="zone_id[]" multiple>

                          </select>
                          @error('zone_id')
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-2"
                          for="vehicle_per_day_price">{{ __('taxido::static.rental_vehicle.vehicle_per_day_price') }}<span>
                              *</span></label>
                      <div class="col-md-10">
                          <input class="form-control" type="number" id="vehicle_per_day_price"
                              name="vehicle_per_day_price" step="0.01"
                              value="{{ isset($rentalVehicle->vehicle_per_day_price) ? $rentalVehicle->vehicle_per_day_price : old('vehicle_per_day_price') }}"
                              placeholder="{{ __('taxido::static.rental_vehicle.enter_vehicle_per_day_price') }}">
                          @error('vehicle_per_day_price')
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-2"
                          for="with_driver">{{ __('taxido::static.rental_vehicle.with_driver') }}</label>
                      <div class="col-md-10">
                          <label class="switch">
                              <input class="form-control" type="hidden" name="is_with_driver" value="0">
                              <input class="form-check-input" type="checkbox" id="is_with_driver"
                                  name="is_with_driver" value="1" @checked(old('is_with_driver', $rentalVehicle->is_with_driver ?? true))>
                              <span class="switch-state"></span>
                          </label>
                          @error('is_with_driver')
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                  </div>

                  <div class="form-group row driver-charge-field"
                      style="display: {{ old('is_with_driver', $rentalVehicle->is_with_driver ?? true) ? 'flex' : 'none' }}">
                      <label class="col-md-2" for="driver_per_day_charge">
                          {{ __('taxido::static.rental_vehicle.driver_per_day_charge') }}<span>
                              *</span>
                      </label>
                      <div class="col-md-10">
                          <input class="form-control" type="number" id="driver_per_day_charge"
                              name="driver_per_day_charge" step="0.01"
                              value="{{ isset($rentalVehicle->driver_per_day_charge) ? $rentalVehicle->driver_per_day_charge : old('driver_per_day_charge') }}"
                              placeholder="{{ __('taxido::static.rental_vehicle.enter_driver_per_day_charge') }}">
                          @error('driver_per_day_charge')
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                  </div>


                  <div class="form-group row">
                      <label class="col-md-2" for="vehicle_subtype">
                          {{ __('taxido::static.rental_vehicle.vehicle_subtype') }}<span>
                              *</span>
                      </label>
                      <div class="col-md-10">
                          <input class="form-control" type="text" id="vehicle_subtype" name="vehicle_subtype"
                              value="{{ isset($rentalVehicle->vehicle_subtype) ? $rentalVehicle->vehicle_subtype : old('vehicle_subtype') }}"
                              placeholder="{{ __('taxido::static.rental_vehicle.enter_vehicle_subtype') }}">
                          @error('vehicle_subtype')
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-2" for="fuel_type">
                          {{ __('taxido::static.rental_vehicle.fuel_type') }}<span>
                              *</span>
                      </label>
                      <div class="col-md-10">
                          <input class="form-control" type="text" id="fuel_type" name="fuel_type"
                              value="{{ isset($rentalVehicle->fuel_type) ? $rentalVehicle->fuel_type : old('fuel_type') }}"
                              placeholder="{{ __('taxido::static.rental_vehicle.enter_fuel_type') }}">
                          @error('fuel_type')
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-2" for="gear_type">
                          {{ __('taxido::static.rental_vehicle.gear_type') }}<span>
                              *</span>
                      </label>
                      <div class="col-md-10">
                          <input class="form-control" type="text" id="gear_type" name="gear_type"
                              value="{{ isset($rentalVehicle->gear_type) ? $rentalVehicle->gear_type : old('gear_type') }}"
                              placeholder="{{ __('taxido::static.rental_vehicle.enter_gear_type') }}">
                          @error('gear_type')
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                  </div>


                  <div class="form-group row">
                      <label class="col-md-2"
                          for="vehicle_speed">{{ __('taxido::static.rental_vehicle.vehicle_speed') }}<span>
                              *</span></label>
                      <div class="col-md-10">
                          <input class="form-control" type="text" id="vehicle_speed" name="vehicle_speed"
                              value="{{ isset($rentalVehicle->vehicle_speed) ? $rentalVehicle->vehicle_speed : old('vehicle_speed') }}"
                              placeholder="{{ __('taxido::static.rental_vehicle.enter_vehicle_speed') }}">
                          @error('vehicle_speed')
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-2" for="mileage">{{ __('taxido::static.rental_vehicle.mileage') }}<span>
                              *</span></label>
                      <div class="col-md-10">
                          <input class="form-control" type="text" id="mileage" name="mileage"
                              value="{{ isset($rentalVehicle->mileage) ? $rentalVehicle->mileage : old('mileage') }}"
                              placeholder="{{ __('taxido::static.rental_vehicle.enter_mileage') }}">
                          @error('mileage')
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-2"
                          for="registration_no">{{ __('taxido::static.rental_vehicle.registration_no') }}<span>
                              *</span></label>
                      <div class="col-md-10">
                          <input class="form-control" type="text" id="registration_no" name="registration_no"
                              value="{{ isset($rentalVehicle->registration_no) ? $rentalVehicle->registration_no : old('registration_no') }}"
                              placeholder="{{ __('taxido::static.rental_vehicle.enter_registration_no') }}">
                          @error('registration_no')
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                  </div>
                  @isset($rentalVehicle)
                      @if (getCurrentRoleName() == BaseRoleEnum::ADMIN)
                          <div class="form-group row">
                              <label for="status" class="col-md-2">
                                  {{ __('taxido::static.driver_documents.status') }}<span>*</span>
                              </label>
                              <div class="col-md-10 select-label-error">
                                  <select class="select-2 form-control" id="status" name="verified_status"
                                      data-placeholder="{{ __('taxido::static.driver_documents.select_status') }}">
                                      <option class="option" value="" selected></option>
                                      <option value="pending" @selected(old('status', @$rentalVehicle?->verified_status) == 'pending')>
                                          {{ __('taxido::static.driver_documents.pending') }}
                                      </option>
                                      <option value="approved" @selected(old('status', @$rentalVehicle?->verified_status) == 'approved')>
                                          {{ __('taxido::static.driver_documents.approved') }}
                                      </option>
                                      <option value="rejected" @selected(old('status', @$rentalVehicle?->verified_status) == 'rejected')>
                                          {{ __('taxido::static.driver_documents.rejected') }}
                                      </option>
                                  </select>
                                  @error('status')
                                      <span class="invalid-feedback d-block" role="alert">
                                          <strong>{{ $message }}</strong>
                                      </span>
                                  @enderror
                              </div>
                          </div>
                      @elseif (getCurrentRoleName() == RoleEnum::DRIVER)
                          <input type="hidden" name="status" value="pending">
                      @endif
                  @endisset
                  <div class="form-group row">
                      <label class="col-md-2" for="status">{{ __('taxido::static.rental_vehicle.status') }}</label>
                      <div class="col-md-10">
                          <label class="switch">
                              <input class="form-control" type="hidden" name="status" value="0">
                              <input class="form-check-input" type="checkbox" id="status" name="status"
                                  value="1" @checked(old('status', $rentalVehicle->status ?? true))>
                              <span class="switch-state"></span>
                          </label>
                          @error('status')
                              <span class="invalid-feedback d-block" role="alert">
                                  <strong>{{ $message }}</strong>
                              </span>
                          @enderror
                      </div>
                  </div>

                  <div id="interior-group">
                      @if (!empty(old('interior', $rentalVehicle->interior ?? [])))
                          @foreach (old('interior', $rentalVehicle->interior ?? []) as $interiorDetail)
                              <div class="form-group row">
                                  <label class="col-md-2" for="interior">
                                      {{ __('taxido::static.rental_vehicle.interior') }}<span>
                                          *</span>
                                  </label>
                                  <div class="col-md-10">
                                      <div class="interior-fields">
                                          <input class="form-control" type="text" name="interior[]"
                                              placeholder="{{ __('taxido::static.rental_vehicle.enter_interior_detail') }}"
                                              value="{{ $interiorDetail }}">
                                          <button type="button" class="btn btn-danger remove-interior">
                                              <i class="ri-delete-bin-line"></i>
                                          </button>
                                      </div>
                                  </div>
                              </div>
                          @endforeach
                      @else
                          <div class="form-group row">
                              <label class="col-md-2" for="interior">
                                  {{ __('taxido::static.rental_vehicle.interior') }}
                              </label>
                              <div class="col-md-10">
                                  <div class="interior-fields">
                                      <input class="form-control" type="text" name="interior[]"
                                          placeholder="{{ __('taxido::static.rental_vehicle.enter_interior_detail') }}">
                                      <button type="button" class="btn btn-danger remove-interior">
                                          <i class="ri-delete-bin-line"></i>
                                      </button>
                                  </div>
                              </div>
                          </div>
                      @endif
                  </div>

                  <button type="button" id="add-interior" class="btn btn-primary mt-2">
                      {{ __('taxido::static.rental_vehicle.add_interior') }}
                  </button>
              </div>
          </div>
      </div>
  </div>
  <div class="col-xl-3">
      <div class="left-part">
          <div class="contentbox">
              <div class="inside">
                  <div class="contentbox-title ">
                      <h3>{{ __('static.blogs.publish') }}</h3>
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
                      <h3>{{ __('taxido::static.rental_vehicle.images') }}</h3>
                      <button class="btn btn-calculate" data-bs-toggle="modal" data-bs-target="#imageHelpModal">
                        <i class="ri-information-line"></i> {{ __('taxido::static.rental_vehicle.view_image_guide') }}
                    </button>

                  </div>
                  <div class="form-group row">
                      <label class="col-md-5" for="normal_image_id">
                          {{ __('taxido::static.rental_vehicle.normal_image') }}<span>*</span>
                      </label>
                      <div class="col-md-7">
                          <div class="form-group">
                              <x-image :name="'normal_image_id'" :data="isset($rentalVehicle->normal_image)
                                  ? $rentalVehicle->normal_image
                                  : old('normal_image_id')" :text="'Normal Image'"
                                  :multiple="false"></x-image>
                              @error('normal_image_id')
                                  <span class="invalid-feedback d-block" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                          </div>
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-5" for="front_view_id">
                          {{ __('taxido::static.rental_vehicle.front_view') }}<span>*</span>
                      </label>
                      <div class="col-md-7">
                          <div class="form-group">
                              <x-image :name="'front_view_id'" :data="isset($rentalVehicle->front_view)
                                  ? $rentalVehicle->front_view
                                  : old('front_view_id')" :text="'Front View'"
                                  :multiple="false"></x-image>
                              @error('front_view_id')
                                  <span class="invalid-feedback d-block" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                          </div>
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-5" for="side_view_id">
                          {{ __('taxido::static.rental_vehicle.side_view') }}<span>*</span>
                      </label>
                      <div class="col-md-7">
                          <div class="form-group">
                              <x-image :name="'side_view_id'" :data="isset($rentalVehicle->side_view)
                                  ? $rentalVehicle->side_view
                                  : old('side_view_id')" :text="'Side View'"
                                  :multiple="false"></x-image>
                              @error('side_view_id')
                                  <span class="invalid-feedback d-block" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                          </div>
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-5" for="boot_view_id">
                          {{ __('taxido::static.rental_vehicle.boot_view') }}<span>*</span>
                      </label>
                      <div class="col-md-7">
                          <div class="form-group">
                              <x-image :name="'boot_view_id'" :data="isset($rentalVehicle->boot_view)
                                  ? $rentalVehicle->boot_view
                                  : old('boot_view_id')" :text="'Boot View'"
                                  :multiple="false"></x-image>
                              @error('boot_view_id')
                                  <span class="invalid-feedback d-block" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                          </div>
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-5" for="interior_image_id">
                          {{ __('taxido::static.rental_vehicle.interior') }}<span>*</span>
                      </label>
                      <div class="col-md-7">
                          <div class="form-group">
                              <x-image :name="'interior_image_id'" :data="isset($rentalVehicle->interior_image)
                                  ? $rentalVehicle->interior_image
                                  : old('interior_image_id')" :text="'Interior'"
                                  :multiple="false"></x-image>
                              @error('interior_image_id')
                                  <span class="invalid-feedback d-block" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                          </div>
                      </div>
                  </div>

                  <div class="form-group row">
                      <label class="col-md-5" for="registration_image_id">
                          {{ __('taxido::static.rental_vehicle.registration_image') }}<span>*</span>
                      </label>
                      <div class="col-md-7">
                          <div class="form-group">
                              <x-image :name="'registration_image_id'" :data="isset($rentalVehicle->registration_image)
                                  ? $rentalVehicle->registration_image
                                  : old('registration_image_id')" :text="'Registration Image'"
                                  :multiple="false"></x-image>
                              @error('registration_image_id')
                                  <span class="invalid-feedback d-block" role="alert">
                                      <strong>{{ $message }}</strong>
                                  </span>
                              @enderror
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <div class="modal fade rental-rides-modal" id="imageHelpModal">
      <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-body">
                  <button type="button" class="btn-close" data-bs-dismiss="modal">
                      <i class="ri-close-line"></i>
                  </button>
                  <div class="swiper rental-images-slider">
                      <div class="swiper-wrapper">
                          <div class="swiper-slide">
                              <img src="{{ asset('modules/taxido/images/defaults/normal.jpg') }}"
                                  class="d-block w-100" alt="Normal Image">
                          </div>
                          <div class="swiper-slide">
                              <img src="{{ asset('modules/taxido/images/defaults/front.jpg') }}"
                                  class="d-block w-100" alt="Front View">
                          </div>
                          <div class="swiper-slide">
                              <img src="{{ asset('modules/taxido/images/defaults/side-view.jpg') }}"
                                  class="d-block w-100" alt="Side View">
                          </div>
                          <div class="swiper-slide">
                              <img src="{{ asset('modules/taxido/images/defaults/boot-view.jpg') }}"
                                  class="d-block w-100" alt="Boot View">
                          </div>
                          <div class="swiper-slide">
                              <img src="{{ asset('modules/taxido/images/defaults/interior.jpg') }}"
                                  class="d-block w-100" alt="Interior View">
                          </div>
                      </div>
                  </div>
                    <div class="swiper rental-content-slider theme-pagination">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="rental-content-box">
                                    <h5>{{ __('taxido::static.rental_vehicle.normal_image') }}</h5>
                                    <p>{{ __('taxido::static.rental_vehicle.normal_image_span') }}</p>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="rental-content-box">
                                    <h5>{{ __('taxido::static.rental_vehicle.front_view') }}</h5>
                                    <p>{{ __('taxido::static.rental_vehicle.front_view_span') }}</p>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="rental-content-box">
                                    <h5>{{ __('taxido::static.rental_vehicle.side_view') }}</h5>
                                    <p>{{ __('taxido::static.rental_vehicle.side_view_span') }}</p>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="rental-content-box">
                                    <h5>{{ __('taxido::static.rental_vehicle.boot_view') }}</h5>
                                    <p>{{ __('taxido::static.rental_vehicle.boot_view_span') }}</p>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div class="rental-content-box">
                                    <h5>{{ __('taxido::static.rental_vehicle.interior') }}</h5>
                                    <p>{{ __('taxido::static.rental_vehicle.interior_span') }}</p>
                                </div>
                            </div>
                        </div>
                      <div class="swiper-pagination pagination-swiper"></div>
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
              $('#rentalVehicleForm').validate({
                  rules: {
                      "name": "required",
                      "vehicle_type_id": "required",
                      "vehicle_subtype": "required",
                      "normal_image_id": "required",
                      "front_view_id": "required",
                      "side_view_id": "required",
                      "boot_view_id": "required",
                      "interior_image_id": "required",
                      "vehicle_per_day_price": "required",
                      "is_with_driver": "required",
                      "driver_per_day_charge": "required",
                      "fuel_type": "required",
                      "gear_type": "required",
                      "vehicle_speed": "required",
                      "mileage": "required",
                      "status": "required",
                      "zone_id[]": "required",
                      "interior[]": "required",
                      "registration_no": "required"
                  }
              });
              $(document).ready(function() {
                  $('#is_with_driver').on('change', function() {
                      if ($(this).is(':checked')) {
                          $('.driver-charge-field').show();
                      } else {
                          $('.driver-charge-field').hide();
                      }
                  });

                  if (!$('#is_with_driver').is(':checked')) {
                      $('.driver-charge-field').hide();
                  }

                  var initialVehicleID = $('#vehicle_type_id').val();
                  var selectedZones = {!! json_encode(old('zone_id', @$rentalVehicle->zones?->pluck('id')?->toArray() ?? [])) !!};

                  if (initialVehicleID) {
                      loadZones(initialVehicleID, selectedZones);
                  }

                  $('#vehicle_type_id').on('change', function() {
                      var vehicleID = $(this).val();
                      loadZones(vehicleID);
                  });

                  function loadZones(vehicleID, selectedZones = []) {
                      let url = "{{ route('admin.rental-vehicle.getVehicleZones', '') }}";
                      if (vehicleID) {
                          $.ajax({
                              url: url + '/' + vehicleID,
                              type: "GET",
                              success: function(data) {
                                  $('#zone_id').empty();
                                  $.each(data, function(id, name) {
                                      var option = new Option(name, id);

                                      if (selectedZones.includes(String(id))) {
                                          $(option).prop("selected", true);
                                      }

                                      $('#zone_id').append(option);
                                  });
                                  $('#zone_id').val(selectedZones).trigger('change');
                              },
                          });
                      } else {
                          $('#zone_id').empty();
                      }
                  }

                  const MAX_INTERIORS = 5;

                  function toggleRemoveButtons() {
                      if ($('#interior-group .form-group').length === 1) {
                          $('#interior-group .remove-interior').hide();
                      } else {
                          $('#interior-group .remove-interior').show();
                      }
                  }

                  $('#add-interior').on('click', function() {
                      const interiorCount = $('#interior-group .form-group').length;

                      if (interiorCount >= MAX_INTERIORS) {

                          toastr.warning('You can add up to 5 interiors only.');
                          return;
                      }

                      var newInteriorField = $('#interior-group .form-group:first').clone();
                      newInteriorField.find('input').val('');
                      $('#interior-group').append(newInteriorField);
                      toggleRemoveButtons();
                  });

                  $(document).on('click', '.remove-interior', function() {
                      $(this).closest('.form-group').remove();
                      toggleRemoveButtons();
                  });

                  toggleRemoveButtons();

              });

              document.addEventListener('DOMContentLoaded', function() {
                  const helpIcons = document.querySelectorAll('.ri-question-line');
                  helpIcons.forEach(icon => {
                      icon.addEventListener('click', () => {});
                  });
              });

          })(jQuery);
      </script>
  @endpush
