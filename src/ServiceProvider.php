<?php

namespace Edalzell\Forma;

use Illuminate\Support\Facades\Route;
use Statamic\CP\Navigation\Nav;
use Statamic\Extend\Addon;
use Statamic\Facades\CP\Nav as NavAPI;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    public function boot()
    {
        parent::boot();

        Statamic::booted(function () {
            $this->bootNav();
            $this->registerCpRoutes(fn () => $this->registerRoutes());
        });
    }

    private function bootNav()
    {
        Forma::addons()->each(function (Addon $addon) {
            NavAPI::extend(fn (Nav $nav) => $nav->content('Config')
                ->section($addon->name())
                ->route($addon->handle().'.config.edit', ['handle' => $addon->handle()])
                ->icon('settings-horizontal')
            );
        });
    }

    private function registerRoutes()
    {
        Forma::addons()->each(function (Addon $addon) {
            Route::name($addon->handle())->prefix('{handle}')->group(function () {
                Route::name('.config.')->prefix('config')->group(function () {
                    Route::get('edit', [ConfigController::class, 'edit'])->name('edit');
                    Route::post('update', [ConfigController::class, 'update'])->name('update');
                });
            });
        });
    }
}
