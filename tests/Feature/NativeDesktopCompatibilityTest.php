<?php

use App\Events\AskForRefresh;
use App\Events\OpenAtLoginToggle;
use App\Providers\NativeAppServiceProvider;
use App\TailscaleNotifier;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Native\Desktop\Contracts\ProvidesPhpIni;

beforeEach(function () {
    config(['nativephp-internal.api_url' => 'http://nativephp.test']);
    Http::preventStrayRequests();
});

it('loads the native provider with the installed desktop contract', function () {
    $provider = new NativeAppServiceProvider;

    expect($provider)->toBeInstanceOf(ProvidesPhpIni::class)
        ->and($provider->phpIni())->toBe([]);
});

it('forwards the open at login checkbox to the desktop API', function (array $item, bool $expected) {
    Http::fake(['nativephp.test/app/open-at-login' => Http::response([])]);

    new OpenAtLoginToggle($item);

    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && $request->url() === 'http://nativephp.test/app/open-at-login'
        && $request['open'] === $expected);
    Http::assertSentCount(1);
})->with([
    'enabled' => [['checked' => true], true],
    'disabled' => [['checked' => false], false],
    'missing checkbox' => [[], false],
]);

it('preserves the context menu using the installed desktop API', function (bool $open) {
    Http::fake([
        'nativephp.test/settings/lastsOnline' => Http::response(['value' => ['laptop', 'server']]),
        'nativephp.test/app/open-at-login' => Http::response(['open' => $open]),
        'nativephp.test/app/version' => Http::response(['version' => '1.2.3']),
        'nativephp.test/menu-bar/context-menu' => Http::response([]),
    ]);

    TailscaleNotifier::updateContextMenu();

    Http::assertSent(function (Request $request) use ($open) {
        if ($request->method() !== 'POST' || $request->url() !== 'http://nativephp.test/menu-bar/context-menu') {
            return false;
        }

        $items = $request['contextMenu'];

        return count($items) === 9
            && $items[0]['label'] === 'Refresh'
            && $items[0]['event'] === AskForRefresh::class
            && $items[0]['accelerator'] === 'Command+R'
            && $items[2]['label'] === 'Connected devices: 2'
            && $items[4]['type'] === 'checkbox'
            && $items[4]['checked'] === $open
            && $items[4]['event'] === OpenAtLoginToggle::class
            && $items[6]['label'] === 'Version: 1.2.3'
            && $items[7]['role'] === 'about'
            && $items[8]['role'] === 'quit'
            && $items[8]['accelerator'] === 'Command+Q';
    });
    Http::assertSentCount(4);
})->with([true, false]);
