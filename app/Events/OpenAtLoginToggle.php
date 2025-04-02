<?php

namespace App\Events;

use App\TailscaleNotifier;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Native\Laravel\Facades\App;

class OpenAtLoginToggle
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct($item, $combo)
    {
        $checked = $item['checked'] ?? false;

        App::openAtLogin($checked);
    }
}
