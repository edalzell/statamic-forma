<?php

namespace Edalzell\Forma;

use Illuminate\Support\Collection;
use Statamic\Facades\Addon as AddonFacade;

class Addons
{
    private Collection $addons;

    public function __construct()
    {
        $this->addons = collect();
    }

    public function add(string $package, ?string $controller = null, ?string $config = null)
    {
        $this->addons->push(new FormaAddon($package, $controller, $config));
    }

    public function findBySlug(string $slug): FormaAddon
    {
        return $this->all()->first(fn (FormaAddon $addon) => $addon->statamicAddon()->slug() === $slug);
    }

    public function all(): Collection
    {
        return $this->addons;
    }
}
