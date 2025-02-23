<?php

namespace Modules\Taxido\Notifications;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UpdateWithdrawRequestNotification extends Notification
{
    use Queueable;

    private $withdrawRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct($withdrawRequest)
    {
        $this->withdrawRequest = $withdrawRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $content = EmailTemplate::where('slug','update-withdraw-request-driver')->first();
        $driver = User::where('id', $this->withdrawRequest->driver_id)->pluck('name')->first();

        $locale = request()->hasHeader('Accept-Lang') ?
        request()->header('Accept-Lang') :
        app()->getLocale();

        $data = [
            '{{driver_name}}' => $driver,
            '{{amount}}'=> $this->withdrawRequest?->amount,
            '{{status}}' => $this->withdrawRequest?->status
        ];

        $emailContent = str_replace(array_keys($data), array_values($data),$content->content[$locale]);

        return (new MailMessage)
                ->subject($content->title[$locale])
                ->markdown('taxido::emails.email-template', ['content' => $content, 'emailContent' => $emailContent ,'locale' => $locale]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // for driver
        $symbol = getDefaultCurrencyCode();
        return [
            'title' => "Withdraw Request is {$this->withdrawRequest->status} by admin",
            'message' => "Your withdrawal request for {$symbol}{$this->withdrawRequest->amount} has been {$this->withdrawRequest->status}",
            'type' => "withdraw"
        ];
    }
}
