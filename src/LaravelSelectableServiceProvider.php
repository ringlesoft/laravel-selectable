<?php

namespace RingleSoft\LaravelSelectable;

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
        Collection::macro('toSelectOptions', function ( $text = null, $value = null, $selected = null) {
            return Selectable::collectionToSelectOptions($this, $text, $value, $selected);
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
            __DIR__ . '/../resources/views' => resource_path('views/vendor/ringlesoft/process_approval'),
        ], 'laravel-selectable-views');

    }
}
