<?php

namespace App\Notifications;

use App\Models\Requisition;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class RequisitionProcessedNotification extends Notification
{
    use Queueable;
    protected $requisition;
    /**
     * Create a new notification instance.
     */
    public function __construct(Requisition $requisition)
    {
        $this->requisition = $requisition;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->requisition->requisition_number . '- Has Been Processed')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your requisition ' . $this->requisition->requisition_number . ' has been processed.')
            ->action('View Requisition', url('/admin/requisitions/' . $this->requisition->id . '/view'))
            ->line('Thank you for using our application!');
    }

    // public function toFcm($notifiable): FcmMessage
    // {
    //     return (new FcmMessage(notification: new FcmNotification(
    //             title: $this->requisition->requisition_number.' - Approved',
    //             body: 'Your requisition has been processed.',
    //             // image: 'http://example.com/your-image-url.png' // Optional image
    //         )))
    //         ->data([
    //             'requisition_id' => $this->requisition->id,
    //             'is_approved' => true,
    //             'url' => url('/admin/requisitions/' . $this->requisition->id . '/view'),
    //         ]);

    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'requisition_id' => $this->requisition->id,
            'is_approved' => true
        ];
    }
}
