<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewDeviceLogin
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public array $deviceInfo;

    public function __construct(int $userId, array $deviceInfo)
    {
        $this->userId = $userId;
        $this->deviceInfo = $deviceInfo;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }
}
