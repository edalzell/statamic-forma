<?php

namespace Edalzell\Forma;

use Illuminate\Support\Facades\Route;
use Statamic\CP\Navigation\Nav;
use Statamic\Extend\Addon;
use Statamic\Facades\Addon as AddonFacade;
use Statamic\Facades\Blink;
use Statamic\Facades\CP\Nav as NavFacade;
use Statamic\Statamic;

class FormaAddon
{
    private string $addon;

    private string $controller;

    private ?string $handle;

    public function __construct(string $package, ?string $controller = null, ?string $handle = null)
    {
        $this->addon = $package;
        $this->controller = $controller ?: ConfigController::class;
        $this->handle = $handle;
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

        NavFacade::extend(fn (Nav $nav) => $nav
            ->content($addon->name())
            ->section('Settings')
            ->can('manage addon settings')
            ->route($this->handle().'.config.edit')
            ->icon('settings-horizontal'));
    }

    private function getAddon(): ?Addon
    {
        return Blink::once($this->addon, fn () => AddonFacade::get($this->addon));
    }

    private function handle(): ?string
    {
        return $this->handle ?? $this->getAddon()->slug();
    }

    private function registerRoutes()
    {
        if (! $handle = $this->handle()) {
            return;
        }

        Statamic::pushCpRoutes(fn () => Route::name($handle)->prefix($handle)->group(function () {
            Route::name('.config.')->prefix('config')->group(function () {
                Route::get('edit', [$this->controller, 'edit'])->name('edit');
                Route::post('update', [$this->controller, 'update'])->name('update');
            });
        }));
    }
}
