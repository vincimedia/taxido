<?php

namespace Modules\Taxido\Imports;

use Modules\Taxido\Models\Rider;
use Spatie\Permission\Models\Role;
use Modules\Taxido\Enums\RoleEnum;
use App\Exceptions\ExceptionHandler;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class RiderImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    private $riders = [];

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,NULL,id,deleted_at,NULL'],
            'country_code' => ['required'],
            'phone' => ['required', 'digits_between:6,15', 'unique:users,phone,NULL,id,deleted_at,NULL'],
            'password' => ['required', 'min:8'],
            'status' => ['required'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => __('validation.name_field_required'),
            'username.required' => __('validation.username_field_required'),
            'email.required' => __('validation.email_field_required'),
            'email.unique' => __('validation.email_already_taken'),
            'phone.required' => __('validation.phone_field_required'),
            'phone.unique' => __('validation.phone_already_taken'),
            'phone.digits_between' => __('validation.phone_digits_between'),
            'password.required' => __('validation.password_field_required'),
            'status.required' => __('validation.status_field_required'),
        ];
    }

    /**
     * @param \Throwable $e
     */
    public function onError(\Throwable $e)
    {
        throw new ExceptionHandler($e->getMessage(), 422);
    }

    public function getImportedRiders()
    {
        return $this->riders;
    }

    /**
     * @param array $row
     */
    public function model(array $row)
    {
        $rider = new Rider([
            'name'  => $row['name'],
            'username'  => $row['username'],
            'email' => $row['email'],
            'phone' => (string) $row['phone'],
            'country_code' => $row['country_code'],
            'status' => $row['status'],
            'password' => Hash::make($row['password']),
        ]);

        $role = Role::where('name', RoleEnum::RIDER)->first();
        $rider->assignRole($role);
        $rider->save();

        if (isset($row['profile_image'])) {
            $media = $rider->addMediaFromUrl($row['profile_image'])->toMediaCollection('attachment');
            $media->save();
            $rider->profile_image_id = $media->id;
            $rider->save();
        }

        $rider = $rider->fresh();

        $this->riders[] = [
            'id' => $rider->id,
            'name'  => $rider->name,
            'username'  => $rider->username,
            'email' => $rider->email,
            'country_code' => $rider->country_code,
            'phone' => $rider->phone,
            'status' => $rider->status,
            'profile_image' => $rider->profile_image
        ];

        return $rider;
    }
}
