<?php

namespace Modules\Taxido\Exports;

use Modules\Taxido\Models\VehicleType;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class VehicleTypesExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * Get the collection of vehicle types to export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $vehicleTypes = VehicleType::whereNull('deleted_at')->latest('created_at');
        return $this->filter($vehicleTypes, request());
    }

    /**
     * Specify the columns for the export.
     *
     * @return array
     */
    public function columns(): array
    {
        return [
            'id',
            'name',
            'slug',
            'vehicle_image_id',
            'vehicle_map_icon_id',
            'min_per_unit_charge',
            'max_per_unit_charge',
            'cancellation_charge',
            'waiting_time_charge',
            'commission_type',
            'commission_rate',
            'tax_id',
            'min_per_min_charge',
            'max_per_min_charge',
            'min_per_weight_charge',
            'max_per_weight_charge',
            'status',
        ];
    }

    /**
     * Map each vehicle type to the appropriate columns.
     *
     * @param VehicleType $vehicleType
     * @return array
     */
    public function map($vehicleType): array
    {
        return [
            $vehicleType->id,
            $vehicleType->name,
            $vehicleType->slug,
            $vehicleType->vehicle_image?->original_url,
            $vehicleType->vehicle_map_icon?->original_url,
            $vehicleType->min_per_unit_charge,
            $vehicleType->max_per_unit_charge,
            $vehicleType->cancellation_charge,
            $vehicleType->waiting_time_charge,
            $vehicleType->commission_type,
            $vehicleType->commission_rate,
            $vehicleType->tax_id,
            $vehicleType->min_per_min_charge,
            $vehicleType->max_per_min_charge,
            $vehicleType->min_per_weight_charge,
            $vehicleType->max_per_weight_charge,
            $vehicleType->status,
        ];
    }

    /**
     * Get the headings for the export file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Slug',
            'Vehicle Image ID',
            'Vehicle Map Icon ID',
            'Min per Unit Charge',
            'Max per Unit Charge',
            'Cancellation Charge',
            'Waiting Time Charge',
            'Commission Type',
            'Commission Rate',
            'Tax ID',
            'Min per Min Charge',
            'Max per Min Charge',
            'Min per Weight Charge',
            'Max per Weight Charge',
            'Status',
        ];
    }

    /**
     * Apply any custom filtering to the query.
     *
     */
    public function filter($vehicleTypes, $request)
    {
        if ($request->has('status')) {
            $vehicleTypes = $vehicleTypes->where('status', $request->status);
        }

        return $vehicleTypes->get();  
    }
}
