<?php

namespace App\Console\Commands;

use App\Events\AskForRefresh;
use Illuminate\Console\Command;

class CheckStatus extends Command
{
    protected $signature = 'app:check-status';

    protected $description = 'Check the status of tailscale';

    public function handle()
    {
        event(new AskForRefresh);
    }
}
