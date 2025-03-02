<?php

namespace Twenty20\Translation\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Twenty20\Translation\Translation
 */
class Translation extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Twenty20\Translation\Translation::class;
    }
}
