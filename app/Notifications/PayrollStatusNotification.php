<?php

namespace App\Notifications;

use App\Models\Payroll;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class PayrollStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $payroll;

    public function __construct(Payroll $payroll)
    {
        $this->payroll = $payroll;
    }

    public function via($notifiable)
    {
        return ['database', 'mail', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Payroll Status Updated')
            ->line("The payroll for period {$this->payroll->period} has been updated to: {$this->payroll->status}")
            ->action('View Payroll', url('/payrolls/' . $this->payroll->id))
            ->line('Thank you for using our payroll system.');
    }

    public function toArray($notifiable)
    {
        return [
            'payroll_id' => $this->payroll->id,
            'status' => $this->payroll->status,
            'message' => "Payroll for period {$this->payroll->period} updated to: {$this->payroll->status}",
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'payroll_id' => $this->payroll->id,
            'status' => $this->payroll->status,
            'message' => "Payroll for period {$this->payroll->period} updated to: {$this->payroll->status}",
        ]);
    }
}