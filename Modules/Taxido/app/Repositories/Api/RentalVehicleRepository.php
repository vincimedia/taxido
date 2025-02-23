<?php

namespace Modules\Taxido\Repositories\Api;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Enums\RequestEnum;
use Modules\Taxido\Models\RentalVehicle;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class RentalVehicleRepository extends BaseRepository
{
    function model()
    {
        return RentalVehicle::class;
    }

    public function boot()
    {
        try {

            $this->pushCriteria(app(RequestCriteria::class));

        } catch (ExceptionHandler $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function show($id)
    {
        try {

            return $this->model->findOrFail($id);

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {
            $driver = getCurrentDriver();
            $interior = implode(',',$request?->interior);
            $rentalVehicle =  $this->model->create([
                'name' => $request->name,
                'description' => $request->description,
                'vehicle_type_id'=> $request->vehicle_type_id,
                'vehicle_per_day_price' => $request->vehicle_per_day_price,
                'allow_with_driver' => $request->allow_with_driver,
                'driver_per_day_charge' => $request?->driver_per_day_charge,
                'vehicle_subtype' => $request->vehicle_subtype,
                'fuel_type' => $request->fuel_type,
                'gear_type' => $request->gear_type,
                'vehicle_speed' => $request->vehicle_speed,
                'mileage' => $request->mileage,
                'interior' => $interior,
                'status' => $request->status,
                'driver_id' => $driver->id,
                'verified_status' => RequestEnum::PENDING,
                'registration_no' =>$request?->registration_no,
                'bag_count' => $request->bag_count,
                'is_ac' => $request->is_ac
            ]);

            if ($request->hasFile('normal_image')) {
                $this->uploadImage($rentalVehicle, $request->file('normal_image'), 'normal_image_id');
            }

            if ($request->hasFile('side_view')) {
                $this->uploadImage($rentalVehicle, $request->file('side_view'), 'side_view_id');
            }

            if ($request->hasFile('boot_view')) {
                $this->uploadImage($rentalVehicle, $request->file('boot_view'), 'boot_view_id');
            }

            if ($request->hasFile('interior_image')) {
                $this->uploadImage($rentalVehicle, $request->file('interior_image'), 'interior_image_id');
            }

            if ($request->hasFile('front_view')) {
                $this->uploadImage($rentalVehicle, $request->file('front_view'), 'front_view_id');
            }

            if ($request->hasFile('registration_image')) {
                $this->uploadImage($rentalVehicle, $request->file('registration_image'), 'registration_image_id');
            }

            $rentalVehicle?->zones()?->attach($request->zone_ids);

            DB::commit();
            return $rentalVehicle;

        } catch (Exception $e) {
            DB::rollback();

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();

        try {
            $rentalVehicle = $this->model->findOrFail($id);
            if ($request->hasFile('normal_image')) {
                $this->uploadImage($rentalVehicle, $request->file('normal_image'), 'normal_image_id');
            }

            if ($request->hasFile('side_view')) {
                $this->uploadImage($rentalVehicle, $request->file('side_view'), 'side_view_id');
            }

            if ($request->hasFile('boot_view')) {
                $this->uploadImage($rentalVehicle, $request->file('boot_view'), 'boot_view_id');
            }

            if ($request->hasFile('interior_image')) {
                $this->uploadImage($rentalVehicle, $request->file('interior_image'), 'interior_image_id');
            }

            if ($request->hasFile('front_view')) {
                $this->uploadImage($rentalVehicle, $request->file('front_view'), 'front_view_id');
            }

            if ($request->hasFile('registration_image')) {
                $this->uploadImage($rentalVehicle, $request->file('registration_image'), 'registration_image_id');
            }


            $rentalVehicle->zones()->sync($request->zone_ids);

            $rentalVehicle->update($request->except(['normal_image', 'side_view', 'boot_view', 'interior_image', 'front_view', 'zone_ids','registration_image_id']));
            $rentalVehicle->interior = implode(',', $request?->interior);
            $rentalVehicle->save();

            DB::commit();
            return $rentalVehicle;

        } catch (Exception $e) {

            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        try {

           return $this->model->where('id', $id)?->delete();

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    private function uploadImage($rentalVehicle, $imageFile, $imageField)
    {
        $attachment = createAttachment();
        $attachmentId = addMedia($attachment, $imageFile)?->id;
        $rentalVehicle->{$imageField} = $attachmentId;
        $rentalVehicle->save();
    }

    public function status($id, $status)
    {
        try {

            $rentalVehicle = $this->model->findOrFail($id);
            $rentalVehicle->update(['status' => $status]);


            return $rentalVehicle;

        } catch (Exception $e) {

            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }


}
