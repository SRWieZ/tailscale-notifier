<?php

namespace App;

use Native\Laravel\Facades\Notification;
use Native\Laravel\Facades\Settings;

class TailscaleNotifier
{
    public static function checkForChanges(): array
    {
        $lastsOnline = Settings::get('lastsOnline');
        $status = shell_exec('/Applications/Tailscale.app/Contents/MacOS/Tailscale status --json');
        $status = json_decode($status, true);

        $onlines = [];
        $changes = [
            'added' => [],
            'removed' => [],
        ];

        foreach ($status['Peer'] as $peer) {
            if ($peer['Online'] === true) {
                $onlines[] = trim(str_replace($status['MagicDNSSuffix'], '', $peer['DNSName']), '.');
            }
        }

        if (! is_null($lastsOnline)) {
            $changes['added'] = array_diff($onlines, $lastsOnline);
            $changes['removed'] = array_diff($lastsOnline, $onlines);
        }

        Settings::set('lastsOnline', $onlines);

        return $changes;
    }

    public static function refreshMenuBar()
    {
        $changes = self::checkForChanges();

        foreach ($changes['added'] as $change) {
            Notification::title('Tailscale')
                ->message("✅ {$change} is now online")
                ->show();
        }

        foreach ($changes['removed'] as $change) {
            Notification::title('Tailscale')
                ->message("⛔️ {$change} is now offline")
                ->show();
        }
    }
}
