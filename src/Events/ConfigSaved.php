<?php

namespace Edalzell\Forma\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Statamic\Extend\Addon;

class ConfigSaved
{
    use Dispatchable;

    public function __construct(public array $config, public Addon $addon) {}
}
