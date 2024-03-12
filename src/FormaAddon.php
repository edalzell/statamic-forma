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
    public function __construct(
        private string $package,
        private ?string $controller = ConfigController::class,
        private ?string $config = null
    ) {
    }

    public function boot()
    {
        $this->bootNav()->registerRoutes();
    }

    public function configHandle(): string
    {
        return $this->config ?? $this->statamicAddon()->slug();
    }

    public function statamicAddon(): ?Addon
    {
        return Blink::once($this->package, fn () => AddonFacade::get($this->package));
    }

    private function bootNav(): self
    {
        if (! $addon = $this->statamicAddon()) {
            return $this;
        }

        NavFacade::extend(fn (Nav $nav) => $nav
            ->content($addon->name())
            ->section('Settings')
            ->can('manage addon settings')
            ->route($addon->slug().'.config.edit')
            ->icon('settings-horizontal'));

        return $this;
    }

    private function registerRoutes()
    {
        if (is_null($addon = $this->statamicAddon())) {
            return $this;
        }

        Statamic::pushCpRoutes(fn () => Route::name($addon->slug().'.')->prefix($addon->slug())->group(function () {
            Route::name('config.')->prefix('config')->group(function () {
                Route::get('edit', [$this->controller, 'edit'])->name('edit');
                Route::post('update', [$this->controller, 'update'])->name('update');
            });
        }));
    }
}
