<?php

namespace App\Events;

use App\TailscaleNotifier;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AskForRefresh
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct()
    {
        TailscaleNotifier::refreshMenuBar();
    }
}
