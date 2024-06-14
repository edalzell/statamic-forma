<?php

namespace Edalzell\Forma;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    public function register()
    {
        $this->app->singleton(Forma::class, fn () => new Forma);
    }

    public function bootAddon()
    {
        Forma::all()->each->boot();
    }
}
