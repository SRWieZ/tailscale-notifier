<?php

use App\Events\AskForRefresh;
use App\TailscaleNotifier;
use Illuminate\Support\Facades\Schedule;

// Schedule::command('app:check-status')
//     ->everyTenSeconds();
Schedule::call(fn() => TailscaleNotifier::refreshMenuBar())->everyTenSeconds();
