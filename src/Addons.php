<?php

namespace Edalzell\Forma;

use Illuminate\Support\Collection;
use Statamic\Extend\Addon;
use Statamic\Facades\Addon as AddonAPI;

class Addons
{
    private array $addons = [];

    public function add(string $package, string $controller = null)
    {
        $this->addons[$package] = $controller;
    }

    public function findBySlug(string $slug): Addon
    {
        return AddonAPI::all()->first(fn ($addon) => $addon->slug() === $slug);
    }

    public function all(): Collection
    {
        return collect($this->addons)->map(fn ($controller, $package) => new FormaAddon($package, $controller));
    }
}
