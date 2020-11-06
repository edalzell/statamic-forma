<?php

namespace Edalzell\Forma;

use Statamic\Extend\Addon;
use Statamic\Facades\Addon as AddonAPI;

class Forma
{
    private static Addon $addon;

    public static function registerAddon(string $package)
    {
        static::$addon = AddonAPI::get($package);
    }

    public static function isRegistered(): bool
    {
        return ! is_null(static::$addon);
    }

    public static function directory(): string
    {
        return static::$addon->directory();
    }

    public static function handle(): string
    {
        return static::$addon->handle();
    }

    /**
     * @return string
     */
    public static function name()
    {
        return static::$addon->name();
    }

    public static function getRoute($action): string
    {
        return static::getRouteNamePrefix().$action;
    }

    public static function getRouteNamePrefix(): string
    {
        return static::handle().'.config.';
    }
}
