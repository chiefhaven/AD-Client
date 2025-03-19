<?php

namespace App\Livewire;

use Livewire\Component;

class Notifications extends Component
{
    public $message;

    protected $listeners = ['payrollUpdated' => 'showNotification'];

    public function showNotification($status)
    {
        $this->message = "Payroll Status Updated: " . $status;
    }

    public function render()
    {
        return view('livewire.notifications');
    }
}
