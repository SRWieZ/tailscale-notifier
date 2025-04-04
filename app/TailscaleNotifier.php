<?php

namespace App;

use App\Events\AskForRefresh;
use App\Events\OpenAtLoginToggle;
use Native\Laravel\Facades\App;
use Native\Laravel\Facades\Menu;
use Native\Laravel\Facades\MenuBar;
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

        self::updateContextMenu();

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

    public static function updateContextMenu()
    {
        MenuBar::contextMenu(
            Menu::make(
                Menu::label('Refresh')->event(AskForRefresh::class)
                    ->accelerator('Command+R'),
                Menu::separator(),
                Menu::label('Connected devices: ' . count(Settings::get('lastsOnline', []))),
                Menu::separator(),
                Menu::checkbox('Open at login', App::openAtLogin())
                    ->event(OpenAtLoginToggle::class),
                Menu::separator(),
                Menu::quit(),
            )
        );
    }
}
