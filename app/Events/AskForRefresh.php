<?php

namespace App\Events;

use App\TailscaleNotifier;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Native\Laravel\Events\Menu\MenuItemClicked;
use Native\Laravel\Events\MenuBar\MenuBarClicked;

class AskForRefresh
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public array $item, public array $combo = [])
    {
        TailscaleNotifier::refreshMenuBar();
    }
}
