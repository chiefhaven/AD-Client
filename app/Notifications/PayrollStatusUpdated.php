<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use App\Models\Client;
use App\Models\Payroll;

class PayrollStatusUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $client;
    public $payroll;

    public function __construct(Client $client, Payroll $payroll)
    {
        $this->client = $client;
        $this->payroll = $payroll;
    }

    // Broadcast event to a client-specific channel
    public function broadcastOn()
    {
        return ['client.' . $this->client->id];
    }

    // Event data
    public function broadcastWith()
    {
        return [
            'message' => "Your payroll has been updated to: {$this->payroll->status}",
            'payroll_id' => $this->payroll->id,
            'status' => $this->payroll->status,
        ];
    }
}