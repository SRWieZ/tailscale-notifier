<?php

use App\Events\AskForRefresh;
use Illuminate\Support\Facades\Schedule;

// Schedule::command('app:check-status')
//     ->everyTenSeconds();
Schedule::call(fn() => AskForRefresh::dispatch())->everyTenSeconds();
