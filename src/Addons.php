<?php

namespace Edalzell\Forma;

use Illuminate\Support\Collection;
use Statamic\Extend\Addon;
use Statamic\Facades\Addon as AddonFacade;

class Addons
{
    private array $addons = [];

    public function add(string $package, string $controller = null, string $handle = null)
    {
        $this->addons[$package] = [
            'controller' => $controller,
            'handle' => $handle,
        ];
    }

    public function findBySlug(string $slug): Addon
    {
        dd($this->addons);
        return AddonFacade::all()->first(fn ($addon) => $addon->slug() === $slug);
    }

    public function all(): Collection
    {
        return collect($this->addons)
            ->map(fn ($config, $package) => new FormaAddon(
                $package,
                ...$config
            ));
    }
}
