<?php

namespace Modules\Taxido\Listeners;

use Exception;
use App\Models\User;
use App\Models\SmsTemplate;
use App\Models\PushNotificationTemplate;
use Modules\Taxido\Events\RejectBiddingEvent;
use Modules\Taxido\Notifications\RejectBiddingNotification;

class RejectBiddingListener
{
    /**
     * Handle the event.
     */
    public function handle(RejectBiddingEvent $event): void
    {
        try {

            $driver = User::where('id', $event->bid->driver_id)->first();
            if(isset($driver))
            {
                $driver->notify(new RejectBiddingNotification($event->bid));
                $sendTo = ('+'.$driver?->country_code.$driver?->phone);
                sendSMS($sendTo, $this->getSMSMessage($event->bid));
                $message = "Your Bid Has Been Rejected";
                $this->sendPushNotification($driver->fcm_token, $message, $event->bid);
            }

        } catch (Exception $e){

            //
        }
    }

    public function getSMSMessage($event)
    {
        $locale = request()->hasHeader('Accept-Lang') ? request()->header('Accept-Lang') : app()->getLocale();
        $slug =  'bid-status-driver';
        $content = SmsTemplate::where('slug', $slug)->first();

        if ($content) {

        $data = [
            '{{driver_name}}' => $event?->driver?->name,
            '{{rider_name}}' => $event?->ride_request?->rider['name'],
            '{{bid_status}}' => $event?->status,
            '{{Your Company Name}}' => config('app.name')
        ];

        $message = str_replace(array_keys($data), array_values($data), $content?->content[$locale]);

        } else {
            $message = "A new ride has been created.";
        }

        return $message;
    }

    public function sendPushNotification($token, $message, $event)
    {
        if ($token) {
            $locale = request()->hasHeader('Accept-Lang') ? request()->header('Accept-Lang') : app()->getLocale();
            $slug = 'bid-status-driver';

            $content = PushNotificationTemplate::where('slug', $slug)->first();

            if ($content) {

                $data = [
                    '{{driver_name}}' => $event?->driver?->name,
                    '{{rider_name}}' => $event?->ride_request?->rider['name'],
                    '{{bid_status}}' => $event?->status,
                    '{{Your Company Name}}' => config('app.name')
                ];


                $title = str_replace(array_keys($data), array_values($data), $content->title[$locale]);
                $body = str_replace(array_keys($data), array_values($data), $content->content[$locale]);
            } else {
                $title = "Bid Rejected";
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
