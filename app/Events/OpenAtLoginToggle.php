<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Native\Laravel\Facades\App;

class OpenAtLoginToggle
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public array $item, public ?array $combo = [])
    {
        $checked = $item['checked'] ?? false;

        App::openAtLogin($checked);
    }
}
