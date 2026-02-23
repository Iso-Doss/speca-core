<?php

namespace Speca\SpecaCore\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Speca\SpecaCore\SpecaCore
 */
class SpecaCore extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Speca\SpecaCore\SpecaCore::class;
    }
}
