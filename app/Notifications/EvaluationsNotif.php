<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EvaluationsNotif extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */


    public function __construct(
        public string $status = "",
        public int $evaluation_id = 0,
        public int $employee_id = 0,
        public int $evaluator_id = 0,
        public string $message = ""
    ) {
        $this->status           =  $status;
        $this->evaluation_id    =  $evaluation_id;
        $this->employee_id      =  $employee_id;
        $this->evaluator_id     =  $evaluator_id;
        $this->message          =  $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
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
            //
        ];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'status'        => $this->status,
            'evaluation_id' => $this->evaluation_id,
            'employee_id'   => $this->employee_id,
            'evaluator_id'  => $this->evaluator_id,
            'message'       => $this->message,
        ];
    }
}
