<?php

namespace Modules\Taxido\Database\Seeders;

use Illuminate\Database\Seeder;

class DefaultImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultImagePaths = [
            'banner-1.png',
            'banner-2.png',
            'freight.svg',
            'cab-icon.svg',
            'parcel-icon.svg',
            'freight-icon.svg',
            'outstation-banner.png',
            'package.png',
            'cab1.png',
            'parcel.png',
            'freight.png',
            'outstation-image.png',
            'parcel.svg',
            'rental.svg',
            'rental-banner.png',
            'rental-image.png',
            'ride-banner.png',
            'ride-image.png',
            'schedule-banner.png',
            'schedule-image.png',
            'boot-view.png',
            'side-view.png',
            'front.png',
            'normal.png',
            'interior.png'
        ];

        $imageDirectory = module_path('Taxido', 'resources/assets/images/defaults');
        $attachments = createAttachment();
        foreach ($defaultImagePaths as $defaultImagePath) {
            $fullImagePath = $imageDirectory . '/' . $defaultImagePath;
            if (file_exists($fullImagePath)) {
                $attachments->copyMedia($fullImagePath)->toMediaCollection('attachment');
            }
        }

        $attachments->delete($attachments?->id);
    }
}
