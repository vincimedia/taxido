<?php

namespace App\Repositories\Api;

use Exception;
use App\Models\Address;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Prettus\Repository\Eloquent\BaseRepository;

class AddressRepository extends BaseRepository
{
    function model()
    {
        return Address::class;
    }

    public function store($request)
    {
        DB::beginTransaction();

        try {

            $user = Auth::user();

            $address = $this->model->create([
                'user_id' => $user->id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'type' => $request->type,
                'postal_code' => $request->postal_code,
                'country_id' => $request->country_id,
                'type' => $request->type,
                'status' => $request->status,
                'state_id' => $request->state_id,
                'address' => $request->address,
                'area' => $request->area,
                'city' => $request->city,
                'code' => $request->code,
                'is_primary' => $request->is_primary,
                'alternative_name' => $request->alternative_name,
                'alternative_phone' => $request->alternative_phone,
            ]);

            DB::commit();

            return response()->json([
                'message' => __('static.addresses.created_successfully'),
                'address' => $address,
            ]);
        } catch (Exception $e) {

            DB::rollBack();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function update($request, $id)
    {
        DB::beginTransaction();

        try {
            $address  = $this->model->findOrFail($id);

            if (auth()->user()->id != $address->user_id) {
                throw new ExceptionHandler('Address id is not valid', Response::HTTP_BAD_REQUEST);
            }

            if ($request['is_primary'] == true) {
                $this->model->where('user_id', Auth::id())->update(['is_primary' => false]);
            }

            $address->update($request);

            DB::commit();

            return response()->json([
                'message' => __('static.addresses.updated_successfully'),
                'address' => $address,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function isPrimary($id)
    {
        DB::beginTransaction();

        try {
            $this->model->where('user_id', Auth::id())->update(['is_primary' => false]);
            $address = $this->model->findOrFail($id);
            $address->update(['is_primary' => true]);

            DB::commit();

            return response()->json([
                'message' => __('static.addresses.primary_updated_successfully'),
                'status' => true,
            ]);
        } catch (Exception $e) {
            DB::rollback();
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function destroy($id)
    {
        try {
            $address = $this->model->findOrFail($id);
            $address->delete();

            return response()->json([
                'message' => __('static.addresses.deleted_successfully'),
            ]);
        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }

    public function changeAddressStatus($request, $id)
    {
        DB::beginTransaction();
        try {
            $address = $this->model->where('id', $id);
            $address->update(['status' => $request->status]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('static.addresses.status_updated_successfully'),
            ]);
        } catch (Exception $e) {
            throw new ExceptionHandler($e->getMessage(), $e->getCode());
        }
    }
}
