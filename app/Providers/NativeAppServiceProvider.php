<?php

namespace App\Providers;

use App\Events\AskForRefresh;
use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Menu\Menu;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        MenuBar::create()
            ->withContextMenu(
                Menu::new()
                    ->label('Tailscale Notifier')
                    ->separator()
                    ->event(AskForRefresh::class, 'Refresh')
                    ->separator()
                    ->quit()
            )
            ->onlyShowContextMenu();

        event(new AskForRefresh);
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [
        ];
    }
}
