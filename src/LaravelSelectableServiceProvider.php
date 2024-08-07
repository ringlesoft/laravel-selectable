<?php

namespace RingleSoft\LaravelSelectable;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class LaravelSelectableServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->alias(\Ringlesoft\LaravelSelectable\Facades\Selectable::class, 'LaravelSelectable');
    }

    public function boot(): void
    {
        $this->publishItems();

        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel_selectable.php', 'laravel_selectable'
        );
        Collection::macro('toSelectOptions', function (
            string|Closure|null $label = null,
            string|Closure|null $value = null,
            mixed               $selected = null,
            mixed               $disabled = null
        ) {
            return Selectable::collectionToSelectOptions($this, $label, $value, $selected, $disabled);
        });
        Collection::macro('toSelectable', function () {
            return Selectable::fromCollection($this);
        });
    }

    private function publishItems(): void
    {
        if (!function_exists('config_path') || !$this->app->runningInConsole()) {
            return;
        }
        $this->publishes([
            __DIR__ . '/../config/laravel_selectable.php' => config_path('laravel_selectable.php'),
        ], 'laravel-selectable-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/ringlesoft/laravel_selectable'),
        ], 'laravel-selectable-views');

        $this->publishes([
            __DIR__ . '/../ide-helper/Collection.php' => base_path('_ide_helper_laravel_selectable.php'),
        ], 'ide-helper');
    }
}
