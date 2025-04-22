<?php

namespace Ringlesoft\LaravelSelectable\Facades;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static self fromCollection(Collection $collection)
 * @method static string collectionToSelectOptions(Collection $collection, string|Closure|null $label = null, string|Closure|null $value = null, mixed $selected = null, mixed $disabled = null)
 * @method static Collection toCollection()
 * @method static string toSelectOptions()
 * @method static Collection toSelectItems()
 * @method static self withLabel(string|Closure $label)
 * @method static self withValue(string|Closure $value)
 * @method static self withSelected(mixed $selected)
 * @method static self withDisabled(mixed $disabled)
 * @method static self withDataAttribute(string|Closure $attribute, string|Closure $value)
 * @method static self withClass(string|array|Closure $class)
 * @method static self withId(Closure $id)
 */
class Selectable extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \RingleSoft\LaravelSelectable\Selectable::class;
    }
}
