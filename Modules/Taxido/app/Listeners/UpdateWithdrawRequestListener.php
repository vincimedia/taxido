<?php

namespace Modules\Taxido\Listeners;

use App\Models\PushNotificationTemplate;
use App\Models\SmsTemplate;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Taxido\Events\UpdateWithdrawRequestEvent;
use Modules\Taxido\Notifications\UpdateWithdrawRequestNotification;

class UpdateWithdrawRequestListener implements ShouldQueue
{

    public function handle(UpdateWithdrawRequestEvent $event): void
    {
        try {

            $driver = User::where('id', $event->withdrawRequest->driver_id)->first();
            if (isset($driver)) {
                $driver->notify(new UpdateWithdrawRequestNotification($event->withdrawRequest));
                $sendTo = ('+'.$driver?->country_code.$driver?->phone);
                sendSMS($sendTo, $this->getSMSMessage($event->withdrawRequest));
                $message = "A Withdraw Request  Status Has Been Updated";
                $this->sendPushNotification($driver->fcm_token, $message, $event->withdrawRequest);
            }

        } catch (Exception $e) {

            //
        }
    }

    public function getSMSMessage($event)
    {
        $locale = request()->hasHeader('Accept-Lang') ? request()->header('Accept-Lang') : app()->getLocale();
        $slug = 'update-withdraw-request-driver';
        $content = SmsTemplate::where('slug', $slug)->first();
        $driver = User::where('id', $event->driver_id)->first();

        if ($content) {

            $data = [
                '{{driver_name}}' => $driver?->name,
                '{{amount}}'=> $event->amount,
                '{{status}}' => $event->status,
            ];

            $message = str_replace(array_keys($data), array_values($data), $content?->content[$locale]);

        } else {
            $message = "A new Withdraw Request has been created.";
        }

        return $message;
    }

    public function sendPushNotification($token, $message, $event)
    {
        if ($token) {
            $locale = request()->hasHeader('Accept-Lang') ? request()->header('Accept-Lang') : app()->getLocale();
            $slug = 'update-withdraw-request-driver';
            $driver = User::where('id', $event->driver_id)->first();
            $content = PushNotificationTemplate::where('slug', $slug)->first();

            if ($content) {

                $data = [
                    '{{driver_name}}' => $driver?->name,
                    '{{amount}}'=> $event->amount,
                    '{{status}}' => $event->status,
                ];

                $title = str_replace(array_keys($data), array_values($data), $content->title[$locale]);
                $body = str_replace(array_keys($data), array_values($data), $content->content[$locale]);
            } else {
                $title = "Withdraw Request Status Updated";
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
