<?php

namespace Edalzell\Forma;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void add(string $package, string $controller = null, string $config = null)
 * @method static FormaAddon findBySlug(string $slug)
 * @method static \Illuminate\Support\Collection all()
 */
class Forma extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Addons::class;
    }
}
