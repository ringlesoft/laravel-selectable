<?php

namespace Ringlesoft\LaravelSelectable\Facades;

use Illuminate\Support\Facades\Facade;
use RingleSoft\LaravelSelectable\LaravelSelectableServiceProvider;

class Selectable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LaravelSelectableServiceProvider::class;
    }
}
