<?php

namespace Edalzell\Forma;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed methodName()
 * @method static void add(string $package, string $controller = null)
 * @method static \Edalzell\Forma\Addon findBySlug(string $slug)
 * @method static \Illuminate\Support\Collection all()
 */
class Forma extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Addons::class;
    }
}
