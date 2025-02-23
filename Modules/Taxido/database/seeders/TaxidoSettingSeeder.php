<?php

namespace Modules\Taxido\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Taxido\Models\TaxidoSetting;

class TaxidoSettingSeeder extends Seeder
{
        /**
         * Run the database seeds.
         *
         * @return void
         */
        public function run()
        {
                $values = [
                        'general' => [
                                'ride_accept' => 15,
                                'greetings' => [
                                        'Hello , Good Morning🌈',
                                        '<p>🌈 Let’s make today productive and successful! 🏆</p>',
                                ]
                        ],
                        'activation' => [
                                'coupon_enable' => true,
                                'driver_wallet' => true,
                                'rider_wallet' => true,
                                'online_payments' => true,
                                'cash_payments' => true,
                                'ride_otp' => true,
                                'parcel_otp' => true,
                                'driver_tips' => true,
                                'allow_driver_negative_balance' => true,
                                'referral_enable' => true,
                                'bidding' => true,
                        ],
                        'wallet' => [
                                'wallet_denominations' => '50',
                                'tip_denominations' => '50',
                        ],
                        'driver_commission' => [
                                'min_withdraw_amount' => 500,
                                'status' => false,
                                'driver_threshold' => '',
                        ],
                        'referral' => [
                                'referral_amount' => '50',
                                'first_ride_discount' => '30',
                                'validity' => '3',
                                'interval' => 'month',
                        ],
                        'location' => [
                                'map_provider' => 'google_map',
                                'radius_meter' => '1000',
                                'radius_per_second' => '10',
                        ]
                ];

                TaxidoSetting::updateOrCreate(['taxido_values' => $values]);
        }
}
