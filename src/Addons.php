<?php

namespace Edalzell\Forma;

use Illuminate\Support\Collection;
use Statamic\Extend\Addon;
use Statamic\Facades\Addon as AddonAPI;

class Addons
{
    private Collection $packages;

    public function __construct()
    {
        $this->packages = collect();
    }

    public function add(string $package)
    {
        $this->packages->add($package);
    }

    public function findByHandle(string $handle): Addon
    {
        return AddonAPI::all()->first(function ($addon) use ($handle) {
            return $addon->handle() === $handle;
        });
    }

    public function all(): Collection
    {
        return $this->packages->map(fn ($package) => new FormaAddon(AddonAPI::get($package)));
    }
}
