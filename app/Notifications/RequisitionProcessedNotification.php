<?php

namespace App\Notifications;

use App\Models\Requisition;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

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
                    ->subject($this->requisition->requisition_number.'- Has Been Approved')
                    ->line('Your Requisition Has Been Approved')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line('Your requisition ' . $this->requisition->requisition_number . ' has been processed.')
                    ->action('View Requisition', url('/admin/requisitions/' . $this->requisition->id.'/view'))
                    ->line('Thank you for using our application!');
    }

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
