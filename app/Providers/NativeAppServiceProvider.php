<?php

namespace App\Providers;

use App\Events\AskForRefresh;
use App\Events\OpenAtLoginToggle;
use Native\Laravel\Contracts\ProvidesPhpIni;
use Native\Laravel\Facades\App;
use Native\Laravel\Facades\Menu;
use Native\Laravel\Facades\MenuBar;

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
                Menu::make(
                    Menu::label('Refresh')->event(AskForRefresh::class)
                    ->accelerator('Command+R'),
                    Menu::checkbox('Open at login', App::openAtLogin())
                        ->event(OpenAtLoginToggle::class),
                    Menu::separator(),
                    Menu::quit(),
                )
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
