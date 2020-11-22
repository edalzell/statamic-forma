<?php

namespace Edalzell\Forma;

use Illuminate\Support\Facades\Route;
use Statamic\CP\Navigation\Nav;
use Statamic\Extend\Addon;
use Statamic\Facades\CP\Nav as NavAPI;
use Statamic\Statamic;

class FormaAddon
{
    private Addon $addon;

    public function __construct(Addon $addon)
    {
        $this->addon = $addon;
    }

    public function boot()
    {
        $this->bootNav();
        $this->registerRoutes();
    }

    private function bootNav()
    {
        NavAPI::extend(function (Nav $nav) {
            $nav->content('Config')
                ->section($this->addon->name())
                ->route($this->addon->handle().'.config.edit', ['handle' => $this->addon->handle()])
                ->icon('settings-horizontal');
        });
    }

    private function registerRoutes()
    {
        Statamic::pushCpRoutes(function () {
            Route::name($this->addon->handle())->prefix('{handle}')->group(function () {
                Route::name('.config.')->prefix('config')->group(function () {
                    Route::get('edit', [ConfigController::class, 'edit'])->name('edit');
                    Route::post('update', [ConfigController::class, 'update'])->name('update');
                });
            });
        });
    }
}
