<?php

namespace Edalzell\Forma;

use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

class ServiceProvider extends AddonServiceProvider
{
    public function register()
    {
        $this->app->singleton(Forma::class, fn () => new Forma);
    }

    public function boot()
    {
        parent::boot();

        Statamic::booted(function () {
            Forma::all()->each->boot();
        });
    }
}
