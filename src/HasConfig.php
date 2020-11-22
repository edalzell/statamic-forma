<?php

namespace Edalzell\Forma;

use Illuminate\Support\Facades\Route;
use Statamic\CP\Navigation\Nav;
use Statamic\Extend\Addon;
use Statamic\Facades\Addon as AddonAPI;
use Statamic\Facades\Blink;
use Statamic\Facades\CP\Nav as NavAPI;

trait HasConfig
{
    private Addon $addon;

    public function addConfig(string $package)
    {
        $this->addon = AddonAPI::get($package);

        Blink::store('forma')->put($this->addon->handle(), $package);

        $this->bootConfigNav();
        $this->registerCpRoutes(fn () => $this->registerConfigRoutes());
    }

    private function bootConfigNav()
    {
        NavAPI::extend(function (Nav $nav) {
            $nav->content('Config')
                ->section($this->addon->name())
                ->route($this->addon->handle().'.config.edit', ['handle' => $this->addon->handle()])
                ->icon('settings-horizontal');
        });
    }

    private function registerConfigRoutes()
    {
        Route::name($this->addon->handle())->prefix('{handle}')->group(function () {
            Route::name('.config.')->prefix('config')->group(function () {
                Route::get('edit', [ConfigController::class, 'edit'])->name('edit');
                Route::post('update', [ConfigController::class, 'update'])->name('update');
            });
        });
    }
}
