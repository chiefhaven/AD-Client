<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\Payroll;

class PayrollNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;
    protected $payroll;

    public function __construct($message, Payroll $payroll)
    {
        $this->message = $message;
        $this->payroll = $payroll;
    }

    // Specify delivery channels (email, database, real-time broadcast)
    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    // Format email notification
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Payroll Status Update')
            ->greeting("Hello {$notifiable->name},")
            ->line($this->message)
            ->action('View Payroll', url("/payrolls/{$this->payroll->id}"))
            ->line('Thank you for using our payroll system!');
    }

    // Save notification in database
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'payroll_id' => $this->payroll->id,
            'status' => $this->payroll->status,
        ];
    }

    // Broadcast real-time notification
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'payroll_id' => $this->payroll->id,
            'status' => $this->payroll->status,
        ]);
    }
}