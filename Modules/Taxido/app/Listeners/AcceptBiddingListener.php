<?php

namespace Modules\Taxido\Listeners;

use Exception;
use App\Models\User;
use App\Enums\RoleEnum;
use App\Models\SmsTemplate;
use App\Models\PushNotificationTemplate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Taxido\Events\AcceptBiddingEvent;
use Modules\Taxido\Enums\RoleEnum as EnumsRoleEnum;
use Modules\Taxido\Notifications\AcceptBiddingNotification;

class AcceptBiddingListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(AcceptBiddingEvent $event): void
    {
        try {

            $admin = User::role(RoleEnum::ADMIN)->first();
            if (isset($admin)) {
                $admin->notify(new AcceptBiddingNotification($event->ride, RoleEnum::ADMIN));
                $sendTo = ('+'.$admin?->country_code.$admin?->phone);
                sendSMS($sendTo, $this?->getSMSMessage($event,RoleEnum::ADMIN));
                $message = "A rider ". $event?->ride->rider['name'] ."has created a new ride";
                $this->sendPushNotification($admin->fcm_token, $message, $event,RoleEnum::ADMIN);
            }
            if ($event->ride->driver) {
                $driver = $event->ride->driver;
                $driver->notify(new AcceptBiddingNotification($event->ride, EnumsRoleEnum::DRIVER));
                $sendTo = ('+'.$driver?->country_code.$driver?->phone);
                sendSMS($sendTo, $this->getSMSMessage($event,EnumsRoleEnum::DRIVER));
                $message = "A rider ". $event?->ride->rider['name'] ."has created a new ride";
                $this->sendPushNotification($driver->fcm_token, $message, $event,EnumsRoleEnum::DRIVER);
            }

        } catch(Exception $e) {

        }
    }

    public function getSMSMessage($event,$role)
    {
        $locale = request()->hasHeader('Accept-Lang') ? request()->header('Accept-Lang') : app()->getLocale();
        $slug = $role === EnumsRoleEnum::DRIVER ? 'create-ride-driver' : 'create-ride-admin';
        $content = SmsTemplate::where('slug', $slug)->first();

        if ($content) {
            $data = [
                '{{driver_name}}' => $event?->ride->driver->name,
                '{{ride_number}}' => $event?->ride->ride_number,
                '{{rider_name}}' => $event?->ride->rider['name'],
                '{{rider_phone}}' => $event?->ride->rider['phone'],
                '{{vehicle_type}}' => $event?->ride->vehicle_type->name,
                '{{services}}' => $event?->ride->service->name,
                '{{service_category}}' => $event?->ride->service_category->name,
                '{{fare_amount}}' => $event?->ride->ride_fare,
                '{{distance}}' => $event?->ride->distance,
                '{{distance_unit}}' => $event?->ride->distance_unit,
                '{{Your Company Name}}' => config('app.name')
            ];

            $message = str_replace(array_keys($data), array_values($data), $content?->content[$locale]);

        } else {
            $message = "A new ride has been created.";
        }

        return $message;
    }

    public function sendPushNotification($token, $message, $event,$role)
    {
        if ($token) {
            $locale = request()->hasHeader('Accept-Lang') ? request()->header('Accept-Lang') : app()->getLocale();
            $slug = $role === EnumsRoleEnum::DRIVER ? 'create-ride-driver' : 'create-ride-admin';
            $content = PushNotificationTemplate::where('slug', $slug)->first();

            if ($content) {
                $data = [
                    '{{driver_name}}' => $event?->ride->driver->name,
                    '{{ride_number}}' => $event?->ride->ride_number,
                    '{{rider_name}}' => $event?->ride->rider['name'],
                    '{{rider_phone}}' => $event?->ride->rider['phone'],
                    '{{vehicle_type}}' => $event?->ride->vehicle_type->name,
                    '{{services}}' => $event?->ride->service->name,
                    '{{service_category}}' => $event?->ride->service_category->name,
                    '{{fare_amount}}' => $event?->ride->ride_fare,
                    '{{distance}}' => $event?->ride->distance,
                    '{{distance_unit}}' => $event?->ride->distance_unit,
                    '{{Your Company Name}}' => config('app.name')
                ];

                $title = str_replace(array_keys($data), array_values($data), $content->title[$locale]);
                $body = str_replace(array_keys($data), array_values($data), $content->content[$locale]);
            } else {
                $title = "New Ride Created";
                $body = $message;
            }

            $notification = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                        'image' => '',
                    ],
                    'data' => [
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'type' => 'service_request',
                    ],
                ],
            ];

            pushNotification($notification);
        }
    }


}
