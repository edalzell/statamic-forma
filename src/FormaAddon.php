<?php

namespace Edalzell\Forma;

use Illuminate\Support\Facades\Route;
use Statamic\CP\Navigation\Nav;
use Statamic\Extend\Addon;
use Statamic\Facades\Addon as AddonAPI;
use Statamic\Facades\Blink;
use Statamic\Facades\CP\Nav as NavAPI;
use Statamic\Statamic;

class FormaAddon
{
    private string $addon;
    private string $controller;

    public function __construct(string $package, ?string $controller)
    {
        $this->addon = $package;
        $this->controller = $controller ?: ConfigController::class;
    }

    public function boot()
    {
        $this->bootNav();
        $this->registerRoutes();
    }

    private function bootNav()
    {
        if (! $addon = $this->getAddon()) {
            return;
        }

        NavAPI::extend(fn (Nav $nav) => $nav
            ->content($addon->name())
            ->section('Addon Settings')
            ->route($addon->handle().'.config.edit')
            ->icon('settings-horizontal'));
    }

    private function getAddon(): Addon
    {
        return Blink::once($this->addon, fn () => AddonAPI::get($this->addon));
    }

    private function registerRoutes()
    {
        if (! $this->addon) {
            return;
        }

        Statamic::pushCpRoutes(function () {
            Route::name($this->getAddon()->handle())->prefix($this->getAddon()->handle())->group(function () {
                Route::name('.config.')->prefix('config')->group(function () {
                    Route::get('edit', [$this->controller, 'edit'])->name('edit');
                    Route::post('update', [$this->controller, 'update'])->name('update');
                });
            });
        });
    }
}
