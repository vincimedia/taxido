<?php

namespace Modules\Taxido\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Taxido\Enums\ServicesEnum;
use Modules\Taxido\Models\ServiceCategory;
use Modules\Taxido\Enums\ServiceCategoryEnum;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $serviceCategories = [
            [
                'name' => ucfirst(ServiceCategoryEnum::RIDE),
                'services' => [ServicesEnum::CAB, ServicesEnum::PARCEL],
                'description' => __('taxido::static.service_categories.intercity_desc'),
                'service_category_image_id' => getAttachmentId('ride-image.png'),
            ],
            [
                'name' => ucfirst(ServiceCategoryEnum::INTERCITY),
                'services' => [ServicesEnum::CAB, ServicesEnum::FREIGHT],
                'description' => __('taxido::static.service_categories.intracity_dec'),
                'service_category_image_id' => getAttachmentId('outstation-image.png'),
            ],
            [
                'name' =>  ucfirst(ServiceCategoryEnum::PACKAGE),
                'services' => [ServicesEnum::CAB],
                'description' => __('taxido::static.service_categories.package_dec'),
                'service_category_image_id' => getAttachmentId('package.png'),
            ],
            [
                'name' => ucfirst(ServiceCategoryEnum::SCHEDULE),
                'services' =>  [ServicesEnum::CAB, ServicesEnum::FREIGHT],
                'description' => __('taxido::static.service_categories.scheduled_dec'),
                'service_category_image_id' => getAttachmentId('schedule-image.png'),
            ],
            [
                'name' => ucfirst(ServiceCategoryEnum::RENTAL),
                'services' =>  [ServicesEnum::CAB],
                'description' => __('taxido::static.service_categories.rental_desc'),
                'service_category_image_id' => getAttachmentId('rental-image.png'),
            ],
        ];

        foreach ($serviceCategories as $value) {
            $category = ServiceCategory::where('name', $value['name'])->first();
            if (!$category) {
                $category = ServiceCategory::create([
                    'name' => $value['name'],
                    'description' => $value['description'],
                    'service_category_image_id' => $value['service_category_image_id'],
                ]);
            }
            $serviceIds = getServiceIdsBySlugs($value['services']);
            $category->services()->sync($serviceIds); 
        }

    }
}
