<?php

namespace Edalzell\Forma;

use Illuminate\Support\Collection;
use Statamic\Extend\Addon;
use Statamic\Facades\Addon as AddonAPI;

class Forma
{
    private static ?Collection $addons = null;

    public static function registerAddon(string $package, $controllerClass = ConfigController::class)
    {
        if (! static::$addons) {
            static::$addons = collect();
        }

        if (! $addon = AddonAPI::get($package)) {
            return;
        }
        static::$addons->put($addon->handle(), $addon);
    }

    public static function addons(): Collection
    {
        return static::$addons ?? collect();
    }

    public static function getAddon($handle): Addon
    {
        return static::$addons->get($handle);
    }
}
