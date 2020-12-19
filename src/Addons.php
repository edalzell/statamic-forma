<?php

namespace Edalzell\Forma;

use Illuminate\Support\Collection;
use Statamic\Extend\Addon;
use Statamic\Facades\Addon as AddonAPI;

class Addons
{
    private Collection $addons;

    public function __construct()
    {
        $this->addons = collect();
    }

    public function add(string $package, string $controller = null)
    {
        $this->addons->add(new FormaAddon($package, $controller));
    }

    public function findByHandle(string $handle): Addon
    {
        return AddonAPI::all()->first(fn ($addon) => $addon->handle() === $handle);
    }

    public function all(): Collection
    {
        return $this->addons;
    }
}
