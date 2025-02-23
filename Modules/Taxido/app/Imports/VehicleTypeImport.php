<?php

namespace Modules\Taxido\Imports;

use App\Exceptions\ExceptionHandler;
use Modules\Taxido\Models\VehicleType;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class VehicleTypeImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    private $vehicleTypes = [];

    public function rules(): array
    {

        return [
            'name' => ['required', 'string', 'max:255'],
            'min_per_unit_charge' => ['required', 'numeric'],
            'max_per_unit_charge' => ['required', 'numeric'],
            'commission_rate' => ['required', 'numeric'],
            'tax_id' => ['required', 'exists:taxes,id'],
            'status' => ['required', 'integer'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => __('validation.name_field_required'),
            'min_per_unit_charge.required' => __('validation.min_per_unit_charge_field_required'),
            'max_per_unit_charge.required' => __('validation.max_per_unit_charge_field_required'),
            'commission_rate.required' => __('validation.commission_rate_field_required'),
            'tax_id.exists' => __('validation.tax_id_invalid'),
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

    public function getImportedVehicleTypes()
    {
        return $this->vehicleTypes;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $vehicleType = new VehicleType([
            'name' => $row['name'],
            'min_per_unit_charge' => (float)$row['min_per_unit_charge'],
            'max_per_unit_charge' => (float)$row['max_per_unit_charge'],
            'commission_rate' => (float)$row['commission_rate'],
            'tax_id' => $row['tax_id'],
            'status' => (int)$row['status'],
            'created_by_id' => getCurrentUserId() ?? getAdmin()?->id,
        ]);

        $vehicleType->save();

        // If an image URL is provided, handle the media upload
        if (isset($row['vehicle_image'])) {
            $media = $vehicleType->addMediaFromUrl($row['vehicle_image'])->toMediaCollection('vehicle_image');
            $media->save();
            $vehicleType->vehicle_image_id = $media->id;
            $vehicleType->save();
        }

        // If a map icon URL is provided, handle the media upload
        if (isset($row['vehicle_map_icon'])) {
            $media = $vehicleType->addMediaFromUrl($row['vehicle_map_icon'])->toMediaCollection('vehicle_map_icon');
            $media->save();
            $vehicleType->vehicle_map_icon_id = $media->id;
            $vehicleType->save();
        }

        if (isset($row['services'])) {
            $services = explode(',', $row['services']);
            $vehicleType->services()->sync($services);
        }

        if (isset($row['service_categories'])) {
            $serviceCategories = explode(',', $row['service_categories']);
            $vehicleType->service_categories()->sync($serviceCategories);
        }

        if (isset($row['hourly_packages'])) {
            $hourlyPackages = explode(',', $row['hourly_packages']);
            $vehicleType->hourly_packages()->sync($hourlyPackages);
        }

        $this->vehicleTypes[] = [
            'id' => $vehicleType->id,
            'name' => $vehicleType->name,
            'charge_unit' => $vehicleType->charge_unit,
            'min_per_unit_charge' => $vehicleType->min_per_unit_charge,
            'max_per_unit_charge' => $vehicleType->max_per_unit_charge,
            'commission_rate' => $vehicleType->commission_rate,
            'tax_id' => $vehicleType->tax_id,
            'status' => $vehicleType->status,
            'vehicle_image' => $vehicleType->vehicle_image,
            'vehicle_map_icon' => $vehicleType->vehicle_map_icon,
            'zones' => $vehicleType->zones,
            'services' => $vehicleType->services,
            'service_categories' => $vehicleType->service_categories,
            'hourly_packages' => $vehicleType->hourly_packages,
        ];

        return $vehicleType;
    }
}
