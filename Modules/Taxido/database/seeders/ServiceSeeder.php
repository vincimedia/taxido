<?php

namespace Modules\Taxido\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Taxido\Models\Service;
use Modules\Taxido\Enums\ServicesEnum;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $services = [
            [
                'name' => ucfirst(ServicesEnum::CAB),
                'type' => 'cab',
                'service_icon_id' => getAttachmentId('cab1.png'),
                'service_image_id' => getAttachmentId('outstation-banner.png'),
                'is_primary' => true,
            ],
            [
                'name' => ucfirst(ServicesEnum::PARCEL),
                'type' => 'parcel',
                'service_icon_id' => getAttachmentId('parcel.png'),
                'service_image_id' => getAttachmentId('ride-banner.png'),
                'is_primary' => false,

            ],
            [
                'name' => ucfirst(ServicesEnum::FREIGHT),
                'type' => 'freight',
                'service_icon_id' => getAttachmentId('freight.png'),
                'service_image_id' => getAttachmentId('rental-banner.png'),
                'is_primary' => false,
            ],
        ];

        foreach ($services as $value) {
            if (!Service::where('name', $value['name'])->first()) {
                Service::create([
                    'name' => $value['name'],
                    'type' => $value['type'],
                    'service_icon_id' =>  $value['service_icon_id'],
                    'service_image_id' =>  $value['service_image_id'],
                    'is_primary' =>  $value['is_primary'],
                ]);
            }
        }
    }
}
