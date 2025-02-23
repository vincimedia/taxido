<?php
namespace Modules\Taxido\Imports;
use Modules\Taxido\Models\Driver;
use Spatie\Permission\Models\Role;
use Modules\Taxido\Enums\RoleEnum;
use App\Exceptions\ExceptionHandler;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DriverImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    private $drivers = [];

    /**
     * Validation rules for the incoming data
     *
     * @return array
     */
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
            'is_verified' => ['required', 'boolean'],
            'is_online' => ['required', 'boolean'],
            'is_on_ride' => ['required', 'boolean'],
        ];
    }

    /**
     * Custom validation messages
     *
     * @return array
     */
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
            'is_verified.required' => __('validation.is_verified_required'),
            'is_online.required' => __('validation.is_online_required'),
            'is_on_ride.required' => __('validation.is_on_ride_required'),
        ];
    }

    /**
     * Handle errors during the import process
     *
     * @param \Throwable $e
     */
    public function onError(\Throwable $e)
    {
        throw new ExceptionHandler($e->getMessage(), 422);
    }

    /**
     * Get the list of imported drivers
     *
     * @return array
     */
    public function getImportedDrivers()
    {
        return $this->drivers;
    }

    /**
     * Model method to create and save a driver
     *
     */
    public function model(array $row)
    {

        $driver = new Driver([
            'name' => $row['name'],
            'username' => $row['username'],
            'email' => $row['email'],
            'phone' => (string)$row['phone'],
            'country_code' => $row['country_code'],
            'status' => $row['status'],
            'is_verified' => (bool)$row['is_verified'],
            'is_online' => (bool)$row['is_online'],
            'is_on_ride' => (bool)$row['is_on_ride'],
            'password' => Hash::make($row['password']),
        ]);

        $role = Role::where('name', RoleEnum::DRIVER)->first();
        if ($role) {
            $driver->assignRole($role);
        }

        $driver->save();

        if (isset($row['profile_image'])) {
            $media = $driver->addMediaFromUrl($row['profile_image'])->toMediaCollection('attachment');
            $media->save();
            $driver->profile_image_id = $media->id;
            $driver->save();
        }

        $driver = $driver->fresh();

        $this->drivers[] = [
            'id' => $driver->id,
            'name' => $driver->name,
            'username' => $driver->username,
            'email' => $driver->email,
            'country_code' => $driver->country_code,
            'phone' => $driver->phone,
            'status' => $driver->status,
            'is_verified' => $driver->is_verified,
            'is_online' => $driver->is_online,
            'is_on_ride' => $driver->is_on_ride,
            'profile_image' => $driver->profile_image,
        ];

        return $driver;
    }
}
