<?php

namespace Edalzell\Forma\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Statamic\Extend\Addon;

class ConfigSaved
{
    use Dispatchable, SerializesModels;

    public function __construct(public array $config, public Addon $addon)
    {
    }
}
