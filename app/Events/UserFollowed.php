<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserFollowed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $follower_name;
    public $followed_id;

    public function __construct(User $follower, $followed_id)
    {
        $this->follower_name = $follower->name; // Nama orang yang nge-follow
        $this->followed_id = $followed_id;
    }

    public function broadcastOn(): array
    {
        // Kirim ke channel milik orang yang di-follow
        return [
            new PrivateChannel('chat.' . $this->followed_id),
        ];
    }
}