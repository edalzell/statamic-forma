<?php

namespace Edalzell\Forma;

use Illuminate\Support\Facades\Route;
use Statamic\CP\Navigation\Nav;
use Statamic\Facades\CP\Nav as NavAPI;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->bootNav();

        Statamic::booted(fn () => $this->registerCpRoutes(fn () => $this->registerRoutes()));
    }

    private function bootNav()
    {
        NavAPI::extend(fn (Nav $nav) => $nav->content('Config')
            ->section(Forma::name())
            ->route(Forma::getRoute('edit'))
            ->icon('settings-horizontal')
        );
    }

    private function registerRoutes()
    {
        if (Forma::isRegistered()) {
            $handle = Forma::handle();
            Route::name(Forma::getRouteNamePrefix())->prefix("{$handle}/config")->group(function () {
                Route::get('edit', [ConfigController::class, 'edit'])->name('edit');
                Route::post('update', [ConfigController::class, 'update'])->name('update');
            });
        }
    }
}
