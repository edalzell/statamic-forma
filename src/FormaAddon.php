<?php

namespace Edalzell\Forma;

use Illuminate\Support\Facades\Route;
use Statamic\CP\Navigation\Nav;
use Statamic\Extend\Addon;
use Statamic\Facades\Addon as AddonAPI;
use Statamic\Facades\Blink;
use Statamic\Facades\CP\Nav as NavAPI;
use Statamic\Facades\Permission;
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
        $this->bootPermissions();
        $this->registerRoutes();
    }

    private function bootNav()
    {
        if (! $addon = $this->getAddon()) {
            return;
        }

        $controllerInstance = app($this->controller);

        NavAPI::extend(fn (Nav $nav) => $nav
            ->content($addon->name())
            ->section($controllerInstance::cpSection())
            ->can('manage '.$addon->slug().' settings')
            ->route($addon->slug() . '.config.edit')
            ->icon($controllerInstance::cpIcon())
        );
    }

    private function bootPermissions()
    {
        if (! $addon = $this->getAddon()) {
            return;
        }

        Permission::register('manage '.$addon->slug().' settings')
            ->label('Manage '.$addon->name().' Settings');
    }

    private function getAddon(): ?Addon
    {
        return Blink::once($this->addon, fn () => AddonAPI::get($this->addon));
    }

    private function registerRoutes()
    {
        if (! $addon = $this->getAddon()) {
            return;
        }

        Statamic::pushCpRoutes(fn () => Route::name($addon->slug())->prefix($addon->slug())->group(function () {
            Route::name('.config.')->prefix('config')->group(function () {
                Route::get('edit', [$this->controller, 'edit'])->name('edit');
                Route::post('update', [$this->controller, 'update'])->name('update');
            });
        }));
    }
}
