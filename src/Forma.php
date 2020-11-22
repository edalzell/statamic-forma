<?php

namespace Edalzell\Forma;

use Illuminate\Support\Facades\Facade;

class Forma extends Facade
{
    protected static function getFacadeAccessor()
    {
        return Addons::class;
    }
}
