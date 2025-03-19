<?php

namespace App\Listeners;

use App\Events\PayrollStatusUpdated;
use App\Models\User;
use App\Notifications\PayrollStatusNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Livewire\Livewire;

class SendPayrollStatusNotification implements ShouldQueue
{
    use InteractsWithQueue;

    // public function handle(PayrollStatusUpdated $event)
    // {
    //     $payroll = $event->payroll;

    //     // Find all users related to this payroll (adjust as needed)
    //     $users = User::where('role', 'payroll_manager')->get();

    //     // Notify users via database and email
    //     foreach ($users as $user) {
    //         $user->notify(new PayrollStatusNotification($payroll));
    //     }
    // }

    public function handle(PayrollStatusUpdated $event)
    {
        // Notify Livewire Component
        Livewire::dispatch('payrollUpdated', $event->payroll->status);
    }
}
