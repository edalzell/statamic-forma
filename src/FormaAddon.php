<?php

namespace Edalzell\Forma;

use Illuminate\Support\Facades\Route;
use Statamic\CP\Navigation\Nav;
use Statamic\Extend\Addon;
use Statamic\Facades\Addon as AddonAPI;
use Statamic\Facades\CP\Nav as NavAPI;
use Statamic\Statamic;

class FormaAddon
{
    private ?Addon $addon;
    private string $controller;

    public function __construct(string $package, ?string $controller)
    {
        $this->addon = AddonAPI::get($package);
        $this->controller = $controller ?: ConfigController::class;
    }

    public function boot()
    {
        $this->bootNav();
        $this->registerRoutes();
    }

    private function bootNav()
    {
        NavAPI::extend(function (Nav $nav) {
            $nav->content($this->addon->name())
                ->section('Addon Settings')
                ->route($this->addon->handle().'.config.edit')
                ->icon('settings-horizontal');
        });
    }

    private function registerRoutes()
    {
        if (! $this->addon) {
            return;
        }

        Statamic::pushCpRoutes(function () {
            Route::name($this->addon->handle())->prefix($this->addon->handle())->group(function () {
                Route::name('.config.')->prefix('config')->group(function () {
                    Route::get('edit', [$this->controller, 'edit'])->name('edit');
                    Route::post('update', [$this->controller, 'update'])->name('update');
                });
            });
        });
    }
}
