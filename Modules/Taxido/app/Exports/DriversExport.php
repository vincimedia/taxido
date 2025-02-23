<?php

namespace Modules\Taxido\Exports;

use Modules\Taxido\Models\Driver;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class DriversExport implements FromCollection, WithMapping, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $drivers = Driver::whereNull('deleted_at')->latest('created_at');
        return $this->filter($drivers, request());
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
            'username',
            'name',
            'email',
            'country_code',
            'phone',
            'profile_image_id',
            'is_online',
            'is_on_ride',
            'location',
            'status',
        ];
    }

    public function map($driver): array
    {
        return [
            $driver->id,
            $driver->username,
            $driver->email,
            $driver->country_code,
            $driver->phone,
            $driver->profile_image?->original_url,
            $driver->is_online,
            $driver->is_on_ride,
            $driver->location,
            $driver->status,
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
            'UserName',
            'Email',
            'Country Code',
            'Phone',
            'Profile Image',
            'Is Online',
            'Is On Ride',
            'Location',
            'Status',
        ];
    }

    public function filter($drivers, $request)
    {
        return $drivers->get();
    }
}
